<?php
// ============================================================
//  Email Configuration — Update these with your Gmail details
// ============================================================
//
//  HOW TO GET A GMAIL APP PASSWORD:
//  1. Go to https://myaccount.google.com/security
//  2. Enable 2-Step Verification
//  3. Search "App Passwords" → Create one → copy the 16-char code
//  4. Paste it below (remove spaces)
//
define('MAIL_SMTP_HOST', 'ssl://smtp.gmail.com');
define('MAIL_SMTP_PORT', 465);
define('MAIL_FROM_ADDRESS', 'mrlachu04@gmail.com');   // <-- Change this
define('MAIL_FROM_PASSWORD', 'pvcv hzfz jqay ntkg'); // <-- Change this
define('MAIL_FROM_NAME', 'AgroCulture Farm');
define('OTP_EXPIRY_MINUTES', 10);
