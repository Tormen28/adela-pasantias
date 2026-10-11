<?php

require_once "clases/Conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$sql = "SELECT * from users where role='admin'";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$validar = $stmt->rowCount() > 0 ? 1 : 0;
?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login - Brigada de Marina</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		:root {
			--primary: #0f172a;
			--secondary: #1e293b;
			--accent: #3b82f6;
			--text: #f8fafc;
		}

		body {
			margin: 0;
			padding: 0;
			font-family: 'Segoe UI', system-ui, sans-serif;
			background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('12.jpg');
			background-size: cover;
			background-position: center;
			height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.login-card {
			background: rgba(30, 41, 59, 0.7);
			backdrop-filter: blur(10px);
			padding: 2.5rem;
			border-radius: 1rem;
			width: 100%;
			max-width: 400px;
			box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
			border: 1px solid rgba(255, 255, 255, 0.1);
			color: white;
			text-align: center;
		}

		.login-card i.anchor {
			font-size: 3rem;
			color: var(--accent);
			margin-bottom: 1rem;
		}

		.login-card h2 {
			font-size: 1.5rem;
			margin-bottom: 0.5rem;
			font-weight: 600;
		}

		.login-card p {
			color: #94a3b8;
			margin-bottom: 2rem;
			font-size: 0.9rem;
		}

		.form-group {
			text-align: left;
			margin-bottom: 1.25rem;
		}

		.form-group label {
			display: block;
			margin-bottom: 0.5rem;
			font-size: 0.875rem;
			color: #cbd5e1;
		}

		.form-control {
			width: 100%;
			padding: 0.75rem 1rem;
			background: rgba(15, 23, 42, 0.5);
			border: 1px solid rgba(255, 255, 255, 0.1);
			border-radius: 0.5rem;
			color: white;
			font-size: 1rem;
			box-sizing: border-box;
			transition: all 0.2s;
		}

		.form-control:focus {
			outline: none;
			border-color: var(--accent);
			box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
		}

		.btn-login {
			width: 100%;
			padding: 0.75rem;
			background: var(--accent);
			color: white;
			border: none;
			border-radius: 0.5rem;
			font-size: 1rem;
			font-weight: 600;
			cursor: pointer;
			transition: background 0.2s;
			margin-top: 1rem;
		}

		.btn-login:hover {
			background: #2563eb;
		}

		.btn-register {
			display: block;
			margin-top: 1.5rem;
			color: #94a3b8;
			text-decoration: none;
			font-size: 0.875rem;
			transition: color 0.2s;
		}

		.btn-register:hover {
			color: var(--accent);
		}

		.logo-img {
			height: 80px;
			margin-bottom: 1rem;
			filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.5));
		}
	</style>
	<script src="librerias/jquery-3.2.1.min.js"></script>
	<script src="js/funciones.js"></script>
</head>

<body>
	<div class="login-card">
		<img src="2.jfif" class="logo-img" alt="Logo">
		<i class="fas fa-anchor anchor" style="display:none;"></i> <!-- Backup icon -->
		<h2>Brigada de Marina</h2>
		<p>Sistema Integral de Gestión</p>

		<form id="frmLogin">
			<div class="form-group">
				<label><i class="fas fa-user"></i> Usuario / Email</label>
				<input type="text" class="form-control" name="usuario" id="usuario" placeholder="Ingresa tu usuario">
			</div>
			<div class="form-group">
				<label><i class="fas fa-lock"></i> Contraseña</label>
				<input type="password" class="form-control" name="password" id="password" placeholder="••••••••">
			</div>
			<button type="button" class="btn-login" id="entrarSistema">
				Entrar al Sistema
			</button>
			<a href="registro.php" class="btn-register">¿No tienes cuenta? Regístrate aquí</a>
		</form>
	</div>
</body>

</html>
<script type="text/javascript">
	$(document).ready(function () {
		$('#entrarSistema').click(function () {
			vacios = validarFormVacio('frmLogin');
			if (vacios > 0) {
				alert("Debes llenar todos los campos!!");
				return false;
			}
			datos = $('#frmLogin').serialize();
			$.ajax({
				type: "POST",
				data: datos,
				url: "procesos/regLogin/login.php",
				success: function (r) {
					if (r == 1) {
						window.location = "src/index.php";
					} else {
						alert("No se pudo acceder :(");
					}
				}
			});
		});
	});
</script>