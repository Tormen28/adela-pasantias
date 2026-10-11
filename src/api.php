<?php
/**
 * API REST para el Sistema de Gestión - Brigada de Marina
 * 
 * Este archivo maneja todas las peticiones AJAX para CRUD de datos
 * Incluye control de acceso basado en roles
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*')); // Limitar en producción
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Usar conexión centralizada
require_once '../clases/Conexion.php';

// Obtener rol del usuario actual
function getCurrentRole()
{
    return $_SESSION['rol'] ?? 'operador';
}

// Verificar si el usuario tiene permiso para la acción
function checkPermission($action, $resource = '')
{
    // Permitir a todos los usuarios actualizar su propio perfil y contraseña
    if ($resource === 'user' || $resource === 'password') {
        return;
    }

    $role = getCurrentRole();

    // Definir permisos por rol
    $permissions = [
        'admin' => ['GET', 'POST', 'PUT', 'DELETE'],
        'operador' => ['GET', 'POST', 'PUT'], // No puede eliminar
        'consulta' => ['GET'] // Solo lectura
    ];

    $allowed = $permissions[$role] ?? ['GET'];

    if (!in_array($action, $allowed)) {
        http_response_code(403);
        echo json_encode([
            'error' => 'No tiene permisos para esta acción',
            'rol' => $role,
            'accion' => $action
        ]);
        exit();
    }
}

// Conexión a la base de datos
function getDBConnection()
{
    try {
        $c = new conectar();
        return $c->conexion();
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage()); // Log interno
        http_response_code(500);
        echo json_encode(['error' => 'Error de conexión a la base de datos']);
        exit();
    }
}

// Obtener método y recurso
$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_GET['resource']) ? $_GET['resource'] : '';

// Verificar permisos antes de procesar
checkPermission($method, $request);

// Router principal
switch ($request) {
    case 'personnel':
        handlePersonnel($method);
        break;
    case 'equipment':
        handleEquipment($method);
        break;
    case 'vehicles':
        handleVehicles($method);
        break;
    case 'supplies':
        handleSupplies($method);
        break;
    case 'users':
        if (getCurrentRole() !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado. Solo administradores pueden gestionar usuarios.']);
            exit();
        }
        handleUsersManagement($method);
        break;
    case 'stats':
        handleStats();
        break;
    case 'user':
        handleCurrentUser($method);
        break;
    case 'password':
        handlePasswordChange($method);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Recurso no encontrado']);
}

// Endpoint para obtener estadísticas del dashboard
function handleStats()
{
    $conn = getDBConnection();

    $stats = [];

    // Personal activo
    $stmt = $conn->query("SELECT COUNT(*) as total FROM personnel WHERE status = 'Activo'");
    $stats['activePersonnel'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total equipamiento
    $stmt = $conn->query("SELECT SUM(quantity) as total FROM equipment");
    $stats['totalEquipment'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total vehículos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM vehicles");
    $stats['totalVehicles'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Alertas (bajo stock + mantenimiento)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM supplies WHERE quantity <= min_stock");
    $lowStock = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->query("SELECT COUNT(*) as total FROM vehicles WHERE status = 'Mantenimiento'");
    $maintV = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->query("SELECT COUNT(*) as total FROM equipment WHERE status = 'Mantenimiento'");
    $maintE = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stats['alerts'] = $lowStock + $maintV + $maintE;

    // Datos para gráficos
    // Por estado de personal
    $stmt = $conn->query("SELECT status, COUNT(*) as count FROM personnel GROUP BY status");
    $stats['personnelByStatus'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Por categoría de equipamiento
    $stmt = $conn->query("SELECT category, SUM(quantity) as count FROM equipment GROUP BY category");
    $stats['equipmentByCategory'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Por estado de vehículos
    $stmt = $conn->query("SELECT status, COUNT(*) as count FROM vehicles GROUP BY status");
    $stats['vehiclesByStatus'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Suministros por categoría
    $stmt = $conn->query("SELECT category, SUM(quantity) as count FROM supplies GROUP BY category");
    $stats['suppliesByCategory'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($stats);
}

// Endpoint para obtener o actualizar datos del usuario actual
function handleCurrentUser($method)
{
    $conn = getDBConnection();

    if ($method === 'GET') {
        echo json_encode([
            'email' => $_SESSION['usuario'] ?? '',
            'username' => $_SESSION['username'] ?? '',
            'role' => $_SESSION['rol'] ?? 'operador',
            'id' => $_SESSION['iduser'] ?? 0
        ]);
    } elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        validateRequired($data, ['firstName', 'lastName', 'email']);

        $id = $_SESSION['iduser'];
        $username = $data['firstName'] . ' ' . $data['lastName'];
        $email = htmlspecialchars($data['email']);

        $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        $success = $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':id' => $id
        ]);

        if ($success) {
            $_SESSION['username'] = $username;
            $_SESSION['usuario'] = $email;
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar el perfil']);
        }
    }
}

// Endpoint para cambiar la contraseña
function handlePasswordChange($method)
{
    if ($method !== 'PUT') {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        exit();
    }

    $conn = getDBConnection();
    $data = json_decode(file_get_contents('php://input'), true);
    validateRequired($data, ['currentPassword', 'newPassword']);

    $id = $_SESSION['iduser'];

    // Verificar contraseña actual
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data['currentPassword'], $user['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'La contraseña actual es incorrecta']);
        exit();
    }

    // Actualizar con nueva contraseña
    $newPasswordHash = password_hash($data['newPassword'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
    $success = $stmt->execute([
        ':password' => $newPasswordHash,
        ':id' => $id
    ]);

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al cambiar la contraseña']);
    }
}

// Función auxiliar de validación
function validateRequired($data, $fields)
{
    $missing = [];
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            $missing[] = $field;
        }
    }
    if (!empty($missing)) {
        http_response_code(400);
        echo json_encode(['error' => 'Campos requeridos faltantes: ' . implode(', ', $missing)]);
        exit();
    }
}

// Manejo de Personal
function handlePersonnel($method)
{
    $conn = getDBConnection();

    switch ($method) {
        case 'GET':
            $stmt = $conn->query("SELECT * FROM personnel ORDER BY id DESC");
            $personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($personnel);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            validateRequired($data, ['name', 'rank', 'position', 'specialty', 'status']);

            $stmt = $conn->prepare("INSERT INTO personnel (name, rank, position, specialty, status, entry_date) 
                                   VALUES (:name, :rank, :position, :specialty, :status, :entry_date)");
            $stmt->execute([
                ':name' => htmlspecialchars($data['name']),
                ':rank' => htmlspecialchars($data['rank']),
                ':position' => htmlspecialchars($data['position']),
                ':specialty' => htmlspecialchars($data['specialty']),
                ':status' => $data['status'],
                ':entry_date' => date('Y-m-d')
            ]);
            echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("UPDATE personnel SET name=:name, rank=:rank, position=:position, 
                                   specialty=:specialty, status=:status WHERE id=:id");
            $stmt->execute([
                ':id' => $data['id'],
                ':name' => $data['name'],
                ':rank' => $data['rank'],
                ':position' => $data['position'],
                ':specialty' => $data['specialty'],
                ':status' => $data['status']
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            $id = $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM personnel WHERE id=:id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true]);
            break;
    }
}

// Manejo de Equipamiento
function handleEquipment($method)
{
    $conn = getDBConnection();

    switch ($method) {
        case 'GET':
            $stmt = $conn->query("SELECT * FROM equipment ORDER BY id DESC");
            $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($equipment);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("INSERT INTO equipment (name, category, quantity, status, location, last_maintenance) 
                                   VALUES (:name, :category, :quantity, :status, :location, :last_maintenance)");
            $stmt->execute([
                ':name' => $data['name'],
                ':category' => $data['category'],
                ':quantity' => $data['quantity'],
                ':status' => $data['status'] ?? 'Operativo',
                ':location' => $data['location'],
                ':last_maintenance' => date('Y-m-d')
            ]);
            echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("UPDATE equipment SET name=:name, category=:category, quantity=:quantity, 
                                   status=:status, location=:location WHERE id=:id");
            $stmt->execute([
                ':id' => $data['id'],
                ':name' => $data['name'],
                ':category' => $data['category'],
                ':quantity' => $data['quantity'],
                ':status' => $data['status'] ?? 'Operativo',
                ':location' => $data['location']
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            $id = $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM equipment WHERE id=:id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true]);
            break;
    }
}

// Manejo de Vehículos
function handleVehicles($method)
{
    $conn = getDBConnection();

    switch ($method) {
        case 'GET':
            $stmt = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");
            $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($vehicles);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("INSERT INTO vehicles (type, model, plate, status, assigned_to, last_maintenance) 
                                   VALUES (:type, :model, :plate, :status, :assigned_to, :last_maintenance)");
            $stmt->execute([
                ':type' => $data['type'],
                ':model' => $data['model'],
                ':plate' => $data['plate'],
                ':status' => $data['status'] ?? 'Operativo',
                ':assigned_to' => $data['assignedTo'],
                ':last_maintenance' => date('Y-m-d')
            ]);
            echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("UPDATE vehicles SET type=:type, model=:model, plate=:plate, 
                                   status=:status, assigned_to=:assigned_to WHERE id=:id");
            $stmt->execute([
                ':id' => $data['id'],
                ':type' => $data['type'],
                ':model' => $data['model'],
                ':plate' => $data['plate'],
                ':status' => $data['status'] ?? 'Operativo',
                ':assigned_to' => $data['assignedTo'] ?? ''
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            $id = $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM vehicles WHERE id=:id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true]);
            break;
    }
}

// Manejo de Suministros
function handleSupplies($method)
{
    $conn = getDBConnection();

    switch ($method) {
        case 'GET':
            $stmt = $conn->query("SELECT * FROM supplies ORDER BY id DESC");
            $supplies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($supplies);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("INSERT INTO supplies (name, category, quantity, unit, min_stock, location) 
                                   VALUES (:name, :category, :quantity, :unit, :min_stock, :location)");
            $stmt->execute([
                ':name' => $data['name'],
                ':category' => $data['category'],
                ':quantity' => $data['quantity'],
                ':unit' => $data['unit'],
                ':min_stock' => $data['minStock'],
                ':location' => $data['location']
            ]);
            echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("UPDATE supplies SET name=:name, category=:category, quantity=:quantity, 
                                   unit=:unit, min_stock=:min_stock, location=:location WHERE id=:id");
            $stmt->execute([
                ':id' => $data['id'],
                ':name' => $data['name'],
                ':category' => $data['category'],
                ':quantity' => $data['quantity'],
                ':unit' => $data['unit'],
                ':min_stock' => $data['minStock'],
                ':location' => $data['location']
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            $id = $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM supplies WHERE id=:id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true]);
            break;
    }
}
// Manejo de Gestión de Usuarios (CRUD para Admin)
function handleUsersManagement($method)
{
    $conn = getDBConnection();

    switch ($method) {
        case 'GET':
            $stmt = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            validateRequired($data, ['username', 'email', 'password', 'role']);

            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
            $success = $stmt->execute([
                ':username' => htmlspecialchars($data['username']),
                ':email' => htmlspecialchars($data['email']),
                ':password' => $password,
                ':role' => $data['role']
            ]);
            echo json_encode(['success' => $success, 'id' => $conn->lastInsertId()]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            validateRequired($data, ['id', 'username', 'email', 'role']);

            $sql = "UPDATE users SET username = :username, email = :email, role = :role";
            $params = [
                ':id' => $data['id'],
                ':username' => htmlspecialchars($data['username']),
                ':email' => htmlspecialchars($data['email']),
                ':role' => $data['role']
            ];

            if (!empty($data['password'])) {
                $sql .= ", password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute($params);
            echo json_encode(['success' => $success]);
            break;

        case 'DELETE':
            $id = $_GET['id'];
            if ($id == $_SESSION['iduser']) {
                http_response_code(400);
                echo json_encode(['error' => 'No puedes eliminar tu propia cuenta']);
                exit();
            }
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $success = $stmt->execute([':id' => $id]);
            echo json_encode(['success' => $success]);
            break;
    }
}
?>