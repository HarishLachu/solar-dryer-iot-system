<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>AgroCulture</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link href="bootstrap\css\bootstrap.min.css" rel="stylesheet">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="bootstrap\js\bootstrap.min.js"></script>
		<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="login.css"/>
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
		<link rel="stylesheet" href="indexfooter.css" />
		<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
	</head>

	<?php
		require 'menu.php';
	?>

		<!-- Banner -->
			<section id="banner" class="wrapper">
				<div class="container">
				<h2>AgroCulture</h2>
				<p>Your Product Our Market</p>
				<br><br>
				<center>
					<div class="row uniform">
						<div class="6u 12u$(xsmall)">
							<button class="button fit" onclick="document.getElementById('id01').style.display='block'" style="width:auto">LOGIN</button>
						</div>

						<div class="6u 12u$(xsmall)">
							<button class="button fit" onclick="document.getElementById('id02').style.display='block'" style="width:auto">REGISTER</button>
						</div>
					</div>
				</center>


			</section>

		<!-- One -->
			<section id="one" class="wrapper style1 align-center">
				<div class="container">
					<header>
						<h2>AgroCulture</h2>
						<p>Explore the new way of trading...</p>
					</header>
					<div class="row 200%">
						<section class="4u 12u$(small)">
							<i class="icon big rounded fa-clock-o"></i>
							<p>Digital Market</p>
						</section>
						<section class="4u 12u$(small)">
							<i class="icon big rounded fa-comments"></i>
							<p>Agro-Blog</p>
						</section>
						<section class="4u$ 12u$(small)">
							<i class="icon big rounded fa-user"></i>
							<p>Register with us</p>
						</section>
					</div>
				</div>
			</section>


		<!-- Footer -->
		<footer class="footer-distributed" style="background-color:black" id="aboutUs">
		<center>
			<h1 style="font: 35px calibri;">About Us</h1>
		</center>
		<div class="footer-left">
			<h3 style="font-family: 'Times New Roman', cursive;">AgroCulture &copy; </h3>
		<!--	<div class="logo">
				<a href="index.php"><img src="images/logo.png" width="200px"></a>
			</div>-->
			<br />
			<p style="font-size:20px;color:white">Your product Our market !!!</p>
			<br />
		</div>

		<div class="footer-center">
			<div>
				<i class="fa fa-map-marker"></i>
				<p style="font-size:20px">Agro Culture Fam<span>Vormir</span></p>
			</div>
			<div>
				<i class="fa fa-phone"></i>
				<p style="font-size:20px">4511148488</p>
			</div>
			<div>
				<i class="fa fa-envelope"></i>
				<p style="font-size:20px"><a href="mailto:agroculture@gmail.com" style="color:white">example@gmail.com</a></p>
			</div>
		</div>

		<div class="footer-right">
			<p class="footer-company-about" style="color:white">
				<span style="font-size:20px"><b>About AgroCulture</b></span>
				AgroCulture is e-commerce trading platform for grains & grocerries...
			</p>
			<div class="footer-icons">
				<a  href="#"><i style="margin-left: 0;margin-top:5px;"class="fa fa-facebook"></i></a>
				<a href="#"><i style="margin-left: 0;margin-top:5px" class="fa fa-instagram"></i></a>
				<a href="#"><i style="margin-left: 0;margin-top:5px" class="fa fa-youtube"></i></a>
			</div>
		</div>

	</footer>


			<div id="id01" class="modal">

  <form class="modal-content animate" action="Login/login.php" method='POST'>
    <div class="imgcontainer">
      <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
    </div>

    <div class="container">
    <h3>Login</h3>
							<form method="post" action="Login/login.php">
								<div class="row uniform 50%">
									<div class="7u$">
										<input type="text" name="uname" id="uname" value="" placeholder="UserName" style="width:80%" required/>
									</div>
									<div class="7u$">
										<input type="password" name="pass" id="pass" value="" placeholder="Password" style="width:80%" required/>
									</div>
								</div>
									<div class="row uniform">
										<p>
				                            <b>Category : </b>
				                        </p>
				                        <div class="3u 12u$(small)">
				                            <input type="radio" id="farmer" name="category" value="1" checked>
				                            <label for="farmer">Farmer</label>
				                        </div>
				                        <div class="3u 12u$(small)">
				                            <input type="radio" id="buyer" name="category" value="0">
				                            <label for="buyer">Buyer</label>
				                        </div>
									</div>
									<center>
									<div class="row uniform">
										<div class="7u 12u$(small)">
											<input type="submit" value="Login" />
										</div>
									</div>
									<div style="margin-top:12px; margin-bottom:8px;">
										<a href="Login/forgotPassword.php" style="color:#888; font-size:13px; text-decoration:none;">
											🔒 Forgot Password?
										</a>
									</div>
									</center>
								</div>
							</form>
						</section>
</div>
    </div>
    </div>
  </form>
</div>


<div id="id02" class="modal">

  <form class="modal-content animate" action="Login/signUp.php" method='POST'>
    <div class="imgcontainer">
      <span onclick="document.getElementById('id02').style.display='none'" class="close" title="Close Modal">&times;</span>
    </div>

    <div class="container">

<section>
						<h3>SignUp</h3>
						<form method="post" action="Login/signUp.php" id="regForm" onsubmit="return regValidateAll()">
							<center>
							<div class="row uniform">
								<div class="3u 12u$(xsmall)">
									<input type="text" name="name" id="reg_name" value="" placeholder="Full Name" required oninput="regVal_name(this.value)"/>
									<div id="msg_name" style="font-size:11px;margin-top:3px;height:14px;"></div>
								</div>
								<div class="3u 12u$(xsmall)">
									<input type="text" name="uname" id="reg_uname" value="" placeholder="UserName" required oninput="regVal_uname(this.value)"/>
									<div id="msg_uname" style="font-size:11px;margin-top:3px;height:14px;"></div>
								</div>
							</div>
							<div class="row uniform">
								<div class="3u 12u$(xsmall)">
									<input type="text" name="mobile" id="reg_mobile" value="" placeholder="Mobile Number (10 digits)" maxlength="10" required oninput="regVal_mobile(this.value)"/>
									<div id="msg_mobile" style="font-size:11px;margin-top:3px;height:14px;"></div>
								</div>
								<div class="3u 12u$(xsmall)">
									<input type="email" name="email" id="reg_email" value="" placeholder="Email" required oninput="regVal_email(this.value)"/>
									<div id="msg_email" style="font-size:11px;margin-top:3px;height:14px;"></div>
								</div>
							</div>
							<div class="row uniform">
								<div class="3u 12u$(xsmall)">
									<input type="password" name="password" id="reg_password" value="" placeholder="Password" required oninput="regCheckStrength(this.value)"/>
									<div id="reg_strength_bar" style="height:4px;border-radius:3px;margin-top:4px;background:#eee;transition:all 0.3s;"></div>
									<div id="reg_strength_label" style="font-size:11px;margin-top:2px;height:14px;"></div>
								</div>
								<div class="3u 12u$(xsmall)">
									<input type="password" name="pass" id="reg_pass" value="" placeholder="Retype Password" required oninput="regCheckMatch()"/>
									<div id="reg_match_msg" style="font-size:11px;margin-top:4px;height:14px;"></div>
								</div>
							</div>
							<div class="row uniform">
								<div class="6u 12u$(xsmall)">
									<input type="text" name="addr" id="reg_addr" value="" placeholder="Address" style="width:80%" required oninput="regVal_addr(this.value)"/>
									<div id="msg_addr" style="font-size:11px;margin-top:3px;height:14px;"></div>
								</div>
							</div>
							<div class="row uniform">
								<p><b>Category : </b></p>
								<div class="3u 12u$(small)">
									<input type="radio" id="reg_farmer" name="category" value="1" checked>
									<label for="reg_farmer">Farmer</label>
								</div>
								<div class="3u 12u$(small)">
									<input type="radio" id="reg_buyer" name="category" value="0">
									<label for="reg_buyer">Buyer</label>
								</div>
							</div>
							<div class="row uniform">
								<div class="3u 12u$(small)">
									<input type="submit" id="reg_submit" value="Submit" name="submit" class="special" />
								</div>
								<div class="3u 12u$(small)">
									<input type="reset" value="Reset" name="reset" onclick="regResetValidation()"/>
								</div>
							</div>
							</center>
						</form>
					</section>
    </div>
    </div>
  </form>
</div>



<script>
/* ===== Signup real-time validation ===== */
function regShow(id, msg, ok) {
    var el = document.getElementById(id);
    el.textContent = msg;
    el.style.color = ok ? '#43A047' : '#e53935';
}

function regVal_name(val) {
    if (!val || !val.trim()) { regShow('msg_name','',true); return false; }
    if (val.trim().length < 2) { regShow('msg_name','\u2717 At least 2 characters',false); return false; }
    if (!/^[a-zA-Z\s]+$/.test(val.trim())) { regShow('msg_name','\u2717 Letters only',false); return false; }
    regShow('msg_name','\u2713 Looks good',true); return true;
}

function regVal_uname(val) {
    if (!val) { regShow('msg_uname','',true); return false; }
    if (val.length < 3) { regShow('msg_uname','\u2717 At least 3 characters',false); return false; }
    if (!/^[a-zA-Z0-9_]+$/.test(val)) { regShow('msg_uname','\u2717 Letters, numbers & _ only',false); return false; }
    regShow('msg_uname','\u2713 Looks good',true); return true;
}

function regVal_mobile(val) {
    var cleaned = val.replace(/[^0-9]/g,'');
    document.getElementById('reg_mobile').value = cleaned;
    if (!cleaned) { regShow('msg_mobile','',true); return false; }
    if (cleaned.length < 10) { regShow('msg_mobile','\u2717 ' + cleaned.length + '/10 digits',false); return false; }
    regShow('msg_mobile','\u2713 Valid mobile number',true); return true;
}

function regVal_email(val) {
    if (!val) { regShow('msg_email','',true); return false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { regShow('msg_email','\u2717 Invalid email address',false); return false; }
    regShow('msg_email','\u2713 Valid email',true); return true;
}

function regVal_addr(val) {
    if (!val || val.trim().length === 0) { regShow('msg_addr','',true); return false; }
    if (val.trim().length < 5) { regShow('msg_addr','\u2717 Too short',false); return false; }
    regShow('msg_addr','\u2713 Looks good',true); return true;
}

function regCheckStrength(val) {
    var bar = document.getElementById('reg_strength_bar');
    var lbl = document.getElementById('reg_strength_label');
    if (!val) { bar.style.background='#eee'; bar.style.width='100%'; lbl.textContent=''; return; }
    var score = 0;
    if (val.length >= 6) score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    if (score <= 1) {
        bar.style.background='#e53935'; bar.style.width='33%';
        lbl.style.color='#e53935'; lbl.textContent='Weak \u2014 add numbers or symbols';
    } else if (score <= 3) {
        bar.style.background='#FB8C00'; bar.style.width='66%';
        lbl.style.color='#FB8C00'; lbl.textContent='Medium \u2014 add uppercase or symbols';
    } else {
        bar.style.background='#43A047'; bar.style.width='100%';
        lbl.style.color='#43A047'; lbl.textContent='\u2713 Strong password';
    }
    regCheckMatch();
}

function regCheckMatch() {
    var p  = document.getElementById('reg_password').value;
    var p2 = document.getElementById('reg_pass').value;
    var msg = document.getElementById('reg_match_msg');
    var btn = document.getElementById('reg_submit');
    if (!p2) { msg.textContent=''; btn.disabled=false; return; }
    if (p === p2 && p.length >= 6) {
        msg.style.color='#43A047'; msg.textContent='\u2713 Passwords match';
        btn.disabled = false;
    } else if (p !== p2) {
        msg.style.color='#e53935'; msg.textContent='\u2717 Passwords do not match';
        btn.disabled = true;
    } else {
        msg.style.color='#e53935'; msg.textContent='\u2717 Min. 6 characters required';
        btn.disabled = true;
    }
}

function regValidateAll() {
    var ok = true;
    if (!regVal_name(document.getElementById('reg_name').value))   ok = false;
    if (!regVal_uname(document.getElementById('reg_uname').value)) ok = false;
    if (!regVal_mobile(document.getElementById('reg_mobile').value)) ok = false;
    if (!regVal_email(document.getElementById('reg_email').value)) ok = false;
    if (!regVal_addr(document.getElementById('reg_addr').value))   ok = false;
    var p  = document.getElementById('reg_password').value;
    var p2 = document.getElementById('reg_pass').value;
    if (p !== p2 || p.length < 6) { regCheckMatch(); ok = false; }
    return ok;
}

function regResetValidation() {
    ['msg_name','msg_uname','msg_mobile','msg_email','msg_addr',
     'reg_strength_label','reg_match_msg'].forEach(function(id){
        var el = document.getElementById(id);
        if (el) el.textContent='';
    });
    document.getElementById('reg_strength_bar').style.background='#eee';
    document.getElementById('reg_submit').disabled=false;
}

// Get the modal
var modal = document.getElementById('id01');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

var modal1= document.getElementById('id02');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal1) {
        modal1.style.display = "none";
    }
}

</script>


	</body>
</html>
