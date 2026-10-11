# Sistema de Gestión - Brigada de Marina

Sistema integral para el control de Recursos Humanos, Logística y Operaciones de la Brigada de Marina. 
Desarrollado para ofrecer una interfaz moderna, rápida y con control granular de accesos.

## 📋 Características Principales

*   **Dashboard Interactivo**: Estadísticas en tiempo real de personal activo, equipamiento, vehículos y alertas críticas (bajo stock, mantenimiento).
*   **Gestión de Personal**: Administración completa de recursos humanos con seguimiento de rangos, especialidades y estados de servicio.
*   **Logística Multimódulo**:
    *   **Equipamiento**: Control detallado de armamento y equipo táctico.
    *   **Vehículos**: Gestión de flota vehicular y embarcaciones con asignación de personal.
    *   **Suministros**: Inventario automatizado con alertas de stock mínimo.
*   **Control de Acceso (RBAC)**: Sistema de roles diferenciado:
    *   **Administrador**: Control total, gestión de usuarios y configuraciones.
    *   **Operador**: Capacidad de edición y registro en módulos operativos.
    *   **Consulta**: Acceso de solo lectura para supervisión.
*   **Reportes Profesionales**: Generación de reportes en **PDF** con formato institucional y exportación a **Excel** (CSV optimizado para celdas) en todos los módulos.

## 🛠 Tecnologías Utilizadas

*   **Frontend**: HTML5, CSS3 Moderno, JavaScript (ES6+ Vanilla).
*   **Backend**: PHP 8.x (Arquitectura limpia, PDO para seguridad).
*   **Base de Datos**: MySQL / MariaDB.
*   **Seguridad**: Cifrado de contraseñas con `password_hash()` y validación de sesiones por nivel de rol.

## 🚀 Instalación y Configuración

### 1. Requisitos Previos
*   Servidor local (**XAMPP**, WAMP o Laragon).
*   PHP 7.4 o superior (Recomendado 8.x).
*   MySQL/MariaDB.

### 2. Base de Datos
1.  Importe el archivo `src/migration_ventas.sql` en su gestor de base de datos (phpMyAdmin).
2.  Asegúrese de que el nombre de la base de datos coincida con la configuración en `clases/Conexion.php`.

### 3. Configuración del Proyecto
1.  Verifique los parámetros de conexión en `clases/Conexion.php`:
    ```php
    private $servidor="localhost";
    private $usuario="root"; 
    private $password="";    
    private $bd="ventas"; 
    ```

## 🔑 Credenciales de Acceso

El sistema soporta múltiples usuarios. Los registros iniciales tienen el rol de **Consulta** por defecto. El administrador debe asignar roles superiores desde el panel de gestión.

*   **Usuario por Defecto**: `admin`
*   **Contraseña**: `admin123`

## 📂 Estructura del Proyecto

*   `src/`: Núcleo del sistema (Dashboard, API PHP, Scripts JS con carga optimizada, Estilos CSS).
*   `clases/`: Clases PHP para la lógica de conexión y usuarios.
*   `procesos/`: Controladores para Login y Registro.
*   `librerias/`: Dependencias gráficas (Chart.js, FontAwesome) y selectores.

---
© 2026 Brigada de Marina - Sistema Integral de Gestión
## Contexto

Sistema de gestión (personal, equipos y vehículos) hecho durante la práctica profesional en la Brigada de Marina, en **PHP + MySQL**. El repositorio no incluye datos reales de personas.
