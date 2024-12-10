<?php
$bdname = "localhost";
$username = "root";
$password = "";
$database = "isima";
$connection = new mysqli($bdname, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
if (isset($_GET['id'])) {
    $id =$_GET['id'];
    $sql = "DELETE FROM etudiant WHERE id='$id'";
    if ($connection->query($sql)) {
        echo "<h1 style='color:green;'>Étudiant supprimé avec succès !</h1>";
        header("Location:../etudiant/etudiant.php");
        exit();
    } else {
        echo "Erreur : " . $connection->error;
    }
} else {
    echo "<h1 style='color:red;'>ID invalide</h1>";
}
?>
