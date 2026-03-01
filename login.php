<?php
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


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recupero dati dal POST
    $CE = $_POST['ce'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $city = $_POST['city'];
    $via = $_POST['via'];
    $civic = $_POST['civic'];
    $telephone = $_POST['telephone'];
    $nationality = $_POST['nationality'];
    $city_born = $_POST['city_born'];
    $registration = $_POST['registration'];
    $birth = $_POST['birth'];
    $password_plain = $_POST['password'];
    $email = $_POST['email'];
    $character_id = $_POST['character'];

    // --- CONTROLLI DI VALIDAZIONE ---
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

    // Controllo Sicurezza Password
    $maiuscola = preg_match('@[A-Z]@', $password_plain);
    $minuscola = preg_match('@[a-z]@', $password_plain);
    $numero    = preg_match('@[0-9]@', $password_plain);
    $speciale  = preg_match('@[^\w]@', $password_plain);

    if (!$maiuscola || !$minuscola || !$numero || !$speciale || strlen($password_plain) < 8) {
        die("Errore: La password non rispetta i requisiti di sicurezza.");
    }

    // Hash della password prima del salvataggio
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    // --- LOGICA DATABASE ---
    // Verifichiamo se il CE esiste già
    $check = $pdo->prepare("SELECT CE FROM private_area WHERE CE = ?");
    $check->execute([$CE]);

    if ($check->rowCount() == 0) {
        // Query di inserimento (usa i backtick per `character` se è il nome della colonna)
        $sql = "INSERT INTO private_area (CE, name, surname, city, via, civic, telephone, nationality, city_born, registration, birth, password, email, `character`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // Esecuzione con l'array dei valori nell'ordine corretto
        $stmt->execute([
            $CE, 
            $name, 
            $surname, 
            $city, 
            $via, 
            $civic, 
            $telephone, 
            $nationality, 
            $city_born, 
            $registration, 
            $birth, 
            $password_hashed, 
            $email, 
            $character_id
        ]);
        
        header("Location: accedi.php");
        exit();
    } else {
        echo "Errore: l'utente con questo CE esiste già.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div id="box">
        <h1 id="login">LOGIN</h1>
        <hr>
        <form action="login.php" method="POST">
            <label>CE:</label><br>
            <input type="text" id="ce" name="ce" maxlength="16" required><br>

            <label>Name:</label><br>
            <input type="text" id="name" name="name" pattern="[A-Za-z\s']+" required><br>

            <label>Surname:</label><br>
            <input type="text" id="surname" name="surname" pattern="[A-Za-z\s']+" required><br>

            <label>City:</label><br>
            <input type="text" id="city" name="city" required><br>

            <label>Via:</label><br>
            <input type="text" id="via" name="via" required><br>

            <label>Civic:</label><br>
            <input type="text" id="civic" name="civic" required><br>

            <label>Telephone:</label><br>
            <input type="text" id="telephone" name="telephone" maxlength="10" required><br>

            <label>Nationality:</label><br>
            <select name="nationality" id="nationality">
                <option value="albania">albanian</option>
		        <option value="australia">australian</option>		
		        <option value="austria">Austrian </option>
		        <option value="bulgaria">bulgaria </option>
		        <option value="canada">canadian</option>	
		        <option value="croazia">croatia</option>
		        <option value="cechia">Cznech </option>
		        <option value="danimarca">danish</option>
		        <option value="estonia">estonia</option>
		        <option value="francia">france</option>	
		        <option value="germania">German </option>
		        <option value="grecia">greece</option>
		        <option value="hungaria">hungary </option>
		        <option value="islanda">iceland</option>
		        <option value="irlanda">irleand</option>
		        <option value="italia">Italy</option>
		        <option value="giappone">Japan</option>
		        <option value="letonia">latvia</option>
                <option value="lithuania">Lithuania</option>
		        <option value="malta">malta</option>
		        <option value="paesi passi">Netherlands</option>
		        <option value="norvegia">norwegian</option>
		        <option value="portogallo">portugal</option>
		        <option value="romania">romania </option>
		        <option value="slovachia">slovakia </option>
		        <option value="slovenia">slovenian</option>
		        <option value="spagna">spain</option>
		        <option value="svezia">sweden</option>
		        <option value="svizzera">Swisser</option>
		        <option value="turchia">turkye</option>
		        <option value="regno unito">united kingdom</option>

                </select><br>

            <label>City of Birth:</label><br>
            <input type="text" id="city_born" name="city_born" required><br>

            <label>Date of Registration:</label><br>
            <input type="date" id="registration" name="registration" max="<?php echo date('Y-m-d');?>" required><br>

            <label>Date of Birth:</label><br>
            <input type="date" id="birth" name="birth" max="<?php echo date('Y-m-d'); ?>" required><br>

            <label>Password:</label><br>
            <input type="password" id="password" name="password" 
                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" 
                   title="Deve contenere almeno 8 caratteri, una maiuscola, una minuscola, un numero e un carattere speciale." required><br>

            <label>Email:</label><br>
            <input type="email" id="email" name="email" required placeholder="esempio@email.it"><br><br>

            <p>Is the user an Administrator?(admin)?</p>
            <input type="radio" id="admin_si" name="character" value="1" required>
            <label for="admin_si">Yes</label>

            <input type="radio" id="admin_no" name="character" value="0">
            <label for="admin_no">No</label><br><br>

            <button type="submit">SUBMIT</button><br><br>

            <a href="accedi.php">Access</a><br>
            <a href="index.php">Home</a>
        </form>
    </div>
</body>
</html>