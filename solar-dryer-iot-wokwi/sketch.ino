// ================================================================
//  Farm Solar Dryer  —  ESP32  (Wokwi Simulator)
//  Bidirectional IoT:
//    → Reads DHT22 + Soil-moisture potentiometer
//    → POSTs sensor data + fan_status to PHP backend
//    → GETs control commands from PHP backend every 5 s
//    → Controls fan relay (LED on pin 26 simulates relay)
//    → Auto-controls fan based on target temperature
//    → Posts alerts when thresholds are breached
//
//  Wokwi project: https://wokwi.com/projects/457011489532622849
// ================================================================

#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <ArduinoJson.h>

// --- Wi-Fi ---
const char* SSID     = "Wokwi-GUEST";
const char* PASSWORD = "";

// --- YOUR ngrok base URL (no trailing slash) ---
//  Update this whenever ngrok restarts
const char* BASE_URL = "http://unsuppliable-fretless-rosita.ngrok-free.dev/FARM_MANAGEMENT_SYSTEM/api";

// --- Pins ---
#define DHT_PIN      4     // DHT22 data
#define DHT_TYPE     DHT22
#define SOIL_PIN     34    // Potentiometer (simulates capacitive soil sensor)
#define FAN_PIN      26    // LED/Relay: HIGH = fan ON, LOW = fan OFF
#define STATUS_LED   2     // Built-in LED: heartbeat blink

// --- Timing ---
#define SENSOR_INTERVAL_MS   5000   // Send readings every 5 s
#define CMD_INTERVAL_MS      5000   // Poll commands every 5 s

// --- Thresholds (overridden by server commands) ---
float targetTemp   = 50.0;   // degrees C: ideal drying temp
float dangerTemp   = 65.0;   // degrees C: critical - turn fan OFF to protect crop
float tooLowTemp   = 35.0;   // degrees C: too cold - fan ON to help air circulation
float highHumidity = 80.0;   // %: alert threshold

// --- State ---
bool  fanOn          = false;
bool  sessionActive  = false;
bool  autoMode       = true;   // auto = server controls fan based on temp
int   sessionId      = -1;
bool  emergencyStop  = false;

DHT dht(DHT_PIN, DHT_TYPE);

unsigned long lastSensor = 0;
unsigned long lastCmd    = 0;

// =================================================================
void setup() {
    Serial.begin(115200);
    delay(200);

    pinMode(FAN_PIN,    OUTPUT);
    pinMode(STATUS_LED, OUTPUT);
    digitalWrite(FAN_PIN,    LOW);
    digitalWrite(STATUS_LED, LOW);

    dht.begin();
    Serial.println("\n[INIT] Farm Solar Dryer starting...");

    connectWiFi();
}

// =================================================================
void loop() {
    unsigned long now = millis();

    // Heartbeat blink
    digitalWrite(STATUS_LED, (now / 500) % 2);

    // 1. Read sensors + send data
    if (now - lastSensor >= SENSOR_INTERVAL_MS) {
        lastSensor = now;
        readAndSend();
    }

    // 2. Get commands from server
    if (now - lastCmd >= CMD_INTERVAL_MS) {
        lastCmd = now;
        fetchCommands();
    }
}

// =================================================================
void connectWiFi() {
    Serial.print("[WiFi] Connecting to ");
    Serial.println(SSID);
    WiFi.begin(SSID, PASSWORD);
    int tries = 0;
    while (WiFi.status() != WL_CONNECTED && tries < 30) {
        delay(500);
        Serial.print(".");
        tries++;
    }
    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\n[WiFi] Connected! IP: " + WiFi.localIP().toString());
    } else {
        Serial.println("\n[WiFi] FAILED - will retry in loop");
    }
}

// =================================================================
void readAndSend() {
    if (WiFi.status() != WL_CONNECTED) { connectWiFi(); return; }

    // Read DHT22
    float temp  = dht.readTemperature();
    float humid = dht.readHumidity();
    if (isnan(temp) || isnan(humid)) {
        Serial.println("[DHT] Read failed, skipping send.");
        return;
    }

    // Read soil moisture (pot: 0-4095 -> 0-100%)
    int raw  = analogRead(SOIL_PIN);
    int soil = map(raw, 0, 4095, 0, 100);

    // -- Auto fan control --
    if (!emergencyStop && autoMode) {
        if (temp >= dangerTemp) {
            setFan(false);   // DANGER: stop fan, protect crop
        } else if (sessionActive && temp < tooLowTemp) {
            setFan(true);    // Too cold: run fan to push hot air
        } else if (sessionActive && temp >= targetTemp + 2.0) {
            setFan(false);   // Over target: rest fan
        } else if (sessionActive && temp < targetTemp) {
            setFan(true);    // Below target: run fan
        }
    }

    Serial.printf("[SENSOR] T=%.1f C  H=%.1f%%  Soil=%d%%  Fan=%s\n",
                  temp, humid, soil, fanOn ? "ON" : "OFF");

    // -- POST to insert_sensor.php --
    HTTPClient http;
    String url = String(BASE_URL) + "/insert_sensor.php";
    http.begin(url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    http.addHeader("ngrok-skip-browser-warning", "1");

    String body = "temperature=" + String(temp, 2) +
                  "&humidity="    + String(humid, 2) +
                  "&soil_moisture=" + String(soil) +
                  "&farm_id=1"     +
                  "&fan_status="   + (fanOn ? "1" : "0");

    int code = http.POST(body);
    if (code == HTTP_CODE_OK) {
        Serial.println("[POST] Sensor data sent OK");
    } else {
        Serial.printf("[POST] Failed, HTTP %d\n", code);
    }
    http.end();

    // -- Alert logic --
    String alertType = "";
    String alertMsg  = "";
    if (temp >= dangerTemp) {
        alertType = "DANGER";
        alertMsg  = "DANGER: " + String(temp,1) + "C exceeds danger limit " + String(dangerTemp,0) + "C!";
    } else if (temp >= 60.0) {
        alertType = "OVERHEAT";
        alertMsg  = "WARNING: Temp " + String(temp,1) + "C too high (>60C)";
    } else if (temp < tooLowTemp && sessionActive) {
        alertType = "TOO_COLD";
        alertMsg  = "WARNING: Temp " + String(temp,1) + "C too low (<35C)";
    } else if (humid > highHumidity) {
        alertType = "HIGH_HUMIDITY";
        alertMsg  = "WARNING: Humidity " + String(humid,1) + "% too high (>80%)";
    }

    if (alertType.length() > 0) {
        postAlert(alertType, alertMsg, temp, humid);
    }
}

// =================================================================
void fetchCommands() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    String url = String(BASE_URL) + "/get_commands.php?farm_id=1";
    http.begin(url);
    http.addHeader("ngrok-skip-browser-warning", "1");
    http.setTimeout(4000);

    int code = http.GET();
    if (code != HTTP_CODE_OK) {
        Serial.printf("[CMD] GET failed, HTTP %d\n", code);
        http.end();
        return;
    }

    String payload = http.getString();
    http.end();

    // Parse JSON
    StaticJsonDocument<512> doc;
    DeserializationError err = deserializeJson(doc, payload);
    if (err) {
        Serial.println("[CMD] JSON parse error");
        return;
    }

    // Update state from server
    bool newSession = doc["session_active"] | false;
    if (newSession != sessionActive) {
        sessionActive = newSession;
        Serial.printf("[CMD] Session %s\n", sessionActive ? "STARTED" : "STOPPED");
        if (!sessionActive) { setFan(false); }
    }
    sessionId  = doc["session_id"] | -1;
    targetTemp = doc["target_temp"] | 50.0f;
    dangerTemp = doc["danger_temp"] | 65.0f;

    // Execute command
    const char* cmd = doc["command"];
    if (cmd) {
        String cmdStr = String(cmd);
        Serial.println("[CMD] Received: " + cmdStr);

        if (cmdStr == "START_SESSION") {
            sessionActive = true;
            emergencyStop = false;
            autoMode      = true;
            setFan(true);
        } else if (cmdStr == "STOP_SESSION") {
            sessionActive = false;
            setFan(false);
        } else if (cmdStr == "START_FAN") {
            emergencyStop = false;
            setFan(true);
        } else if (cmdStr == "STOP_FAN") {
            setFan(false);
        } else if (cmdStr == "EMERGENCY_STOP") {
            emergencyStop = true;
            sessionActive = false;
            setFan(false);
            Serial.println("[CMD] *** EMERGENCY STOP ACTIVATED ***");
        } else if (cmdStr == "SET_TEMP") {
            JsonObject val = doc["command_value"];
            if (val.containsKey("target_temp")) {
                targetTemp = val["target_temp"];
                Serial.printf("[CMD] Target temp set to %.1f C\n", targetTemp);
            }
        }
    }
}

// =================================================================
void setFan(bool on) {
    if (fanOn == on) return;  // no change
    fanOn = on;
    digitalWrite(FAN_PIN, on ? HIGH : LOW);
    Serial.printf("[FAN] Turned %s\n", on ? "ON" : "OFF");
}

// =================================================================
void postAlert(String type, String msg, float temp, float humid) {
    HTTPClient http;
    String url = String(BASE_URL) + "/log_alert.php";
    http.begin(url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    http.addHeader("ngrok-skip-browser-warning", "1");
    String body = "type=" + type +
                  "&message=" + msg +
                  "&temperature=" + String(temp,2) +
                  "&humidity=" + String(humid,2);
    int code = http.POST(body);
    http.end();
    if (code == HTTP_CODE_OK) Serial.println("[ALERT] Logged: " + type);
}