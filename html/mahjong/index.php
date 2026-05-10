<?php
/**
 * mahjong/index.php — Mah-Jong rules and downloads.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';
require_once includePath('download.lib.php');

do_header('Quelques informations sur le mah-jong', ['keywords' => 'Mah-Jong,Français']);
?>
<body>
<?php do_page_open('Mah-Jong', 0); ?>

<p>Pour ceux que le mah-jong intéresse, voici un recueil de quelques règles que
j'ai pu glaner ici et là.</p>

<div class="Attention"><h1>ATTENTION</h1>
<p>Ceci n'est pas le jeu que l'on trouve sous l'appellation Mah-jong sur la
plupart des machines ou sur internet (ceux où l'on retire des paires de
tuiles).
En effet, ces jeux sont au Mah-jong ce que les réussites sont au bridge:
cela n'a rien à voir si ce n'est le support, à savoir les cartes.</p></div>

<p>Il existe de multiples variantes (suivant les régions du globes) et de
multiples façons de compter les points. Le document que je vous présente ici
n'est donc pas exhaustif. Je l'ai juste réalisé car ceux fournis avec les
boîtes de jeu sont souvent succincts (doux euphémisme) voire farfelus.</p>

<p>Les variations les plus flagrantes sont dans la manière de compter les points:
il existe par exemple une façon (chinoise) de compter qui rapporte au
maximum 3 points par partie...</p>

<p>Une grande partie du texte figurant dans ce document est tiré du site
d'<a href="http://www.mahjongfr.com">Alexis Beuve et Bertrand Le Roy</a>
avec leur aimable autorisation. Il en va de même pour la feuille de comptage
des points.<br>
<i>Note : ce site a disparu et je n'ai pas trouvé de remplaçant.</i></p>
<p><br>Les drapeaux sont issus de <a src="https://flagpedia.net">flagpedia.net</a></p>
<?php renderDownloadTable('mahjong_download.txt', 'Download'); ?>

<?php do_footer(); ?>
</body>
</html>
