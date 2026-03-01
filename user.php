<?php

session_start();
// 1. Configurazione Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sanitario";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}

$ruolo = isset($_SESSION['character']) ? $_SESSION['character'] : 0;
// 2. Definizione del cognome da cercare
$ce_ricercato = $_SESSION['ce'];
$utente = null;

if ($ce_ricercato) {
    // Selezioniamo tutto dalla tabella
    $stmt = $pdo->prepare("SELECT * FROM `private_area` WHERE ce = ?");
    $stmt->execute([$ce_ricercato]);
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 3. Preparazione dell'indirizzo (Via)
$indirizzo_completo = "--------------";
/*if ($utente) {
    $citta = $utente['city'] ?? '';
    $civico = $utente['civic'] ?? '';
    $nazione = $utente['nationality'] ?? '';
    
    // Uniamo i campi. trim() serve a pulire virgole e spazi se i dati mancano
    $indirizzo_completo = trim("$citta, $civico ($nazione)", ", ()");
    if (empty($indirizzo_completo)) { $indirizzo_completo = "--------------"; }
}*/
  echo $ce_ricercato;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo Utente - <?= htmlspecialchars($ce_ricercato) ?></title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

    <?php if ($ruolo == 1): ?>

    <div id="box">
        <div style="display: flex; align-items: center; justify-content: center;">
        <img src="doctor.png" width="50" height="50" >
        
        </div>
        <div id="colonna">
            <div id="nome">
                <p><strong>NAME:</strong></p>
                <p><?= htmlspecialchars($_SESSION['name'] ?? 'Non trovato') ?></p>
            </div>

            <div id="telefono">
                <p><strong>TELEPHONE:</strong></p>
                <p><?= htmlspecialchars($_SESSION['telephone'] ?? '--------------') ?></p>
            </div>
            
            <div id="registration">
                <p><strong>REGISTRATION:</strong></p>
                <p><?= htmlspecialchars($_SESSION['registration'] ?? '----------------') ?></p>
            </div>
        </div>

        <div id="colonna">
            <div id="cognome">
                <p><strong>SURNAME:</strong></p>
                <p><?= htmlspecialchars($_SESSION['surname'] ?? '--------------') ?></p>
            </div>

            <div id="via">
                <p><strong>ADDRESS:</strong></p>
                <p><?= htmlspecialchars($_SESSION['place'] ?? '--------------') ?></p>
            </div>
        
            <div id="nascita">
                <p><strong>YEAR OF BIRTH:</strong></p>
                <p><?= htmlspecialchars($_SESSION['birth'] ?? '----------------') ?></p>
            </div>
        </div>

        <a href="index.php"><p>Home</p></a>
        <a href="accedi.php">log out</a>
    </div>

    <?php else: ?>


        <div id="box">
        <div style="display: flex; align-items: center; justify-content: center;">
        <img src="user.png" width="50" height="50" >
        
        </div>
        <div id="colonna">
            <div id="nome">
                <p><strong>NAME:</strong></p>
                <p><?= htmlspecialchars($_SESSION['name'] ?? 'Non trovato') ?></p>
            </div>

            <div id="telefono">
                <p><strong>TELEPHONE:</strong></p>
                <p><?= htmlspecialchars($_SESSION['telephone'] ?? '--------------') ?></p>
            </div>
            
            <div id="registration">
                <p><strong>REGISTRATION:</strong></p>
                <p><?= htmlspecialchars($_SESSION['registration'] ?? '----------------') ?></p>
            </div>
        </div>

        <div id="colonna">
            <div id="cognome">
                <p><strong>SURNAME:</strong></p>
                <p><?= htmlspecialchars($_SESSION['surname'] ?? '--------------') ?></p>
            </div>

            <div id="via">
                <p><strong>ADDRESS:</strong></p>
                <p><?= htmlspecialchars($_SESSION['place'] ?? '--------------') ?></p>
            </div>
        
            <div id="nascita">
                <p><strong>YEAR OF BIRTH:</strong></p>
                <p><?= htmlspecialchars($_SESSION['birth'] ?? '----------------') ?></p>
            </div>
        </div>

        <a href="index.php"><p>Home</p></a>
        <a href="accedi.php">log out</a>
    </div>



        <?php endif; ?>

</body>
</html>