<?php
/**
 * recettes/index.php — Recipe index, grouped by category.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Base.lib.php';

require_once included('MiseEnPage.lib.php');
require_once included('BoutonsGeneraux.lib.php');
require_once included('Recettes.lib.php');

$pageTitle = 'Recettes de cuisine';
DebutEnTete('Recettes: Pour les affamés', 'Recettes, Cuisine, Français');
FinEnTete();
DebutPage($pageTitle, 2, $BoutonsGeneraux);

// Tracks the current category heading to avoid repeating it.
$lastCategory = '';

function AfficherRecette($recipeKey)
{
    global $lastCategory;
    $category = EstTexteSimple($recipeKey, 'CATEGORIE');

    if ($category !== $lastCategory) {
        if ($lastCategory !== '') Ligne('</ul></blockquote>');
        $lastCategory = $category;
        Ligne('<H3 style="text-align:left">' . $category . '</H3>');
        Ligne('<blockquote><ul>');
    }

    Ligne('<li><a href="UneRecette.php?NOMRECETTE=' . $recipeKey . '">'
        . EstTexteSimple($recipeKey, 'TITRE') . '</a></li>');
}

Ligne('<H1>' . $pageTitle . '</H1>');
?>
<p>À la suite de quelques fêtes et soirées, il m'a été demandé
des précisions sur les procédés culinaires relatifs aux
éléments nutritifs apportés lors desdites agapes.<BR>
Les recettes décrites ci-dessous ont toutes été réalisées par mes
soins et ne sont donc pas seulement une compilation de « trucs »
trouvés ici ou là.<BR><BR>
<B>NOTE</B> : Si certaines sont de mon cru, quasiment aucune des autres
n'est d'origine ; sauf mention contraire elles ont toutes été modifiées et
adaptées, et ce même lorsque je précise leur provenance.<BR><BR>
Pour l'instant c'est un peu en vrac mais une refonte est en cours (qui se
finira probablement un jour).</p>
<?php
array_walk($DescriptionRecettes['RECETTE'], 'AfficherRecette');
Ligne('</ul></blockquote>');
FinPage();
