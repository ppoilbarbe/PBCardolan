<?php
/**
 * errs/Err401.php — 401 Unauthorized error page.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Base.lib.php';

require_once included('MiseEnPage.lib.php');
require_once included('BoutonsGeneraux.lib.php');

DebutEnTete('Erreur 401', '');
FinEnTete();
DebutPage('ERREUR', -1, $BoutonsGeneraux);
Titre('Erreur 401');
if (isset($BaseError)) SousTitre($BaseError);
Ligne('<BR><BR><BR>');
SousTitre(
    "Ce serveur n'a pu vérifier si vous êtes autorisé à accéder au document demandé.<BR>"
    . "Soit vous n'avez pas donné les bons droits (par exemple un mauvais mot de passe) "
    . "soit votre butineur ne sait pas comment fournir les éléments nécessaires à l'authentification."
);
Ligne('<BR><BR><BR>');
FinPage();
