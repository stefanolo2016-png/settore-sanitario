// --- 1. GESTIONE FORM ADMIN (APERTURA E CHIUSURA) ---
const parola = document.getElementById('new_pay');
const divDettagli = document.getElementById('link_pay');
const btnChiudi = document.getElementById('go');
const linkIndietro = document.getElementById('indietro');

if (parola) {
    parola.addEventListener('click', function() {
        parola.style.display = 'none'; 
        divDettagli.classList.remove('nascosto'); 
    });
}

function chiudiDettagli(event) {
    if (event && event.target.id === 'indietro') {
        event.preventDefault(); 
    }
    if (divDettagli) divDettagli.classList.add('nascosto'); 
    const adminDiv = document.getElementById('admin');
    if (adminDiv) adminDiv.style.display = 'block';
    if (parola) parola.style.display = 'block'; 
}

if (btnChiudi) btnChiudi.addEventListener('click', chiudiDettagli);
if (linkIndietro) linkIndietro.addEventListener('click', chiudiDettagli);

// --- 2. FUNZIONI MODAL (DESC E EDIT) ---
// Queste devono essere fuori da ogni IF per essere globali
function apriModal(idModal) {
    var modal = document.getElementById(idModal);
    if (modal) {
        modal.style.display = "block";
    }
}

function chiudiModal(idModal) {
    var modal = document.getElementById(idModal);
    if (modal) {
        modal.style.display = "none";
    }
}

// Chiudi il modal se clicchi fuori dalla finestra bianca
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}

// --- 3. FUNZIONE PER IL PAGAMENTO (Paziente) ---
function pagaOra(idPagamento) {
    const contenitore = document.getElementById('status-pay-' + idPagamento);
    if (contenitore) {
        contenitore.innerHTML = '<span style="font-size: 1.2rem; color: green; font-weight: bold;">✅ Pagato</span>';
    }

    fetch('update_pay_status.php?id=' + idPagamento)
        .then(response => {
            if (!response.ok) alert("Errore nel salvataggio del pagamento.");
        })
        .catch(error => console.error('Errore:', error));
}

// --- 4. FUNZIONE DI RICERCA (Tasto Invio) ---
const inputCerca = document.getElementById('cerca');
const selectFiltro = document.getElementById('filtro-doc') || document.getElementById('filtro-paziente');

if (inputCerca) {
    inputCerca.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') { 
            const testo = inputCerca.value.toLowerCase();
            const categoria = selectFiltro ? selectFiltro.value : 'tutto'; 
            const listaPagamenti = document.querySelectorAll('ul li');

            listaPagamenti.forEach(item => {
                let contenutoDacercare = item.textContent.toLowerCase(); 
                
                if (categoria === 'titolo') {
                    const h5 = item.querySelector('h5');
                    if (h5) contenutoDacercare = h5.textContent.toLowerCase();
                }
                if (categoria === 'value') {
                    const spanValue = item.querySelector('span[style*="font-weight: bold"]');
                    if (spanValue) contenutoDacercare = spanValue.textContent.toLowerCase();
                }

                if (contenutoDacercare.includes(testo)) {
                    item.style.display = "block";
                    if (item.nextElementSibling && item.nextElementSibling.tagName === 'HR') item.nextElementSibling.style.display = "block";
                } else {
                    item.style.display = "none";
                    if (item.nextElementSibling && item.nextElementSibling.tagName === 'HR') item.nextElementSibling.style.display = "none";
                }
            });
        }
    });
}

// --- 5. FUNZIONE RESET (Mostra tutti) ---
const btnReset = document.getElementById('reset-ricerca');
if (btnReset) {
    btnReset.addEventListener('click', function(e) {
        e.preventDefault();
        if (inputCerca) inputCerca.value = "";
        const listaPagamenti = document.querySelectorAll('ul li');
        listaPagamenti.forEach(item => {
            item.style.display = "block";
            if (item.nextElementSibling && item.nextElementSibling.tagName === 'HR') {
                item.nextElementSibling.style.display = "block";
            }
        });
    });
}