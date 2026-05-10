<?php
/**
 * links/index.php — Useful links (under construction).
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';

do_header('Quelques liens plus ou moins utiles', ['keywords' => 'Liens,Français']);
?>
<body>
<?php
do_page_open('Liens', 4);

underConstruction();

do_footer();
?>
</body>
</html>
