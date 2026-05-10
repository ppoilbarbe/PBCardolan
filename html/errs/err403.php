<?php
/**
 * err403.php — 403 Forbidden error page.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';

do_header('Erreur 403');
?>
<body>
<?php do_page_open('ERREUR', -1); ?>

<?php
h1('Erreur 403');
if (isset($BaseError)) h2($BaseError);
writeLine('<BR><BR><BR>');
h2("Vous avez essayé d'accéder à une ressource non autorisée.");
writeLine('<BR><BR><BR>');

do_footer();
?>
</body>
</html>
