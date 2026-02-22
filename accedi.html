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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ce=$_POST['ce'];
    $password=$_POST['password'];

   $sql = "SELECT * FROM private_area WHERE ce=? AND password=?";
   $stmt = $pdo->prepare($sql);
   $stmt->execute([$ce,$password]);

   
   

        if ($stmt->rowCount() == 1) {
   
        // Se tutto va bene, vai alla pagina accedi.php
        header("Location: index.php");

        $_SESSION['ce']=$ce;
        

        exit();
    } else {
        // Se l'utente esiste già
        echo "Errore: l'utente con questo CE e telephone NON ESISTE.";

    }

   }
?>
<html>

<head>

    <title>ACCESSO</title>
    <link rel="stylesheet" href="accedi.css">



</head>

<body>

    <div id="box">

    <h1 id="accesso">ACCESSO</h1>

    <form action="accedi.php" method="POST">
        <label>CE:</label><br>
        <input type="text" id="ce" name="ce" maxlength="16" required><br>
        <label>password</label><br>
        <input type="password" id="password" name="password"  required><br><br>
        
        <button type="submit">invio</button>
    </form>

    <a id="login" href="login.php"><p>login</p></a>
    <a id="home" href="index.php"><p>home</p></a>

    </div>





</body>


</html>
