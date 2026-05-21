// Gráfico de barras para "Estudiantes Por Estatus" (cambiado de pie a bar)
document.addEventListener("DOMContentLoaded", function () {
    var ctx = document.getElementById("myChart");

    // Si no existe el canvas, no intentamos crear el gráfico
    if (!ctx) return;

    // Verificar que existan las variables globales necesarias
    if (typeof labels === 'undefined' || typeof data === 'undefined') {
        console.warn('labels o data no están definidos. Se omite myChart.');
        return;
    }

    // Paleta de colores mejorada (azules y verdes para coherencia)
    var backgroundColors = [
        'rgba(54, 162, 235, 0.8)',  // Azul
        'rgba(75, 192, 192, 0.8)',  // Verde
        'rgba(255, 205, 86, 0.8)',  // Amarillo
        'rgba(255, 99, 132, 0.8)',  // Rojo
        'rgba(153, 102, 255, 0.8)', // Púrpura
        // Agrega más si necesitas para más labels
    ];

    // Si hay más labels que colores, genera colores aleatorios
    while (backgroundColors.length < labels.length) {
        var randomColor = 'rgba(' + Math.floor(Math.random() * 255) + ',' + Math.floor(Math.random() * 255) + ',' + Math.floor(Math.random() * 255) + ',0.8)';
        backgroundColors.push(randomColor);
    }

    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Participantes',
                data: data,
                backgroundColor: function (context) {
                    const chart = context.chart;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return null;
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.7)');
                    gradient.addColorStop(1, 'rgba(54, 162, 235, 1)');
                    return gradient;
                },
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            return context.parsed.y + ' Participantes';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'N* Participantes',
                        color: '#64748b',
                        font: { size: 12, weight: '600' }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                    ticks: { stepSize: 1, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            },
            animation: { duration: 1000, easing: 'easeInOutQuad' }
        }
    });
});

// Gráfico de barras horizontal para "Estudiantes por Género en Formación" (cambiado de doughnut a horizontal bar)
document.addEventListener("DOMContentLoaded", function () {
    var chart = document.getElementById("myBarChart");
    if (!chart) return;

    if (typeof generoData === 'undefined' || generoData === null ||
        typeof generoData.masculino === 'undefined' || typeof generoData.femenino === 'undefined') {
        console.warn('generoData no está definido correctamente. Se omite myBarChart.');
        return;
    }

    var myBarChart = new Chart(chart, {
        type: 'bar',
        data: {
            labels: ["Masculino", "Femenino"],
            datasets: [{
                label: 'Participantes',
                data: [generoData.masculino, generoData.femenino],
                backgroundColor: function (context) {
                    const chart = context.chart;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return null;
                    const gM = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
                    gM.addColorStop(0, 'rgba(54, 162, 235, 0.7)');
                    gM.addColorStop(1, 'rgba(54, 162, 235, 1)');
                    const gF = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
                    gF.addColorStop(0, 'rgba(255, 99, 132, 0.7)');
                    gF.addColorStop(1, 'rgba(255, 99, 132, 1)');
                    return context.dataIndex === 0 ? gM : gF;
                },
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            return context.parsed.x + ' Participantes';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'N* Participantes',
                        color: '#64748b',
                        font: { size: 12, weight: '600' }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                    ticks: { stepSize: 1, color: '#94a3b8' }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { weight: '600' } }
                }
            },
            animation: { duration: 1000, easing: 'easeInOutQuad' }
        }
    });
});

// Lógica para ocultar elementos basados en el rol del usuario
try {
    if (typeof userRole !== 'undefined' && userRole === "Apoyo") {
        var usuarioItem = document.getElementById('sidebar-usuario');
        if (usuarioItem) {
            usuarioItem.remove();
        }
    }
} catch (e) {
    console.warn('userRole no está definido. Se omite control de visibilidad.');
}


//grafica en diagonal
document.addEventListener("DOMContentLoaded", function () {

    var canvas = document.getElementById("statsChart");
    if (!canvas) return;
    var ctx = canvas.getContext('2d');

    // Inicializa el gráfico vacío; se llenará con datos del backend
    var statsChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: [],
            datasets: [{
                label: "Cursos por ámbito",
                data: [],
                borderWidth: 3,
                borderColor: "rgba(54, 162, 235, 1)",
                backgroundColor: function (context) {
                    const chart = context.chart;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return null;
                    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    gradient.addColorStop(0, 'rgba(54, 162, 235, 0.3)');
                    gradient.addColorStop(1, 'rgba(54, 162, 235, 0)');
                    return gradient;
                },
                tension: 0.4,
                fill: true,
                pointBackgroundColor: "#fff",
                pointBorderColor: "rgba(54, 162, 235, 1)",
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: { color: "#64748b", font: { weight: '600' } }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: "#94a3b8" }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'N* Cursos',
                        color: '#64748b',
                        font: { size: 12, weight: '600' }
                    },
                    grid: { color: "rgba(0, 0, 0, 0.05)", drawBorder: false },
                    ticks: {
                        color: "#94a3b8",
                        callback: function (value) { return Number.isInteger(value) ? value : null; },
                        stepSize: 1
                    }
                }
            }
        }
    });

    function cargar(periodo) {
        var url = '../../app/controlador/obtenerAmbitos.php' + (periodo ? ('?periodo=' + encodeURIComponent(periodo)) : '');
        fetch(url, { cache: 'no-store' })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (!json || json.ok !== true) throw new Error(json && json.error ? json.error : 'Respuesta inválida');

                var labels = json.labels || [];
                var data = json.data || [];

                statsChart.data.labels = labels;
                statsChart.data.datasets[0].data = data;
                statsChart.data.datasets[0].label = periodo ? ('Cursos por ámbito (' + periodo + ')') : 'Cursos por ámbito';

                // Ajuste dinámico del rango Y a múltiplos enteros razonables
                var max = Math.max.apply(null, data.concat([0]));
                var suggestedMax = max <= 5 ? 5 : (max <= 10 ? 10 : Math.ceil(max / 5) * 5);
                if (statsChart.options && statsChart.options.scales && statsChart.options.scales.y) {
                    statsChart.options.scales.y.suggestedMax = suggestedMax;
                }

                statsChart.update();
            })
            .catch(function (err) {
                console.error('Error cargando ámbitos:', err);
                statsChart.data.labels = [];
                statsChart.data.datasets[0].data = [];
                statsChart.update();
            });
    }

    // Cargar por defecto el periodo mensual (puedes cambiarlo a 'semanal' si lo prefieres)
    cargar('semanal');

    // Manejo de botones del filtro (incluyendo el nuevo anual)
    var botones = document.querySelectorAll('.filter');
    if (botones && botones.length) {
        botones.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var actual = document.querySelector('.filter.active');
                if (actual) actual.classList.remove('active');
                btn.classList.add('active');
                var periodo = btn.dataset.filter; // esperado: 'semanal' | 'mensual' | 'trimestral' | 'anual'
                cargar(periodo);
            });
        });
    }

});