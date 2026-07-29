$(document).ready(function() {
    // Manejar el envío del formulario de agregar docente
    $('#form-addDocente').on('submit', function(e) {
        e.preventDefault();
        
        console.log("Formulario de agregar docente enviado");
        
        // Validar campos requeridos
        const camposRequeridos = [
            '#primer_nombre',
            '#primer_apellido', 
            '#cedula',
            '#correo',
            '#fechaNacimiento',
            '#genero',
            '#tipo_docente'
        ];
        
        let camposVacios = [];
        camposRequeridos.forEach(campo => {
            if (!$(campo).val().trim()) {
                const label = $(campo).closest('.mb-3').find('label').text().replace(' *', '');
                camposVacios.push(label);
            }
        });
        
        if (camposVacios.length > 0) {
            toastr.warning('Complete los campos requeridos: ' + camposVacios.join(', '));
            return;
        }
        
        // Validar cédula (solo números)
        const cedula = $('#cedula').val();
        if (!/^\d+$/.test(cedula)) {
            toastr.warning('La cédula debe contener solo números');
            $('#cedula').focus();
            return;
        }
        
        // Validar correo
        const correo = $('#correo').val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) {
            toastr.warning('Por favor ingrese un correo válido');
            $('#correo').focus();
            return;
        }
        
        // Mostrar carga
        const btnSubmit = $('#btn-addDocente-submit');
        const originalText = btnSubmit.html();
        btnSubmit.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Guardando...
        `);
        
        // Recoger datos del formulario
        const formData = {
            primer_nombre: $('#primer_nombre').val().trim(),
            segundo_nombre: $('#segundo_nombre').val().trim() || null,
            primer_apellido: $('#primer_apellido').val().trim(),
            segundo_apellido: $('#segundo_apellido').val().trim() || null,
            cedula: cedula,
            telefono: $('#telefono').val().trim() || '',
            correo: correo,
            fechaNacimiento: $('#fechaNacimiento').val(),
            genero: $('#genero').val(),
            tipo_docente: $('#tipo_docente').val()
        };
        
        console.log("Datos a enviar:", formData);
        
        // Enviar datos via AJAX
        $.ajax({
            url: '../../app/controlador/Docente/cargardocente.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log("Respuesta del servidor:", response);
                
                if (response.success) {
                    // Mostrar mensaje de éxito
                    toastr.success(response.message);
                    
                    // Cerrar el modal
                    $('#modal-addDocente').modal('hide');
                    
                    // Recargar la tabla de docentes
                    if (typeof aplicarFiltros === 'function') {
                        setTimeout(() => {
                            aplicarFiltros();
                        }, 500);
                    }
                    
                    // Limpiar el formulario
                    $('#form-addDocente')[0].reset();
                    $('#edad').val(''); // Limpiar edad calculada
                } else {
                    toastr.error(response.message || 'Error al registrar el docente');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX:", error);
                console.error("Respuesta del servidor:", xhr.responseText);
                
                let errorMessage = 'Error de conexión con el servidor';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse && errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // Si no es JSON, usar el texto plano
                    if (xhr.responseText) {
                        errorMessage = xhr.responseText.substring(0, 100);
                    }
                }
                
                toastr.error(errorMessage);
            },
            complete: function() {
                // Restaurar botón
                btnSubmit.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Validar que la cédula solo tenga números y no empiece con 0
    $('#cedula').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '');
    });
    
    // Calcular edad automáticamente cuando cambia la fecha de nacimiento
    $('#fechaNacimiento').on('change', function() {
        if (this.value) {
            const birthDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            // Validar que la edad sea razonable
            if (age >= 18 && age <= 100) {
                $('#edad').val(age);
            } else {
                toastr.warning('La fecha de nacimiento debe corresponder a una edad entre 18 y 100 años');
                $('#fechaNacimiento').val('');
                $('#edad').val('');
            }
        }
    });
    
    // Cuando se cierra el modal, limpiar el formulario
    $('#modal-addDocente').on('hidden.bs.modal', function() {
        $('#form-addDocente')[0].reset();
        $('#edad').val('');
    });
    
    // Validar formato de teléfono
    $('#telefono').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});