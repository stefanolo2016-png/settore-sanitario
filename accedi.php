
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>ACCESSO</title>
    <link rel="stylesheet" href="accedi.css">
</head>
<body>

    <div id="box">
        <h1 id="accesso">SIGN IN</h1>

       

        <form action="accedi.php" method="POST">
            <label>CE:</label><br>
            <input type="text" id="ce" name="ce" maxlength="16" required><br>
            <label>password</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <p>Access as Administrator (admin)?</p>
            <input type="radio" id="admin_si" name="character" value="1" required>
            <label for="admin_si">Yes</label>

            <input type="radio" id="admin_no" name="character" value="0">
            <label for="admin_no">No</label><br><br>

            <button type="submit">SUBMIT</button>
        </form>

     
        <a id="login" href="login.php"><p>login</p></a>
        

    </div>

</body>
</html>

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

$errore = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ce = $_POST['ce'];
    $password_inserita = $_POST['password'];
    $scelta_admin = $_POST['character']; 

    $sql = "SELECT * FROM private_area WHERE CE = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ce]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password_inserita, $user['password'])) {
        
        // Controllo coerenza Admin
        if ($scelta_admin == "1" && $user['character'] == "0") {
            $errore = "Accesso negato: non hai i permessi di Amministratore.";
        } else {
            session_regenerate_id();
            
            // Salvataggio di TUTTI i dati che ti servono (quelli che avevi prima)
            $_SESSION['ce'] = $user['ce'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['surname'] = $user['surname'];
            $_SESSION['character'] = $user['character']; 
            $_SESSION['telephone'] = $user['telephone'];
            $_SESSION['city'] = $user['city'];
            $_SESSION['registration'] = $user['registration'];
            $_SESSION['birth'] = $user['birth'];
            $_SESSION['place'] = $user['city'] . " " . $user['via'] . " " .$user['civic'];

            header("Location: index.php");
            exit();
        }
    } else {
        $errore = "Errore: CE o password errati.";
    }
}
?>
