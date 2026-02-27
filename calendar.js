
// Funzione per aggiornare i timer
function updateTimers() {
    const now = new Date().getTime();
    
    // Selezioniamo sia l'ID "clock" che tutti gli elementi "countdown-timer"
    const clocks = document.querySelectorAll('#clock, .countdown-timer');

    clocks.forEach(timer => {
        const dataStr = timer.getAttribute('data-next');
        if (!dataStr) return;

        // Convertiamo la data del DB in formato leggibile per JS
        const nextDate = new Date(dataStr.replace(' ', 'T')).getTime();
        const distance = nextDate - now;

        if (distance < 0) {
            timer.innerHTML = "APPUNTAMENTO SCADUTO";
            timer.style.color = "gray";
        } else {
            // Calcoli matematici per le unità di tempo
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Aggiungiamo lo zero davanti se il numero è singolo (es: 05 invece di 5)
            const fHours = hours < 10 ? "0" + hours : hours;
            const fMins = minutes < 10 ? "0" + minutes : minutes;
            const fSecs = seconds < 10 ? "0" + seconds : seconds;

            // Visualizzazione finale
            timer.innerHTML = days + "g " + fHours + "o " + fMins + "m " + fSecs + "s";
            
            // Un tocco di interattività: se mancano meno di 10 minuti, diventa rosso
            if (days === 0 && hours === 0 && minutes < 10) {
                timer.style.color = "red";
                timer.style.fontWeight = "bold";
            }
        }
    });
}

// Avviamo lo script al caricamento della pagina
document.addEventListener('DOMContentLoaded', function() {
    console.log("Timer avviato correttamente!");
    
    // Eseguiamo la funzione subito
    updateTimers();
    
    // Poi la ripetiamo ogni secondo (1000 millisecondi)
    setInterval(updateTimers, 1000);
});
