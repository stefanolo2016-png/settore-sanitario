<?php
// update_pay_status.php
$servername = "localhost";
$username = "root";
$password_db = ""; 
$dbname = "sanitario";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Controlliamo se l'ID è arrivato tramite l'URL (dal fetch del JS)
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Prepariamo la query per aggiornare la colonna 'paid'
        $stmt = $pdo->prepare("UPDATE payment SET paid = 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        echo "successo"; 
    }
} catch (PDOException $e) {
    echo "errore: " . $e->getMessage();
}
?>