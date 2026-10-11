<?php

require_once "clases/Conexion.php";
$obj = new conectar();
$conexion = $obj->conexion();

$sql = "SELECT * from users where role='admin'";
$stmt = $conexion->prepare($sql);
$stmt->execute();

/* 
if ($stmt->rowCount() > 0) {
	header("location:index.php");
}
*/
?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Registro Admin - Brigada de Marina</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		:root {
			--primary: #0f172a;
			--accent: #3b82f6;
		}

		body {
			margin: 0;
			padding: 0;
			font-family: 'Segoe UI', system-ui, sans-serif;
			background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url('12.jpg');
			background-size: cover;
			background-position: center;
			height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.register-card {
			background: rgba(30, 41, 59, 0.7);
			backdrop-filter: blur(12px);
			padding: 2.5rem;
			border-radius: 1rem;
			width: 100%;
			max-width: 450px;
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
			border: 1px solid rgba(255, 255, 255, 0.1);
			color: white;
			text-align: center;
		}

		.header-icon {
			font-size: 2.5rem;
			color: #ef4444;
			/* Color rojo para admin */
			margin-bottom: 1rem;
		}

		.register-card h2 {
			font-size: 1.5rem;
			margin-bottom: 0.5rem;
			color: white;
		}

		.register-card p {
			color: #94a3b8;
			margin-bottom: 2rem;
			font-size: 0.9rem;
		}

		.form-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 1rem;
			text-align: left;
		}

		.form-group {
			margin-bottom: 1.25rem;
			text-align: left;
		}

		.full-width {
			grid-column: 1 / -1;
		}

		.form-group label {
			display: block;
			margin-bottom: 0.5rem;
			font-size: 0.813rem;
			color: #cbd5e1;
			font-weight: 500;
		}

		.form-control {
			width: 100%;
			padding: 0.625rem 0.875rem;
			background: rgba(15, 23, 42, 0.5);
			border: 1px solid rgba(255, 255, 255, 0.1);
			border-radius: 0.5rem;
			color: white;
			font-size: 0.938rem;
			box-sizing: border-box;
			transition: all 0.2s;
		}

		.form-control:focus {
			outline: none;
			border-color: var(--accent);
			box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
		}

		.actions {
			margin-top: 1rem;
			display: flex;
			flex-direction: column;
			gap: 0.75rem;
		}

		.btn-register {
			width: 100%;
			padding: 0.75rem;
			background: #ef4444;
			color: white;
			border: none;
			border-radius: 0.5rem;
			font-size: 1rem;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.2s;
		}

		.btn-register:hover {
			background: #dc2626;
			transform: translateY(-1px);
		}

		.btn-back {
			color: #94a3b8;
			text-decoration: none;
			font-size: 0.875rem;
			transition: color 0.2s;
		}

		.btn-back:hover {
			color: white;
		}

		.logo-img {
			height: 60px;
			margin-bottom: 1rem;
		}
	</style>
	<script src="librerias/jquery-3.2.1.min.js"></script>
	<script src="js/funciones.js"></script>
</head>

<body>
	<div class="register-card">
		<img src="2.jfif" class="logo-img" alt="Logo">
		<h2>Registrar Administrador</h2>
		<p>Configuración inicial del sistema de Marina</p>

		<form id="frmRegistro">
			<div class="form-grid">
				<div class="form-group">
					<label>Nombre</label>
					<input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ej: Juan">
				</div>
				<div class="form-group">
					<label>Apellido</label>
					<input type="text" class="form-control" name="apellido" id="apellido" placeholder="Ej: Pérez">
				</div>
				<div class="form-group full-width">
					<label>Email / Usuario</label>
					<input type="text" class="form-control" name="usuario" id="usuario" placeholder="admin@marina.mil">
				</div>
				<div class="form-group full-width">
					<label>Contraseña</label>
					<input type="password" class="form-control" name="password" id="password" placeholder="••••••••">
				</div>
			</div>

			<div class="actions">
				<button type="button" class="btn-register" id="registro">
					Completar Registro
				</button>
				<a href="index.php" class="btn-back">
					<i class="fas fa-arrow-left"></i> Regresar al login
				</a>
			</div>
		</form>
	</div>
</body>

</html>

<script type="text/javascript">
	$(document).ready(function () {
		$('#registro').click(function () {

			vacios = validarFormVacio('frmRegistro');

			if (vacios > 0) {
				alert("Debes llenar todos los campos!!");
				return false;
			}

			datos = $('#frmRegistro').serialize();
			$.ajax({
				type: "POST",
				data: datos,
				url: "procesos/regLogin/registrarUsuario.php",
				success: function (r) {
					alert(r);

					if (r == 1) {
						alert("Agregado con exito");
					} else {
						alert("Fallo al agregar :(");
					}
				}
			});
		});
	});
</script>