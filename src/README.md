# Sistema de Gestión - Brigada de Marina

Sistema completo de gestión de recursos humanos y logística desarrollado en HTML, CSS, JavaScript y PHP.

## 📋 Características

- **Dashboard**: Vista general con estadísticas y operaciones activas
- **Gestión de Personal**: CRUD completo para administrar el personal de la brigada
- **Gestión de Logística**: 
  - Control de equipamiento
  - Administración de vehículos
  - Gestión de suministros e inventario
- **Búsqueda en tiempo real** en todas las secciones
- **Almacenamiento local** con LocalStorage (modo offline)
- **API REST** con PHP para conexión a base de datos MySQL

## 🚀 Instalación

### Requisitos
- Servidor web (Apache/Nginx)
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Navegador web moderno

### Configuración Básica (Solo Frontend)

1. Descargue todos los archivos en una carpeta
2. Abra `index.html` en su navegador
3. El sistema funcionará con LocalStorage (los datos se guardan en el navegador)

### Configuración Completa (Con Base de Datos)

1. **Instalar servidor web con PHP y MySQL** (XAMPP, WAMP, LAMP, etc.)

2. **Crear la base de datos:**
   ```bash
   mysql -u root -p < database.sql
   ```

3. **Configurar la conexión en `api.php`:**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'tu_contraseña');
   define('DB_NAME', 'brigada_marina');
   ```

4. **Modificar `script.js`** para usar la API en lugar de LocalStorage:
   - Descomentar las llamadas AJAX
   - Comentar las funciones de LocalStorage

5. **Colocar archivos en el directorio web:**
   - Para XAMPP: `C:\xampp\htdocs\brigada-marina\`
   - Para Linux: `/var/www/html/brigada-marina/`

6. **Acceder desde el navegador:**
   ```
   http://localhost/brigada-marina/
   ```

## 📁 Estructura de Archivos

```
/
├── index.html          # Página principal
├── modals.php          # Modales para formularios
├── styles.css          # Estilos personalizados
├── script.js           # Lógica JavaScript
├── api.php             # API REST para backend
├── database.sql        # Script de base de datos
└── README.md           # Este archivo
```

## 🔧 Uso

### Dashboard
- Visualice estadísticas generales
- Monitoree operaciones activas
- Revise actividad reciente

### Recursos Humanos
- Agregar nuevo personal con el botón "Agregar Personal"
- Buscar por nombre, rango o posición
- Editar o eliminar registros
- Ver estadísticas de personal activo

### Logística
**Equipamiento:**
- Registrar armamento y equipo táctico
- Control de ubicación y mantenimiento

**Vehículos:**
- Gestionar flota vehicular
- Asignar vehículos a unidades
- Control de mantenimiento

**Suministros:**
- Control de inventario
- Alertas de stock bajo
- Gestión de ubicaciones

## 🔐 Seguridad

Para uso en producción, asegúrese de:
- Cambiar credenciales de base de datos
- Implementar autenticación de usuarios
- Validar y sanitizar entradas
- Usar conexiones HTTPS
- Implementar control de acceso basado en roles

## 🎨 Personalización

### Modificar colores
Edite `styles.css` para cambiar la paleta de colores:
```css
/* Color primario */
.btn-primary { background: #0f172a; }
```

### Agregar campos
1. Modifique la estructura en `database.sql`
2. Actualice los formularios en `modals.php`
3. Ajuste las funciones en `script.js`
4. Actualice la API en `api.php`

## 📱 Responsive

El sistema es completamente responsive y se adapta a:
- Escritorio (1920px+)
- Laptop (1366px)
- Tablet (768px)
- Móvil (320px+)

## 🐛 Solución de Problemas

**No se guardan los datos:**
- Verifique que LocalStorage esté habilitado en su navegador
- Si usa la API, verifique la conexión a la base de datos

**Error de conexión a base de datos:**
- Verifique credenciales en `api.php`
- Asegúrese de que MySQL esté corriendo
- Verifique que la base de datos exista

**Los estilos no se cargan:**
- Verifique que `styles.css` esté en la misma carpeta
- Limpie caché del navegador

## 📄 Licencia

Este proyecto es de código abierto para uso educativo y militar.

## 👥 Soporte

Para soporte técnico o consultas, contacte al administrador del sistema.

---

**Desarrollado para la Brigada de Marina** - Sistema de Gestión v1.0
