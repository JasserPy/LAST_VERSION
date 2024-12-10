<?php
if (!isset($_GET['emails']) && !isset($_GET['id'])) {
    echo "<h3 class='text-danger'>Email ou ID non trouvé.</h3>";
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

$errors = array();

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
        $errors[] = "Veuillez remplir tous les champs.";
    }
    if (!is_numeric($cin) || strlen($cin) != 8) {
        $errors[] = "Le CIN doit être composé de 8 chiffres.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer un email valide.";
    }
    if (strlen($passwordd) < 8) {
        $errors[] = "Le mot de passe doit comporter au moins 8 caractères.";
    }
    if (!ctype_alpha($nom) || !ctype_alpha($prenom)) {
        $errors[] = "Le nom et le prénom doivent contenir uniquement des lettres.";
    }
    if (strtotime($date) === false) {
        $errors[] = "Veuillez entrer une date valide.";
    }
    if ($sexe !== 'M' && $sexe !== 'F') {
        $errors[] = "Veuillez sélectionner un sexe valide.";
    }

    if (empty($errors)) {
        $checkCinQuery = "SELECT * FROM etudiant WHERE cin='$cin' AND id != '$id'";
        $result = $connection->query($checkCinQuery);

        if ($result->num_rows > 0) {
            $errors[] = "Ce CIN est déjà utilisé par un autre étudiant.";
        } else {
            $sql = "UPDATE etudiant 
                    SET cin='$cin', nom='$nom', prenom='$prenom', email='$email', passwordd='$passwordd', sexe='$sexe', datee='$date', classe='$classe' 
                    WHERE id='$id'";
            if ($connection->query($sql)) {
                echo "<h3 class='text-success'>Les modifications ont été enregistrées avec succès.</h3>";
                if (isset($_GET['id'])) {
                    header("Location: ../etudiant/etudiant.php?x=1");
                } else {
                    header("Location: ../etudiant/confirmation_etudiant1.php");
                }
                exit();
            } else {
                $errors[] = "Erreur lors de l'enregistrement : " . $connection->error;
            }
        }
    }
}

$sql = "SELECT * FROM etudiant WHERE email='$email'";
$res = $connection->query($sql);

if ($res->num_rows == 0) {
    $errors[] = "Aucun étudiant trouvé avec cet email.";
} else {
    $row = $res->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inscription ISIMA</title>
        <link rel="stylesheet" href="../styles/style_formulaire1.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <header>
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="../homepage.html">
                <img src="../img/img.png" alt="ISIMA home" class="navbar-logo">
                <h2>Home</h2>
            </a>
        </div>
    </nav>
</header>
    <body class="container py-5">
        <form method="post" class="border p-4 shadow">
            <h1 class="mb-4 text-center">INSCRIPTION ÉTUDIANT</h1>
            <div class="mb-3">
                <label class="form-label">CIN :</label>
                <input type="text" name="cin" id="cin" class="form-control" value="<?php echo $row['cin']; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">ID :</label>
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="text" class="form-control" value="<?php echo $row['id']; ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Nom :</label>
                <input type="text" name="nom" class="form-control" value="<?php echo $row['nom']; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom :</label>
                <input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $row['prenom']; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email :</label>
                <input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de Passe :</label>
                <input type="text" name="passwordd" class="form-control" value="<?php echo $row['passwordd']; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Sexe :</label><br>
                <input type="radio" name="sexe" value="M" id="masculin" <?php echo ($row['sexe'] == 'M') ? 'checked' : ''; ?>>
                <label for="masculin">Masculin</label>
                <input type="radio" name="sexe" value="F" id="feminin" <?php echo ($row['sexe'] == 'F') ? 'checked' : ''; ?>>
                <label for="feminin">Féminin</label>
            </div>
            <div class="mb-3">
                <label class="form-label">Date de Naissance :</label>
                <input type="date" name="date" id="date" class="form-control" value="<?php echo $row['datee']; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Classe :</label>
                <select id="select" name="classe" class="form-select">
                    <option value="LCE" <?php echo ($row['classe'] == 'LCE') ? 'selected' : ''; ?>>LCE</option>
                    <option value="LCS" <?php echo ($row['classe'] == 'LCS') ? 'selected' : ''; ?>>LCS</option>
                    <option value="LBC" <?php echo ($row['classe'] == 'LBC') ? 'selected' : ''; ?>>LBC</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Modifier</button>

        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger mt-4">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        </form>
    </body>
    </html>
    <?php
}
?>
