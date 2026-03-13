// ========== REPORTES DE METAS ==========

function cargarGenerarConstancias() {
    const contentDiv = document.getElementById('reportContent');
    const categoria = categoriasReportes.find(c => c.id === currentCategoryId);
    
    contentDiv.innerHTML = `
        <div class="card shadow-lg border-0">
            <div class="card-header bg-${categoria.color} text-white">
                <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Generar Constancias</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Generar Constancias de Participación</h5>
                                <form id="formGenerarConstancias">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="selectCursoConstancia" class="form-label">Seleccionar Curso:</label>
                                            <select id="selectCursoConstancia" class="form-select" required>
                                                <option value="">Seleccione un curso</option>
                                                <option value="1">Programación en Python</option>
                                                <option value="2">Diseño UX/UI</option>
                                                <option value="3">Machine Learning</option>
                                                <option value="4">Desarrollo Web Full Stack</option>
                                                <option value="5">Base de Datos Avanzada</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="selectTipoConstancia" class="form-label">Tipo de Constancia:</label>
                                            <select id="selectTipoConstancia" class="form-select" required>
                                                <option value="participacion">Participación</option>
                                                <option value="aprobacion">Aprobación</option>
                                                <option value="asistencia">Asistencia</option>
                                                <option value="excelencia">Excelencia Académica</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="fechaInicio" class="form-label">Fecha de Inicio del Curso:</label>
                                            <input type="date" id="fechaInicio" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="fechaFin" class="form-label">Fecha de Fin del Curso:</label>
                                            <input type="date" id="fechaFin" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="selectParticipantes" class="form-label">Seleccionar Participantes:</label>
                                        <select id="selectParticipantes" class="form-select" multiple size="5">
                                            <option value="todos" selected>Todos los participantes</option>
                                            <option value="aprobados">Solo aprobados</option>
                                            <option value="asistencia">Con asistencia completa</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="incluirFirma" checked>
                                            <label class="form-check-label" for="incluirFirma">
                                                Incluir firma digital del docente
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="incluirLogo" checked>
                                            <label class="form-check-label" for="incluirLogo">
                                                Incluir logo de la institución
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="generarIndividual">
                                            <label class="form-check-label" for="generarIndividual">
                                                Generar constancias individuales (una por participante)
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-${categoria.color} btn-lg">
                                            <i class="bi bi-file-earmark-pdf me-2"></i>Generar Constancias
                                        </button>
                                        <button type="button" class="btn btn-outline-${categoria.color}" onclick="previsualizarConstancia()">
                                            <i class="bi bi-eye me-2"></i>Previsualizar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Información de Constancias</h5>
                                <div class="mt-3">
                                    <p><strong>Constancias Generadas (Este Mes):</strong> 45</p>
                                    <p><strong>Constancias Pendientes:</strong> 12</p>
                                    <p><strong>Última Generación:</strong> 15/Nov/2024</p>
                                </div>
                                
                                <hr>
                                
                                <h6>Plantillas Disponibles:</h6>
                                <div class="list-group mt-2">
                                    <button class="list-group-item list-group-item-action" onclick="seleccionarPlantilla('estandar')">
                                        <i class="bi bi-file-text text-primary me-2"></i>Plantilla Estándar
                                    </button>
                                    <button class="list-group-item list-group-item-action" onclick="seleccionarPlantilla('premium')">
                                        <i class="bi bi-star-fill text-warning me-2"></i>Plantilla Premium
                                    </button>
                                    <button class="list-group-item list-group-item-action" onclick="seleccionarPlantilla('minimalista')">
                                        <i class="bi bi-file-earmark text-success me-2"></i>Plantilla Minimalista
                                    </button>
                                </div>
                                
                                <hr>
                                
                                <div class="mt-3">
                                    <h6>Acciones Rápidas:</h6>
                                    <div class="d-grid gap-2 mt-2">
                                        <button class="btn btn-outline-success btn-sm" onclick="generarConstanciasMasivas()">
                                            <i class="bi bi-lightning me-1"></i>Generación Masiva
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="verHistorialConstancias()">
                                            <i class="bi bi-clock-history me-1"></i>Ver Historial
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Historial Reciente de Constancias</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Curso</th>
                                                <th>Tipo</th>
                                                <th>Cantidad</th>
                                                <th>Generado Por</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>15/11/2024</td>
                                                <td>Programación en Python</td>
                                                <td>Aprobación</td>
                                                <td>25</td>
                                                <td>Admin Sistema</td>
                                                <td><span class="badge bg-success">Completado</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Descargar
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>10/11/2024</td>
                                                <td>Diseño UX/UI</td>
                                                <td>Participación</td>
                                                <td>18</td>
                                                <td>Docente María</td>
                                                <td><span class="badge bg-success">Completado</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Descargar
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>05/11/2024</td>
                                                <td>Machine Learning</td>
                                                <td>Excelencia</td>
                                                <td>5</td>
                                                <td>Admin Sistema</td>
                                                <td><span class="badge bg-warning">Pendiente</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="bi bi-clock"></i> En proceso
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Event listener para el formulario
    document.getElementById('formGenerarConstancias')?.addEventListener('submit', function(e) {
        e.preventDefault();
        generarConstancias();
    });
}

// Funciones auxiliares para el reporte de constancias
function seleccionarPlantilla(tipo) {
    alert(`Plantilla ${tipo} seleccionada.`);
}

function previsualizarConstancia() {
    alert('Previsualización de constancia generada. Esta función mostraría una vista previa del PDF.');
}

function generarConstanciasMasivas() {
    alert('Función para generar constancias masivas de todos los cursos aprobados.');
}

function verHistorialConstancias() {
    alert('Función para ver el historial completo de constancias generadas.');
}

function generarConstancias() {
    const curso = document.getElementById('selectCursoConstancia').value;
    const tipo = document.getElementById('selectTipoConstancia').value;
    const participantes = document.getElementById('selectParticipantes').value;
    
    // Mostrar mensaje de éxito
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
    alertDiv.innerHTML = `
        <i class="bi bi-check-circle me-2"></i>
        <strong>¡Constancias generadas exitosamente!</strong>
        Se han generado 25 constancias para el curso seleccionado.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.getElementById('formGenerarConstancias').appendChild(alertDiv);
    
    // Simular descarga
    setTimeout(() => {
        alert('Las constancias se están descargando. Revisa tu carpeta de descargas.');
    }, 1000);
}