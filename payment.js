// 1. Queste variabili servono a trovare gli elementi nella pagina
const parola = document.getElementById('new_pay');
const divDettagli = document.getElementById('link_pay');
const btnChiudi = document.getElementById('go');
const linkIndietro = document.getElementById('indietro');

// 2. Funzione per APRIRE
parola.addEventListener('click', function() {
    parola.style.display = 'none'; // Nasconde la scritta
    divDettagli.classList.remove('nascosto'); // Mostra il form
});

// 3. Funzione per CHIUDERE
function chiudiDettagli(event) {
    if (event.target.id === 'indietro') {
        event.preventDefault(); // Evita che il link ricarichi la pagina
    }
    
    divDettagli.classList.add('nascosto'); // Nasconde il form
    
    // Mostriamo di nuovo il contenitore e la scritta
    document.getElementById('admin').style.display = 'block';
    parola.style.display = 'block'; 
}

// 4. Colleghiamo la funzione di chiusura ai pulsanti
btnChiudi.addEventListener('click', chiudiDettagli);
linkIndietro.addEventListener('click', chiudiDettagli);