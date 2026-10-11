// Global State
let state = {
    personnel: [],
    equipment: [],
    vehicles: [],
    supplies: []
};

// Pagination State
const pagination = {
    personnel: { page: 1, perPage: 10 },
    equipment: { page: 1, perPage: 10 },
    vehicles: { page: 1, perPage: 10 },
    supplies: { page: 1, perPage: 10 }
};

// Pagination Helper
function paginate(data, page, perPage) {
    const start = (page - 1) * perPage;
    const end = start + perPage;
    return {
        items: data.slice(start, end),
        total: data.length,
        totalPages: Math.ceil(data.length / perPage),
        currentPage: page
    };
}

function renderPagination(resource, totalPages, currentPage, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<div class="pagination">';

    // Previous button
    html += `<button class="btn btn-sm" ${currentPage === 1 ? 'disabled' : ''} 
             onclick="changePage('${resource}', ${currentPage - 1})">
             <i class="fas fa-chevron-left"></i>
             </button>`;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button class="btn btn-sm ${i === currentPage ? 'btn-primary' : ''}" 
                     onclick="changePage('${resource}', ${i})">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += '<span class="pagination-ellipsis">...</span>';
        }
    }

    // Next button
    html += `<button class="btn btn-sm" ${currentPage === totalPages ? 'disabled' : ''} 
             onclick="changePage('${resource}', ${currentPage + 1})">
             <i class="fas fa-chevron-right"></i>
             </button>`;

    html += '</div>';
    container.innerHTML = html;
}

window.changePage = function (resource, page) {
    pagination[resource].page = page;

    switch (resource) {
        case 'personnel': renderPersonnelTable(); break;
        case 'equipment': renderEquipmentTable(); break;
        case 'vehicles': renderVehiclesTable(); break;
        case 'supplies': renderSuppliesTable(); break;
    }
};

// Toast Notification System
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const icons = {
        success: '✓',
        error: '✗',
        warning: '⚠',
        info: 'ℹ'
    };

    toast.innerHTML = `<span>${icons[type] || 'ℹ'}</span> ${message}`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// API Service
const API = {
    baseUrl: 'api.php',

    async get(resource) {
        try {
            const response = await fetch(`${this.baseUrl}?resource=${resource}`);
            if (!response.ok) throw new Error(`Error ${response.status}: ${response.statusText}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error(`API Error (${resource}):`, error);
            // Return empty array for lists, null for objects
            const listResources = ['personnel', 'equipment', 'vehicles', 'supplies', 'users'];
            return listResources.includes(resource) ? [] : null;
        }
    },

    async post(resource, data) {
        try {
            const response = await fetch(`${this.baseUrl}?resource=${resource}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { success: false };
        }
    },

    async put(resource, data) {
        try {
            const response = await fetch(`${this.baseUrl}?resource=${resource}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { success: false };
        }
    },

    async delete(resource, id) {
        try {
            const response = await fetch(`${this.baseUrl}?resource=${resource}&id=${id}`, {
                method: 'DELETE'
            });
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { success: false };
        }
    }
};

// Initialize
document.addEventListener('DOMContentLoaded', async function () {
    try {
        // Set current year
        const yearEl = document.getElementById('currentYear');
        if (yearEl) yearEl.textContent = new Date().getFullYear();

        // Setup UI
        setupTabs();
        setupSubTabs();
        setupSearch();
        setupForms();

        // 1. First Load Essential User Info
        const user = await API.get('user');
        state.user = user || { role: 'consulta', username: 'Usuario' };

        // 2. Load the rest of the data
        await loadAllData();

        // 3. Initialize everything else
        await initCharts();
        await loadUserInfo(); // This refreshes UI elements

        // Initialize Menu
        initUserMenu();
    } catch (err) {
        console.error('Carga fallida:', err);
    } finally {
        // Remove loading indicators if any
        document.querySelectorAll('.loading-indicator').forEach(el => el.remove());
        const userInfo = document.getElementById('userInfo');
        if (userInfo && userInfo.textContent === 'Cargando...') {
            userInfo.textContent = state.user?.username || 'Usuario';
        }
    }
});

async function loadAllData() {
    const currentRole = state.user ? state.user.role : 'consulta';

    const [personnel, equipment, vehicles, supplies, users] = await Promise.all([
        API.get('personnel'),
        API.get('equipment'),
        API.get('vehicles'),
        API.get('supplies'),
        (currentRole === 'admin' ? API.get('users') : Promise.resolve([]))
    ]);

    // Update global state
    state = {
        ...state,
        personnel: personnel || [],
        equipment: equipment || [],
        vehicles: vehicles || [],
        supplies: supplies || [],
        users: users || []
    };

    renderPersonnelTable();
    renderEquipmentTable();
    renderVehiclesTable();
    renderSuppliesTable();
    renderUsersTable();
    updatePersonnelStats();
    updateDashboardStats();
}

// Tab Navigation
function setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            button.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

function setupSubTabs() {
    const subTabButtons = document.querySelectorAll('.sub-tab-btn');
    const subTabContents = document.querySelectorAll('.sub-tab-content');

    subTabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.subtab;
            subTabButtons.forEach(btn => btn.classList.remove('active'));
            subTabContents.forEach(content => content.classList.remove('active'));
            button.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

// Modal & Form Handling
function openModal(modalId, mode = 'create', data = null) {
    const modal = document.getElementById(modalId);
    const form = modal.querySelector('form');
    // Change title
    const title = modal.querySelector('.modal-header h3');
    title.textContent = mode === 'edit' ? 'Editar Registro' : 'Nuevo Registro';

    // Reset form first
    form.reset();

    // Set ID if editing
    let idInput = form.querySelector('input[name="id"]');
    if (!idInput) {
        idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        form.appendChild(idInput);
    }

    if (mode === 'edit' && data) {
        idInput.value = data.id;
        // Populate fields
        for (const key in data) {
            const input = form.querySelector(`[name="${key}"]`) || form.querySelector(`[name="${keyToCamel(key)}"]`);
            if (input) input.value = data[key];
        }
    } else {
        idInput.value = '';
    }

    modal.classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Expose modal functions globally for onclick handlers
window.openModal = openModal;
window.closeModal = closeModal;

// Helper to handle snake_case to camelCase mapping for form names if needed
// But forms usually match DB columns or simple names. 
// DB: min_stock, Form: minStock.
function keyToCamel(s) {
    return s.replace(/_([a-z])/g, (g) => g[1].toUpperCase());
}

function setupForms() {
    // Generic form handler
    const handleForm = async (e, resource, callback) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const id = formData.get('id');

        let result;
        if (id) {
            result = await API.put(resource, data);
        } else {
            result = await API.post(resource, data);
        }

        if (result.success || result.id) { // Post returns {id: ...}, Put returns {success: true}
            await loadAllData();
            closeModal(form.closest('.modal').id);
            showToast(id ? 'Registro actualizado correctamente' : 'Registro creado correctamente', 'success');
        } else {
            showToast(result.error || 'Error al guardar datos', 'error');
        }
    };

    document.getElementById('personalForm') && document.getElementById('personalForm').addEventListener('submit', (e) => handleForm(e, 'personnel'));
    document.getElementById('equipmentForm') && document.getElementById('equipmentForm').addEventListener('submit', (e) => handleForm(e, 'equipment'));
    document.getElementById('vehicleForm') && document.getElementById('vehicleForm').addEventListener('submit', (e) => {
        // Map assignedTo to assigned_to if needed, or handle in API
        // API expects assignedTo? No API checks $_POST keys.
        // My API update checks $data['assignedTo'].
        // Let's ensure form has correct name="assignedTo".
        handleForm(e, 'vehicles');
    });
    document.getElementById('supplyForm') && document.getElementById('supplyForm').addEventListener('submit', (e) => handleForm(e, 'supplies'));
    document.getElementById('userManagementForm') && document.getElementById('userManagementForm').addEventListener('submit', (e) => handleForm(e, 'users'));

    // Settings Form
    document.getElementById('settingsForm') && document.getElementById('settingsForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        const result = await API.put('user', data);
        if (result.success) {
            showToast('Perfil actualizado correctamente', 'success');
            loadUserInfo();
            closeModal('settingsModal');
        } else {
            showToast(result.error || 'Error al actualizar perfil', 'error');
        }
    });

    // Change Password Form
    document.getElementById('changePasswordForm') && document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        if (data.newPassword !== data.confirmPassword) {
            showToast('Las contraseñas no coinciden', 'error');
            return;
        }

        const result = await API.put('password', data);
        if (result.success) {
            showToast('Contraseña cambiada correctamente', 'success');
            closeModal('changePasswordModal');
            e.target.reset();
        } else {
            showToast(result.error || 'Error al cambiar contraseña', 'error');
        }
    });
}

// PERSONNEL
function renderPersonnelTable(data = state.personnel) {
    const { page, perPage } = pagination.personnel;
    const { items, totalPages, currentPage } = paginate(data, page, perPage);

    const tbody = document.getElementById('personnelTableBody');
    tbody.innerHTML = '';
    items.forEach(p => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${p.id}</td>
            <td>${p.name}</td>
            <td>${p.rank}</td>
            <td>${p.position}</td>
            <td>${p.specialty}</td>
            <td><span class="badge badge-${p.status.toLowerCase()}">${p.status}</span></td>
            <td>${p.entry_date}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-icon" onclick="openEdit('personnel', '${p.id}')"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger" onclick="deleteItem('personnel', '${p.id}')"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });

    renderPagination('personnel', totalPages, currentPage, 'personnelPagination');
    applyRoleRestrictions(state.user.role);
}

function updatePersonnelStats() {
    const active = state.personnel.filter(p => p.status === 'Activo').length;
    const license = state.personnel.filter(p => p.status === 'Licencia').length;
    document.getElementById('activePersonnelCount').textContent = active;
    document.getElementById('licensePersonnelCount').textContent = license;
    document.getElementById('totalPersonnelCount').textContent = state.personnel.length;
}

// EQUIPMENT
function renderEquipmentTable(data = state.equipment) {
    const { page, perPage } = pagination.equipment;
    const { items, totalPages, currentPage } = paginate(data, page, perPage);

    const tbody = document.getElementById('equipmentTableBody');
    tbody.innerHTML = '';
    items.forEach(e => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${e.id}</td>
            <td>${e.name}</td>
            <td>${e.category}</td>
            <td>${e.quantity}</td>
            <td><span class="badge badge-${(e.status || 'operativo').toLowerCase()}">${e.status || 'Operativo'}</span></td>
            <td>${e.location}</td>
            <td>${e.last_maintenance || '-'}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-icon" onclick="openEdit('equipment', '${e.id}')"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger" onclick="deleteItem('equipment', '${e.id}')"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });

    renderPagination('equipment', totalPages, currentPage, 'equipmentPagination');
    applyRoleRestrictions(state.user.role);
}

// VEHICLES
function renderVehiclesTable(data = state.vehicles) {
    const { page, perPage } = pagination.vehicles;
    const { items, totalPages, currentPage } = paginate(data, page, perPage);

    const tbody = document.getElementById('vehiclesTableBody');
    tbody.innerHTML = '';
    items.forEach(v => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${v.id}</td>
            <td>${v.type}</td>
            <td>${v.model}</td>
            <td>${v.plate}</td>
            <td><span class="badge badge-${(v.status || 'operativo').toLowerCase()}">${v.status || 'Operativo'}</span></td>
            <td>${v.assigned_to || '-'}</td>
            <td>${v.last_maintenance || '-'}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-icon" onclick="openEdit('vehicles', '${v.id}')"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger" onclick="deleteItem('vehicles', '${v.id}')"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });

    renderPagination('vehicles', totalPages, currentPage, 'vehiclesPagination');
    applyRoleRestrictions(state.user.role);
}

// SUPPLIES
function renderSuppliesTable(data = state.supplies) {
    const { page, perPage } = pagination.supplies;
    const { items, totalPages, currentPage } = paginate(data, page, perPage);

    const tbody = document.getElementById('suppliesTableBody');
    tbody.innerHTML = '';
    items.forEach(s => {
        const stockStatus = getStockStatus(s.quantity, s.min_stock);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${s.id}</td>
            <td>${s.name}</td>
            <td>${s.category}</td>
            <td>${parseInt(s.quantity).toLocaleString()}</td>
            <td>${s.unit}</td>
            <td>${parseInt(s.min_stock).toLocaleString()}</td>
            <td><span class="badge ${stockStatus.class}">${stockStatus.text}</span></td>
            <td>${s.location}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-icon" onclick="openEdit('supplies', '${s.id}')"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger" onclick="deleteItem('supplies', '${s.id}')"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });

    renderPagination('supplies', totalPages, currentPage, 'suppliesPagination');
    applyRoleRestrictions(state.user.role);
}

// USERS
function renderUsersTable(data = state.users) {
    if (!data) return;
    const tbody = document.getElementById('usersTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';
    data.forEach(u => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${u.id}</td>
            <td><strong>${u.username}</strong></td>
            <td>${u.email}</td>
            <td><span class="badge badge-${u.role}">${u.role.toUpperCase()}</span></td>
            <td>${new Date(u.created_at).toLocaleDateString()}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-icon" onclick="openEdit('users', '${u.id}')"><i class="fas fa-edit"></i></button>
                    ${u.id != state.user.id ? `<button class="btn btn-danger" onclick="deleteItem('users', '${u.id}')"><i class="fas fa-trash"></i></button>` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function getStockStatus(quantity, minStock) {
    quantity = parseInt(quantity);
    minStock = parseInt(minStock);
    if (quantity <= minStock) return { text: 'Bajo Stock', class: 'badge-stock-bajo' };
    if (quantity <= minStock * 1.5) return { text: 'Stock Medio', class: 'badge-stock-medio' };
    return { text: 'Stock OK', class: 'badge-stock-ok' };
}

// ACTIONS
window.openEdit = function (resource, id) {
    const item = state[resource].find(i => i.id == id);
    if (!item) return;

    // Map resource to modal ID
    const modalMap = {
        'personnel': 'personalModal',
        'equipment': 'equipmentModal',
        'vehicles': 'vehicleModal',
        'supplies': 'supplyModal',
        'users': 'userManagementModal'
    };

    // Handle specific field mapping DB -> Form
    // DB: entry_date, Form: ignore? No, form likely has no date field in Add?
    // Let's check modal forms later. Assume form names match expectation.
    // DB: min_stock -> Form: minStock (handled by keyToCamel logic if manual)
    // But my fill logic is simple. I might need to patch data object.
    const formData = { ...item };
    if (formData.min_stock) formData.minStock = formData.min_stock;
    if (formData.assigned_to) formData.assignedTo = formData.assigned_to;

    openModal(modalMap[resource], 'edit', formData);
};

window.deleteItem = async function (resource, id) {
    if (confirm('¿Está seguro de eliminar este registro? Esta acción no se puede deshacer.')) {
        const result = await API.delete(resource, id);
        if (result.success) {
            await loadAllData();
            showToast('Registro eliminado correctamente', 'success');
        } else {
            showToast('Error al eliminar el registro', 'error');
        }
    }
};

window.openModal = openModal; // Export for HTML onclick
window.closeModal = closeModal;

// Search Logic
function setupSearch() {
    const setup = (inputId, resource, fields) => {
        const input = document.getElementById(inputId);
        if (!input) return;

        input.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const filtered = state[resource].filter(item =>
                fields.some(field => String(item[field] || '').toLowerCase().includes(term))
            );
            // Re-render specifics
            if (resource === 'personnel') renderPersonnelTable(filtered);
            if (resource === 'equipment') renderEquipmentTable(filtered);
            if (resource === 'vehicles') renderVehiclesTable(filtered);
            if (resource === 'supplies') renderSuppliesTable(filtered);
            if (resource === 'users') renderUsersTable(filtered);
        });
    };

    setup('searchPersonal', 'personnel', ['name', 'rank', 'position']);
    setup('searchEquipment', 'equipment', ['name', 'category']);
    setup('searchVehicles', 'vehicles', ['type', 'model', 'plate']);
    setup('searchSupplies', 'supplies', ['name', 'category']);
    setup('searchUsers', 'users', ['username', 'email', 'role']);
}

// Dashboard Stats
function updateDashboardStats() {
    // Active Personnel
    const active = state.personnel.filter(p => p.status === 'Activo').length;
    document.getElementById('dashActivePersonal').textContent = active;

    // Total Equipment (Sum quantities)
    const equipmentCount = state.equipment.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
    document.getElementById('dashTotalEquipment').textContent = equipmentCount.toLocaleString();

    // Total Vehicles
    document.getElementById('dashTotalVehicles').textContent = state.vehicles.length;

    // Alerts (Low Stock + Maintenance)
    const lowStock = state.supplies.filter(s => parseInt(s.quantity) <= parseInt(s.min_stock)).length;
    const maintenanceV = state.vehicles.filter(v => v.status === 'Mantenimiento').length;
    const maintenanceE = state.equipment.filter(e => e.status === 'Mantenimiento').length;

    const totalAlerts = lowStock + maintenanceV + maintenanceE;
    document.getElementById('dashAlerts').textContent = totalAlerts;

    const alertCard = document.getElementById('alertStatCard');
    if (alertCard) {
        if (totalAlerts > 0) {
            alertCard.classList.add('alerts-critical');
        } else {
            alertCard.classList.remove('alerts-critical');
        }
    }
}

// Modal Click Outside
window.addEventListener('click', function (event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
});

// Translation mapping for headers
const headerMap = {
    'id': 'ID',
    'name': 'Nombre',
    'rank': 'Rango/Grado',
    'position': 'Cargo/Función',
    'specialty': 'Especialidad',
    'status': 'Estado',
    'entry_date': 'Fecha de Ingreso',
    'created_at': 'Fecha Registro',
    'updated_at': 'Última Act.',
    'category': 'Categoría',
    'quantity': 'Cantidad',
    'location': 'Ubicación',
    'last_maintenance': 'Últ. Mantenimiento',
    'type': 'Tipo',
    'model': 'Modelo',
    'plate': 'Placa/Matrícula',
    'assigned_to': 'Asignado a',
    'username': 'Usuario',
    'email': 'Email',
    'role': 'Rol',
    'unit': 'Unidad',
    'min_stock': 'Stock Mín.'
};

const resourceTitles = {
    'personnel': 'Personal',
    'equipment': 'Equipamiento',
    'vehicles': 'Vehículos',
    'supplies': 'Suministros',
    'users': 'Usuarios'
};

// Export Functions
window.exportToExcel = function (resource) {
    const data = state[resource];
    if (!data || data.length === 0) {
        showToast('No hay datos para exportar', 'warning');
        return;
    }

    const rawHeaders = Object.keys(data[0]);
    const translatedHeaders = rawHeaders.map(h => headerMap[h] || h);

    // Use semicolon as delimiter for better Excel compatibility in Spanish locales
    const delimiter = ';';

    // Create CSV content (Excel friendly with BOM)
    let csv = translatedHeaders.join(delimiter) + '\n';

    data.forEach(row => {
        csv += rawHeaders.map(h => {
            let value = row[h] || '';
            // Escape quotes and wrap in quotes if contains delimiter or double quotes
            if (String(value).includes(delimiter) || String(value).includes('"') || String(value).includes('\n')) {
                value = '"' + String(value).replace(/"/g, '""') + '"';
            }
            return value;
        }).join(delimiter) + '\n';
    });

    // Download
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const fileName = `Reporte_${resourceTitles[resource] || resource}_${new Date().toLocaleDateString().replace(/\//g, '-')}.csv`;
    link.href = URL.createObjectURL(blob);
    link.download = fileName;
    link.click();

    showToast('Archivo Excel exportado correctamente', 'success');
};

window.exportToPDF = function (resource) {
    const data = state[resource];
    if (!data || data.length === 0) {
        showToast('No hay datos para exportar', 'warning');
        return;
    }

    const rawHeaders = Object.keys(data[0]);
    const translatedHeaders = rawHeaders.map(h => headerMap[h] || h);
    const title = resourceTitles[resource] || resource;

    // Create printable HTML
    let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de ${title}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
                h1 { color: #0f172a; text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 10px; }
                .meta { margin-bottom: 20px; font-size: 0.9rem; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.85rem; }
                th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
                th { background-color: #0f172a; color: white; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
                tr:nth-child(even) { background-color: #f8fafc; }
                .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 0.8rem; border-top: 1px solid #e2e8f0; padding-top: 10px; }
            </style>
        </head>
        <body>
            <h1>REPORTE DE ${title.toUpperCase()}</h1>
            <div class="meta">
                <p><strong>Fecha de generación:</strong> ${new Date().toLocaleString('es-ES')}</p>
                <p><strong>Total de registros:</strong> ${data.length}</p>
            </div>
            <table>
                <thead>
                    <tr>${translatedHeaders.map(h => `<th>${h}</th>`).join('')}</tr>
                </thead>
                <tbody>
                    ${data.map(row => `<tr>${rawHeaders.map(h => `<td>${row[h] || '-'}</td>`).join('')}</tr>`).join('')}
                </tbody>
            </table>
            <div class="footer">Sistema Integral de Gestión - Brigada de Marina Boliviana</div>
        </body>
        </html>
    `;

    // Open print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(html);
    printWindow.document.close();

    // Wait for content to load before printing
    setTimeout(() => {
        printWindow.print();
    }, 500);

    showToast('Reporte PDF generado correctamente', 'success');
};

// Chart instances
let charts = {};

// Chart color palette
const chartColors = {
    primary: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
    pastel: ['#93c5fd', '#6ee7b7', '#fcd34d', '#fca5a5', '#c4b5fd', '#f9a8d4']
};

// Initialize charts
async function initCharts() {
    try {
        const response = await fetch('api.php?resource=stats');
        if (!response.ok) return;

        const stats = await response.json();

        // Personnel by status
        if (stats.personnelByStatus && stats.personnelByStatus.length > 0) {
            createDoughnutChart('personnelChart', stats.personnelByStatus, 'status', 'count');
        }

        // Equipment by category
        if (stats.equipmentByCategory && stats.equipmentByCategory.length > 0) {
            createBarChart('equipmentChart', stats.equipmentByCategory, 'category', 'count');
        }

        // Vehicles by status
        if (stats.vehiclesByStatus && stats.vehiclesByStatus.length > 0) {
            createDoughnutChart('vehiclesChart', stats.vehiclesByStatus, 'status', 'count');
        }

        // Supplies by category
        if (stats.suppliesByCategory && stats.suppliesByCategory.length > 0) {
            createBarChart('suppliesChart', stats.suppliesByCategory, 'category', 'count');
        }

    } catch (error) {
        console.error('Error loading charts:', error);
    }
}

function createDoughnutChart(canvasId, data, labelKey, valueKey) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    // Destroy existing chart
    if (charts[canvasId]) {
        charts[canvasId].destroy();
    }

    charts[canvasId] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d[labelKey] || 'Sin definir'),
            datasets: [{
                data: data.map(d => parseInt(d[valueKey]) || 0),
                backgroundColor: chartColors.primary,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

function createBarChart(canvasId, data, labelKey, valueKey) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    // Destroy existing chart
    if (charts[canvasId]) {
        charts[canvasId].destroy();
    }

    charts[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d[labelKey] || 'Sin definir'),
            datasets: [{
                label: 'Cantidad',
                data: data.map(d => parseInt(d[valueKey]) || 0),
                backgroundColor: chartColors.primary.slice(0, data.length),
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

// User Menu Logic
function initUserMenu() {
    const menuBtn = document.getElementById('userMenuBtn');
    const menu = document.querySelector('.user-menu');

    if (menuBtn) {
        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('active');
        });
    }

    document.addEventListener('click', () => {
        if (menu) menu.classList.remove('active');
    });

    window.closeUserMenu = () => {
        if (menu) menu.classList.remove('active');
    };

    window.confirmLogout = () => {
        if (confirm('¿Está seguro de que desea cerrar la sesión actual?')) {
            window.location.href = '../procesos/salir.php';
        }
    };
}

// Load user info and role
async function loadUserInfo() {
    try {
        const response = await fetch('api.php?resource=user');
        if (!response.ok) return;

        const user = await response.json();

        // Update Dashboard Info
        const userInfoEl = document.getElementById('userInfo');
        const roleBadgeEl = document.getElementById('userRoleBadge');

        if (userInfoEl) userInfoEl.textContent = user.username || user.email || 'Usuario';

        if (roleBadgeEl) {
            const roleNames = { 'admin': 'Administrador', 'operador': 'Operador', 'consulta': 'Consulta' };
            roleBadgeEl.textContent = roleNames[user.role] || user.role;
            roleBadgeEl.className = 'role-badge ' + (user.role || 'operador');
        }

        // Update Header & Dropdown
        const headerUsername = document.getElementById('headerUsername');
        const dropdownUsername = document.getElementById('dropdownUsername');
        const dropdownRole = document.getElementById('dropdownRole');

        const roleNames = { 'admin': 'Administrador', 'operador': 'Operador', 'consulta': 'Consulta' };
        const displayName = user.username || user.email || 'Usuario';

        if (headerUsername) headerUsername.textContent = displayName;
        if (dropdownUsername) dropdownUsername.textContent = displayName;
        if (dropdownRole) dropdownRole.textContent = roleNames[user.role] || user.role;

        // Populate Settings Form
        if (document.getElementById('settingsForm')) {
            const names = (user.username || '').split(' ');
            document.getElementById('settingsFirstName').value = names[0] || '';
            document.getElementById('settingsLastName').value = names.slice(1).join(' ') || '';
            document.getElementById('settingsEmail').value = user.email || '';
        }

        // Apply role-based UI restrictions
        applyRoleRestrictions(user.role);

    } catch (error) {
        console.error('Error loading user info:', error);
    }
}

function applyRoleRestrictions(role) {
    const deleteButtons = document.querySelectorAll('.btn-danger');
    const addButtons = document.querySelectorAll('.btn-primary');

    if (role === 'consulta') {
        // Hide all action buttons for query users (read only)
        deleteButtons.forEach(btn => btn.style.display = 'none');
        addButtons.forEach(btn => btn.style.display = 'none');
        document.querySelectorAll('.btn-icon').forEach(btn => btn.style.display = 'none');
    } else if (role === 'operador') {
        // Hide only delete buttons for operators
        deleteButtons.forEach(btn => {
            if (btn.onclick && btn.onclick.toString().includes('deleteItem')) {
                btn.style.display = 'none';
            }
        });
    }
}


// Global Search Handler
window.handleGlobalSearch = function (term) {
    const resultsContainer = document.getElementById('globalSearchResults');
    if (!term || term.length < 2) {
        resultsContainer.classList.remove('active');
        return;
    }

    const searchData = [
        { resource: 'personnel', label: 'Personal', icon: 'fa-user', fields: ['name', 'rank'] },
        { resource: 'equipment', label: 'Equipos', icon: 'fa-box', fields: ['name', 'category'] },
        { resource: 'vehicles', label: 'Vehículos', icon: 'fa-truck', fields: ['type', 'model', 'plate'] },
        { resource: 'supplies', label: 'Suministros', icon: 'fa-boxes', fields: ['name', 'category'] }
    ];

    let matches = [];
    searchData.forEach(type => {
        const items = state[type.resource] || [];
        items.forEach(item => {
            const matchesField = type.fields.some(f => String(item[f] || '').toLowerCase().includes(term.toLowerCase()));
            if (matchesField) {
                matches.push({
                    id: item.id,
                    title: item.name || item.type || item.username,
                    subtitle: item.rank || item.category || item.model || item.email,
                    resource: type.resource,
                    label: type.label,
                    icon: type.icon
                });
            }
        });
    });

    resultsContainer.innerHTML = '';
    if (matches.length === 0) {
        resultsContainer.innerHTML = '<div class="search-result-item"><small>No se encontraron resultados</small></div>';
    } else {
        matches.slice(0, 10).forEach(m => {
            const div = document.createElement('div');
            div.className = 'search-result-item';
            div.innerHTML = `
                <i class="fas ${m.icon}"></i>
                <div class="search-result-info">
                    <span class="search-result-title">${m.title}</span>
                    <span class="search-result-subtitle">${m.subtitle}</span>
                </div>
                <span class="search-result-category">${m.label}</span>
            `;
            div.onclick = () => {
                // Switch tab
                const tabBtn = document.querySelector(`.tab-btn[data-tab="${m.resource}"]`);
                if (tabBtn) tabBtn.click();

                // Highlight or search in that tab's search box to filter
                const tabSearchId = {
                    'personnel': 'searchPersonal',
                    'equipment': 'searchEquipment',
                    'vehicles': 'searchVehicles',
                    'supplies': 'searchSupplies'
                }[m.resource];

                const tabSearch = document.getElementById(tabSearchId);
                if (tabSearch) {
                    tabSearch.value = m.title;
                    tabSearch.dispatchEvent(new Event('input'));
                }

                resultsContainer.classList.remove('active');
                document.getElementById('globalSearch').value = '';
            };
            resultsContainer.appendChild(div);
        });
    }
    resultsContainer.classList.add('active');
};

// Close global search results on click outside
document.addEventListener('click', (e) => {
    const results = document.getElementById('globalSearchResults');
    const searchInput = document.getElementById('globalSearch');
    if (results && !results.contains(e.target) && !searchInput.contains(e.target)) {
        results.classList.remove('active');
    }
});
