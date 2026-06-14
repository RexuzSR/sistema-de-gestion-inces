// Configuración global de categorías y reportes para el buscador
var categoriasReportes = [
    {
        id: 1, nombre: "Participantes",
        icono: `<i class="bi bi-people-fill"></i>`, color: "primary",
        bgColor: "rgba(13,110,253,0.9)", lightColor: "rgba(13,110,253,0.5)",
        darkColor: "rgba(0,80,200,0.95)", bodyBgColor: "rgba(13,110,253,0.06)",
        reportes: [
            { id: 1, nombre: "Total de Participantes", funcion: 'cargarTotalParticipantes', archivo: 'RE-Participante-Total.js' },
            { id: 2, nombre: "Participantes Aprobados", funcion: 'cargarParticipantesAprobados', archivo: 'RE-Participante-Aprobados.js' },
            { id: 9, nombre: "Participantes por Ámbito", funcion: 'cargarParticipantesPorAmbito', archivo: 'RE-Participante-Ambito.js' },
            { id: 12, nombre: "Trayectoria de Participantes", funcion: 'cargarTrayectoriaParticipantes', archivo: 'RE-Participante-Trayectoria.js' }
        ]
    },
    {
        id: 2, nombre: "Cursos",
        icono: `<i class="bi bi-book-half"></i>`, color: "success",
        bgColor: "rgba(25,135,84,0.9)", lightColor: "rgba(25,135,84,0.5)",
        darkColor: "rgba(15,100,60,0.95)", bodyBgColor: "rgba(25,135,84,0.06)",
        reportes: [
            { id: 3, nombre: "Total de Cursos", funcion: 'cargarTotalCursos', archivo: 'RE-Curso-Total.js' },
            { id: 4, nombre: "Cursos con más Participantes", funcion: 'cargarCursosMasParticipantes', archivo: 'RE-Curso-MasParticipantes.js' },
            { id: 5, nombre: "Curso más Requerido", funcion: 'cargarCursoMasRequerido', archivo: 'RE-Curso-MasRequerido.js' },
            { id: 13, nombre: "Trayectoria de Cursos", funcion: 'cargarTrayectoriaCursos', archivo: 'RE-Curso-Trayectoria.js' },
            { id: 14, nombre: "Cursos sin Docente", funcion: 'cargarCursosSinDocente', archivo: 'RE-Curso-SinDocente.js' }
        ]
    },
    {
        id: 3, nombre: "Docentes",
        icono: `<i class="bi bi-person-workspace"></i>`, color: "warning",
        bgColor: "rgba(255,193,7,0.9)", lightColor: "rgba(255,193,7,0.5)",
        darkColor: "rgba(200,150,0,0.95)", bodyBgColor: "rgba(255,193,7,0.07)",
        reportes: [
            { id: 10, nombre: "Total de Docentes",        funcion: 'cargarTotalDocentes',        archivo: 'RE-Docente-Total.js'        },
            { id: 6,  nombre: "Carga Horaria por Docente", funcion: 'cargarCargaHorariaDocentes', archivo: 'RE-Docente-CargaHoraria.js'  },
            { id: 15, nombre: "Historial de Docente",      funcion: 'cargarHistorialDocente',     archivo: 'RE-Docente-Historial.js'     }
        ]
    },
    {
        id: 4, nombre: "Metas",
        icono: `<i class="bi bi-bullseye"></i>`, color: "danger",
        bgColor: "rgba(220,53,69,0.9)", lightColor: "rgba(220,53,69,0.5)",
        darkColor: "rgba(180,35,50,0.95)", bodyBgColor: "rgba(220,53,69,0.06)",
        reportes: [
            { id: 11, nombre: "Metas por Ámbito",              funcion: 'cargarMetaPorAmbito',    archivo: 'RE-Meta-PorAmbito.js'  },
            { id: 16, nombre: "Histórico de Metas",            funcion: 'cargarHistoricoMetas',   archivo: 'RE-Meta-Historico.js'  },
            { id: 17, nombre: "Detalle de Participantes por Meta", funcion: 'cargarDetalleMeta',  archivo: 'RE-Meta-Detalle.js'    }
        ]
    }
];

// Asegurar que esté en el objeto window para el buscador
window.categoriasReportes = categoriasReportes;