document.addEventListener('DOMContentLoaded', function() {

    // Recuperiamo tutti i riferimenti agli elementi
    const btnVisit = document.getElementById('visit');
    const btnSpecific = document.getElementById('specific_title');
    const btnRegen = document.getElementById('btn_regen'); 
    const divDoctor = document.getElementById('doctor');
    const divSpecific = document.getElementById('specific');

    // Funzione per generare i dati random
    function generaDatiCasuali() {
        if (typeof doc_list !== 'undefined' && doc_list.length > 0) {
            const scelto = doc_list[Math.floor(Math.random() * doc_list.length)];
            
            const dataObj = new Date();
            dataObj.setDate(dataObj.getDate() + Math.floor(Math.random() * 14) + 1);
            const formattedDate = dataObj.toISOString().split('T')[0];

            const orari = ["00:00","00:30","01:00","01:30","02:00","02:30","03:30","04:00","04:30","05:00","05:30","06:00","06:30","07:00","07:30","08:00","08:30","09:00","09:30", "10:00","10:30", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:30", "17:00" ,"17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30"];
            const randomTime = orari[Math.floor(Math.random() * orari.length)];

            // Scrittura negli SPAN
            if(document.getElementById('display_doc')) document.getElementById('display_doc').innerText = scelto.nome;
            if(document.getElementById('display_date')) document.getElementById('display_date').innerText = formattedDate;
            if(document.getElementById('display_time')) document.getElementById('display_time').innerText = randomTime;
            if(document.getElementById('display_prov')) document.getElementById('display_prov').innerText = scelto.city;

            // Scrittura negli INPUT HIDDEN
            if(document.getElementById('input_doc')) document.getElementById('input_doc').value = scelto.nome;
            if(document.getElementById('input_date')) document.getElementById('input_date').value = formattedDate;
            if(document.getElementById('input_time')) document.getElementById('input_time').value = randomTime;
            if(document.getElementById('input_prov')) document.getElementById('input_prov').value = scelto.city;
        }
    }

    // --- LOGICA DI CONTROLLO (Esegui solo se l'elemento esiste) ---

    if (btnVisit) {
        btnVisit.onclick = function() {
            if (divDoctor) divDoctor.style.display = 'block';
            if (divSpecific) divSpecific.style.display = 'none';
            generaDatiCasuali();
        };
    }

    if (btnRegen) {
        btnRegen.onclick = function() {
            generaDatiCasuali();
        };
    }

    if (btnSpecific) {
        btnSpecific.onclick = function() {
            if (divSpecific) divSpecific.style.display = 'block';
            if (divDoctor) divDoctor.style.display = 'none';
        };
    }
});