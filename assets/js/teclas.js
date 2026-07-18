// teclas.js - Manejador de atajos de teclado globales para el sistema

(function() {
    let activeNormalSectionId = 'resumen';
    let isPruebaModeActive = false;

    // Función auxiliar para ejecutar la transición de pantalla con lluvia digital de Matrix
    function runLoadingTransition(title, subtitle, onComplete) {
        // Asegurar que existan los estilos del loader
        if (!document.getElementById('bsc-loader-styles')) {
            const style = document.createElement('style');
            style.id = 'bsc-loader-styles';
            style.textContent = `
                @keyframes pulseGreenText {
                    0%, 100% { opacity: 0.5; }
                    50% { opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }

        // Crear e inyectar el overlay de Matrix en el body
        const overlay = document.createElement('div');
        overlay.id = 'bsc-loading-overlay';
        overlay.setAttribute('style', `
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: #000000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 999999;
            opacity: 0;
            transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        `);

        // Contenedor de Texto Neon Terminal
        overlay.innerHTML = `
            <div style="text-align: center; z-index: 10; padding: 20px; background: rgba(0, 0, 0, 0.85); border-radius: 8px; border: 1px solid rgba(0, 255, 0, 0.2); box-shadow: 0 0 20px rgba(0, 255, 0, 0.1);">
                <h3 style="font-weight: 700; color: #00ff00; margin-bottom: 8px; font-size: 1.35rem; font-family: 'Courier New', Courier, monospace; text-shadow: 0 0 10px rgba(0, 255, 0, 0.7); letter-spacing: 1px;">${title}</h3>
                <p style="color: #00cc00; font-size: 0.88rem; font-family: 'Courier New', Courier, monospace; animation: pulseGreenText 1.2s infinite; margin: 0; text-shadow: 0 0 5px rgba(0, 255, 0, 0.4);">${subtitle}</p>
            </div>
        `;

        // Crear el Canvas para la Lluvia Matrix
        const canvas = document.createElement('canvas');
        canvas.setAttribute('style', 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;');
        overlay.appendChild(canvas);
        document.body.appendChild(overlay);

        // Inicializar el Canvas de Matrix
        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        const alphabet = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ$#@%&*+-/=<>[]{}嵐カタカナ".split("");
        const fontSize = 15;
        const columns = width / fontSize;
        const rainDrops = [];

        for (let x = 0; x < columns; x++) {
            rainDrops[x] = 1;
        }

        function drawMatrixRain() {
            // Fondo traslúcido negro para dar el efecto de rastro/fading
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, width, height);

            ctx.fillStyle = '#0f0'; // Color verde Matrix
            ctx.font = fontSize + 'px monospace';

            for (let i = 0; i < rainDrops.length; i++) {
                const text = alphabet[Math.floor(Math.random() * alphabet.length)];
                ctx.fillText(text, i * fontSize, rainDrops[i] * fontSize);

                if (rainDrops[i] * fontSize > height && Math.random() > 0.975) {
                    rainDrops[i] = 0;
                }
                rainDrops[i]++;
            }
        }

        // Loop de renderizado
        const matrixInterval = setInterval(drawMatrixRain, 33);

        // Forzar reflow y aplicar fade-in
        overlay.offsetHeight;
        overlay.style.opacity = '1';

        // Duración visible de la pantalla de carga (1200ms para permitir el efecto lluvia)
        setTimeout(function() {
            // Ejecutar el cambio de DOM en medio de la transición oculta
            if (typeof onComplete === 'function') {
                onComplete();
            }

            // Desvanecer el overlay
            overlay.style.opacity = '0';

            // Quitar del DOM al finalizar la animación (350ms) y limpiar interval
            setTimeout(function() {
                clearInterval(matrixInterval);
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 350);
        }, 1200);
    }

    document.addEventListener('keydown', function(e) {
        // Detectar CTRL + Q (independiente de mayúsculas/minúsculas)
        if (e.ctrlKey && (e.key === 'q' || e.key === 'Q')) {
            e.preventDefault(); // Prevenir el comportamiento por defecto de algunos navegadores

            const navContainer = document.querySelector('.dashboard-nav');
            const sections = document.querySelectorAll('.dashboard-section');
            const navItems = document.querySelectorAll('.dashboard-nav-item');
            const sectionPrueba = document.getElementById('section-prueba');

            if (!sectionPrueba) return;

            if (!isPruebaModeActive) {
                // === ACTIVAR MODO PRUEBA (Reemplazar Home) ===
                runLoadingTransition(
                    '[ ACCESO AL MANDO INTEGRAL ]',
                    'Descompilando módulos del balanced scorecard...',
                    function() {
                        // 1. Guardar la pestaña activa actual antes de ocultarla
                        let foundActive = false;
                        sections.forEach(function(s) {
                            if (s.id !== 'section-prueba' && s.classList.contains('active')) {
                                activeNormalSectionId = s.id.replace('section-', '');
                                foundActive = true;
                            }
                        });
                        
                        // Valor por defecto en caso de no encontrarse
                        if (!foundActive) {
                            activeNormalSectionId = 'resumen';
                        }

                        // 2. Ocultar todas las secciones estándar
                        sections.forEach(function(s) {
                            s.classList.remove('active');
                        });

                        // 3. Quitar clase active de los botones de la navegación superior
                        navItems.forEach(function(btn) {
                            btn.classList.remove('active');
                        });

                        // 4. Ocultar la barra de navegación de pestañas internas
                        if (navContainer) {
                            navContainer.style.display = 'none';
                        }

                        // 5. Mostrar la sección de Prueba
                        sectionPrueba.classList.add('active');
                        sectionPrueba.style.display = 'block';

                        isPruebaModeActive = true;

                        // Disparar evento personalizado para redibujar gráficos interactivos
                        document.dispatchEvent(new CustomEvent('prueba-mode-activated'));
                    }
                );
            } else {
                // === DESACTIVAR MODO PRUEBA (Volver al Home Normal) ===
                runLoadingTransition(
                    '[ SALIDA DE MANDO INTEGRAL ]',
                    'Restaurando interfaz académica de administración...',
                    function() {
                        // 1. Ocultar sección de Prueba
                        sectionPrueba.classList.remove('active');
                        sectionPrueba.style.display = 'none';

                        // 2. Volver a mostrar la barra de navegación
                        if (navContainer) {
                            navContainer.style.display = '';
                        }

                        // 3. Restaurar la sección estándar activa previa
                        const previousSection = document.getElementById('section-' + activeNormalSectionId);
                        if (previousSection) {
                            previousSection.classList.add('active');
                        }

                        // 4. Restaurar el botón de navegación activo previo
                        navItems.forEach(function(btn) {
                            if (btn.dataset.section === activeNormalSectionId) {
                                btn.classList.add('active');
                            }
                        });

                        isPruebaModeActive = false;
                    }
                );
            }
        }
    });

    // Revisar si venimos de un redireccionamiento desde otra página
    if (window.location.search.includes('prueba=true')) {
        // Limpiar la URL para evitar que al recargar se vuelva a ejecutar
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({path: newUrl}, '', newUrl);

        // Esperar un momento a que el DOM y sections se carguen y disparar el evento
        setTimeout(() => {
            document.dispatchEvent(new KeyboardEvent('keydown', { key: 'q', ctrlKey: true }));
        }, 300);
    }
})();
