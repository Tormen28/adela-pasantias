<?php
/**
 * Script de migración para agregar columna role a la tabla users
 * Ejecutar una sola vez y luego eliminar este archivo
 */

require_once 'clases/Conexion.php';

try {
    $c = new conectar();
    $conn = $c->conexion();

    if (!$conn) {
        die("Error: No se pudo conectar a la base de datos");
    }

    echo "<h2>Migración de Base de Datos</h2>";

    // Verificar si la columna role ya existe
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
    $columnExists = $stmt->rowCount() > 0;

    if ($columnExists) {
        echo "<p style='color: orange;'>⚠️ La columna 'role' ya existe en la tabla users.</p>";
    } else {
        // Agregar columna role
        $conn->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'operador'");
        echo "<p style='color: green;'>✅ Columna 'role' agregada exitosamente.</p>";
    }

    // Actualizar el primer usuario como admin
    $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = 1");
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Usuario ID 1 actualizado a rol 'admin'.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No se encontró usuario con ID 1 o ya tiene rol admin.</p>";
    }

    // Mostrar usuarios actuales
    echo "<h3>Usuarios en el sistema:</h3>";
    $stmt = $conn->query("SELECT id, username, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td><strong>{$user['role']}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay usuarios en la tabla.</p>";
    }

    echo "<br><p style='color: blue;'><strong>Migración completada. Puedes eliminar este archivo (run_migration.php).</strong></p>";
    echo "<p><a href='src/index.php'>Ir al sistema</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>