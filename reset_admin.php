<?php
require_once "clases/Conexion.php";
$c = new conectar();
$conexion = $c->conexion();

$password = password_hash("admin123", PASSWORD_DEFAULT);
$sql = "UPDATE users SET role = 'admin', password = :password WHERE email = 'admin' OR username = 'admin'";
$stmt = $conexion->prepare($sql);
$success = $stmt->execute([':password' => $password]);

if ($success) {
    if ($stmt->rowCount() > 0) {
        echo "Admin actualizado con éxito. Password: admin123";
    } else {
        // Si no existe, lo creamos
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@marina.mil', :password, 'admin')";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':password' => $password]);
        echo "Admin creado con éxito. Password: admin123";
    }
} else {
    echo "Error al actualizar admin";
}
?>