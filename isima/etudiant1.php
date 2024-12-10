<?php
if (!isset($_GET['emails']) && !isset($_GET['id'])) {
    echo "<h3 style='color:red;'>Email ou ID non trouvé.</h3>";
    exit();
}

$email = $_GET['emails'];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$hostname = "localhost";
$username = "root";
$password = "";
$bdname = "isima";
$connection = new mysqli($hostname, $username, $password, $bdname);

if ($connection->connect_error) {
    die("Échec de connexion : " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin = $_POST['cin'];
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $passwordd = $_POST['passwordd'];
    $sexe = $_POST['sexe'];
    $date = $_POST['date'];
    $classe = $_POST['classe'];

    if (empty($cin) || empty($id) || empty($nom) || empty($prenom) || empty($email) || empty($passwordd) || empty($sexe) || empty($date) || $classe === "null") {
        echo "<h3 style='color:red;'>Veuillez remplir tous les champs.</h3>";
    } elseif (!is_numeric($cin) || strlen($cin) != 8) {
        echo "<h3 style='color:red;'>Le CIN doit être composé de 8 chiffres.</h3>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<h3 style='color:red;'>Veuillez entrer un email valide.</h3>";
    } elseif (strlen($passwordd) < 6) {
        echo "<h3 style='color:red;'>Le mot de passe doit comporter au moins 6 caractères.</h3>";
    } elseif (!ctype_alpha($nom) || !ctype_alpha($prenom)) {
        echo "<h3 style='color:red;'>Le nom et le prénom doivent contenir uniquement des lettres.</h3>";
    } elseif (strtotime($date) === false) {
        echo "<h3 style='color:red;'>Veuillez entrer une date valide.</h3>";
    } elseif ($sexe !== 'M' && $sexe !== 'F') {
        echo "<h3 style='color:red;'>Veuillez sélectionner un sexe valide.</h3>";
    } else {
        $checkCinQuery = "SELECT * FROM etudiant WHERE cin='$cin' AND id != '$id'";
        $result = $connection->query($checkCinQuery);

        if ($result->num_rows > 0) {
            echo "<h3 style='color:red;'>Ce CIN est déjà utilisé par un autre étudiant.</h3>";
        } else {
            $sql = "UPDATE etudiant 
                    SET cin='$cin', nom='$nom', prenom='$prenom', email='$email', passwordd='$passwordd', sexe='$sexe', datee='$date', classe='$classe' 
                    WHERE id='$id'";
            if ($connection->query($sql)) {
                echo "<h3 style='color:green;'>Les modifications ont été enregistrées avec succès.</h3>";
                if (isset($_GET['id'])) {
                    header("Location: ../etudiant/etudiant.php?x=1");
                } else {
                    header("Location: ../etudiant/confirmation_etudiant1.php");
                }
                exit();
            } else {
                echo "<h3 style='color:red;'>Erreur lors de l'enregistrement : " . $connection->error . "</h3>";
            }
        }
    }
}

$sql = "SELECT * FROM etudiant WHERE email='$email'";
$res = $connection->query($sql);

if ($res->num_rows == 0) {
    echo "<h3 style='color:red;'>Aucun étudiant trouvé avec cet email.</h3>";
} else {
    $row = $res->fetch_assoc();
    ?>
    <HTML>
    <HEAD>
        <TITLE>Inscription ISIMA</TITLE>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../styles/style_formulaire1.css">
    </HEAD>
    <BODY>
        <form method="post">
            <div>
                <h1>INSCRIPTION ÉTUDIANT</h1>
                <label>CIN :</label>
                <input type="text" name="cin" id="cin" value="<?php echo $row['cin']; ?>"><br>
                <label>ID :</label>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="text" value="<?php echo $row['id']; ?>" disabled><br>
                <label>NOM :</label>
                <input type="text" name="nom" value="<?php echo $row['nom']; ?>"><br>
                <label>PRÉNOM :</label>
                <input type="text" name="prenom" id="prenom" value="<?php echo $row['prenom']; ?>"><br>
                <label>EMAIL :</label>
                <input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
                <label>PASSWORD :</label>
                <input type="text" name="passwordd" value="<?php echo $row['passwordd']; ?>"><br>
                <label>SEXE :</label><br>
                <input type="radio" name="sexe" value="M" id="masculin" <?php echo ($row['sexe'] == 'M') ? 'checked' : ''; ?>>
                <label for="masculin">Masculin</label><br>
                <input type="radio" name="sexe" value="F" id="feminin" <?php echo ($row['sexe'] == 'F') ? 'checked' : ''; ?>>
                <label for="feminin">Féminin</label><br>
                <label>DATE de Naissance :</label>
                <input type="date" name="date" id="date" value="<?php echo $row['datee']; ?>"><br>
                <label>CLASSE :</label>
                <select id="select" name="classe">
                    <option value="LCE" <?php echo ($row['classe'] == 'LCE') ? 'selected' : ''; ?>>LCE</option>
                    <option value="LCS" <?php echo ($row['classe'] == 'LCS') ? 'selected' : ''; ?>>LCS</option>
                    <option value="LBC" <?php echo ($row['classe'] == 'LBC') ? 'selected' : ''; ?>>LBC</option>
                </select><br>
                <button type="submit" class="btnm">Modifier</button><br>
            </div>
        </form>
    </BODY>
    </HTML>
    <?php
}
?>
