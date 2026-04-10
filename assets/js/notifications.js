/* ============================================================
   notifications.js  —  INCES Sistema de Control (Notificaciones de Cursos)
   ============================================================ */

(function () {
  // Endpoint para obtener notificaciones de cursos (Búsqueda dinámica de ruta)
  function getEndpoint() {
      const paths = [
          '../../app/controlador/notification/api_notificaciones.php',
          '../app/controlador/notification/api_notificaciones.php',
          'app/controlador/notification/api_notificaciones.php'
      ];
      // Para propósitos de este entorno, usaremos la lógica de nivel relativa al documento actual
      return '../../app/controlador/notification/api_notificaciones.php'; 
  }
  const ENDPOINT = getEndpoint();

  // Almacenamiento local para ID de notificaciones "leídas"
  const STORAGE_KEY = 'inces_read_notifications';

  // Cache de notificaciones cargadas
  let cachedNotifications = [];

  // Utilidad para crear elementos DOM rápidos
  function el(tag, opts = {}) {
    const n = document.createElement(tag);
    if (opts.class) n.className = opts.class;
    if (opts.attrs) Object.entries(opts.attrs).forEach(([k, v]) => n.setAttribute(k, v));
    if (opts.html) n.innerHTML = opts.html;
    return n;
  }

  // Formateo relativo de tiempo (ej: "Hace 2 horas")
  function timeAgo(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + " años";
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + " meses";
    interval = seconds / 86400;
    if (interval >= 1) {
      const days = Math.floor(interval);
      return days === 1 ? "Ayer" : `Hace ${days} días`;
    }
    interval = seconds / 3600;
    if (interval >= 1) {
      const hours = Math.floor(interval);
      return `Hace ${hours} hora${hours > 1 ? 's' : ''}`;
    }
    interval = seconds / 60;
    if (interval >= 1) {
      const minutes = Math.floor(interval);
      return `Hace ${minutes} min`;
    }
    return "Hace un momento";
  }

  // Renderiza las notificaciones dentro de la lista del Header
  function renderItems(data) {
    const listEl = document.getElementById('notifList');
    const badgeBtn = document.querySelector('.notification-badge-modern');
    if (!listEl) return;

    listEl.innerHTML = '';

    // Obtener historial de lecturas
    let readNotifs = [];
    try {
      readNotifs = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    } catch (e) { }

    // Calcular unreadCount basado en TODAS las notificaciones cacheadas para el badge
    let totalUnreadCount = cachedNotifications.filter(n => !readNotifs.includes(n.notif_id)).length;

    if (!data || data.length === 0) {
      listEl.innerHTML = `
                <div style="padding: 20px; text-align: center; color: #888;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-bell-slash" viewBox="0 0 16 16" style="opacity: 0.5; margin-bottom: 10px;">
                        <path d="M5.164 14H15c-1.5-1-2-5.902-2-7 q0-.396-.06-.776l-9.776 9.776c.328.04.692.06 1.05.06m6.208-10.608A4.98 4.98 0 0 0 8 3c-2.3 0-4.14 1.76-4.992 4.08A28 28 0 0 0 3.013 12H1.5a.5.5 0 0 1 0-1h.638a27 27 0 0 1 .156-4.152A5.98 5.98 0 0 1 8 2c.983 0 1.9.23 2.709.638zM14 14.5a1.5 1.5 0 0 1-3 0h3zm-13.646.354l13-13 .708.708-13 13z"/>
                    </svg>
                    <div>No hay notificaciones con este filtro</div>
                </div>`;
      
      handleBadge(totalUnreadCount, badgeBtn);
      return;
    }

    data.forEach(t => {
      const isRead = readNotifs.includes(t.notif_id);

      const item = el('div', {
        class: `notification-item ${isRead ? '' : 'unread'}`,
        attrs: {
          'data-id': t.notif_id
        }
      });

      item.innerHTML = `
                <div style="display: flex; gap: 10px; align-items:flex-start;">
                    <div style="color: ${t.color}; margin-top: 2px;">
                        ${t.icono}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 13px; color: #333;">${t.titulo}</div>
                        <div style="font-size: 12px; color: #555; margin-top: 3px; line-height: 1.3;">${t.mensaje}</div>
                        <div style="font-size: 11px; color: #888; margin-top: 4px; font-weight: 500;">
                            ${timeAgo(t.fecha_evento)}
                        </div>
                    </div>
                </div>
            `;

      // Nuevo manejador de clic para persistencia
      item.addEventListener('click', function(e) {
          e.preventDefault();
          const id = t.notif_id;
          
          // Marcar como leído
          let currentRead = [];
          try { currentRead = JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; } catch (err) {}
          if (!currentRead.includes(id)) {
              currentRead.push(id);
              localStorage.setItem(STORAGE_KEY, JSON.stringify(currentRead));
          }
          
          // Navegar
          window.location.href = `cursos.php?buscar=${encodeURIComponent(t.id)}`;
      });

      listEl.appendChild(item);
    });

    handleBadge(totalUnreadCount, badgeBtn);
  }

  function handleBadge(unreadCount, badgeBtn) {
    if (badgeBtn) {
      if (unreadCount > 0) {
        badgeBtn.style.display = 'block';
        badgeBtn.style.animation = 'pulse 1.5s infinite';
      } else {
        badgeBtn.style.display = 'none';
        badgeBtn.style.animation = 'none';
      }
    }
  }

  async function fetchData() {
    try {
      const res = await fetch(ENDPOINT, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const json = await res.json();
      return json.success ? json.data : [];
    } catch (err) {
      console.error('Error cargando notificaciones de cursos', err);
      // En caso de error, aseguramos que el badge se oculte
      const badgeBtn = document.querySelector('.notification-badge-modern');
      handleBadge(0, badgeBtn);
      return [];
    }
  }

  // Exponer la función global para el botón "Marcar todas como leídas" del header.php
  window.markAllAsRead = function () {
    let readNotifs = [];
    try { readNotifs = JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; } catch (e) { }

    // Marcar TODOS los cacheados como leídos (los visibles y los que no)
    cachedNotifications.forEach(n => {
      if (n.notif_id && !readNotifs.includes(n.notif_id)) {
        readNotifs.push(n.notif_id);
      }
    });

    localStorage.setItem(STORAGE_KEY, JSON.stringify(readNotifs));

    // Actualizar UI inmediatamente
    const badgeBtn = document.querySelector('.notification-badge-modern');
    handleBadge(0, badgeBtn);
    
    // Refrescar la lista de items para quitar estilos de "unread"
    const unreadItems = document.querySelectorAll('.notification-item.unread');
    unreadItems.forEach(item => item.classList.remove('unread'));
  };

  async function loadAndRender() {
    const data = await fetchData();
    cachedNotifications = data;
    
    // Mantener filtro activo si existe
    const filterEl = document.getElementById('notif-filter');
    const type = filterEl ? filterEl.value : 'all';
    
    filterAndRender(type);
  }

  function filterAndRender(type) {
    if (type === 'all') {
      renderItems(cachedNotifications);
    } else {
      const filtered = cachedNotifications.filter(n => n.tipo === type);
      renderItems(filtered);
    }
  }

  // Listeners
  document.addEventListener('change', e => {
      if (e.target && e.target.id === 'notif-filter') {
          filterAndRender(e.target.value);
      }
  });

  // Iniciar
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAndRender);
  } else {
    loadAndRender();
  }

  // Refrescar data cada X minutos (3 minutos)
  setInterval(loadAndRender, 180000);

})();
