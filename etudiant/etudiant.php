<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Étudiants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #c9d6ff, #e2e2e2);
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            margin: 20px;
        }

        .btnm {
            background-color: #28a745;
            color: white;
        }

        .btnm:hover {
            background-color: #218838;
        }

        .btns {
            background-color: #dc3545;
            color: white;
        }

        .btns:hover {
            background-color: #c82333;
        }

        table th, table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center my-4">Gestion des Étudiants</h1>
        <form method="post" class="d-flex justify-content-center align-items-center mb-4 gap-3">
            <input type="text" name="cin1" id="cin1" class="form-control w-25" placeholder="CIN d'étudiant">
            <input type="submit" value="Chercher" class="btn btn-primary">
            <input type="submit" name="show_all" value="Afficher tous" class="btn btn-secondary">
        </form>

        <?php
        if (isset($_GET['x'])) {
            echo "<div class='alert alert-success'>Étudiant Modifier avec succès</div>";
        }
        $bdname = "localhost";
        $username = "root";
        $password = "";
        $database = "isima";
        $connection = new mysqli($bdname, $username, $password, $database);

        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];
            $deleteQuery = "DELETE FROM etudiant WHERE id = '$id'";
            if ($connection->query($deleteQuery) === TRUE) {
                echo "<div class='alert alert-success'>Étudiant supprimé avec succès.</div>";
                header("Refresh: 2; url=" . $_SERVER['PHP_SELF']);
            } else {
                echo "<div class='alert alert-danger'>Erreur lors de la suppression de l'étudiant.</div>";
            }
        }

        if (isset($_POST['show_all'])) {
            $sql = "SELECT * FROM etudiant";
        } elseif (isset($_POST['cin1']) && !empty($_POST['cin1'])) {
            $cin1 = htmlspecialchars($_POST['cin1']);
            $sql = "SELECT * FROM etudiant WHERE cin = '$cin1'";
        } else {
            $sql = "SELECT * FROM etudiant";
        }

        $res = $connection->query($sql);

        if (!$res) {
            die("Invalid query: " . $connection->error);
        }

        if ($res->num_rows > 0) {
            echo "<table class='table table-striped table-hover text-center'>
                <thead class='table-dark'>
                    <tr>
                        <th>ID</th>
                        <th>CIN</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Sexe</th>
                        <th>Date de Naissance</th>
                        <th>Classe</th>
                        <th>Opération</th>
                    </tr>
                </thead>
                <tbody>";

            while ($row = $res->fetch_assoc()) {
                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['cin']}</td>
                    <td>{$row['nom']}</td>
                    <td>{$row['prenom']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['sexe']}</td>
                    <td>{$row['datee']}</td>
                    <td>{$row['classe']}</td>
                    <td>
                        <a href='../isima/etudiant1.php?emails={$row['email']}&id={$row['id']}' class='btn btn-success btn-sm'>Modifier</a>
                        <a href='?id={$row['id']}' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet étudiant ?\");' class='btn btn-danger btn-sm'>Supprimer</a>
                    </td>
                </tr>";
            }
            echo "</tbody></table>";
        }
         else {
            echo "<div class='alert alert-warning'>Asucun étudiant trouver.</div>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
