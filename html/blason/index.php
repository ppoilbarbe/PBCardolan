<?php
/**
 * blason/index.php — Explication du blason de Cardolan.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';

do_header('Le blason de Cardolan', ['keywords' => 'Cardolan,Tolkien,Blason,Arnor,Français']);
?>
<body>
<?php
do_page_open('', -1);

h1('Le blason de Cardolan');

writeLine('<div class="Blason">');
writeLine('<p>' . imageLink(
    'https://lotr.fandom.com/fr/wiki/Cardolan',
    'pbcardolan-full.png',
    'Le blason de Cardolan',
    'Blason-img',
    '', '', '', '', '', '',
    '_blank'
) . '</p>');

writeLine('<p>Ce blason a été composé pour représenter le royaume de Cardolan, '
    . 'l\'un des trois royaumes nés du partage d\'Arnor entre les fils du roi '
    . 'Eärendur, aux côtés d\'Arthedain et de Rhudaur, dans l\'œuvre de '
    . 'J.R.R. Tolkien.</p>');

writeLine('<p>L\'<strong>arbre doré</strong> qui occupe le centre de l\'écu '
    . 'rappelle l\'Arbre Blanc, symbole de la lignée numénoréenne d\'Isildur : '
    . 'les rois de Cardolan, comme ceux de Gondor, descendaient des Rois '
    . 'd\'Arnor et partageaient cet héritage.</p>');

writeLine('<p>Les <strong>étoiles</strong> qui l\'entourent évoquent celles '
    . 'associées à Eärendil et à la lignée elendilienne, motif que l\'on '
    . 'retrouve également dans les armes de Gondor, arbre et étoiles surmontés '
    . 'd\'une couronne.</p>');

writeLine('<p>Les <strong>montagnes</strong> en arrière-plan et les deux '
    . '<strong>rivières</strong> qui serpentent à leurs pieds figurent le '
    . 'territoire de Cardolan en Eriador, borné par le Baranduin (le '
    . 'Brandevin) et la Mitheithel (la Fontgrise), et son relief, notamment '
    . 'les Tyrn Gorthad (les Hauts-des-Galgals).</p>');

writeLine('<p>La <strong>couronne</strong> à la base de l\'écu rappelle enfin '
    . 'le statut de véritable royaume de Cardolan, héritier au même titre que '
    . 'ses voisins du titre royal d\'Arnor.</p>');

writeLine('<p><em>Tolkien n\'a pas laissé d\'héraldique officielle pour '
    . 'Cardolan : ce blason est une composition inspirée du motif canonique '
    . 'de l\'Arbre Blanc et des étoiles, transposé à ce royaume.</em></p>');
writeLine('</div>');

do_footer();
?>
</body>
</html>
