<?php

class conectar
{
	private $servidor = "localhost";
	private $usuario = "root";
	private $password = "";
	private $bd = "ventas";

	public function conexion()
	{
		try {
			$conexion = new PDO("mysql:host={$this->servidor};dbname={$this->bd}", $this->usuario, $this->password);
			$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conexion;
		} catch (PDOException $e) {
			die("Error de conexión: " . $e->getMessage());
		}
	}
}


?>