<?php
/**
 * err401.php — 401 Unauthorized error page.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';

do_header('Erreur 401');
?>
<body>
<?php do_page_open('ERREUR', -1); ?>

<?php
h1('Erreur 401');
if (isset($BaseError)) h2($BaseError);
writeLine('<BR><BR><BR>');
h2(
    "Ce serveur n'a pu vérifier si vous êtes autorisé à accéder au document demandé.<BR>"
    . "Soit vous n'avez pas donné les bons droits (par exemple un mauvais mot de passe) "
    . "soit votre butineur ne sait pas comment fournir les éléments nécessaires à l'authentification."
);
writeLine('<BR><BR><BR>');


do_footer();
?>
</body>
</html>
