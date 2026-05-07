<?php
/**
 * cocktails/index.php — Cocktail recipes (under construction).
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Site.lib.php';

do_header('Cocktails: Pour les assoiffés', ['keywords' => 'Cocktails,Français']);
?>
<body>
<?php
do_page_open('Cocktails', 1);

EnConstruction();

do_footer();
?>
</body>
</html>
