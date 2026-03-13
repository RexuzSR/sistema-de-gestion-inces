// assets/js/Curso/agregarCurso.js
$(document).ready(function () {
    // Manejar el envío del formulario de agregar curso
    $('#form_addCurso').on('submit', function (e) {
        e.preventDefault();

        if (validarFormularioAgregar()) {
            // Enviar datos via AJAX
            $.ajax({
                url: '../controlador/Curso/CargarCurso.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (data) {
                    // Log de depuración: mostrar contenido devuelto por el servidor (JSON ya parseado)
                    console.log('Server response (agregarCurso):', data);
                    if (data && data.success) {
                        toastr.success('Curso agregado correctamente');
                        // Guardar el ID del nuevo curso para destacarlo tras el reload
                        if (data.id_curso) {
                            sessionStorage.setItem('cursoRecienCreado', data.id_curso);
                        }
                        // Esperar a que el modal cierre completamente su animación y luego recargar
                        const modalEl = document.getElementById('modal-addCurso');
                        modalEl.addEventListener('hidden.bs.modal', function () {
                            location.reload();
                        }, { once: true });
                        $('#modal-addCurso').modal('hide');
                    } else {
                        toastr.error((data && data.message) || 'Error al agregar curso');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error (agregarCurso):', status, error, xhr.responseText);
                    // Intentar extraer mensaje del servidor si devuelve JSON
                    let serverMsg = 'Error de conexión con el servidor (' + xhr.status + ')';
                    try {
                        const resp = JSON.parse(xhr.responseText);
                        if (resp && resp.message) serverMsg = resp.message;
                    } catch (e) {
                        // No JSON: mantener el mensaje genérico
                    }
                    toastr.error(serverMsg);
                }
            });
        }
    });

    // Validación del formulario
    function validarFormularioAgregar() {
        let valid = true;

        // Limpiar errores anteriores
        $('.error-feedback').text('');

        // Validar campos requeridos
        const camposRequeridos = [
            'nombre_curso',
            'id_modalidad',
            'id_motor',
            'id_areaFormativa',
            'id_sectoreconomico',
            'id_subtipo',
            'id_ambito',
            'turno',
            'fecha_inicio',
            'fecha_fin',
            'max_participantes'
        ];

        camposRequeridos.forEach(function (campo) {
            const valor = $(`#${campo}`).val();
            if (!valor || valor.trim() === '') {
                $(`#error-${campo}`).text('Este campo es requerido');
                valid = false;
            }
        });

        // Validar fechas
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();

        if (fechaInicio && fechaFin) {
            if (new Date(fechaInicio) >= new Date(fechaFin)) {
                $('#error-fecha_fin').text('La fecha de fin debe ser posterior a la fecha de inicio');
                valid = false;
            }
        }

        // Validar mínimo de participantes (mínimo 12)
        const minPart = parseInt($('#min_participantes').val());
        if (isNaN(minPart) || minPart < 12) {
            $('#error-min_participantes').text('El mínimo de participantes debe ser al menos 12');
            valid = false;
        }

        return valid;
    }

    // ==========================================
    // VALIDACIONES EN TIEMPO REAL
    // ==========================================

    // Validación de nombre de curso y ámbito en tiempo real
    function validarNombreYAmbito() {
        const nombreInput = $('#nombre_curso');
        let nombre = nombreInput.val();
        const idAmbito = $('#id_ambito').val();
        const errorDiv = $('#error-nombre_curso');

        if (!nombre) {
            errorDiv.text('');
            return;
        }

        // Limpiar espacios al inicio y truncar a 70 chars
        if (nombre.startsWith(' ')) {
            nombreInput.val(nombre.trimStart());
            nombre = nombreInput.val();
        }
        if (nombre.length > 70) {
            nombreInput.val(nombre.substring(0, 70));
            nombre = nombreInput.val();
        }

        if (nombre.length > 0 && !/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
            errorDiv.text('Solo letras, números y espacios');
            nombreInput.data('valido', false);
            return;
        }

        // Check against the database
        $.ajax({
            url: '../controlador/Curso/verificarCurso.php',
            type: 'GET',
            data: {
                nombre_curso: nombre,
                validar_ambito: 1,
                id_ambito: idAmbito
            },
            dataType: 'json',
            success: function (response) {
                if (response.existe_nombre) {
                    if (!idAmbito) {
                        // Existe el nombre pero no han seleccionado ámbito aún
                        errorDiv.text('Ya hay una coincidencia con este nombre. Seleccione un Ámbito para verificar.');
                        nombreInput.data('valido', false);
                    } else if (response.existe_en_ambito) {
                        // Existe el nombre en el MISMO ámbito: el usuario quiere que NO LO DEJE avanzar
                        errorDiv.text('Ya existe un curso con este nombre en el ámbito seleccionado.');
                        nombreInput.data('valido', false);
                    } else {
                        // Existe el nombre en OTRO ámbito. Se permite.
                        errorDiv.text('');
                        nombreInput.data('valido', true);
                    }
                } else {
                    errorDiv.text('');
                    nombreInput.data('valido', true);
                }
            }
        });
    }

    $('#nombre_curso').on('input', function () {
        clearTimeout($(this).data('timeout'));
        $(this).data('timeout', setTimeout(validarNombreYAmbito, 500));
    });

    $('#id_ambito').on('change', function () {
        if ($('#nombre_curso').val().length > 0) {
            validarNombreYAmbito();
        }
    });

    const inputs = {
        fechaInicio: $('#fecha_inicio'),
        fechaLimite: $('#fecha_limite_inicio'),
        horasClase: $('#horas_por_clase'),
        errorFechaInicio: $('#error-fecha_inicio'),
        errorHorasClase: $('#error-horas_por_clase')
    };

    // Función para sumar días hábiles
    function sumarDiasHabiles(fecha, dias) {
        let copia = new Date(fecha);
        let contados = 0;

        while (contados < dias) {
            copia.setDate(copia.getDate() + 1);
            // 0 = Domingo, 6 = Sábado
            if (copia.getDay() !== 0 && copia.getDay() !== 6) {
                contados++;
            }
        }
        return copia;
    }

    // Función para verificar si es día hábil
    function esDiaHabil(fecha) {
        const dia = fecha.getDay();
        return dia !== 0 && dia !== 6;
    }

    // Función para contar días hábiles de diferencia desde hoy
    function diasHabilesDesdeHoy(fechaDestino) {
        let hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        let destino = new Date(fechaDestino);
        destino.setHours(0, 0, 0, 0); // Asegurar utc/local match
        // Ajuste por zona horaria simple: usar fecha string 'YYYY-MM-DD' + 'T00:00:00'
        // Mejor usamos la fecha input directa

        // Reiniciamos conteo
        let count = 0;
        let iter = new Date(hoy);

        // Si destino es anterior a hoy, retornamos negativo
        if (destino < hoy) return -1;

        while (iter < destino) {
            iter.setDate(iter.getDate() + 1);
            if (iter.getDay() !== 0 && iter.getDay() !== 6) {
                count++;
            }
        }
        return count;
    }

    // Evento: Al abrir el modal, establecer fecha mínima
    $('#modal-addCurso').on('show.bs.modal', function () {
        // Calcular fecha mínima: Hoy + 7 días hábiles
        const hoy = new Date();
        const minFecha = sumarDiasHabiles(hoy, 7);

        // Formatear YYYY-MM-DD
        const anio = minFecha.getFullYear();
        const mes = String(minFecha.getMonth() + 1).padStart(2, '0');
        const dia = String(minFecha.getDate()).padStart(2, '0');
        const minDateStr = `${anio}-${mes}-${dia}`;

        inputs.fechaInicio.attr('min', minDateStr);
        // Opcional: limpiar si tiene un valor antiguo menor
        if (inputs.fechaInicio.val() && inputs.fechaInicio.val() < minDateStr) {
            inputs.fechaInicio.val('');
            inputs.fechaLimite.val('');
        }
    });

    // Evento: Cambio en Fecha de Inicio
    inputs.fechaInicio.on('change', function () {
        const valor = $(this).val();
        if (!valor) return;

        // Ajustar a zona horaria local para evitar saltos de día
        const fechaIngresada = new Date(valor + 'T00:00:00');

        // 1. Validar que NO sea fin de semana
        if (!esDiaHabil(fechaIngresada)) {
            inputs.errorFechaInicio.text('La fecha de inicio no puede ser sábado ni domingo.');
            $(this).val('');
            inputs.fechaLimite.val('');
            // Limpiar fecha fin también
            $('#fecha_fin').val('');
            return;
        }

        // 2. Validar antelación mínima de 7 días hábiles
        const diasAntelacion = diasHabilesDesdeHoy(valor + 'T00:00:00');
        if (diasAntelacion < 7) {
            inputs.errorFechaInicio.text('La fecha debe tener al menos 7 días hábiles de antelación.');
            $(this).val('');
            inputs.fechaLimite.val('');
            $('#fecha_fin').val('');
            return;
        }

        // Si pasa validaciones, limpiar error
        inputs.errorFechaInicio.text('');

        // 3. Calcular Fecha Límite (+7 días hábiles)
        const fechaLimite = sumarDiasHabiles(fechaIngresada, 7);

        // Formatear a YYYY-MM-DD
        const anio = fechaLimite.getFullYear();
        const mes = String(fechaLimite.getMonth() + 1).padStart(2, '0');
        const dia = String(fechaLimite.getDate()).padStart(2, '0');

        inputs.fechaLimite.val(`${anio}-${mes}-${dia}`);

        // 4. Calcular Fecha Fin si otros datos están listos
        calcularFechaFin();
    });

    // min_participantes es siempre 12 (readonly), no necesita validación en tiempo real

    // Evento: Validación de Horas por Clase
    inputs.horasClase.on('change', function () {
        const valor = $(this).val();

        if (valor === "") {
            inputs.errorHorasClase.text('Debe seleccionar una opción válida.');
            $('#fecha_fin').val(''); // Limpiar si hay error
            return;
        }

        if (valor !== "4" && valor !== "5") {
            inputs.errorHorasClase.text('Solo se permiten bloques de 4 o 5 horas.');
            $('#fecha_fin').val(''); // Limpiar si hay error
        } else {
            inputs.errorHorasClase.text('');
            calcularFechaFin(); // Recalcular si es válido
        }
    });

    // Evento: Validación de Horas Totales
    $('#horas_totales').on('change input', function () {
        const valor = parseInt($(this).val());
        const errorDiv = $('#error-horas_totales');

        if (isNaN(valor)) {
            errorDiv.text('Ingrese un valor numérico.');
            return;
        }

        if (valor > 1200) {
            errorDiv.text('El curso no puede durar más de 1200 horas.');
            $('#fecha_fin').val(''); // Limpiar si hay error
            return;
        }

        if (valor < 40) {
            errorDiv.text('El curso debe durar mínimo 40 horas.');
            $('#fecha_fin').val(''); // Limpiar si hay error
        } else {
            errorDiv.text('');
            calcularFechaFin(); // Recalcular si es válido
        }
    });

    // Nueva función para calcular Fecha Fin
    function calcularFechaFin() {
        const fInicio = inputs.fechaInicio.val();
        const hTotales = parseInt($('#horas_totales').val());
        const hClase = parseInt(inputs.horasClase.val());

        // Validar que tengamos todos los datos necesarios y validos
        if (!fInicio || !hTotales || !hClase || hTotales < 40 || (hClase !== 4 && hClase !== 5)) {
            return;
        }

        // Calcular número de clases (días) necesarios
        // Math.ceil redondea hacia arriba (ej: 42 / 4 = 10.5 -> 11 días)
        const diasClase = Math.ceil(hTotales / hClase);

        // Calcular fecha fin sumando días hábiles
        // Restamos 1 porque la fecha de inicio cuenta como el primer día de clase
        const fechaFin = sumarDiasHabiles(new Date(fInicio + 'T00:00:00'), diasClase - 1);

        // Formatear AAAA-MM-DD
        const anio = fechaFin.getFullYear();
        const mes = String(fechaFin.getMonth() + 1).padStart(2, '0');
        const dia = String(fechaFin.getDate()).padStart(2, '0');

        $('#fecha_fin').val(`${anio}-${mes}-${dia}`);
        $('#error-fecha_fin').text(''); // Limpiar error si existía
    }

    // Prevenir ingreso de otros caracteres en horas
    inputs.horasClase.on('keypress', function (e) {
        // Permitir solo 4 o 5 como primer caracter si está vacío? 
        // Mejor dejar que escriban y validar en input/change para mejor UX
    });

    // Validación del formulario de envío
    $('#form-crear-curso').on('submit', function (e) {
        const horasClase = $('#horas_por_clase').val();
        const errorHorasClase = $('#error-horas_por_clase');

        if (!horasClase) {
            e.preventDefault(); // Evitar el envío del formulario
            errorHorasClase.text('Este campo es requerido.');
        } else {
            errorHorasClase.text(''); // Limpiar el mensaje de error si es válido
        }
    });
});