const nombre = document.getElementById('nombre');
const edad = document.getElementById('edad');
const dni = document.getElementById('dni');
const correoElectronico = document.getElementById('correo_electronico');
const contrasena = document.getElementById('contrasena');
const confirmarContrasena = document.getElementById('confirmarContrasena');
const button = document.getElementById('button');
const registerForm = document.getElementById('registerForm');
const mensajeFlotante = document.getElementById('mensaje-flotante');
const textoMensaje = document.getElementById('texto-mensaje');
const botonAceptar = document.getElementById('boton-aceptar');

button.addEventListener('click', (e) => {
    e.preventDefault();
    let warnings = "";
    let register = false;

    /* Expresiones regulares */
    let validarNombre = /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]{3,}$/;
    let validarDNI = /^\d{8,20}$/;  // Adaptado para aceptar entre 8 y 20 dígitos
    let validarEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

    if(!validarNombre.test(nombre.value)){
        warnings += `El nombre ingresado no es correcto <br>`;
        register = true;
    }

    if(edad.value < 18 || edad.value===null){
        warnings += `La edad ingresada no es la correcta. <br>`;
        register = true;
    }

    if (!validarDNI.test(dni.value)) {
        warnings += `El DNI ingresado no es válido <br>`;
        register = true;
    }

    if(!validarEmail.test(correoElectronico.value)){
        warnings += `Correo electrónico no válido <br>`;
        register = true;
    }

    if(contrasena.value.trim().length < 8){
        warnings += `La contraseña debe tener 8 caracteres como mínimo <br>`;
        register = true;
    }

    if(confirmarContrasena.value.trim() !== contrasena.value.trim()){
        warnings += `Las contraseñas no coinciden <br>`;
        register = true;
    }

    if(register){
        textoMensaje.innerHTML = warnings; // Mostrar advertencias en el mensaje flotante
        mensajeFlotante.style.display = 'block'; // Mostrar el mensaje flotante
    } else {
        textoMensaje.innerHTML = `Cuenta creada exitosamente<br>`; // Mostrar mensaje de éxito
        mensajeFlotante.style.display = 'block'; // Mostrar el mensaje flotante
    }
});

botonAceptar.addEventListener('click', () => {
    mensajeFlotante.style.display = 'none'; // Ocultar el mensaje flotante al aceptar
    if (textoMensaje.innerHTML === `Cuenta creada exitosamente<br>`) {
        registerForm.submit(); // Enviar el formulario solo si se acepta el mensaje de éxito
    }
});
