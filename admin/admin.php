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

//vérifie si le mot existe déjà dans le fichier de mots --> suppression du mot
    if(isset($_POST["mot_suppr"])) {

        $mot_suppr = strtolower(trim($_POST["mot_suppr"]));

        $mots = file('../mots/mots.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $mots = array_filter($mots, function($mot) use ($mot_suppr) {
            return strtolower(trim($mot)) !== $mot_suppr;
        });

        file_put_contents(
            '../mots/mots.txt',
            implode(PHP_EOL, $mots) . PHP_EOL
        );

        header("Location: " . $_SERVER["PHP_SELF"]);
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

    //vérifie si le mot existe déjà dans le fichier de mots
    foreach($lstMot as $mot) {
        if ($mot_nouv == $mot){
            $_SESSION["erreur"] = "Le mot existe déjà dans le fichier !";
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
    <link rel="stylesheet" href="../CSS/style.css">
    <title>Administration</title>
</head>
<body>
    <h1>Jeu du Pendu !</h1>
    <div class="container-jeu">
    <img src="../img/vache.png" alt="" id="vache">
    <?php
    echo "<div class='container-mots'>";
    
    echo "<p>Liste des mots déjà présents : </p>";
    foreach($lstMot as $mot) {
        echo "<form method='post' style='display:inline'>";
        echo $mot . " ";
        echo "<button type='submit' name='mot_suppr' value='$mot' class='btn-supp'>🗑️</button>";
        echo "</form><br>";
    }

    echo "</div>";
    ?>

    <br>

    <div class="container-ajout-mot">
        <form action="" method="post">
            <label for="mot_nouv">Entrez un nouveau mot : </label>
            <input type="text" name="mot_nouv" placeholder="Nouveau mot...">
            <?php
            if (isset($_SESSION["erreur"])) {
                echo "<div class='message-erreur'>" . $_SESSION["erreur"] . "</div>";
                unset($_SESSION["erreur"]);
            }
            ?>

            <div class="btn-row">
                <button type="submit" name="envoyer">Valider</button>
                <button type="submit" name="jeu">Retour au jeu</button>
            </div>
        </form>
    </div>
    <img src="../img/vache.png" alt="" id="vache">
    </div>
    
</body>
</html>