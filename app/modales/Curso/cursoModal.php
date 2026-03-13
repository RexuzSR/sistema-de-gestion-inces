<!-- Modal Curso -->
<div class="modal fade" id="cursoModal" tabindex="-1" aria-labelledby="cursoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0">
      <!-- Encabezado con gradiente azul -->
      <div class="modal-header border-0 py-4 px-4" style="background: linear-gradient(135deg, #0061ff 0%, #00c8ff 100%);">
        <div class="d-flex align-items-center">
          <div class="rounded-circle bg-white bg-opacity-20 p-3 me-3">
            <i class="fas fa-graduation-cap text-white fs-4"></i>
          </div>
          <div>
            <h5 class="modal-title text-white fw-light mb-1">Gestión de Curso</h5>
            <span id="modalCursoNombre" class="text-white fw-bold fs-4 d-block"></span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <!-- Cuerpo -->
      <div class="modal-body p-0">
        
        <!-- Alerta mejorada -->
        <div id="cursoFinalizadoAlert" class="d-none mx-4 mt-4"></div>

        <!-- Lista de participantes -->
        <div class="participantes-container p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <h6 class="mb-1 fw-semibold" style="color: #1e293b;">
                <i class="fas fa-users me-2" style="color: #0061ff;"></i>Lista de Estudiantes
              </h6>
              <p class="text-muted small mb-0">Gestiona los estudiantes inscritos en el curso</p>
            </div>
            <span class="badge px-3 py-2 rounded-pill" style="background: linear-gradient(135deg, #0061ff 0%, #00c8ff 100%); color: white;" id="contadorEstudiantes">
              <i class="fas fa-user-graduate me-1"></i>0 estudiantes
            </span>
          </div>
          
          <!-- Lista con scroll -->
          <div class="list-group" id="listaParticipantes" style="max-height: 400px; overflow-y: auto; border-radius: 12px;">
            <!-- Los items se generarán dinámicamente -->
          </div>
          
          <!-- Mensaje cuando no hay estudiantes -->
          <div id="noEstudiantesMessage" class="text-center py-5 d-none">
            <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
              <i class="fas fa-user-slash fs-1" style="color: #94a3b8;"></i>
            </div>
            <p class="text-muted mb-0">No hay estudiantes inscritos en este curso</p>
            <small class="text-muted">Los estudiantes aparecerán aquí cuando se inscriban</small>
          </div>
        </div>
      </div>

      <!-- Pie del modal con botones de colores sólidos -->
      <div class="modal-footer border-0 px-4 py-3" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <div class="d-flex gap-2">
          <!-- Botón Finalizar Rojo Sólido -->
          <button type="button" class="btn btn-solid btn-solid-danger px-4" id="btnFinalizar">
            <i class="fas fa-stop-circle me-2"></i>Finalizar Curso
          </button>
          
          <!-- Botón Reiniciar Azul Sólido -->
          <button type="button" class="btn btn-solid btn-solid-primary px-4" id="btnReiniciarCurso" disabled>
            <i class="fas fa-redo-alt me-2"></i>Reiniciar Curso
          </button>
        </div>
        <div class="d-flex gap-2">
          <!-- Botón Guardar Verde Sólido -->
          <button type="button" class="btn btn-solid btn-solid-success px-4" id="btnGuardar">
            <i class="fas fa-save me-2"></i>Guardar Cambios
          </button>
          
          <!-- Botón Cerrar Gris Sólido -->
          <button type="button" class="btn btn-solid btn-solid-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Estilos base */
  .bg-opacity-20 {
    --bs-bg-opacity: 0.2;
    background-color: rgba(255, 255, 255, var(--bs-bg-opacity)) !important;
  }
  
  .modal-content {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 97, 255, 0.2);
  }
  
  /* Estilos para los items de participantes */
  .participante-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    background: white;
    margin-bottom: 2px;
    border-radius: 0 !important;
  }
  
  .participante-item:hover {
    background: #f8fafc;
    border-left-color: #0061ff;
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }
  
  .participante-avatar {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0061ff 0%, #00c8ff 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    box-shadow: 0 2px 4px rgba(0, 97, 255, 0.2);
  }
  
  .participante-info h6 {
    color: #1e293b;
    margin-bottom: 0.25rem;
    font-weight: 600;
  }
  
  .participante-info small {
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .estado-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .estado-activo {
    background: #e8f0fe;
    color: #0061ff;
    border: 1px solid #0061ff20;
  }
  
  .estado-finalizado {
    background: #fee8e8;
    color: #dc2626;
    border: 1px solid #dc262620;
  }
  
  /* Estilos de botones sólidos */
  .btn-solid {
    position: relative;
    border: none;
    color: white;
    font-weight: 600;
    letter-spacing: 0.5px;
    border-radius: 10px;
    transition: all 0.2s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .btn-solid:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
  }
  
  .btn-solid:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .btn-solid:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }
  
  /* Botón Sólido Primario (Azul) */
  .btn-solid-primary {
    background: #0061ff;
  }
  
  .btn-solid-primary:hover {
    background: #0052d6;
  }
  
  .btn-solid-primary:active {
    background: #0043ad;
  }
  
  /* Botón Sólido Danger (Rojo) */
  .btn-solid-danger {
    background: #dc2626;
  }
  
  .btn-solid-danger:hover {
    background: #b91c1c;
  }
  
  .btn-solid-danger:active {
    background: #991b1b;
  }
  
  /* Botón Sólido Success (Verde) */
  .btn-solid-success {
    background: #16a34a;
  }
  
  .btn-solid-success:hover {
    background: #15803d;
  }
  
  .btn-solid-success:active {
    background: #166534;
  }
  
  /* Botón Sólido Secondary (Gris) */
  .btn-solid-secondary {
    background: #64748b;
  }
  
  .btn-solid-secondary:hover {
    background: #475569;
  }
  
  .btn-solid-secondary:active {
    background: #334155;
  }
  
  /* Animaciones */
  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .participante-item {
    animation: slideIn 0.3s ease forwards;
  }
  
  /* Scroll personalizado azul */
  #listaParticipantes::-webkit-scrollbar {
    width: 6px;
  }
  
  #listaParticipantes::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
  }
  
  #listaParticipantes::-webkit-scrollbar-thumb {
    background: #0061ff;
    border-radius: 10px;
  }
  
  #listaParticipantes::-webkit-scrollbar-thumb:hover {
    background: #0052d6;
  }
  
  /* Efecto sutil en el badge */
  .badge {
    position: relative;
    box-shadow: 0 2px 4px rgba(0, 97, 255, 0.2);
  }
  
  /* Animación de entrada del modal */
  .modal.show .modal-content {
    animation: modalPop 0.3s ease-out;
  }
  
  @keyframes modalPop {
    0% {
      transform: scale(0.95);
      opacity: 0;
    }
    100% {
      transform: scale(1);
      opacity: 1;
    }
  }
  
  /* Estilos para los bordes de la lista */
  .list-group-item:first-child {
    border-top-left-radius: 12px !important;
    border-top-right-radius: 12px !important;
  }
  
  .list-group-item:last-child {
    border-bottom-left-radius: 12px !important;
    border-bottom-right-radius: 12px !important;
    border-bottom: none !important;
  }
</style>

<script>
  // Función para renderizar participantes
  function renderizarParticipantes(participantes) {
    const lista = document.getElementById('listaParticipantes');
    const contador = document.getElementById('contadorEstudiantes');
    const noEstudiantes = document.getElementById('noEstudiantesMessage');
    
    if (!participantes || participantes.length === 0) {
      lista.innerHTML = '';
      noEstudiantes.classList.remove('d-none');
      contador.innerHTML = '<i class="fas fa-user-graduate me-1"></i>0 estudiantes';
      return;
    }
    
    noEstudiantes.classList.add('d-none');
    contador.innerHTML = `<i class="fas fa-user-graduate me-1"></i>${participantes.length} ${participantes.length === 1 ? 'estudiante' : 'estudiantes'}`;
    
    lista.innerHTML = participantes.map((p, index) => `
      <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-0 border-bottom participante-item" style="animation-delay: ${index * 0.05}s">
        <div class="d-flex align-items-center gap-3">
          <div class="participante-avatar">
            ${p.nombre.charAt(0).toUpperCase()}
          </div>
          <div class="participante-info">
            <h6 class="mb-1">${p.nombre}</h6>
            <small>
              <i class="fas fa-envelope"></i> ${p.email}
              <i class="fas fa-circle ms-2" style="font-size: 0.3rem; color: #cbd5e0;"></i>
              <span class="${p.estado === 'activo' ? 'text-primary' : 'text-danger'}">
                <i class="fas fa-${p.estado === 'activo' ? 'check-circle' : 'times-circle'}"></i>
                ${p.estado === 'activo' ? 'Activo' : 'Finalizado'}
              </span>
            </small>
          </div>
        </div>
        <span class="estado-badge ${p.estado === 'activo' ? 'estado-activo' : 'estado-finalizado'}">
          ${p.estado === 'activo' ? 'En curso' : 'Completado'}
        </span>
      </div>
    `).join('');
  }

  // Ejemplo de uso
  const participantesEjemplo = [
    { nombre: 'Ana García', email: 'ana.garcia@email.com', estado: 'activo' },
    { nombre: 'Carlos López', email: 'carlos.lopez@email.com', estado: 'activo' },
    { nombre: 'María Rodríguez', email: 'maria.rodriguez@email.com', estado: 'finalizado' },
    { nombre: 'Juan Pérez', email: 'juan.perez@email.com', estado: 'activo' },
    { nombre: 'Laura Martínez', email: 'laura.martinez@email.com', estado: 'activo' },
  ];

  // Renderizar al cargar
  document.addEventListener('DOMContentLoaded', () => {
    renderizarParticipantes(participantesEjemplo);
    
    // Efecto de entrada al abrir el modal
    const modal = document.getElementById('cursoModal');
    modal.addEventListener('shown.bs.modal', function () {
      this.querySelector('.modal-content').style.animation = 'none';
      this.querySelector('.modal-content').offsetHeight;
      this.querySelector('.modal-content').style.animation = 'modalPop 0.3s ease-out';
    });
  });
</script>