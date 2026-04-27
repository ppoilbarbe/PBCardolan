<?php
/**
 * errs/Err404.php — 404 Not Found error page.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Base.lib.php';

require_once included('MiseEnPage.lib.php');
require_once included('BoutonsGeneraux.lib.php');

DebutEnTete('Erreur 404', '');
FinEnTete();
DebutPage('ERREUR', -1, $BoutonsGeneraux);
Titre('Erreur 404');
if (isset($BaseError)) SousTitre('<em>' . $BaseError . '</em>');
Ligne('<BR><BR><BR>');
SousTitre(
    "Vous avez essayé d'accéder à une ressource inexistante.<BR>"
    . "Ou bien j'ai malencontreusement brisé un lien "
    . '(merci de me le <a href="mailto:webmaster@cardolan.net">signaler</a>).'
);
Ligne('<BR><BR><BR>');
FinPage();
