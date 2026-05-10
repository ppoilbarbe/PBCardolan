<?php
/**
 * download/index.php — Home-made programs available for download.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';
require_once includePath('download.lib.php');

do_header('Quelques programmes maison');
?>
<body>
<?php do_page_open('Programmes', 3); ?>

<p>Voici quelques programmes développés à la maison pour un usage personnel.
Je les ai mis sur <a src="https://github.com/ppoilbarbe">Github</a>
pour les rendre accessibles à ceux que cela intéresse.<br>
N'hésitez pas à ouvrir des tickets en cas de problème rencontrés<br></p>
<div class="Attention"><h1>ATTENTION</h1>
Je n'ai <big><i>ni Windows, ni MacOS</i></big> sous la main, donc les exécutables
pour ces systèmes ne sont testés que par Github CI (Continuous Integration).
</div>
<p>Les drapeaux sont issus de <a src="https://flagpedia.net">flagpedia.net</a></p>
<?php
renderDownloadTable('file_download.txt', 'Download');

do_footer();
?>
</body>
</html>
