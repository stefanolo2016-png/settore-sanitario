<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $CE = $_POST['ce'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $city = $_POST['city'];
    $civic = $_POST['civic'];
    $telephone = $_POST['telephone'];
    $nationality = $_POST['nationality'];
    $city_born = $_POST['city_born'];
    $registration = $_POST['registration'];
    $birth = $_POST['birth'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // --- CONTROLLI ---
    if ($birth > date('Y-m-d')) {
        die("Errore: La data di nascita non può essere nel futuro.");
    }
    if (strlen($CE) < 16) {
        die("Errore: Il codice CE deve essere di almeno 16 caratteri.");
    }
    if (strlen($telephone) > 10 || !is_numeric($telephone)) {
        die("Errore: Il telefono deve essere di massimo 10 cifre numeriche.");
    }
    if (!preg_match("/^[a-zA-Z\s']*$/", $name) || !preg_match("/^[a-zA-Z\s']*$/", $surname)) {
        die("Errore: Nome e Cognome possono contenere solo lettere.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    // Requisiti:
    $maiuscola = preg_match('@[A-Z]@', $password);
    $minuscola = preg_match('@[a-z]@', $password);
    $numero    = preg_match('@[0-9]@', $password);
    $speciale  = preg_match('@[^\w]@', $password); // Cerca tutto ciò che non è lettera o numero

    if (!$maiuscola || !$minuscola || !$numero || !$speciale || strlen($password) < 8) {
        echo "La password non è abbastanza sicura.";
    } else {
        echo "Password valida! Procedo con l'hashing...";
        // Ricorda di non salvare mai la password in chiaro! Usa:
        // $hash = password_hash($password, PASSWORD_DEFAULT);
    }
    }
    

    // --- LOGICA DATABASE ---
    $check = $pdo->prepare("SELECT CE FROM private_area WHERE CE = ?");
    $check->execute([$CE]);

    if ($check->rowCount() == 0) {
        $sql = "INSERT INTO private_area (CE, name, surname, city, civic, telephone, nationality, city_born, registration, birth, password, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$CE, $name, $surname, $city, $civic, $telephone, $nationality, $city_born, $registration, $birth, $password, $email]);
        
        // Se tutto va bene, vai alla pagina accedi.php
        header("Location: accedi.php");
        exit();
    } else {
        // Se l'utente esiste già
        echo "Errore: l'utente con questo CE esiste già.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div id="box">
        <h1 id="login">LOGIN</h1>
        <hr>
        <form action="login.php" method="POST">
            <label>CE:</label><br>
            <input type="text" id="ce" name="ce" maxlength="16" required><br>
            <label>name:</label><br>
            <input type="text" id="name" name="name" pattern="[A-Za-z\s']+" required><br>
            <label>surname:</label><br>
            <input type="text" id="surname" name="surname" pattern="[A-Za-z\s']+" required><br>
            <label>city:</label><br>
            <input type="text" id="city" name="city" required><br>
            <label>civic:</label><br>
            <input type="text" id="civic" name="civic" required><br>
            <label>telephone:</label><br>
            <input type="text" id="telephone" name="telephone" maxlength="10" required><br>
            <label>nationality:</label><br>
           
            <select name="nationality" id="nationality">
                <option value="italia">Italian </option>
                <option value="svizzera">Swisser</option>
                <option value="austria">Austrian </option>
                <option value="germania">German </option>
                <option value="cechia">Cznech </option>
                <option value="slovachia">slovakia </option>
                <option value="hungaria">hungary </option>
                <option value="romania">romania </option>
                <option value="bulgaria">bulgaria </option>
                <option value="grecia">greece</option>
                <option value="spagna">spain</option>
                <option value="portogallo">portugal</option>
                <option value="malta">malta</option>
                <option value="danimarca">danish</option>
                <option value="grecia">greece</option>
                <option value="regno unito">united kingdom</option>
                <option value="irleand">irleand</option>
                <option value="svezia">sweden</option>
                <option value="norvegia">norwegian</option>
                <option value="estonia">estonia</option>
                <option value="letonia">latvia</option>
                <option value="lithuania">greece</option>
                <option value="albania">albanian</option>
                <option value="slovenia">slovenian</option>
                <option value="francia">france</option>
                <option value="paesi passi">greece</option>
                <option value="bulgaria">bulgaria</option>
                <option value="croazia">croatia</option>
                <option value="islanda">iceland</option>
                <option value="turchia">turkye</option>
                <option value="giappone">giappan</option>
                <option value="australia">australian</option>
                <option value="canada">canadian</option>
                

                
                
            </select><br>
            <label>city of born:</label><br>
            <input type="text" id="city_born" name="city_born" required><br>
            <label>date registration:</label><br>
            <input type="date" id="registration" name="registration" max="<?php echo date('Y-m-d');?>" required><br>
            <label>date birth:</label><br>
            <input type="date" id="birth" name="birth" max="<?php echo date('Y-m-d'); ?>" required><br>
            <label>password:</label><br>
            <input type="password" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" 
           title="Deve contenere almeno 8 caratteri, una maiuscola, una minuscola, un numero e un carattere speciale." required><br><br>


            <label>email:</label><br>
            <input type="text" id="email" name="email" required placeholder="esempio@email.it" required><br><br>
            
            <button type="submit">invio</button><br>

            <a href="accedi.php">accedi</a><br>
            <a href="index.php">home</a>
        </form>
    </div>
</body>
</html>
