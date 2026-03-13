(function (window) {
    const estados = {
        1: 'En espera',
        2: 'Diferido',
        3: 'En proceso',
        4: 'En pausa',
        5: 'Culminado',
        6: 'Inactivo'
    };

    function getTextoEstado(idOrName) {
        // Si es número o string numérico, devolver por id
        if (typeof idOrName === 'number' || /^\d+$/.test(String(idOrName))) {
            return estados[Number(idOrName)] || 'Desconocido';
        }

        // Si ya es texto, normalizar y devolver si coincide con alguno
        if (typeof idOrName === 'string') {
            const trimmed = idOrName.trim();
            // Buscar coincidencia exacta entre valores
            for (const k in estados) {
                if (estados[k] === trimmed) return trimmed;
            }
            // Aceptar también 'Aprobado' como valor conocido (mantener compatibilidad)
            if (trimmed === 'Aprobado') return 'Aprobado';
        }

        return 'Desconocido';
    }

    function getBadgeClass(texto) {
        switch (texto) {
            case 'En espera': return 'bg-secondary';
            case 'En proceso': return 'bg-success';
            case 'Culminado': return 'bg-danger';
            case 'Diferido': return 'bg-warning';
            case 'En pausa': return 'bg-info';
            case 'Inactivo': return 'bg-dark';
            case 'Aprobado': return 'bg-primary';
            default: return 'bg-dark';
        }
    }

    function getInfo(idOrName) {
        const texto = getTextoEstado(idOrName);
        return {
            texto,
            badgeClass: getBadgeClass(texto),
            // Mostrar barra/progreso solo para estados temporales
            mostrarBarraProgreso: ['En espera', 'En proceso', 'Culminado'].includes(texto)
        };
    }

    function renderBadge(idOrName) {
        const info = getInfo(idOrName);
        return `<span class="badge ${info.badgeClass}">${info.texto}</span>`;
    }

    window.estadoCurso = {
        getTextoEstado,
        getInfo,
        renderBadge
    };

})(window);
