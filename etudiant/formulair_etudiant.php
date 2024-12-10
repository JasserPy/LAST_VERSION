<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    echo "<h3 style='color:red;'>Aucun ID trouvé.</h3>";
    exit();
}

if (isset($_POST['cin'])) {
    $cin = $_POST['cin'];
    $id = $_POST['id'];
    $prenom = $_POST['prenom'];
    $sexe = $_POST['sexe'];
    $datee = $_POST['date'];
    $classe = $_POST['classe'];

    // Validation of form fields
    if (empty($cin) || empty($id) || empty($prenom) || empty($sexe) || empty($datee) || empty($classe)) {
        echo "<h4><span style='color:red;'>Veuillez remplir tous les champs.</span></h4>";
    } else {
        // Establishing database connection
        $bdname = "localhost";
        $username = "root";
        $password = "";
        $database = "isima";

        $connection = new mysqli($bdname, $username, $password, $database);
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Check if CIN already exists in the database
        $checkCinQuery = "SELECT * FROM etudiant WHERE cin='$cin' AND id != '$id'";
        $result = $connection->query($checkCinQuery);

        if ($result->num_rows > 0) {
            echo "<h4><span style='color:red;'>Ce CIN est déjà utilisé par un autre étudiant.</span></h4>";
        } else {
            // Update the student's information
            $sql = "UPDATE etudiant SET cin='$cin', prenom='$prenom', sexe='$sexe', datee='$datee', classe='$classe' WHERE id='$id'";

            if ($connection->query($sql)) {
                echo "<h4 style='color:green;'>Les modifications ont été enregistrées avec succès.</h4>";
                header("Location: confirmation_etudiant.php");
                exit();
            } else {
                echo "<h4><span style='color:red;'>Erreur lors de l'enregistrement : " . $connection->error . "</span></h4>";
            }
        }
    }
}
?>

<HTML>
<HEAD>
    <TITLE>Inscription ISIMA</TITLE>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles/style_formulaire.css">
</HEAD>
<BODY>
    <form method="post">
        <div>
            <h1>Inscription Étudiant</h1>
            <label>CIN :</label>
            <input type="text" name="cin" id="cin" value=""><br>
            <label>ID :</label>
            <input type="text" name="id" value="<?php echo $id; ?>"><br>
            <label>PRÉNOM :</label>
            <input type="text" name="prenom" id="prenom" value=""><br>
            <label>SEXE :</label><br>
            <div class="tet">
                <input type="radio" name="sexe" value="M" id="masculin"><label for="masculin">Masculin</label><br>
                <input type="radio" name="sexe" value="F" id="feminin"><label for="feminin">Féminin</label><br>
            </div>
            <label>DATE de Naissance :</label>
            <input type="date" name="date" id="date" value=""><br>
            <label>CLASSE :</label>
            <select id="select" name="classe">
                <option value="null" selected>Null</option>
                <option value="LCE">LCE</option>
                <option value="LCS">LCS</option>
                <option value="LBC">LBC</option>
            </select><br>
            <input type="submit" value="Enregistrer">
            <input type="reset" value="Clear">
        </div>
    </form>
</BODY>
</HTML>
