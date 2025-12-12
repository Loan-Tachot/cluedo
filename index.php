<?php
session_start();


$db = new SQLite3('cluedo.db');

// Récupération du couloir de départ
$depart = $db->querySingle("SELECT id_piece FROM piece WHERE nom_piece='Hall'");
if (!$depart) {
    die("Erreur : Aucune piece nommé 'Hall' trouvé.");
}

echo "Choix des personnage : \n 1	Mademoiselle Rose \n 2	Colonel Moutarde \n 3	Professeur Violet \n 4	Madame Leblanc \n 5	Monsieur Olive \n 6	Docteur Pervenche  ";

// Réinitialisation de la session au démarrage d'une partie
$_SESSION = []; // réinitialise toute la session
$_SESSION['personage']; 
$_SESSION['question?'] = false;
$_SESSION['armecrime'] = rand(1,6);
$_SESSION['criminel'] = rand(1,6);
$_SESSION['piece du meutres'] = rand(1,8);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Cluedo</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Bienvenue dans le Cluedo</h1>
<p>Votre but est de decouvrir le meutrier l'arme et la salle du meutre.</p> 
<p>Cliquez sur le bouton correspondant à la salle dans laquelle vous souhaitez aller.</p>
<p>Bonne Chance !</p>
<p>Cliquez pour commencer :</p>
<a href="jeu.php?id=<?= $depart ?>">Commencer la partie</a>
</body>
</html>


<?php
