<!-- Personal Modal -->
<div id="personalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar Nuevo Personal</h3>
            <button class="modal-close" onclick="closeModal('personalModal')">&times;</button>
        </div>
        <form id="personalForm">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="name" required placeholder="Ej: Cap. Juan Pérez">
            </div>
            <div class="form-group">
                <label>Rango</label>
                <select name="rank" required>
                    <option value="">Seleccionar rango</option>
                    <option value="Almirante">Almirante</option>
                    <option value="Capitán">Capitán</option>
                    <option value="Teniente">Teniente</option>
                    <option value="Sargento">Sargento</option>
                    <option value="Cabo">Cabo</option>
                    <option value="Marinero">Marinero</option>
                </select>
            </div>
            <div class="form-group">
                <label>Posición</label>
                <input type="text" name="position" required placeholder="Ej: Comandante de Unidad">
            </div>
            <div class="form-group">
                <label>Especialidad</label>
                <input type="text" name="specialty" required placeholder="Ej: Operaciones Anfibias">
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="status" required>
                    <option value="Activo">Activo</option>
                    <option value="Licencia">Licencia</option>
                    <option value="Baja">Baja</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('personalModal')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Agregar</button>
            </div>
        </form>
    </div>
</div>

<!-- Equipment Modal -->
<div id="equipmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar Nuevo Equipamiento</h3>
            <button class="modal-close" onclick="closeModal('equipmentModal')">&times;</button>
        </div>
        <form id="equipmentForm">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="category" required>
                    <option value="">Seleccionar</option>
                    <option value="Armamento">Armamento</option>
                    <option value="Protección">Protección</option>
                    <option value="Comunicaciones">Comunicaciones</option>
                    <option value="Óptica">Óptica</option>
                    <option value="Navegación">Navegación</option>
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad</label>
                <input type="number" name="quantity" required min="1">
            </div>
            <div class="form-group">
                <label>Ubicación</label>
                <input type="text" name="location" required>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="status" required>
                    <option value="Operativo">Operativo</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                    <option value="Fuera de Servicio">Fuera de Servicio</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('equipmentModal')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Vehicle Modal -->
<div id="vehicleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar Nuevo Vehículo</h3>
            <button class="modal-close" onclick="closeModal('vehicleModal')">&times;</button>
        </div>
        <form id="vehicleForm">
            <div class="form-group">
                <label>Tipo</label>
                <input type="text" name="type" required>
            </div>
            <div class="form-group">
                <label>Modelo</label>
                <input type="text" name="model" required>
            </div>
            <div class="form-group">
                <label>Placa</label>
                <input type="text" name="plate" required>
            </div>
            <div class="form-group">
                <label>Asignado a</label>
                <input type="text" name="assignedTo">
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="status" required>
                    <option value="Operativo">Operativo</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                    <option value="Fuera de Servicio">Fuera de Servicio</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('vehicleModal')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Supply Modal -->
<div id="supplyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar Nuevo Suministro</h3>
            <button class="modal-close" onclick="closeModal('supplyModal')">&times;</button>
        </div>
        <form id="supplyForm">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="category" required>
                    <option value="">Seleccionar</option>
                    <option value="Municiones">Municiones</option>
                    <option value="Alimentación">Alimentación</option>
                    <option value="Sanidad">Sanidad</option>
                    <option value="Combustible">Combustible</option>
                    <option value="Electrónica">Electrónica</option>
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad</label>
                <input type="number" name="quantity" required min="1">
            </div>
            <div class="form-group">
                <label>Unidad</label>
                <input type="text" name="unit" required placeholder="Ej: unidades, litros, kg">
            </div>
            <div class="form-group">
                <label>Stock Mínimo</label>
                <input type="number" name="minStock" required min="0">
            </div>
            <div class="form-group">
                <label>Ubicación</label>
                <input type="text" name="location" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('supplyModal')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Agregar</button>
            </div>
        </form>
    </div>
</div>

<!-- Settings Modal -->
<div id="settingsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-cog"></i> Ajustes de Perfil</h3>
            <button class="modal-close" onclick="closeModal('settingsModal')">&times;</button>
        </div>
        <form id="settingsForm">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="firstName" id="settingsFirstName" required placeholder="Tu nombre">
            </div>
            <div class="form-group">
                <label>Apellido</label>
                <input type="text" name="lastName" id="settingsLastName" required placeholder="Tu apellido">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="settingsEmail" required placeholder="tu@email.com">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('settingsModal')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
            <button class="modal-close" onclick="closeModal('changePasswordModal')">&times;</button>
        </div>
        <form id="changePasswordForm">
            <div class="form-group">
                <label>Contraseña Actual</label>
                <input type="password" name="currentPassword" id="currentPassword" required
                    placeholder="Ingresa tu contraseña actual">
            </div>
            <div class="form-group">
                <label>Nueva Contraseña</label>
                <input type="password" name="newPassword" id="newPassword" required
                    placeholder="Ingresa la nueva contraseña" minlength="6">
            </div>
            <div class="form-group">
                <label>Confirmar Nueva Contraseña</label>
                <input type="password" name="confirmPassword" id="confirmPassword" required
                    placeholder="Confirma la nueva contraseña">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    onclick="closeModal('changePasswordModal')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
            </div>
        </form>
    </div>
</div>

<!-- User Management Modal -->
<div id="userManagementModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Gestionar Usuario</h3>
            <button class="modal-close" onclick="closeModal('userManagementModal')">&times;</button>
        </div>
        <form id="userManagementForm">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="username" required placeholder="Ej: Juan Pérez">
            </div>
            <div class="form-group">
                <label>Email / Usuario</label>
                <input type="text" name="email" required placeholder="perez@marina.mil">
            </div>
            <div class="form-group" id="userPasswordGroup">
                <label>Contraseña Temporal</label>
                <input type="password" name="password" placeholder="••••••••">
                <small style="color: #64748b;">Dejar en blanco para no cambiar si edita</small>
            </div>
            <div class="form-group">
                <label>Rol de Sistema</label>
                <select name="role" required>
                    <option value="consulta">Consulta (Solo Lectura)</option>
                    <option value="operador">Operador (Edición)</option>
                    <option value="admin">Administrador (Control Total)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    onclick="closeModal('userManagementModal')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>