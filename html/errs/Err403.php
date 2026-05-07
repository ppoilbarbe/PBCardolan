<?php
/**
 * errs/Err403.php — 403 Forbidden error page.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Site.lib.php';

do_header('Erreur 403');
?>
<body>
<?php do_page_open('ERREUR', -1); ?>

<?php
Titre('Erreur 403');
if (isset($BaseError)) SousTitre($BaseError);
Ligne('<BR><BR><BR>');
SousTitre("Vous avez essayé d'accéder à une ressource non autorisée.");
Ligne('<BR><BR><BR>');

do_footer();
?>
</body>
</html>
