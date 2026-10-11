<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("location:../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión - Brigada de Marina</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-left">
                    <i class="fas fa-anchor header-icon"></i>
                    <div class="header-text">
                        <h1>Sistema de Gestión - Brigada de Marina</h1>
                        <p>Control de Recursos Humanos y Logística</p>
                        <div class="header-search">
                            <i class="fas fa-search"></i>
                            <input type="text" id="globalSearch" placeholder="Búsqueda global (Personal, Equipos...)"
                                oninput="handleGlobalSearch(this.value)">
                            <div id="globalSearchResults" class="search-results-dropdown"></div>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="user-menu">
                        <button class="user-menu-btn" id="userMenuBtn">
                            <i class="fas fa-user-circle"></i>
                            <span id="headerUsername">Usuario</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="dropdown-header">
                                <i class="fas fa-user"></i>
                                <div>
                                    <span id="dropdownUsername">Usuario</span>
                                    <small id="dropdownRole">Rol</small>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item" onclick="openModal('settingsModal'); closeUserMenu();">
                                <i class="fas fa-cog"></i> Ajustes
                            </a>
                            <a href="#" class="dropdown-item"
                                onclick="openModal('changePasswordModal'); closeUserMenu();">
                                <i class="fas fa-key"></i> Cambiar Contraseña
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item dropdown-logout"
                                onclick="confirmLogout()">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Navigation Tabs -->
            <div class="tabs">
                <button class="tab-btn active" data-tab="dashboard">
                    <i class="fas fa-chart-line"></i> Dashboard
                </button>
                <button class="tab-btn" data-tab="personal">
                    <i class="fas fa-users"></i> Recursos Humanos
                </button>
                <button class="tab-btn" data-tab="logistics">
                    <i class="fas fa-boxes"></i> Logística
                </button>
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <button class="tab-btn" data-tab="users">
                        <i class="fas fa-user-shield"></i> Usuarios
                    </button>
                <?php endif; ?>
            </div>

            <!-- Tab Contents -->
            <div id="dashboard" class="tab-content active">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <span>Personal Activo</span>
                            <i class="fas fa-users stat-icon blue"></i>
                        </div>
                        <div class="stat-value" id="dashActivePersonal">0</div>
                        <div class="stat-description">Verificados en sistema</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <span>Equipamiento</span>
                            <i class="fas fa-box stat-icon green"></i>
                        </div>
                        <div class="stat-value" id="dashTotalEquipment">0</div>
                        <div class="stat-description">Items registrados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <span>Vehículos</span>
                            <i class="fas fa-truck stat-icon purple"></i>
                        </div>
                        <div class="stat-value" id="dashTotalVehicles">0</div>
                        <div class="stat-description">Flota total</div>
                    </div>
                    <div class="stat-card" id="alertStatCard">
                        <div class="stat-header">
                            <span>Alertas</span>
                            <i class="fas fa-exclamation-triangle stat-icon orange"></i>
                        </div>
                        <div class="stat-value" id="dashAlerts">0</div>
                        <div class="stat-description">Stock bajo / Mant.</div>
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <h3>Operaciones Activas</h3>
                            <p class="card-subtitle">Estado de misiones en curso</p>
                        </div>
                        <div class="card-content">
                            <div class="mission-item">
                                <div class="mission-header">
                                    <span>Operación Neptuno</span>
                                    <span class="progress-text">65%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 65%"></div>
                                </div>
                                <span class="mission-status">En curso</span>
                            </div>
                            <div class="mission-item">
                                <div class="mission-header">
                                    <span>Patrullaje Costero</span>
                                    <span class="progress-text">80%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 80%"></div>
                                </div>
                                <span class="mission-status">En curso</span>
                            </div>
                            <div class="mission-item">
                                <div class="mission-header">
                                    <span>Entrenamiento Táctico</span>
                                    <span class="progress-text">25%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 25%"></div>
                                </div>
                                <span class="mission-status">Programado</span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="card full-width">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-pie"></i> Estadísticas Visuales</h3>
                            <p class="card-subtitle">Distribución de recursos en tiempo real</p>
                        </div>
                        <div class="card-content">
                            <div class="charts-grid">
                                <div class="chart-container">
                                    <h4>Personal por Estado</h4>
                                    <canvas id="personnelChart"></canvas>
                                </div>
                                <div class="chart-container">
                                    <h4>Equipamiento por Categoría</h4>
                                    <canvas id="equipmentChart"></canvas>
                                </div>
                                <div class="chart-container">
                                    <h4>Vehículos por Estado</h4>
                                    <canvas id="vehiclesChart"></canvas>
                                </div>
                                <div class="chart-container">
                                    <h4>Suministros por Categoría</h4>
                                    <canvas id="suppliesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Info Card -->
                <div class="user-info-card">
                    <i class="fas fa-user-circle"></i>
                    <span id="userInfo">Cargando...</span>
                    <span class="role-badge" id="userRoleBadge">-</span>
                </div>
            </div>

            <div id="personal" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user"></i> Gestión de Personal</h3>
                        <p class="card-subtitle">Administración de recursos humanos de la brigada</p>
                    </div>
                    <div class="card-content">
                        <!-- Search and Add -->
                        <div class="toolbar">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchPersonal" placeholder="Buscar personal...">
                            </div>
                            <div class="export-buttons">
                                <button class="btn-export btn-export-excel" onclick="exportToExcel('personnel')">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button class="btn-export btn-export-pdf" onclick="exportToPDF('personnel')">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </div>
                            <button class="btn btn-primary" onclick="openModal('personalModal')">
                                <i class="fas fa-plus"></i> Agregar Personal
                            </button>
                        </div>

                        <!-- Personnel Table -->
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Rango</th>
                                        <th>Posición</th>
                                        <th>Especialidad</th>
                                        <th>Estado</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="personnelTableBody">
                                    <!-- Populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div id="personnelPagination" class="pagination-container"></div>

                        <!-- Stats -->
                        <div class="stats-grid" style="margin-top: 2rem;">
                            <div class="stat-card">
                                <div class="card-header">
                                    <span>Personal Activo</span>
                                </div>
                                <div class="stat-value" id="activePersonnelCount">0</div>
                            </div>
                            <div class="stat-card">
                                <div class="card-header">
                                    <span>En Licencia</span>
                                </div>
                                <div class="stat-value" id="licensePersonnelCount">0</div>
                            </div>
                            <div class="stat-card">
                                <div class="card-header">
                                    <span>Total Personal</span>
                                </div>
                                <div class="stat-value" id="totalPersonnelCount">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="logistics" class="tab-content">
                <!-- Logistics Tabs -->
                <div class="sub-tabs">
                    <button class="sub-tab-btn active" data-subtab="equipment">Equipamiento</button>
                    <button class="sub-tab-btn" data-subtab="vehicles">Vehículos</button>
                    <button class="sub-tab-btn" data-subtab="supplies">Suministros</button>
                </div>

                <!-- Equipment Tab -->
                <div id="equipment" class="sub-tab-content active">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-box"></i> Gestión de Equipamiento</h3>
                            <p class="card-subtitle">Control de armamento, equipo táctico y material</p>
                        </div>
                        <div class="card-content">
                            <div class="toolbar">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="searchEquipment" placeholder="Buscar equipamiento...">
                                </div>
                                <div class="export-buttons">
                                    <button class="btn-export btn-export-excel" onclick="exportToExcel('equipment')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button class="btn-export btn-export-pdf" onclick="exportToPDF('equipment')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                </div>
                                <button class="btn btn-primary" onclick="openModal('equipmentModal')">
                                    <i class="fas fa-plus"></i> Agregar Equipamiento
                                </button>
                            </div>
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Cantidad</th>
                                            <th>Estado</th>
                                            <th>Ubicación</th>
                                            <th>Último Mant.</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="equipmentTableBody">
                                        <!-- Populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="equipmentPagination" class="pagination-container"></div>
                        </div>
                    </div>
                </div>

                <!-- Vehicles Tab -->
                <div id="vehicles" class="sub-tab-content">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-truck"></i> Gestión de Vehículos</h3>
                            <p class="card-subtitle">Control de flota vehicular y embarcaciones</p>
                        </div>
                        <div class="card-content">
                            <div class="toolbar">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="searchVehicles" placeholder="Buscar vehículos...">
                                </div>
                                <div class="export-buttons">
                                    <button class="btn-export btn-export-excel" onclick="exportToExcel('vehicles')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button class="btn-export btn-export-pdf" onclick="exportToPDF('vehicles')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                </div>
                                <button class="btn btn-primary" onclick="openModal('vehicleModal')">
                                    <i class="fas fa-plus"></i> Agregar Vehículo
                                </button>
                            </div>
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tipo</th>
                                            <th>Modelo</th>
                                            <th>Placa</th>
                                            <th>Estado</th>
                                            <th>Asignado a</th>
                                            <th>Último Mant.</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="vehiclesTableBody">
                                        <!-- Populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="vehiclesPagination" class="pagination-container"></div>
                        </div>
                    </div>
                </div>

                <!-- Supplies Tab -->
                <div id="supplies" class="sub-tab-content">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-boxes"></i> Gestión de Suministros</h3>
                            <p class="card-subtitle">Control de inventario y stock de suministros</p>
                        </div>
                        <div class="card-content">
                            <div class="toolbar">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="searchSupplies" placeholder="Buscar suministros...">
                                </div>
                                <div class="export-buttons">
                                    <button class="btn-export btn-export-excel" onclick="exportToExcel('supplies')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button class="btn-export btn-export-pdf" onclick="exportToPDF('supplies')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                </div>
                                <button class="btn btn-primary" onclick="openModal('supplyModal')">
                                    <i class="fas fa-plus"></i> Agregar Suministro
                                </button>
                            </div>
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Cantidad</th>
                                            <th>Unidad</th>
                                            <th>Stock Mín.</th>
                                            <th>Estado Stock</th>
                                            <th>Ubicación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="suppliesTableBody">
                                        <!-- Populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="suppliesPagination" class="pagination-container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <div id="users" class="tab-content">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-user-shield"></i> Gestión de Usuarios</h3>
                            <p class="card-subtitle">Control de accesos y roles del sistema para la Brigada</p>
                        </div>
                        <div class="card-content">
                            <div class="toolbar">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="searchUsers" placeholder="Buscar por nombre o email...">
                                </div>
                                <div class="export-buttons">
                                    <button class="btn-export btn-export-excel" onclick="exportToExcel('users')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                    <button class="btn-export btn-export-pdf" onclick="exportToPDF('users')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                </div>
                                <button class="btn btn-primary" onclick="openModal('userManagementModal')">
                                    <i class="fas fa-plus"></i> Nuevo Usuario
                                </button>
                            </div>
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="usersTableBody">
                                        <!-- Populated by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>Sistema de Gestión - Brigada de Marina © <span id="currentYear"></span></p>
        </div>
    </footer>

    <!-- Modals -->
    <?php include 'modals.php'; ?>

    <script src="script.js"></script>
</body>

</html>