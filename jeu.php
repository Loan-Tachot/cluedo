<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();
$db = new SQLite3('cluedo.db');

// Initialisation des variables de session

$id = isset($_GET['id_piece']) ? intval($_GET['id_piece']) : $db->querySingle("SELECT id_piece FROM piece WHERE nom_piece='Hall'");


// Récupération du couloir
$piece = $db->querySingle("SELECT * FROM piece WHERE id_piece=$id", true);
if (!$piece) die("Erreur : piece introuvable.");

// Récupération du nom de la piece 

$piece_nom = $db->querySingle("SELECT nom_piece FROM piece WHERE id_piece=$id", true);
if (!$piece_nom) die("Erreur : piece introuvable.");

// Récupération des passages
$passages = $db->query("SELECT * FROM portes WHERE id_piece1=$id OR id_piece2=$id");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Piece <?= $id ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>



<?php 
echo "<h1> $piece_nom </h1>"  
?>

<!DOCTYPE html>
<div class="infos">
    <p>🔑 Clés : <strong><?= $_SESSION['cles'] ?></strong></p>
    <p>🚶 Déplacements : <strong><?= $_SESSION['deplacements'] ?></strong></p>
    <p>🧭 Orientation : <strong><?= $_SESSION['orientation'] ?></strong></p>
</div>

<?php if ($message): ?>
<p class="message"><?= $message ?></p>
<?php endif; ?>

<h2>Passages disponibles :</h2>
<ul>
<?php
while ($p = $passages->fetchArray(SQLITE3_ASSOC)) {

    // Détermination du couloir suivant et de la position absolue
    if ($p['couloir1'] == $id) {
        $prochain = $p['couloir2'];
        $posAbsolue = $p['position2'];
    } else {
        $prochain = $p['couloir1'];
        $posAbsolue = $p['position1'];
    }

    // Direction relative
    $dirRel = directionRelative($_SESSION['orientation'], $posAbsolue);

    // Cas : passage libre ou secret
    if ($p['type'] === 'libre' || $p['type'] === 'secret') {
        $lien = "jeu.php?id=$prochain&from=$id";
        echo "<li><a href='$lien'>$dirRel</a></li>";
    }

    // Cas : grille
    else if ($p['type'] === 'grille') {

    // Initialisation de la session si nécessaire
    if (!isset($_SESSION['grille_ouverte'])) {
        $_SESSION['grille_ouverte'] = [];
    }

    // Vérifie si la grille est déjà ouverte
    if (!in_array($p['couloir1'], $_SESSION['grille_ouverte'])) {

        // Le joueur a au moins une clé
        if (isset($_SESSION['cles']) && $_SESSION['cles'] > 0) {
            $lien = "ouvrir.php?id=$id&vers=$prochain&from=$id";
            echo "<li><a href='$lien'>$dirRel (1 clé)</a></li>";
        } 
        // Pas de clé
        else {
            echo "<li>$dirRel ❌ (clé requise)</li>";
        }

    } 
    // Si la grille est déjà ouverte
    else {
        $lien = "jeu.php?id=$prochain&from=$id";
        echo "<li><a href='$lien'>$dirRel</a></li>";
    }
}
}
?>
</ul>
</body>
</html>