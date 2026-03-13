// Control de sesión - VERSIÓN SIMPLIFICADA Y SEGURA
document.addEventListener('DOMContentLoaded', function() {
    
    // Solo ejecutar en páginas protegidas (no en login)
    if (window.location.pathname.includes('index.php')) {
        return;
    }
    
    // Variable para controlar si es una navegación interna
    let isInternalNavigation = false;
    
    // Marcar todos los enlaces internos
    document.querySelectorAll('a').forEach(link => {
        if (link.href && !link.href.startsWith('http') || 
            link.href.includes(window.location.hostname)) {
            link.addEventListener('click', () => {
                isInternalNavigation = true;
            });
        }
    });
    
    // Verificar sesión periódicamente
    function checkSession() {
        fetch('../../../check_session.php?t=' + Date.now())
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    window.location.href = 'index.php?session_expired=1';
                }
            })
            .catch(() => {
                console.log('Error al verificar sesión');
            });
    }
    
    // Verificar cada 1 minuto
    setInterval(checkSession, 60000);
    
    // Verificar al cargar la página
    checkSession();
    
    // Detectar cierre REAL de pestaña/ventana (no navegación interna)
    window.addEventListener('beforeunload', function(e) {
        // NO hacer nada si es navegación interna
        if (isInternalNavigation) {
            return;
        }
        
        // Solo enviar logout si realmente se cierra la pestaña
        // (no podemos detectar perfectamente, pero esto ayuda)
        const isPageRefresh = performance.navigation && 
                             (performance.navigation.type === 1 || 
                              performance.getEntriesByType('navigation')[0]?.type === 'reload');
        
        if (!isPageRefresh) {
            // Enviar señal de logout solo en cierre real
            try {
                navigator.sendBeacon('../../../app/controlador/Usuario/cerrar_sesion.php');
            } catch (e) {
                // Silenciar errores
            }
        }
    });
    
    // Detectar cuando la pestaña pierde focus por mucho tiempo
    let hiddenTime = null;
    
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            hiddenTime = Date.now();
            // Si la pestaña está oculta por más de 1 minuto, verificar sesión
            setTimeout(function() {
                if (document.hidden && hiddenTime) {
                    checkSession();
                }
            }, 60000);
        } else {
            hiddenTime = null;
        }
    });
    
    // Prevenir cache del navegador
    window.onpageshow = function(event) {
        if (event.persisted) {
            checkSession();
        }
    };
});