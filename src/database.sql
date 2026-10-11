-- Base de datos para el Sistema de Gestión - Brigada de Marina
-- Ejecutar este script en MySQL para crear la estructura de datos

CREATE DATABASE IF NOT EXISTS brigada_marina CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE brigada_marina;

-- Tabla de Usuarios (Auth)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Hash argon2/bcrypt
    role ENUM('admin', 'operador', 'consulta') DEFAULT 'operador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Personal
CREATE TABLE IF NOT EXISTS personnel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    rank VARCHAR(100) NOT NULL,
    position VARCHAR(255) NOT NULL,
    specialty VARCHAR(255) NOT NULL,
    status ENUM('Activo', 'Licencia', 'Baja') DEFAULT 'Activo',
    entry_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Equipamiento
CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    status ENUM('Operativo', 'Mantenimiento', 'Fuera de Servicio') DEFAULT 'Operativo',
    location VARCHAR(255) NOT NULL,
    last_maintenance DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Vehículos
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    model VARCHAR(255) NOT NULL,
    plate VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('Operativo', 'Mantenimiento', 'Fuera de Servicio') DEFAULT 'Operativo',
    assigned_to VARCHAR(255),
    last_maintenance DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Suministros
CREATE TABLE IF NOT EXISTS supplies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit VARCHAR(50) NOT NULL,
    min_stock INT NOT NULL,
    location VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo - Usuarios (password: admin123)
-- Nota: La contraseña debe ser hasheada en producción. Aquí se inserta un hash de ejemplo para 'admin123'

-- Datos de ejemplo para Personal

-- Datos de ejemplo para Equipamiento

-- Datos de ejemplo para Vehículos

-- Datos de ejemplo para Suministros

-- Índices para mejorar rendimiento
CREATE INDEX idx_personnel_status ON personnel(status);
CREATE INDEX idx_equipment_status ON equipment(status);
CREATE INDEX idx_vehicles_status ON vehicles(status);
CREATE INDEX idx_supplies_category ON supplies(category);
