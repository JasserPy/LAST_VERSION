<?php
if(isset($_POST['id'])){
    
                $id=$_POST['id'];
                $email=$_POST['email'];
                $password=$_POST['password'];
                if (empty($email) || empty($id) || empty($password)) {
                    echo "<h4><span style=color:red;>Please fill in all the fields.</span></h4>";
                }
                else{
                $mysqli = new mysqli("localhost", "root", "", "isima");
                $mysqli -> set_charset("utf8");
                if($mysqli->connect_error){
                die("connection failed: ". $mysqli->connect_error);
                }
            }
        }
            ?>
<HTML>

<HEAD>
    <TITLE>inscription d'un visteur </TITLE>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles/style_formulaire1.css">
</HEAD>

<BODY>
    <form method="post">
        <h1>BIENVENU </h1>
        <label>ID: <span>*</span></label>
        <input type="text" name="id" value="">
        <label>Email: <span>*</span></label>
        <input type="email" name="email" value="">
        <label>Mot de passe: <span>*</span></label>
        <input type="password" name="password" value="">
        <button type="submit">Envoyer</button>
        <?php
            if(isset($_POST['id'])){
                if (empty($email) || empty($id) || empty($password)) {
                    echo "<h4><span style=color:red;>Please fill in all the fields.</span></h4>";
                }
                else{
                    $sql = "SELECT id, email, password FROM prof WHERE id = ? AND email = ? AND password = ?";
                    $stmt = $mysqli->prepare($sql);
                    if ($stmt === false) {
                        echo "<h4 style='color:red;'>Error preparing the statement: " . $mysqli->error . "</h4>";
                        exit;
                    }
                    $stmt->bind_param("sss", $id, $email, $password);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($res->num_rows > 0) {
                        header("Location:../etudiant/etudiant.php");
                        exit();
                    } else {
                        echo "<h5 style='color:red;'>Please verify your ID, email, or password.</h5>";
                    }
                
                    $stmt->close();
                }
            }
        ?>
    </form>
</BODY>

</HTML>