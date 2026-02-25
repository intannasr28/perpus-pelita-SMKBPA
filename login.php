<?php
include "inc/koneksi.php";

if (isset($_POST['btnLogin'])) {  
	$username = mysqli_real_escape_string($koneksi, $_POST['username']);
	$password = mysqli_real_escape_string($koneksi, md5($_POST['password']));

	$sql_login = "SELECT * FROM tb_pengguna WHERE BINARY username='$username' AND password='$password'";
	$query_login = mysqli_query($koneksi, $sql_login);
	$data_login = mysqli_fetch_array($query_login, MYSQLI_BOTH);
	$jumlah_login = mysqli_num_rows($query_login);

	if ($jumlah_login == 1) {
		session_start();
		$_SESSION["ses_id"] = $data_login["id_pengguna"];
		$_SESSION["ses_nama"] = $data_login["nama_pengguna"];
		$_SESSION["ses_username"] = $data_login["username"];
		$_SESSION["ses_password"] = $data_login["password"];
		$_SESSION["ses_level"] = $data_login["level"];
		
		header("location: index.php");
		exit;
	} else {
		$login_error = true;
	}
}
?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Login | Sistem Informasi Perpustakaan</title>
	<link rel="icon" href="dist/img/logo.png">
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Google Fonts - Poppins -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="dist/css/AdminLTE.min.css">

	<style>
		body.login-page {
			background: url('dist/img/background.jpeg') no-repeat center center fixed;
			background-size: cover;
			background-attachment: fixed;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			font-family: 'Poppins', sans-serif;
			position: relative;
		}

		body.login-page::before {
			content: '';
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 31, 63, 0.5);
			z-index: -1;
		}

		.login-box {
			width: 360px;
			margin: auto;
			position: relative;
			z-index: 1;
		}

		.login-logo {
			text-align: center;
			margin-bottom: 30px;
		}

		.login-logo h3 {
			font-size: 28px;
			font-weight: 700;
			text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
		}

		   .login-box-body {
			   background: #fff;
			   border-radius: 16px;
			   box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
			   padding: 32px 30px 30px 30px;
			   border: 1.5px solid #f0f0f0;
			   backdrop-filter: blur(10px);
			   position: relative;
			   overflow: hidden;
		   }
		   .login-box-body::before {
			   display: none;
		   }

		.login-box-body .login-box-msg {
			font-size: 18px;
			font-weight: 600;
			color: #001f3f;
			margin-bottom: 25px;
		}

		.form-control {
			border: 2px solid #e0e0e0;
			border-radius: 6px;
			height: 42px;
			font-size: 14px;
			transition: all 0.3s ease;
		}

		.form-control:focus {
			border-color: #0051b3;
			box-shadow: 0 0 10px rgba(0, 81, 179, 0.2);
			background-color: #f8f9ff;
		}

		   .form-group.has-feedback .form-control-feedback {
			   color: #0051b3;
			   font-size: 16px;
			   right: 12px;
			   cursor: pointer;
			   z-index: 2;
		   }

		   .btn-primary {
			   background: linear-gradient(90deg, #0051b3 0%, #003d82 100%);
			   border: none;
			   color: #fff;
			   font-weight: 700;
			   height: 44px;
			   font-size: 16px;
			   border-radius: 8px;
			   box-shadow: 0 2px 12px rgba(0, 81, 179, 0.18), 0 1.5px 0 #fff inset;
			   transition: all 0.3s cubic-bezier(.4,2,.3,1);
			   position: relative;
			   overflow: hidden;
		   }
		   .btn-primary::after {
			   content: '';
			   position: absolute;
			   left: 0; top: 0; width: 100%; height: 100%;
			   background: linear-gradient(120deg, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0.08) 100%);
			   opacity: 0.7;
			   pointer-events: none;
			   border-radius: 8px;
			   transition: opacity 0.3s;
		   }
		   .btn-primary:hover,
		   .btn-primary:focus {
			   background: linear-gradient(90deg, #003d82 0%, #0051b3 100%);
			   box-shadow: 0 6px 20px rgba(0, 81, 179, 0.22), 0 2px 0 #fff inset;
			   transform: translateY(-2px) scale(1.03);
		   }
		   .btn-primary:active {
			   transform: translateY(0) scale(0.98);
		   }
		   .btn-primary b {
			   letter-spacing: 0.5px;
			   text-shadow: 0 1px 4px rgba(0,0,0,0.08);
		   }
	</style>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body class="hold-transition login-page">
	<div class="login-box">
		<div class="login-logo">
			<h3 style="color: #ffffff;">
				<b>Sistem Informasi Perpustakaan Pelita</b>
			</h3>
		</div>
		<!-- /.login-logo -->
		<div class="login-box-body">
			<center>
				<img src="dist/img/logo.png" width=160px />
			</center>
			<br>
			<p class="login-box-msg">Login System</p>			<?php if (isset($login_error)) { ?>
				<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<h4><i class="icon fa fa-ban"></i> Login Gagal!</h4>
					Username atau Password salah!
				</div>
			<?php } ?>			<form action="#" method="post">
				   <div class="form-group has-feedback">
					   <input type="text" class="form-control" name="username" placeholder="Username" required>
				   </div>
				   <div class="form-group has-feedback" style="position:relative;">
					   <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
					   <span class="fa fa-eye form-control-feedback" id="showHidePassword" style="pointer-events:auto; right:12px;"></span>
				   </div>
				<div class="row">
					<div class="col-xs-8">

					</div>
					<!-- /.col -->
					<div class="col-xs-4">
						<button type="submit" class="btn btn-primary btn-block btn-flat" name="btnLogin" title="Masuk Sistem">
							<b>Masuk</b>
						</button>
						<div class="text-center" style="margin-top:10px;">
    <p>Belum punya akun siswa? <a href="register.php"><b>Daftar di sini</b></a></p>
</div>
					</div>
					<!-- /.col -->
				</div>
			</form>
			<!-- /.social-auth-links -->

		</div>
		<!-- /.login-box-body -->
	</div>
	<!-- /.login-box -->

	   <!-- jQuery 2.2.3 -->
	   <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
	   <!-- Bootstrap 3.3.6 -->
	   <script src="bootstrap/js/bootstrap.min.js"></script>
	   <!-- iCheck -->
	   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
	   <!-- sweet alert -->
	   <script>
	   // Show/hide password toggle
	   $(function() {
		   $('#showHidePassword').addClass('fa-eye');
		   $('#showHidePassword').on('click', function() {
			   var input = $('#password');
			   var type = input.attr('type') === 'password' ? 'text' : 'password';
			   input.attr('type', type);
			   $(this).toggleClass('fa-eye fa-eye-slash');
		   });
	   });
	   </script>
</body>

</html>