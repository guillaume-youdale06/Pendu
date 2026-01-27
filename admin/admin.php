<?php

session_start();
$lstMot = file('../mots/mots.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if(isset($_POST["jeu"])) {
    if (isset($_SESSION["erreur"])) {
        unset($_SESSION["erreur"]);
    }
    header("Location: ../index.php");
    exit;
}

if(isset($_POST["mot_nouv"])) {
    $mot_nouv = strtolower(trim($_POST["mot_nouv"]));

    //vérifie si le nouveau mot est vide
    if(trim($mot_nouv) == "") {
        $_SESSION["erreur"] = "Le nouveau mot ne peut pas être vide";
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    }

    //vérifie si le nouveau mot contient 1 chiffre
    if(preg_match('/\d/', $mot_nouv)) {
        $_SESSION["erreur"] = "Le nouveau mot ne peut pas contenir de chiffres";
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    }

    foreach($lstMot as $mot) {
        if ($mot_nouv == $mot) {
            $_SESSION["erreur"] = "Le nouveau mot existe déjà dans la BDD";
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit;
        }
    }

    //ajout du mot dans le fichiers txt
    file_put_contents(
        '../mots/mots.txt',
        $mot_nouv . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
</head>
<body>
    <?php
    
    foreach($lstMot as $mot) {
        echo $mot . "<br>";
    }

    ?>

    <br>
    <form action="" method="post">
        <label for="mot_nouv">Entrez un nouveau mot</label>
        <input type="text" name="mot_nouv" placeholder="Nouveau mot...">
        <?php
        if (isset($_SESSION["erreur"])) {
            echo $_SESSION["erreur"];
            unset($_SESSION["erreur"]);
        }
        ?>
        <button type="submit" name="envoyer">Valider</button>
        <button type="submit" name="jeu">Retour au jeu</button>
    </form>
    
</body>
</html>