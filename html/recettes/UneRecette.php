<?php
/**
 * recettes/UneRecette.php — Single recipe display page.
 * Expects GET parameter: NOMRECETTE (recipe key, case-sensitive after uppercasing).
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Base.lib.php';

require_once included('MiseEnPage.lib.php');
require_once included('BoutonsGeneraux.lib.php');
require_once included('Recettes.lib.php');

// Redirect to 404 if the recipe key is missing or unknown.
if (!isset($_GET['NOMRECETTE'])) {
    header('Location: ' . PHP_SITE . '/errs/Err404.php?BaseError=' . rawurlencode(PHP_FICHIERSCRIPT));
    return;
}
$recipeKey = strtoupper($_GET['NOMRECETTE']);
if (!isset($DescriptionRecettes[$recipeKey . '.RECETTE'])) {
    header('Location: ' . PHP_SITE . '/errs/Err404.php?BaseError=' . rawurlencode(PHP_FICHIERSCRIPT . '?' . $recipeKey));
    return;
}

$title = EstTexteSimple($recipeKey, 'TITRE');
$photo = $DescriptionRecettes[$recipeKey . '.PHOTO'][0] ?? '';

// Printable version: loads print stylesheets.
DebutEnTete('Recettes: ' . $title, EstTexteSimple($recipeKey, 'MOTSCLEFS') . ', Français');
FinEnTete('', 1);
DebutPage('Recette', -1, $BoutonsGeneraux);

$hasIngredients = isset($DescriptionRecettes[$recipeKey . '.INGREDIENT']);
$twoColumns     = $photo && $hasIngredients;
$span           = $twoColumns ? ' COLSPAN=2' : '';

Ligne('<DIV class="Recettes">');
Ligne('<H1>' . $title . '</H1>');
Ligne('<H2>' . EstTexteSimple($recipeKey, 'CATEGORIE') . '</H2>');
Ligne('<P ALIGN=CENTER><TABLE>');

// Quantity row (spans both columns when two-column layout is active).
$quantity = EstTexteSimple($recipeKey, 'QUANTITE');
if ($quantity)
    Ligne('<TR><TD' . $span . '>' . $quantity . '</TD></TR>');

// Ingredients + photo section.
if ($photo || $hasIngredients) {
    if ($hasIngredients)
        Ligne('<TR><TH' . $span . '>Ingrédients</TH></TR>');
    Ligne('<TR>');
    if ($photo)
        Ligne('<TD' . ($twoColumns ? ' width="33%"' : '') . '>'
            . '<P align="center">' . LienImage('', $photo) . '</P></TD>');
    if ($hasIngredients) {
        Ligne('<TD><UL>');
        foreach ($DescriptionRecettes[$recipeKey . '.INGREDIENT'] as $ingredient)
            Ligne('<li>' . ExpandReferences($ingredient) . '</li>');
        Ligne('</UL></TD>');
    }
    Ligne('</TR>');
}

// Preparation steps.
Ligne('<TR><TH' . $span . '>Réalisation</TH></TR>');
Ligne('<TR><TD' . $span . '>' . EstTexte($recipeKey, 'RECETTE') . '</TD></TR>');

// Optional comments section.
if (isset($DescriptionRecettes[$recipeKey . '.COMMENTAIRES'])) {
    Ligne('<TR><TH' . $span . '>Commentaires</TH></TR>');
    Ligne('<TR><TD' . $span . '>' . EstTexte($recipeKey, 'COMMENTAIRES') . '</TD></TR>');
}

// Optional source attribution.
$source = EstTexteSimple($recipeKey, 'SOURCE');
if (isset($DescriptionRecettes[$recipeKey . '.SOURCE']) && $source)
    Ligne('<TR><TH' . $span . ' CLASS="Source"><I>Source : </I>' . $source . '</TH></TR>');

Ligne('</TABLE></P></DIV><BR>');
FinPage();
