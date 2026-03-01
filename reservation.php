<?php
session_start();

// 1. Configurazione Database
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

// 2. Controllo Sessione
$mio_ce = isset($_SESSION['ce']) ? $_SESSION['ce'] : null;
if (!$mio_ce) {
    die("Errore: Accesso non autorizzato. Effettua il login.");
}

$ruolo = isset($_SESSION['character']) ? $_SESSION['character'] : 0;

// --- LOGICA DI RECUPERO DOTTORI DINAMICA ---
$query_user = $pdo->prepare("SELECT city, nationality FROM private_area WHERE ce = ?");
$query_user->execute([$mio_ce]);
$user_info = $query_user->fetch(PDO::FETCH_ASSOC);

$user_city  = $user_info['city'] ?? '';
$user_state = $user_info['nationality'] ?? '';

// Recupero dottori (Città -> Nazionalità -> Tutti)
$stmt = $pdo->prepare("SELECT CONCAT(name, ' ', surname) as nome, city FROM private_area WHERE `character` = 1 AND city = ?");
$stmt->execute([$user_city]);
$dottori = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($dottori)) {
    $stmt = $pdo->prepare("SELECT CONCAT(name, ' ', surname) as nome, city FROM private_area WHERE `character` = 1 AND nationality = ?");
    $stmt->execute([$user_state]);
    $dottori = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($dottori)) {
    $stmt = $pdo->query("SELECT CONCAT(name, ' ', surname) as nome, city FROM private_area WHERE `character` = 1");
    $dottori = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$json_dottori = json_encode($dottori);
$json_citta_user = json_encode($user_city);

// 3. Gestione del salvataggio nel Database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date        = $_POST['date'] ?? '';
    $time        = $_POST['time'] ?? '';
    $province    = $_POST['province'] ?? ''; 
    $description = $_POST['description'] ?? '';
    $doc_name    = $_POST['doc_name'] ?? '';

    // NUOVA LOGICA: Se è un dottore che compila il campo 'paziente_ce', usiamo quello.
    // Se il campo non esiste o è vuoto (caso paziente), usiamo il CE della sessione.
    $ce_finale = (!empty($_POST['paziente_ce'])) ? $_POST['paziente_ce'] : $mio_ce;

    if (!empty($date) && !empty($doc_name)) {
        // Aggiungiamo un controllo rapido: il CE paziente esiste nel DB?
        $check = $pdo->prepare("SELECT ce FROM private_area WHERE ce = ?");
        $check->execute([$ce_finale]);
        
        if ($check->rowCount() > 0) {
            $sql = "INSERT INTO reservation (date, time, province, description, ce, doc_name) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            try {
                $stmt->execute([$date, $time, $province, $description, $ce_finale, $doc_name]);
                echo "<script>alert('Prenotazione effettuata con successo!'); window.location='calendar.php';</script>";
            } catch (PDOException $e) {
                echo "Errore durante il salvataggio: " . $e->getMessage();
            }
        } else {
            echo "<script>alert('Errore: Il codice CE del paziente non è valido o inesistente.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>reservation</title>
    <link rel="stylesheet" href="reservation.css">
    <style>
        /* Aggiungo un minimo di stile per nascondere i div all'inizio */
        #doctor, #specific { display: none; margin-top: 20px; }
    </style>
</head>
<body>

    <h1 style="background:white;">RESERVATION</h1>


    <?php if ($ruolo == 1): ?>

    <a href="index.php" style="background:white;font-size:70px;">Home</a>

    <h2 id="specific_title" style="cursor:pointer; color:green;">SPECIFIC APPOINTMENT</h2>

   <!-- <a href="index.php"><p>home</p></a>

 

        <form method="POST">
            <input type="hidden" id="input_doc" name="doc_name">
            <input type="hidden" id="input_date" name="date">
            <input type="hidden" id="input_time" name="time">
            <input type="hidden" id="input_prov" name="province">
            <input type="hidden" name="description" value="Visita medica generica (Rapida)">

            <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                Conferma e Prenota
            </button>
        </form>
    </div>-->

    <div id="specific"> 
        <form method="POST">
            <h3>Crea Appuntamento per Paziente</h3>
            
            <label>CE Paziente:</label>
            <input type="text" name="paziente_ce" placeholder="Inserisci CE paziente" required><br>

            <label>Data:</label>
            <input type="date" name="date" required>

            <label>Ora:</label>
            <input type="time" name="time" required><br>

            <label>Città:</label>
            <input type="text" name="province" required><br>

            <label>Descrizione:</label>
            <input type="text" name="description" required><br>

            <label>Dottore (Nome e Cognome):</label>
            <input type="text" name="doc_name" required><br><br>

            <button type="submit">Registra Appuntamento</button>
        </form>
    </div>

    <script>
        const doc_list = <?php echo $json_dottori; ?>;
        const user_city = <?php echo $json_citta_user; ?>;
    </script>

    <?php else: ?> <!--PROGRAMMA PER IL PAZIENTE-->

        <h2 id="visit" style="cursor:pointer; color:blue;">VISIT THE DOCTOR</h2>
    <h2 id="specific_title" style="cursor:pointer; color:green;">SPECIFIC APPOINTMENT</h2>

    <a href="index.php" style="background:white;font-size:70px;"><p>home</p></a>

    <div id="doctor"> 
        <h3>🩺 Proposta di Prenotazione Rapida</h3>
        <hr>
        <p>Abbiamo trovato un posto disponibile per te:</p>
        <div style="background: #f9f9f9; padding: 10px; margin-bottom: 15px;">
            <p><strong>Dottore:</strong> <span id="display_doc">--</span></p>
            <p><strong>Data:</strong> <span id="display_date">--</span></p>
            <p><strong>Ora:</strong> <span id="display_time">--</span></p>
            <p><strong>Luogo:</strong> <span id="display_prov">--</span></p>

            <button type="submit" id="btn_regen" style="background-color: #ffc107; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">
            🔄 Riprogramma (Cambia dati)
            </button>

        </div>

        <form method="POST">
            <input type="hidden" id="input_doc" name="doc_name">
            <input type="hidden" id="input_date" name="date">
            <input type="hidden" id="input_time" name="time">
            <input type="hidden" id="input_prov" name="province">
            <input type="hidden" name="description" value="Visita medica generica (Rapida)">

            <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                Conferma e Prenota
            </button>
        </form>
    </div>

    <div id="specific"> 
        <form method="POST">
            <h3>📅 Inserimento Manuale Appuntamento</h3>
        <br>
        <label>Data:</label>
        <input type="date" name="date" required>

        <label>Ora:</label>
        <input type="time" name="time" required><br>

        <label>Città/Provincia:</label>
        <input type="text" name="province" required><br>

        <label>Descrizione Visita:</label>
        <input type="text" name="description" placeholder="es. Visita di controllo" required><br>

        <label>Nome Medico:</label>
        <input type="text" name="doc_name" placeholder="Nome e Cognome" required><br><br>

        <button type="submit">Conferma Appuntamento</button>
        </form>
    </div>

    <script>
        const doc_list = <?php echo $json_dottori; ?>;
        const user_city = <?php echo $json_citta_user; ?>;
    </script>



        <?php endif; ?>

    <script src="reservation.js"></script>

</body>
</html>