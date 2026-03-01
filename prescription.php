<?php
session_start();

// --- 1. CONNESSIONE AL DATABASE ---
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
// Definiamo $mio_ce recuperandolo dalla sessione
$mio_ce = isset($_SESSION['ce']) ? $_SESSION['ce'] : '';

// --- 2. GESTIONE AZIONI (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && $ruolo == 1) {
    
    // CASO A: Creazione Nuova Prescrizione
    if (isset($_POST['medical_info']) && !isset($_POST['update_btn'])) { 
        $ce_input = $_POST['ce'];
        $priority = $_POST['priority'];
        $cod_exception = $_POST['cod_exception'];
        $emission_date = $_POST['emission_date'];
        $medical_info = $_POST['medical_info'];
        $description = $_POST['description'];

        $sql = "INSERT INTO medical_prescription (priority, cod_exception, ce, emission_date, medical_info, description) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$priority, $cod_exception, $ce_input, $emission_date, $medical_info, $description]);
            echo "<script>alert('Prescrizione registrata!'); window.location.href='prescription.php';</script>";
        } catch (PDOException $e) {
            echo "Errore: " . $e->getMessage();
        }
    }

    // CASO B: Eliminazione
    if (isset($_POST['delete_btn'])) {
        $id = $_POST['id_da_eliminare'];
        $sql = "DELETE FROM medical_prescription WHERE cod_prescription = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo "<script>alert('Eliminata!'); window.location.href='prescription.php';</script>";
    }
}

// --- 3. RECUPERO DATI ---
if ($ruolo == 1) {
    // Il dottore vede tutto
    $sql_presc = "SELECT * FROM medical_prescription ORDER BY emission_date DESC";
    $stmt_presc = $pdo->query($sql_presc);
} else {
    // IL PAZIENTE VEDE SOLO LE SUE
    $sql_presc = "SELECT * FROM medical_prescription WHERE ce = ? ORDER BY emission_date DESC";
    $stmt_presc = $pdo->prepare($sql_presc);
    // QUI C'ERA L'ERRORE: Usiamo $mio_ce definito sopra
    $stmt_presc->execute([$mio_ce]); 
}
$anteprime_presc = $stmt_presc->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <link rel="stylesheet" href="prescription.css">
    <script src="payment.js" defer></script> 
</head>
<body>
    <a href="index.php" style="background:white;font-size:70px;">Home</a>

    <?php if ($ruolo == 1): ?> 

        <h3 id="alto">CREATE PRESCRIPTION</h3>
        <div id="admin">
            
            <div id="link_pay">
                <form action="prescription.php" method="POST">
                    <label>CE Patient:</label> <input type="text" name="ce" required><br>
                    <label>Date:</label> <input type="datetime-local" name="emission_date" required><br>
                    <label>Medical Info:</label> <input type="text" name="medical_info" required><br>
                    <label>Description:</label> <input type="text" name="description" required><br>
                    <label>Priority:</label> <input type="text" name="priority" required><br>
                    <label>Cod Exception:</label> <input type="text" name="cod_exception" required><br>
                    <button type="submit">GO</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div id="cronologia">
        <h4>Historical Records</h4>
        <ul>
            <?php if (count($anteprime_presc) > 0): ?>
                <?php foreach ($anteprime_presc as $p): ?>
                    <li style="margin-bottom: 15px; border-bottom: 1px solid #ccc; padding: 10px;">
                        <strong><?php echo htmlspecialchars($p['medical_info']); ?></strong> 
                        <small>(<?php echo date("d/m/Y", strtotime($p['emission_date'])); ?>)</small><br>
                        <span>Cod: <?php echo htmlspecialchars($p['cod_prescription']); ?></span><br>
                        
                        <button type="button" onclick="apriModal('modal-<?php echo $p['cod_prescription']; ?>')">Dettagli</button>

                        <?php if ($ruolo == 1): ?>
                            <form action="prescription.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id_da_eliminare" value="<?php echo $p['cod_prescription']; ?>">
                                <button type="submit" name="delete_btn" onclick="return confirm('Eliminare?')">Elimina</button>
                            </form>
                        <?php endif; ?>

                        <div id="modal-<?php echo $p['cod_prescription']; ?>" class="modal" style="display:none; position:fixed; z-index:1; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
                            <div class="modal-content" style="background-color:white; margin:15% auto; padding:20px; width:50%; border-radius:8px;">
                                <span onclick="chiudiModal('modal-<?php echo $p['cod_prescription']; ?>')" style="float:right; cursor:pointer; font-weight:bold; font-size:20px;">&times;</span>
                                <h3>Dettagli Prescrizione</h3>
                                <hr>
                                <p><strong>Paziente (CE):</strong> <?php echo htmlspecialchars($p['ce']); ?></p>
                                <p><strong>Priorità:</strong> <?php echo htmlspecialchars($p['priority']); ?></p>
                                <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($p['description']); ?></p>
                                <p><strong>Cod. Esenzione:</strong> <?php echo htmlspecialchars($p['cod_exception']); ?></p>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Nessuna prescrizione trovata.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>s