<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webp2_07";


$kunden_name = $_POST['name'];
$kunden_email = $_POST['email'];
// Hash Algorithmus für Passwörter
$kunden_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

echo "<table style='border: solid 1px black;'>";
echo "<tr><th>id</th><th>name</th><th>email</th><th>password</th></tr>";

class TableRows extends RecursiveIteratorIterator {
    
    function __construct($it) {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    function current() {
        return "<td style='width: 150px; border: 1px solid black'>".parent::current()."</td>";
    }

    function beginChildren() {
        echo "<tr>";
    }

    function endChildren() {
        echo "</tr>"."\n";
    }
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Verbindung erfolgreich! ";
    // Einfache Hochkommata sind notwendig, sonst droht Leid
    $insert = "INSERT INTO kunden (name, email, password) VALUES ('$kunden_name', '$kunden_email', '$kunden_password');";
    // try-Block innerhalb des try-Blocks
    try {
        // use exec because no results are returned
        $conn->exec($insert);
        echo "Neuer Eintrag hinzugefügt! ";
    }
    catch (PDOException $e) {
        echo $insert."<br>".$e->getMessage();
    }
} catch (PDOException $e) {
    echo "Verbindung fehlgeschlagen";
}
try {
    $select = $conn->prepare("SELECT * FROM kunden;");
    $select->execute();
    $result = $select->setFetchMode(PDO::FETCH_ASSOC);
    foreach(new TableRows(new RecursiveArrayIterator($select->fetchAll())) as $k=>$v) {
        echo $v;
    }
    
} catch (PDOException $e) {
    echo $select."<br>".$e->getMessage();
}
$conn = null;
echo "</table>";