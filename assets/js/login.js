/* ════════════════════════════════════════════════════════════════════
   CAPA 1 — MOTOR DE ESFERAS
   Posiciones iniciales aleatorias en todo el viewport
════════════════════════════════════════════════════════════════════ */
(function () {

    const MAX_OPACITY = 0.72;

    // Coloca cada esfera en una posición inicial aleatoria dentro del viewport
    const orbs = document.querySelectorAll('.orbs-layer .lava-orb');
    orbs.forEach(function (orb, index) {
        const size = parseInt(window.getComputedStyle(orb).width);
        orb.style.left = (Math.random() * (window.innerWidth * 0.50 - size)) + 'px';
        orb.style.top  = (Math.random() * (window.innerHeight - size)) + 'px';
        
        setTimeout(function () {
            runOrbCycle(orb);
        }, index * 50); 
    });

    function runOrbCycle(orb) {
        const duration = 2000 + Math.random() * 2000; 

        const angle    = Math.random() * Math.PI * 2;
        const distance = 80 + Math.random() * 140;
        const dx = Math.cos(angle) * distance;
        const dy = Math.sin(angle) * distance;

        const keyframes = [
            { opacity: 0,           transform: 'translate(0px, 0px)',                     offset: 0   },
            { opacity: MAX_OPACITY, transform: `translate(${dx * .5}px, ${dy * .5}px)`,   offset: 0.5 },
            { opacity: 0,           transform: `translate(${dx}px, ${dy}px)`,             offset: 1   }
        ];

        const anim = orb.animate(keyframes, {
            duration: duration,
            easing:   'ease-in-out',
            fill:     'none'
        });

        anim.onfinish = function () {
            const size    = parseInt(window.getComputedStyle(orb).width);
            const newLeft = Math.random() * (window.innerWidth * 0.50 - size);
            const newTop  = Math.random() * (window.innerHeight - size);
            orb.style.left = newLeft + 'px';
            orb.style.top  = newTop  + 'px';

            setTimeout(function () {
                runOrbCycle(orb);
            }, 50 + Math.random() * 200); 
        };
    }
})();


/* ════════════════════════════════════════════════════════════════════
   CAPA 2 — LÓGICA DEL CARD DE LOGIN
   Estados: formulario → cargando → ok / error → formulario
════════════════════════════════════════════════════════════════════ */
(function () {

    const form              = document.getElementById('loginForm');
    const btn               = document.getElementById('loginBtn');
    const cardFormView      = document.getElementById('cardFormView');
    const cardFeedbackView  = document.getElementById('cardFeedbackView');
    const feedbackSpinner   = document.getElementById('feedbackSpinner');
    const feedbackOk        = document.getElementById('feedbackOk');
    const feedbackError     = document.getElementById('feedbackError');
    const feedbackLabel     = document.getElementById('feedbackLabel');
    const credentialsError  = document.getElementById('credentialsError');

    /* ── Helpers de estado ─────────────────────────────────────────── */

    function showForm(withError) {
        cardFeedbackView.classList.remove('visible');
        cardFormView.classList.remove('hidden');
        btn.disabled = false;

        if (withError) {
            credentialsError.classList.add('visible');
        }
    }

    function showLoading() {
        credentialsError.classList.remove('visible');
        cardFormView.classList.add('hidden');

        // Resetea los íconos
        feedbackSpinner.classList.remove('hide');
        feedbackOk.classList.remove('show');
        feedbackError.classList.remove('show');
        feedbackLabel.textContent = 'Verificando credenciales…';

        cardFeedbackView.classList.add('visible');
        btn.disabled = true;
    }

    function showSuccess() {
        feedbackSpinner.classList.add('hide');
        feedbackOk.classList.add('show');
        feedbackLabel.textContent = '¡Acceso concedido!';
        // La redirección la maneja el servidor — no hacemos nada más
    }

    function showErrorFeedback() {
        feedbackSpinner.classList.add('hide');
        feedbackError.classList.add('show');
        feedbackLabel.textContent = 'Credenciales incorrectas';

        // Después de 1.6 s vuelve al formulario con el banner de error
        setTimeout(function () {
            showForm(true);
        }, 1600);
    }

    /* ── Limpia el banner cuando el usuario escribe ────────────────── */
    ['nombre_usuario', 'contrasena'].forEach(function (id) {
        var input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function () {
                credentialsError.classList.remove('visible');
            });
        }
    });

    /* ── Submit con fetch (intercepta el POST para animar el card) ─── */
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) return;

        showLoading();

        var formData = new FormData(form);

        fetch(form.action, {
            method:      'POST',
            body:        formData,
            redirect:    'follow',
            credentials: 'same-origin'
        })
        .then(function (response) {
            var finalUrl = response.url;
            var isLogin  = finalUrl.includes('index') || finalUrl.includes('login');

            if (response.ok && !isLogin) {
                // Credenciales correctas — servidor redirigió al dashboard
                showSuccess();
                setTimeout(function () {
                    window.location.href = finalUrl;
                }, 1200);
            } else {
                // Servidor redirigió de vuelta al login → credenciales malas
                showErrorFeedback();
            }
        })
        .catch(function () {
            // Error de red
            feedbackSpinner.classList.add('hide');
            feedbackError.classList.add('show');
            feedbackLabel.textContent = 'Error de conexión. Intenta de nuevo.';
            setTimeout(function () {
                showForm(false);
            }, 2000);
        });

        // Seguridad: si fetch no resuelve en 15 s, vuelve al formulario
        setTimeout(function () {
            if (cardFeedbackView.classList.contains('visible')) {
                feedbackLabel.textContent = 'Tiempo de espera agotado.';
                setTimeout(function () { showForm(false); }, 1500);
            }
        }, 15000);
    });

    /* ── Detecta si PHP ya había seteado un error de sesión ─────────
       (por si el usuario llega al login con el error ya en sesión,
       por ejemplo tras un submit sin JS)                              */
    if (window.__loginError === true) {
        showForm(true);
    }

    /* ── Botón atrás (bfcache) ──────────────────────────────────────── */
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) showForm(false);
    });

})();