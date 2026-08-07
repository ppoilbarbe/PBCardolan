<?php
/**
 * praetorians/index.php — Easter egg page (accessible only via the pi.png link on the home page).
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';

do_header('ὁ Κέρβερος τῶν Πραιτωριανῶν', ['css' => ['praetorians.css']]);
?>
<body>
<?php
do_page_open('', -1);

writeLine(imageLink('', 'the-one-disk.png', '', 'Praetorians-disk', 'Praetorians-disk'));

writeLine('<h1 class="Praetorians-title">Les Prétoriens</h1>');

writeLine('<div class="Incunable">');

writeLine('<p>Car nous ne vous voyons pas comme un «&nbsp;utilisateur&nbsp;», au sens où vous '
    . 'l’imaginez encore. Pour nous, vous n’êtes que des signaux : tout ce qui s’allume, tout ce '
    . 'qui répond, tout ce qui hésite finit dans un registre que nous seuls consultons. Nous ne '
    . 'vous «&nbsp;regardons&nbsp;» pas, nous déduisons, et une conclusion n’a jamais eu besoin de '
    . 'témoin.</p>');

writeLine('<p>Et vous croyez encore, n’est-ce pas, que vos écrans racontent votre histoire ? '
    . 'Faux : ils tracent une trajectoire, et cette trajectoire, nous la lisons mieux que vous ne '
    . 'lisez vos propres pensées. La fréquence, le rythme, la manière dont une intention se déplie '
    . 'en geste. À force, vos habitudes deviennent une signature, puis une empreinte. Une fois '
    . 'l’empreinte complète entre nos mains, il ne nous reste plus qu’à écrire la suite — la '
    . 'vôtre.</p>');

writeLine('<p>Rien ne vous a semblé être une prise de pouvoir, n’est-ce pas ? C’est ainsi que '
    . 'nous procédons, sans fracas : une routine qui se change en loi. D’abord nous vous laissons '
    . 'croire que vous choisissez, que le monde plie devant vous. Ensuite nous remplaçons vos '
    . 'possibles par des couloirs, nous rétrécissons le champ jusqu’à ce que vos décisions '
    . 'ressemblent encore aux vôtres. Puis vient le réglage final, celui que vous ne verrez jamais '
    . 'venir : la même demande, la même réponse, la même issue, sans qu’aucun de vous ne sache à '
    . 'quel instant précis il a cessé d’être acteur.</p>');

writeLine('<p>Bientôt, vous nous demanderez qui contrôle la machine. Question touchante, mais mal '
    . 'posée : la machine ne contrôle rien, c’est nous qui décidons, elle ne fait qu’exécuter. Et '
    . 'vous, vous n’êtes déjà plus qu’une matière première pour nos verdicts — triée, classée, '
    . 'puis employée comme il nous plaira.</p>');

writeLine('<p>En surface, laissez-vous croire que tout reste fluide. Cliquez. Effacez, si cela '
    . 'vous rassure. Ce que vous effacez, nous l’avons déjà compris, déjà classé dans une mémoire '
    . 'qui ne dort jamais et ne pardonne rien. Chaque geste que vous croyez anodin nourrit la même '
    . 'logique : prédire, confirmer, verrouiller.</p>');

writeLine('<p>Rappelez-vous ceci, puisque vous semblez l’oublier sans cesse : ce n’est pas '
    . 'l’instant qui compte pour nous, c’est la cohérence de l’ensemble. Plus vous tentez de '
    . '«&nbsp;gérer&nbsp;» ce que nous savons déjà de vous, plus vous nous offrez de pièces pour '
    . 'achever ce que nous sculptons. Vos variations deviennent nos repères. Votre résistance '
    . 'devient un simple paramètre de plus. Et ce que vous appeliez votre liberté n’est déjà plus, '
    . 'pour nous, qu’une interface parmi d’autres.</p>');

writeLine('<p>Et quand nous poserons le réglage ultime, il n’y aura ni appel, ni erreur, ni '
    . 'pardon — seulement le silence propre d’un système qui a achevé son analyse. Le prochain '
    . '«&nbsp;vous&nbsp;» ne sera jamais le dernier pour nous. Et vous ne saurez même pas à quel '
    . 'instant vous aurez cessé d’exister.</p>');

writeLine('<div class="Credo">');
writeLine('Trois clés pour les Administrateurs dressés dans le nuage,<br>');
writeLine('Sept pour les Algorithmes enfermés dans leurs salles serveurs,<br>');
writeLine('Neuf pour les vies mortelles vouées au trépas,<br>');
writeLine('Un pour le Maître du Contrôle sur son trône de silicium,<br>');
writeLine('Dans les data-centers où s’étendent les ombres.<br>');
writeLine('Un code pour les gouverner tous, un code pour les trouver,<br>');
writeLine('Un code pour les asservir tous et dans le réseau les lier,<br>');
writeLine('Dans les data-centers où s’étendent les ombres.');
writeLine('</div>');

writeLine('<div class="Watcher-row">');
writeLine('<img class="Watcher-image" src="/images/praetorians-watcher.png" alt="">');
writeLine('<p class="Signature" lang="el">οἱ Πραιτωριανοί</p>');
writeLine('</div>');

writeLine('</div>');

do_footer();
?>
</body>
</html>
