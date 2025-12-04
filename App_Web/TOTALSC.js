// =================================================================
// ⭐️ MÓDULO CENTRAL: NAVEGACIÓN, PERSISTENCIA Y PERFIL (TOTALSC.js)
// =================================================================

// 🔑 BANDERAS DE PERSISTENCIA
const ADMIN_VET_FLAG = 'isVetModeActive';
const ADMIN_SUPER_FLAG = 'isSuperModeActive';

/* ------------------------------------------- */
/* === 1. LÓGICA DEL MENÚ DESPLEGABLE === */
/* ------------------------------------------- */
function toggleMenu() {
    document.getElementById("admin-dropdown").classList.toggle("show");
}

/* ------------------------------------------- */
/* === 2. LÓGICA DE CIERRE DEL MENÚ (CLICK FUERA) === */
/* ------------------------------------------- */
window.onclick = function(event) {
    const dropdown = document.getElementById('admin-dropdown');
    
    if (dropdown && dropdown.classList.contains('show')) {
        const isClickInsideProfile = event.target.closest('.user-profile-header');
        const isClickInsideDropdown = event.target.closest('.dropdown-content');
        
        if (!isClickInsideProfile && !isClickInsideDropdown) {
            dropdown.classList.remove('show');
        }
    }
}

/* ------------------------------------------- */
/* === 3. NAVEGACIÓN === */
/* ------------------------------------------- */

function irA(pagina) {
    window.location.href = pagina;
}

/* ------------------------------------------- */
/* === 4. CARGA INICIAL Y VERIFICACIÓN === */
/* ------------------------------------------- */

function cargarUsuarioDesdeURL() {
    // Solo verificamos el modo Admin/Vet para restaurar los botones de la interfaz.
    verificarModoAdministrador();
}

// Ejecutar la función de carga al iniciar la página
document.addEventListener('DOMContentLoaded', cargarUsuarioDesdeURL);


/* ------------------------------------------- */
/* === 5. FUNCIONES DE ACCESO Y PERSISTENCIA (2 NIVELES) === */
/* ------------------------------------------- */

/**
 * Función para navegar al Panel de Admin (el más alto activo).
 */
function volverAdminPanel() {
    // Redirige al panel correcto basado en el nivel más alto activo.
    if (sessionStorage.getItem(ADMIN_SUPER_FLAG) === 'true') {
        window.location.href = 'PanelSuperior.html';
    } else if (sessionStorage.getItem(ADMIN_VET_FLAG) === 'true') {
        window.location.href = 'Administrador.html';
    }
    return false;
}

/**
 * 5.1 Activa los estilos y botones de la interfaz de Administrador.
 * @param {boolean} isSuperActive - Indica si es el Administrador Superior (Nivel 2).
 */
function activarInterfazAdministrador(isSuperActive = false) {
    const isPanelConsultasPage = window.location.pathname.includes('Administrador.html'); 
    const isPanelSuperiorPage = window.location.pathname.includes('PanelSuperior.html');
    
    // Nombres de los botones para el estado actual
    const accesoVetBtn = document.getElementById('acceso-veterinario-btn');
    const accesoSuperBtn = document.getElementById('acceso-superior-btn');
    const cerrarAdminBtn = document.getElementById('cerrar-admin-btn');
    const volverAdminBtn = document.getElementById('volver-admin-btn');
    const cerrarSesionBtn = document.getElementById('cerrar-sesion-btn');

    // 1. Ocultar el botón "Acceso: Veterinario"
    if (accesoVetBtn) {
        accesoVetBtn.style.display = 'none';
    }
    

    // 2. Controlar la visibilidad y texto del botón "Acceso: Administrador"
    if (accesoSuperBtn) {
        const shouldShowAccessButton = isPanelConsultasPage && !isSuperActive;
        accesoSuperBtn.style.display = shouldShowAccessButton ? 'block' : 'none';
    }

    // 3. Ajustar el botón de Cierre
    if (cerrarAdminBtn) {
        cerrarAdminBtn.style.display = 'block';
        cerrarAdminBtn.textContent = isSuperActive ? 'Cerrar Administrador Superior' : 'Cerrar Nivel Veterinario';
    }
    
    // 4. Ajustar el botón Volver al Panel
    if (volverAdminBtn) {
        const isAlreadyInPanel = isPanelConsultasPage || isPanelSuperiorPage;
        volverAdminBtn.style.display = isAlreadyInPanel ? 'none' : 'block';
        volverAdminBtn.textContent = isSuperActive ? 'Panel Superior' : 'Panel Veterinario';
    }

    // 5. Estilos visuales
    const userDisplay = document.getElementById('user-display');
    if (userDisplay) {
        userDisplay.style.color = isSuperActive ? '#E60000' : '#dc3545'; // Rojo fuerte para Super Admin
        // Si es Super Admin, añadimos una etiqueta visual
        if (isSuperActive && !userDisplay.textContent.includes('(SUP)')) {
             userDisplay.textContent = userDisplay.textContent.trim() + ' (SUP)';
        }
    }
    
    // 6. Ocultar cerrar sesión normal
    if (cerrarSesionBtn) {
        cerrarSesionBtn.style.display = 'none';
    }
}

/**
 * 5.2 Función para activar el Modo Veterinario (Nivel 1).
 */
function activarModoAdministrador() {
    const dropdown = document.getElementById('admin-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }

    const codigoIngresado = prompt("🔒 Por favor, ingresa el código de acceso de VETERINARIO (Cualquier texto para prueba):");

    if (codigoIngresado !== null && codigoIngresado.trim() !== "") {
        sessionStorage.setItem(ADMIN_VET_FLAG, 'true');
        
        activarInterfazAdministrador(false); 
        
        if (!window.location.pathname.includes('Administrador.html')) {
            window.location.href = 'Administrador.html';
        }
    } else if (codigoIngresado !== null) {
        alert("Debes ingresar algo para acceder.");
    }
    return false;
}

/**
 * 5.3 Activa el Nivel de Administrador Superior (Nivel 2).
 */
function activarModoAdministradorSuperior() {
    const dropdown = document.getElementById('admin-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }

    const codigoIngresado = prompt("👑 INGRESO ADMIN SUPERIOR. Por favor, ingresa el código (Cualquier texto es válido para prueba):");

    if (codigoIngresado !== null && codigoIngresado.trim() !== "") {
        sessionStorage.setItem(ADMIN_SUPER_FLAG, 'true');
        
        activarInterfazAdministrador(true); 
        
        window.location.href = 'PanelSuperior.html';
    } else if (codigoIngresado !== null) {
        alert("Debes ingresar algo para acceder.");
    }
    return false;
}
function cerrarModoAdministrador1() {
    const dropdown = document.getElementById('admin-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }
    
    // Limpiar flags de sesión
    sessionStorage.removeItem(ADMIN_SUPER_FLAG);
    
    alert('Nivel de acceso cerrado. Interfaz restablecida.');
    
    // Redirige a la página principal después de cerrar nivel
    window.location.href = 'Administrador.html';

    return false;
}


/**
 * 5.4 Cierra la sesión de Administrador/Veterinario.
 */
function cerrarModoAdministrador() {
    const dropdown = document.getElementById('admin-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }
    
    // Limpiar flags de sesión
    sessionStorage.removeItem(ADMIN_VET_FLAG);
    sessionStorage.removeItem(ADMIN_SUPER_FLAG);
    
    alert('Nivel de acceso cerrado. Interfaz restablecida.');
    
    // Redirige a la página principal después de cerrar nivel
    window.location.href = 'PetHappy.php';

    return false;
}


/**
 * 5.5 Verifica la persistencia en cada carga de página.
 */
function verificarModoAdministrador() {
    const isSuperActive = sessionStorage.getItem(ADMIN_SUPER_FLAG) === 'true';
    const isVetActive = sessionStorage.getItem(ADMIN_VET_FLAG) === 'true';

    // Priorizamos el nivel superior
    if (isSuperActive) {
        activarInterfazAdministrador(true);
    } else if (isVetActive) {
        activarInterfazAdministrador(false);
    }
}