<?php

session_start();

$lstMot = file('mots/mots.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

//réinitialise le jeu 
if(isset($_POST["reset"])){
   session_unset();
   session_destroy();
   header("Location: " . $_SERVER["PHP_SELF"]);
   exit;
}

//vérifie si l'utilisateur souhaite un mot aléatoire
if(isset($_POST["motAlea"])){
   $_SESSION["motValide"] = $lstMot[array_rand($lstMot)];
   header("Location: " . $_SERVER["PHP_SELF"]);
   exit;
}

//Si l'utilisateur veut se rendre sur la page Admin
if(isset($_POST["admin"])) {
   session_unset();
   session_destroy();
   header("Location: admin/admin.php");
   exit;
}

//vérifie si le mot à trouvé est initilisé puis vérifie si il ne contient pas de chiffre et qu'il ne soit pas vide
if(isset($_POST["motValide"])) {
   if (trim($_POST["motValide"]) == "") {
      $_SESSION["erreurMOTvalide"] = "Le mot à trouver ne peut pas être vide !";
      header("Location: " . $_SERVER["PHP_SELF"]);
      exit;
   }
   
   if(preg_match('/\d/', $_POST["motValide"])) {
      $_SESSION["erreurMOTvalide"] = "Le mot à trouver ne peut pas contenir de chiffres !";
      header("Location: " . $_SERVER["PHP_SELF"]);
      exit;
   }

   else {
      $_SESSION["motValide"] = $_POST["motValide"];
      header("Location: " . $_SERVER["PHP_SELF"]);
      exit;
   }
}

//vérifie si le mot de l'utilisateur est initialisé
if(isset($_POST["motUser"])){
   $_SESSION["motUser"] = $_POST["motUser"];
   header("Location: " . $_SERVER["PHP_SELF"]);
   exit;
}

//initialise le compteur de coups
if(!isset($_SESSION["compt"])) {
   $_SESSION["compt"] = 0;
}

//initialise la liste des lettres rentrés par l'utilisateur
if (!isset($_SESSION["lstLettre"])) {
   $_SESSION["lstLettre"] = [];
}

//vérifie si la session de motUser et de motValide est initialisé
if(isset($_SESSION["motUser"]) && isset($_SESSION["motValide"])) {

   //stockage de la valeur de session qui permet de savoir si un utilisateur à trouver le mot
   if (!isset($_SESSION["motTrouve"])) {
      $_SESSION["motTrouve"] = false;
   }

   //stockage des valeures de session
   $motValide = trim(strtolower($_SESSION["motValide"]));
   $motUser = trim(strtolower($_SESSION["motUser"]));
   
   //stockage de la longueur des chaînes
   $motValideLong = strlen($motValide);
   $motUserLong = strlen($motUser);

   //variable qui affiche le mot en train d'être trouvé
   $affiche = "";

   //variable qui affiche les lettres déjà rentré par l'utilisateur
   $afficheLettre = "";

   //si le mot de l'utilisateur ne contient qu'un seul caractère
   if($motUserLong == 1){

      if (in_array($motUser, $_SESSION["lstLettre"])) {
         $_SESSION["erreurLETTRE"] = "Vous avez déjà rentré cette lettre !";
      } else {
         $_SESSION["lstLettre"][] = $motUser;
      }
      for($i=0; $i < $motValideLong; $i++){
      
         if (isset($_SESSION["lettre_$i"])){
            $affiche .= $_SESSION["lettre_$i"] . " ";
         }

         elseif($motValide[$i] == $motUser) {
            $affiche .= $motUser . " ";
            $_SESSION["lettre_$i"] = $motUser;

         } else {
            $affiche .= "_ ";
         }
      }
   }

   //si le mot de l'utilisateur contient plusieurs caractères
   else {

      for($i=0; $i < $motValideLong; $i++){

         //affiche les lettres déjà trouvé du mot
         if (isset($_SESSION["lettre_$i"])){
            $affiche .= $_SESSION["lettre_$i"] . " ";
         } else {
            $affiche .= "_ ";
         }
      }
   }

   //Si le mot de l'utilisateur est égale au mot valide
   if ($motUser == $motValide) {
      for ($i = 0; $i < $motValideLong; $i++) {
         $_SESSION["lettre_$i"] = $motValide[$i];
      }
      $_SESSION["motTrouve"] = true;
   }
   $_SESSION["compt"] += 1;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="CSS/style.css">
   <title>Pendu</title>
</head>
<body>

   <h1>Jeu du Pendu !</h1>
   <div class="container-jeu">
   <img src="img/vache.png" alt="" id="vache">
   <div class="container">
   <?php

   //le nombre d'essais est trop élevé
   if((isset($_SESSION["compt"]) && $_SESSION["compt"] > 7) && (isset($_SESSION["motTrouve"]) && !$_SESSION["motTrouve"])) {
      echo "<div class='container-affiche'><p>Perdu ! le mot était : " . "<strong>$motValide</strong></p></div>
            <img src='img/tete-de-mort.png' alt='' id='img-tete'>

            <form action='' method='post'>
            <button type='submit' name='reset' class='btn-reset'>Rejouer</button>
            </form>";
           unset($_SESSION["motTrouve"]);
           unset($_SESSION["compt"]);
           unset($_SESSION["lstLettre"]);
   }

   //le mot à été trouvé !
   if(isset($_SESSION["motTrouve"]) && $_SESSION["motTrouve"] == true) {
      echo "<div class='container-affiche'>Félicitation vous avez trouvé le mot " . "<strong>$motValide</strong></div>
            <img src='img/emojis-content.png' alt='' id='img-tete'>
            <form action='' method='post'>
            <button type='submit' name='reset' class='btn-reset'>Rejouer</button>
            </form>";

      unset($_SESSION["motTrouve"]);
      unset($_SESSION["compt"]);
      unset($_SESSION["lstLettre"]);
   }

   //le mot à trouver n'est pas initialisé
   if(!isset($_SESSION["motValide"])) {
      echo "<form action='' method='post'>
            <input type='text' name='motValide' placeholder='Votre mot...'>
            <div class='btn-row'><button type='submit'>Valider</button>
            <button type='submit' name='motAlea'>Mot aléatoire</button></div>";

      echo "<p>Vous pouvez soit entrer un mot (non vide et sans chiffres), ou bien en choisir un parmis ceux déjà enregistré en cliquant sur le bouton 'Mot aléatoire'</p>";
      echo "<p>Vous avez 7 chances !";
      echo "<img src='img/etat-pendu.png' alt='' id='img-pendu'>";
      if (isset($_SESSION["erreurMOTvalide"])) {
         echo "<div class='message-erreur'>" . $_SESSION["erreurMOTvalide"] . "</div>";
         unset($_SESSION["erreurMOTvalide"]);
      }

      echo "<button type='submit' name='reset' class='btn-reset'>Reset</button>
            </form>";
   }

   //le mot à trouver est initialisé et le premier mot de l'utilisateur ne l'est pas
   if(isset($_SESSION["motValide"]) && !isset($_SESSION["motUser"])){
      echo  "<form action='' method='post'>
            <input type='text' name='motUser' placeholder='A vous de jouer !'>
            <button type='submit'>Valider</button>
            <button type='submit' name='reset' class='btn-reset'>Reset</button>
            </form>";

      echo "<p>Entrez une lettre pour l'essayer dans le mot recherché, entrez le mot si vous pensez l'avoir deviner</p>";
      echo "<div class='nbr-essais'>Nombre d'essais : " . $_SESSION["compt"] . "</div>";
      unset($_SESSION["motUser"]);
   }

   //le mot à trouver est initialisé et l'utilisateur à rentrer au moins une valeure
   if(isset($_SESSION["motValide"]) && isset($_SESSION["motUser"]) && (isset($_SESSION["motTrouve"]) && !$_SESSION["motTrouve"])) {
      echo "<div class='container-affiche'><p>" . $affiche;
      if (isset($_SESSION["lstLettre"])) {
         echo "| ";
         foreach($_SESSION["lstLettre"] as $lettre) {
            echo $lettre;
         }
      }
      echo "</p></div>";

      echo "<form action='' method='post'>
            <input type='text' name='motUser' placeholder='A vous de jouer !'>
            <button type='submit'>Valider</button>";

      echo "<p>Entrez une lettre pour l'essayer dans le mot recherché, entrez le mot si vous pensez l'avoir deviner</p>";
      
      if (isset($_SESSION["erreurLETTRE"])) {
         echo "<div class=message-erreur>";
         echo $_SESSION["erreurLETTRE"];
         unset($_SESSION["erreurLETTRE"]);
         echo "</div>";
      }
      
      echo "<button type='submit' name='reset' class='btn-reset'>Reset</button>
            </form>";
      echo "<div class='nbr-essais'>Nombre d'essais : " . $_SESSION["compt"] . "</div>";
   }
   ?>

   <form action="" method="post">
      <button type="submit" name="admin" class="btn-admin">Admin</button>
   </form>

   </div>
   <?php

   //vérifie le nombre d'essais de l'utilisateur
   if (isset($_SESSION["compt"]) && ($_SESSION["compt"] > 0 && $_SESSION["compt"] < 8) ) {
      echo "<div class='container-img'><img src='img/" . $_SESSION["compt"] . ".png' alt=''>";
      if ($_SESSION["compt"] == 1) {
         echo "<p>Première erreur !</p>";
      }
      if ($_SESSION["compt"] == 2) {
         echo "<p>Deuxième erreur !</p>";
      }
      if ($_SESSION["compt"] == 3) {
         echo "<p>Troisième erreur...</p>";
      }
      if ($_SESSION["compt"] == 4) {
         echo "<p>Tu veux vraiment que ce petit bonhomme meurt !?</p>";
      }
      if ($_SESSION["compt"] == 5) {
         echo "<p>MAIS TROUVES LE MOT IL VA MOURRIR !</p>";
      }
      if ($_SESSION["compt"] == 6) {
         echo "<p>Plus que deux chances...sans coeur.</p>";
      }
      if ($_SESSION["compt"] == 7) {
         echo "<p>c'est sans espoir désormais... vois-tu ce que tu as fait ?</p>";
      }
      echo "</div>";
   }
   ?>
   <img src="img/vache.png" alt="" id="vache">
   </div>

   
</body>
</html>