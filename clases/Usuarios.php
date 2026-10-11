<?php

class usuarios
{

	// Registrar nuevo usuario (Hash seguro)
	public function registroUsuario($datos)
	{
		$c = new conectar();
		$conexion = $c->conexion();

		$fecha = date('Y-m-d');
		$password = password_hash($datos[3], PASSWORD_DEFAULT); // Hash seguro

		// Los nuevos registros por defecto son de nivel 'consulta' (solo lectura)
		$sql = "INSERT INTO users (username, email, password, role) 
					VALUES (:username, :email, :password, 'consulta')";

		$stmt = $conexion->prepare($sql);
		return $stmt->execute([
			':username' => $datos[2], // Usamos el campo usuario del formulario
			':email' => $datos[2],
			':password' => $password
		]);
	}

	// Login de usuario (Verificación segura)
	public function loginUser($datos)
	{
		$c = new conectar();
		$conexion = $c->conexion();

		$email = $datos[0];
		$password = $datos[1];

		// Buscar por email o por username
		$sql = "SELECT * FROM users WHERE email = :info OR username = :info";
		$stmt = $conexion->prepare($sql);
		$stmt->execute([':info' => $email]);

		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($user && password_verify($password, $user['password'])) {
			$_SESSION['usuario'] = $user['email'];
			$_SESSION['iduser'] = $user['id'];
			$_SESSION['rol'] = $user['role'];
			$_SESSION['username'] = $user['username'];
			return 1;
		}
		return 0;
	}

	// Traer ID (Helper)
	public function traeID($datos)
	{
		// Ya se maneja en loginUser al asignar sesión, pero mantenemos compatibilidad por si acaso
		$c = new conectar();
		$conexion = $c->conexion();

		$sql = "SELECT id FROM users WHERE email = :email";
		$stmt = $conexion->prepare($sql);
		$stmt->execute([':email' => $datos[0]]);

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['id'] ?? null;
	}

	// Obtener datos de usuario
	public function obtenDatosUsuario($idusuario)
	{
		$c = new conectar();
		$conexion = $c->conexion();

		$sql = "SELECT id, username, email, role FROM users WHERE id = :id";
		$stmt = $conexion->prepare($sql);
		$stmt->execute([':id' => $idusuario]);

		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	// Actualizar usuario
	public function actualizaUsuario($datos)
	{
		$c = new conectar();
		$conexion = $c->conexion();

		$sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
		$stmt = $conexion->prepare($sql);
		return $stmt->execute([
			':id' => $datos[0],
			':username' => $datos[1] . " " . $datos[2],
			':email' => $datos[3]
		]);
	}

	// Eliminar usuario
	public function eliminaUsuario($idusuario)
	{
		$c = new conectar();
		$conexion = $c->conexion();

		$sql = "DELETE FROM users WHERE id = :id";
		$stmt = $conexion->prepare($sql);
		return $stmt->execute([':id' => $idusuario]);
	}
}

?>