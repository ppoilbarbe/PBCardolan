<?php
/**
 * licence/index.php — Mentions légales et licences du site.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';

do_header('Mentions légales et licences', ['keywords' => 'Licence,CC BY-NC-SA,Français']);
?>
<body>
<?php
do_page_open('', -1);

h1('Mentions légales et licences');

writeLine('<p>Le code HTML de ce site, ainsi que tous les textes affichés '
    . '(recettes de cuisine, recettes de cocktails et autres contenus rédactionnels), '
    . 'sont mis à disposition sous licence '
    . '<a href="https://creativecommons.org/licenses/by-nc-sa/4.0/deed.fr" target="_blank" rel="noopener">'
    . 'Creative Commons Attribution - Pas d\'Utilisation Commerciale - Partage dans les Mêmes Conditions 4.0 '
    . '(CC BY-NC-SA 4.0)</a>.</p>');

writeLine('<p>Toutes les images et icônes du site sont également mises à disposition sous licence '
    . '<a href="https://creativecommons.org/licenses/by-nc-sa/4.0/deed.fr" target="_blank" rel="noopener">'
    . 'CC BY-NC-SA 4.0</a>.</p>');

writeLine('<p>Pour toute question, contactez le webmaster : '
    . '<a href="mailto:webmaster@cardolan.net">webmaster@cardolan.net</a>.</p>');

do_footer();
?>
</body>
</html>
