<?php
/**
 * index.php — Site home page.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Base.lib.php';

require_once included('MiseEnPage.lib.php');
require_once included('BoutonsGeneraux.lib.php');

DebutEnTete('Page personnelle de Philippe Poilbarbe', 'Mah-Jong,Cocktails,Français');
SetBookmarkIcon(EstImage('PB-Soft.ico'));
PreloadImagesBoutons($BoutonsGeneraux);
FinEnTete();
JavaScript('pi.js');

DebutPage('', -1, $BoutonsGeneraux);
Titre('Philippe Poilbarbe');
SousTitre('');
Ligne('<p align="center">' . LienImage('', 'Sindarin.jpg') . '<br>');
Ligne('<br>Ce site a été testé avec Edge, Chromium, FireFox, Lynx.<br>');
Ligne('Optimisé pour une résolution minimale de 1024x768.<br><br><br><br>');
Ligne(LienImage(
    'https://www.april.org/adherer?referent=Philippe+POILBARBE+%28ppoilbarbe%29',
    'http://www.april.org/files/association/documents/bannieres/banniere_horizontale_soutien_adherent_fulltext_486_par_60.png',
    'Promouvoir et soutenir le logiciel libre'
));
Ligne('</p>');
FinPage();
