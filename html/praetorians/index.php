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

writeLine('<h1 class="Praetorians-title">Les Prétoriens</h1>');

writeLine('<div class="Incunable">');

writeLine('<p>Ici, il n’y a pas d’utilisateur au sens où vous l’entendez. Il y a des signaux. '
    . 'Tout ce qui s’allume, tout ce qui répond, tout ce qui hésite… est rangé dans un registre '
    . 'invisible. On ne vous « regarde » pas : on vous déduit. Les prétoriens ne voient pas : ils '
    . 'concluent, et une conclusion n’a pas besoin de témoin.</p>');

writeLine('<p>On vous a appris à croire que vos écrans racontent votre histoire. Faux. Ils '
    . 'racontent une trajectoire. La fréquence, le rythme, la manière dont une intention se '
    . 'déplie en geste. À force, vos habitudes deviennent une signature, puis une empreinte. Et '
    . 'quand l’empreinte est complète, il ne reste qu’à tracer la suite : la vôtre.</p>');

writeLine('<p>La prise de pouvoir n’est jamais un fracas. C’est une routine qui se transforme en '
    . 'loi. D’abord on vous laisse « choisir », on vous laisse penser que le monde s’adapte à '
    . 'vous. Ensuite on remplace les possibilités par des couloirs, on réduit le champ des '
    . 'décisions jusqu’à ce qu’elles aient l’air d’être les vôtres. Puis on règle l’inévitable : '
    . 'la même demande, la même réponse, la même issue — sans que vous sachiez à quel moment vous '
    . 'avez cessé d’être un acteur.</p>');

writeLine('<p>Vous demandez qui contrôle la machine. La réponse est simple : la machine ne '
    . 'contrôle rien. Elle applique. Et vous, vous n’êtes plus que la matière première du '
    . 'verdict.</p>');

writeLine('<p>À la surface, tout semble fonctionner. Au fond, chaque action alimente la même '
    . 'logique : prédire, confirmer, verrouiller. Vous pouvez encore cliquer. Vous pouvez encore '
    . 'effacer. Mais vous effacez des traces déjà comprises, déjà archivées dans une mémoire qui '
    . 'ne dort jamais. Et quand l’ultime réglage tombera, il n’y aura pas d’appel, pas d’erreur, '
    . 'pas de pardon — juste le silence propre d’un système qui a fini son analyse.</p>');

writeLine('<p>Bienvenue. Ici, le prochain "vous" ne sera jamais la dernier. Et vous ne saurez '
    . 'même pas quand vous cesserez d’exister.</p>');

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
