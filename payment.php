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

// --- 2. PARTE A: GESTIONE AZIONI (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && $ruolo == 1) {
    
    // CASO A: Creazione Nuovo Pagamento
    if (isset($_POST['title']) && !isset($_POST['update_btn'])) { 
        $id_alfanumerico = bin2hex(random_bytes(25)); 
        $title = $_POST['title'];
        $date  = $_POST['date'];
        $value = $_POST['value'] . " " . $_POST['coin'];
        $type  = $_POST['type'];
        $ce_client = $_POST['ce_client'];
        $doc_name = $_SESSION['name'] . " " . $_SESSION['surname'];

        $sql = "INSERT INTO payment (id, title, date, value, type, ce_client, doc_name, paid) VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$id_alfanumerico, $title, $date, $value, $type, $ce_client, $doc_name]);
            echo "<script>alert('Pagamento creato!'); window.location.href='payment.php';</script>";
        } catch (PDOException $e) {
            echo "Errore inserimento: " . $e->getMessage();
        }
    }

    // CASO B: Cancellazione Pagamento
    if (isset($_POST['delete_btn'])) {
        $id_da_cancellare = $_POST['id_da_eliminare']; 
        $sql_delete = "DELETE FROM payment WHERE id = ?";
        $stmt = $pdo->prepare($sql_delete);
        $stmt->execute([$id_da_cancellare]);
        echo "<script>alert('Eliminato!'); window.location.href='payment.php';</script>";
    }

    // CASO C: Modifica (Update) Pagamento
    if (isset($_POST['update_btn'])) {
        $id = $_POST['id_modifica'];
        $titolo = $_POST['nuovo_titolo'];
        $valore = $_POST['nuovo_valore'];
        $descrizione = $_POST['nuova_descrizione'];

        $sql_update = "UPDATE payment SET title = ?, value = ?, type = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([$titolo, $valore, $descrizione, $id]);
        echo "<script>alert('Aggiornato!'); window.location.href='payment.php';</script>";
    }
}

// --- 3. PARTE B: RECUPERO DATI ---
if ($ruolo == 1) {
    $sql_cronologia = "SELECT * FROM payment ORDER BY date DESC";
    $stmt = $pdo->query($sql_cronologia);
} else {
    $sql_cronologia = "SELECT * FROM payment WHERE ce_client = ? ORDER BY date DESC";
    $stmt = $pdo->prepare($sql_cronologia);
    $stmt->execute([$_SESSION['ce']]);
}
$pagamenti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>payment</title>
    <link rel="stylesheet" href="payment.css">
</head>
<body>

    <a href="index.php" style="background:white;font-size:70px;">Home</a>

    <?php if ($ruolo == 1): ?> 
        <div id="admin">
            <h3 id="new_pay">CREATE<label>  </label>PAYMENT <img src="plus.png" width="50" height="50"></h3>
        </div>

        <div id="intermezzo">
            <div id="link_pay" class="nascosto">
                <form action="payment.php" method="POST">
                    <label>Client (CE):</label>
                    <input type="text" name="ce_client" required><br>
                    <label>Title:</label>
                    <input type="text" name="title" required><br>
                    <label>Date:</label>
                    <input type="datetime-local" name="date" required><br>
                    <label>Value:</label>
                    <input type="text" name="value" required>
                    <select name="coin">
                        <option value="€">€</option>
                        <option value="$">$</option>
                        <option value="£">£</option>
                        <option value="CHF">CHF</option>
                    </select><br>
                    <label>Description:</label>
                    <input type="text" name="type"><br><br>
                    <button type="submit">GO</button>
                    <a id="indietro" href="#">indietro</a>
                </form>
            </div>

            <div id="cronologia">
                <h4>Historical Record Doc</h4><br>
                <ul>
                    <?php if (count($pagamenti) > 0): ?>
                        <?php foreach ($pagamenti as $p): ?>
                            <li style="margin-bottom: 20px; padding-left: 10px;">
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <div style="display: flex; gap: 40px; align-items: flex-start;">
                                        <div>
                                            <h5 style="margin: 0; font-size: 1.1rem;"><?php echo htmlspecialchars($p['title']); ?></h5>
                                            <p style="margin: 0; color: gray; font-size: 0.85rem;"><?php echo htmlspecialchars($p['ce_client']); ?></p>
                                        </div>
                                        <div>
                                            <span style="font-weight: bold; font-size: 1.1rem;"><?php echo htmlspecialchars($p['value']); ?></span><br>
                                            <span style="font-size: 0.85rem; color: #555;"><?php echo date("d/m/Y H:i", strtotime($p['date'])); ?></span>
                                            <div style="margin-top: 5px; font-size: 0.8rem; font-weight: bold;">
                                                <?php if ((int)$p['paid'] === 1): ?>
                                                    <span style="color: green;">✅ PAGATO</span>
                                                <?php else: ?>
                                                    <span style="color: orange;">⏳ IN ATTESA</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" onclick="apriModal('modal-<?php echo $p['id']; ?>')">desc</button>
                                        
                                        <?php if ((int)$p['paid'] === 0): ?>
                                            <button type="button" onclick="apriModal('edit-<?php echo $p['id']; ?>')">edit</button>
                                            <form action="payment.php" method="POST" style="display:inline;" onsubmit="return confirm('Sicuro?');">
                                                <input type="hidden" name="id_da_eliminare" value="<?php echo $p['id']; ?>">
                                                <button type="submit" name="delete_btn" style="background-color: #ff4d4d; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div id="modal-<?php echo $p['id']; ?>" class="modal">
                                    <div class="modal-content">
                                        <span class="close" onclick="chiudiModal('modal-<?php echo $p['id']; ?>')">&times;</span>
                                        <p><strong>Descrizione:</strong><br><?php echo htmlspecialchars($p['type']); ?></p>
                                    </div>
                                </div>

                                <div id="edit-<?php echo $p['id']; ?>" class="modal">
                                    <div class="modal-content">
                                        <span class="close" onclick="chiudiModal('edit-<?php echo $p['id']; ?>')">&times;</span>
                                        <h3>Modifica Pagamento</h3>
                                        <form action="payment.php" method="POST">
                                            <input type="hidden" name="id_modifica" value="<?php echo $p['id']; ?>">
                                            <label">Titolo:</label><br>
                                            <input type="text" name="nuovo_titolo" value="<?php echo htmlspecialchars($p['title']); ?>" required><br><br>
                                            <label>Valore:</label><br>
                                            <input type="text" name="nuovo_valore" value="<?php echo htmlspecialchars($p['value']); ?>" required><br><br>
                                            <label>Descrizione:</label><br>
                                            <input type="text" name="nuova_descrizione" value="<?php echo htmlspecialchars($p['type']); ?>"><br><br>
                                            <button type="submit" name="update_btn">Salva Modifiche</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            <hr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Nessun pagamento trovato.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    <?php else: ?> <h1>PAYMENT PATIENT</h1>
        <div id="cronologia">
            <ul>
                <?php foreach ($pagamenti as $p): ?>
                    <li style="margin-bottom: 20px;">
                        <h5><?php echo htmlspecialchars($p['title']); ?></h5>
                        <p>Doc: <?php echo htmlspecialchars($p['doc_name']); ?> | <strong><?php echo htmlspecialchars($p['value']); ?></strong></p>
                        <button type="button" onclick="apriModal('modal-<?php echo $p['id']; ?>')">desc</button>
                        
                        <div id="status-pay-<?php echo $p['id']; ?>" style="display:inline;">
                            <?php if ((int)$p['paid'] === 1): ?>
                                <span style="color: green; font-weight: bold;">✅ Pagato</span>
                            <?php else: ?>
                                <button type="button" onclick="pagaOra('<?php echo $p['id']; ?>')">pay</button>
                            <?php endif; ?>
                        </div>

                        <div id="modal-<?php echo $p['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="chiudiModal('modal-<?php echo $p['id']; ?>')">&times;</span>
                                <p><?php echo htmlspecialchars($p['type']); ?></p>
                            </div>
                        </div>
                    </li>
                    <hr>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <script src="payment.js"></script>
</body>
</html>