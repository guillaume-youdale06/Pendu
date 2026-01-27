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

if(isset($_POST["admin"])) {
   session_unset();
   session_destroy();
   header("Location: admin/admin.php");
   exit;
}

//vérifie si le mot à trouver est initialisé et qu'il n'est pas vide
if(isset($_POST["motValide"]) && trim($_POST["motValide"]) != ""){
   $_SESSION["motValide"] = $_POST["motValide"];
   header("Location: " . $_SERVER["PHP_SELF"]);
   exit;
}

//vérifie si le mot de l'utilisateur est initialisé
if(isset($_POST["motUser"])){
   $_SESSION["motUser"] = $_POST["motUser"];
   header("Location: " . $_SERVER["PHP_SELF"]);
   exit;
}

//vérifie si l'utilisateur souhaite un mot aléatoire
if(isset($_POST["motAlea"])){
   $_SESSION["motValide"] = $lstMot[array_rand($lstMot)];
   header("Location: " . $_SERVER["PHP_SELF"]);
   exit;
}

if(!isset($_SESSION["compt"])) {
   $_SESSION["compt"] = 0;
}

//vérifie si la session de motUser et de motValide est initialisé
if(isset($_SESSION["motUser"]) && isset($_SESSION["motValide"])){

   //stockage de la valeur de session qui permet de savoir si un utilisateur à trouver le mot
   $_SESSION["motTrouve"] = false;

   //stockage des valeures de session
   $motValide = trim(strtolower($_SESSION["motValide"]));
   $motUser = trim(strtolower($_SESSION["motUser"]));
   
   //stockage de la longueur des chaînes
   $motValideLong = strlen($motValide);
   $motUserLong = strlen($motUser);

   //variable qui affiche le mot en train d'être trouvé
   $affiche = "";

   //si le mot de l'utilisateur ne contient qu'un seul caractère
   if($motUserLong == 1){

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
   if($motUserLong > 1){

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
   <title>pendu</title>
</head>
<body>
   <?php

   //le mot à été trouvé !
   if(isset($_SESSION["motTrouve"]) && $_SESSION["motTrouve"] == true) {
      echo "Félicitation vous avez trouvé le mot " . "<strong>$motValide</strong><br><br>
           <form action='' method='post'>
           <button type='submit' name='reset'>Rejouer !</button>
           </form>";
           unset($_SESSION["motTrouve"]);
           unset($_SESSION["compt"]);
   }

   //le mot à trouver n'est pas initialisé
   if(!isset($_SESSION["motValide"])) {
      echo "<form action='' method='post'>
            <input type='text' name='motValide' placeholder='Votre mot...'>
            <button type='submit'>Valider</button><br>
            <button type='submit' name='motAlea'>Mot aléatoire</button><br><br>
            <button type='submit' name='reset'>Reset</button>
            </form>";
   }

   //le mot à trouver est initialisé et le premier mot de l'utilisateur ne l'est pas
   if(isset($_SESSION["motValide"]) && !isset($_SESSION["motUser"])){
      echo  "<form action='' method='post'>
            <input type='text' name='motUser' placeholder='A vous de jouer !'>
            <button type='submit'>Valider</button><br><br>
            <button type='submit' name='reset'>Reset</button>
            </form>";
      echo "Nombre d'essais : " . $_SESSION["compt"];
   }

   //le mot à trouver est initialisé et l'utilisateur à rentrer au moins une valeure
   if(isset($_SESSION["motValide"]) && isset($_SESSION["motUser"]) && (isset($_SESSION["motTrouve"]) && !$_SESSION["motTrouve"])) {
      echo $affiche . "<br>";
      echo "<br><form action='' method='post'>
            <input type='text' name='motUser' placeholder='A vous de jouer !'>
            <button type='submit'>Valider</button><br><br>
            <button type='submit' name='reset'>Reset</button>
            </form>";
      echo "Nombre d'essais : " . $_SESSION["compt"];
   }
   ?>
   <br>
   <form action="" method="post">
   <button type="submit" name="admin">Admin</button>
   </form>
</body>
</html>