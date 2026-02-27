<?php
session_start();

$servername = "localhost";
$username = "root";
$password_db = ""; 
$dbname = "sanitario";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}

$ruolo = isset($_SESSION['character']) ? $_SESSION['character'] : 0;
$mio_ce = isset($_SESSION['ce']) ? $_SESSION['ce'] : '';

// 1. RECUPERO DATI
if ($ruolo == 1) {
    $sql_anteprima = "SELECT title, date, value, ce_client FROM payment ORDER BY date DESC LIMIT 3";
    $stmt_ant = $pdo->query($sql_anteprima);
} else {
    // AGGIUNTA LA VIRGOLA tra doc_name e date qui sotto
    $sql_anteprima = "SELECT title, value, doc_name, date FROM payment WHERE ce_client = ? ORDER BY date DESC LIMIT 3";
    $stmt_ant = $pdo->prepare($sql_anteprima);
    $stmt_ant->execute([$mio_ce]);
}
$anteprime = $stmt_ant->fetchAll(PDO::FETCH_ASSOC);

// 2. PREPARO LA LISTA HTML
$lista_html = ""; 

foreach ($anteprime as $a) {
    $titolo = htmlspecialchars($a['title']);
    $data   = date("d/m", strtotime($a['date']));
    $valore = htmlspecialchars($a['value']);
    $sottotitolo = ($ruolo == 1) ? htmlspecialchars($a['ce_client']) : "Doc: " . htmlspecialchars($a['doc_name']);

    $lista_html .= "<li style='list-style:none; margin-bottom:10px; border-bottom:1px solid #888;'>
                        <strong>$titolo</strong> <small style='color:gray; font-size:0.7rem;'>($data)</small><br>
                        <span style='font-size:0.8rem; color:#555;'>$sottotitolo</span>
                        <strong style='float:right; color:#d32f2f;'>$valore</strong>
                    </li>";
} // Chiusura corretta del foreach


// --- RECUPERO ANTEPRIME PRESCRIZIONI ---
if ($ruolo == 1) {
    // Il dottore vede le ultime 3 in assoluto
    $sql_presc = "SELECT medical_info, emission_date, cod_prescription FROM medical_prescription ORDER BY emission_date DESC LIMIT 3";
    $stmt_presc = $pdo->query($sql_presc);
} else {
    // Il paziente vede solo le sue ultime 3
    $sql_presc = "SELECT medical_info, emission_date, cod_prescription FROM medical_prescription WHERE ce = ? ORDER BY emission_date DESC LIMIT 3";
    $stmt_presc = $pdo->prepare($sql_presc);
    $stmt_presc->execute([$mio_ce]);
}
$anteprime_presc = $stmt_presc->fetchAll(PDO::FETCH_ASSOC);

// Prepariamo l'HTML per il secondo box (Prescription)
$lista_presc_html = "";

foreach ($anteprime_presc as $p) {
    $info = htmlspecialchars($p['medical_info']);
    $data = date("d/m", strtotime($p['emission_date']));
    $cod  = htmlspecialchars($p['cod_prescription']);

    // Usiamo lo stesso stile scuro e visibile dei pagamenti
    $lista_presc_html .= "<li style='list-style:none; margin-bottom:12px; border-bottom: 2px solid #888; padding-bottom: 8px;'>
                            <strong>$info</strong> <small style='color:gray; font-size:0.7rem;'>($data)</small><br>
                            <span style='font-size:0.8rem; color:#555;'>Cod: $cod</span>
                          </li>";
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Sistema Sanitario</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <?php if ($ruolo == 1): ?>
    <div id="upper">
        <div id="calendar">
            <a href="calendar.php"><h2>CALENDAR</h2></a>
        </div>
        <div id="reservation">
            <a href="reservation.php"><h2>RESERVATION [+]</h2></a>
        </div>
        <div id="user-icon">
            <a href="user.php"><img src="user.png" width="30" alt="User"></a>
        </div>
    </div>

    <hr>
    
    <div id="center-title"> 
        <h1>TAKE CARE OF YOURSELF</h1>
    </div>

    <div id="box-container">
        
    <div id="payment" class="card">
    <div class="card-header">
        <a href="payment.php"><h2>PAYMENT</h2></a>


    </div>
    <div class="card-content">
        <ul>
            <?php 
                if ($lista_html !== "") {
                    echo $lista_html; 
                } else {
                    echo "<li>Nessun record trovato</li>";
                }
            ?>
        </ul>
    </div>
</div>

        <div id="prescription" class="card">
            <div class="card-header">
                <a href="prescription.php"><h2>PRESCRIPTION</h2></a>
            </div>
            <div class="card-content">
                <ul>

                <?php 
                if ($lista_presc_html !== "") {
                    echo $lista_presc_html; 
                } else {
                    echo "<li>Nessuna prescrizione trovata</li>";
                }
                ?>
            
                </ul>
            </div>
        </div>

    </div>

    <?php else: ?> <!--PROGRAMMA PER IL PAZIENTE-->

        <div id="upper">
        <div id="calendar">
            <a href="calendar.php"><h2>CALENDAR</h2></a>
        </div>
        <div id="reservation">
            <a href="reservation.php"><h2>RESERVATION [+]</h2></a>
        </div>
        <div id="user-icon">
            <a href="user.php"><img src="user.png" width="30" alt="User"></a>
        </div>
    </div>

    <hr>
    
    <div id="center-title"> 
        <h1>TAKE CARE OF YOURSELF</h1>
    </div>

    <div id="box-container">
        
       <div id="payment" class="card">
    <div class="card-header">
        <a href="payment.php"><h2>PAYMENT</h2></a>
    </div>
    <div class="card-content">
        <ul>
            <?php 
            if ($lista_html !== "") {
                echo $lista_html; 
            } else {
                echo "<li>Nessun record trovato</li>";
            }
        ?>
        </ul>
    </div>
</div>

        <div id="prescription" class="card">
            <div class="card-header">
                <a href="prescription.php"><h2>PRESCRIPTION</h2></a>
            </div>
            <div class="card-content">
                <ul>
            <?php 
                if ($lista_presc_html !== "") {
                    echo $lista_presc_html; 
                } else {
                    echo "<li>Nessuna prescrizione trovata</li>";
                }
            ?>
            
                </ul>
            </div>
        </div>

    </div>

    <?php endif; ?>

</body>
</html>
