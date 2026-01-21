// cuando se reenvia un correo de confirmacion bloque un tiempo antes de poder reenviar otro
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('resend-btn');
    if (!btn) return;
    const timerDisplay = document.getElementById('timer');
    const Segundos = 150; //150 segundos equivale a 2:30 minutos

    btn.addEventListener('click', function () {
        // Guardar tiempo en el momento que se cliqueo 
        localStorage.setItem('resend_timestamp', Date.now().toString());
    });

    // Revisar si hay bloqueo guardado si no hay no se hace nada 
    const lastClicked = localStorage.getItem('resend_timestamp');//toma el tiepo de la recarga y la guarda en la localstorage
    if (lastClicked) {
        const segundosTime = Math.floor((Date.now() - parseInt(lastClicked)) / 1000);
        if (segundosTime < Segundos) {
            const seg = Segundos - segundosTime;
            startCooldown(seg);
        }
    }

    //comienza con el conteo del temporizador
    function startCooldown(seconds) {
        btn.disabled = true; //deshabilita el boton de reenviar
        let seg = seconds;

        updateTimerDisplay(seg);

        const interval = setInterval(() => {
            seg--;
            updateTimerDisplay(seg);

            if (seg <= 0) { //verifica si ya termino el temporizado
                clearInterval(interval);
                btn.disabled = false;//vuelve a habilitar el boton de reenviar
                btn.innerText = 'Reenviar Verificación';  //coloca de nuevo su titulo inical
                timerDisplay.textContent = ''; //limpia la etiqueta del temporizador en el html
                localStorage.removeItem('resend_timestamp'); //elimina la variable del timepo del localstorage
            }
        }, 1000);
    }

    //muestra el temporizador en la pantalla
    function updateTimerDisplay(seconds) { 
        const min = Math.floor(seconds / 60);//convierte los segundo en minutos 
        const sec = seconds % 60; //guarda los segunos restantes
        btn.innerText = `Espera...`;//cambia el titulo del boton 
        timerDisplay.textContent = `Puedes reenviar en ${min}:${sec.toString().padStart(2, '0')} minutos`; //muestra el temporizador en pantalla
    }
});


//se encarga de realizar los cambios de iconos en el ojo de contraseñas
window.togglePassword = function(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
    }
}

