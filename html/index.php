<?php
/**
 * index.php — Site home page.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/site.lib.php';

do_header('Page personnelle de Philippe Poilbarbe', [
    'keywords'   => 'Mah-Jong,Cocktails,Français',
    'favicon'    => 'PB-Soft.ico',
    'javascript' => ['pi.js'],
]);
?>
<body>
<?php

do_page_open('', -1, ['logo' => true]);

writeLine('<h1 class="HomeTitle">Philippe Poilbarbe</h1>');
writeLine('<p class="HomeSubtitle">Cardolan</p>');
writeLine('<hr class="HomeRule">');
writeLine('<p align="center">' . imageLink('', 'Sindarin.jpg', '', 'Sindarin-img', 'Sindarin-img') . '<br>');
writeLine('<br>Ce site a été testé avec Chromium et FireFox.<br><br><br><br>');

writeLine('<div id="Sindarin-modal" class="Sindarin-modal" hidden>');
writeLine('  <div class="Sindarin-modal-box">');
writeLine('    <p>Traduction de l\'anglais phonémique écrit en Tengwars&nbsp;: «&nbsp;Welcome to the home site of Philippe Poilbarbe, as known as Marcel Spock&nbsp;», soit, à peu près «&nbsp;Bienvenue sur le site personnel de Philippe Poilbarbe, aussi connu sous le nom de Marcel Spock&nbsp;».</p>');
writeLine('  </div>');
writeLine('</div>');
writeLine('<script>');
writeLine('(function () {');
writeLine('  var img = document.images["Sindarin-img"];');
writeLine('  var modal = document.getElementById("Sindarin-modal");');
writeLine('  if (!img || !modal) return;');
writeLine('  img.addEventListener("click", function () { modal.hidden = false; });');
writeLine('  modal.addEventListener("click", function () { modal.hidden = true; });');
writeLine('  document.addEventListener("keydown", function (e) { if (e.key === "Escape") modal.hidden = true; });');
writeLine('})();');
writeLine('</script>');
writeLine(imageLink(
    'https://www.april.org/adherer?referent=Philippe+POILBARBE+%28ppoilbarbe%29',
    'http://www.april.org/files/association/documents/bannieres/banniere_horizontale_soutien_adherent_fulltext_486_par_60.png',
    'Promouvoir et soutenir le logiciel libre',
    linkTarget: '_blank'
));
writeLine('</p>');

do_footer();
?>
</body>
</html>
