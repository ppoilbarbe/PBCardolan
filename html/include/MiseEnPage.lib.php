<?php
if (isset($C_MISEENPAGE_LIB_INC)) return;
$C_MISEENPAGE_LIB_INC = 1;

require_once included('Edition.lib.php');

// ---------------------------------------------------------------------------
// <HEAD> section
// ---------------------------------------------------------------------------

/** Outputs a favicon <link> tag. */
function SetBookmarkIcon($iconFile)
{
    Ligne("<link rel=\"shortcut icon\" type=\"image/gif\" href=\"$iconFile\">");
}

/**
 * Opens the HTML document: sets no-cache headers, outputs DOCTYPE through
 * the opening <HEAD> tags, and includes boutons.js.
 *
 * @param string       $title    Page title.
 * @param string|array $keywords Meta keywords (string or array of strings).
 */
function DebutEnTete($title, $keywords = '')
{
    header('Expires: Mon, 01 Jan 2004 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    Ligne('<!DOCTYPE HTML>');
    Ligne('<HTML lang="fr">');
    Ligne('<HEAD>');
    Ligne('<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=utf-8">');
    Ligne('<META HTTP-EQUIV="Expires" CONTENT="' . date('l, F d Y 00:00:00', time() + 60) . ' GMT">');
    Ligne("<TITLE>$title</TITLE>");
    Ligne('<META NAME="author" CONTENT="philippe@cardolan.net">');

    foreach ((array) $keywords as $kw) {
        if (!$kw) continue;
        Ligne('<META HTTP-EQUIV="Keywords" CONTENT="' . $kw . '">');
        Ligne('<META NAME="keywords"       CONTENT="' . $kw . '">');
    }

    Ligne('<META NAME="COPYRIGHT" CONTENT="Ph. Poilbarbe 1999-2026">');
    Ligne('<META NAME="ROBOTS"    CONTENT="NOARCHIVE">');
    Ligne('<META NAME="ROBOTS"    CONTENT="ALL">');
    JavaScript('boutons.js');
}

/**
 * Emits a <SCRIPT> block that preloads the -over and -down variants of each
 * active navigation button using window.addEventListener (no body onload needed).
 *
 * @param array $buttons Navigation button array (see BoutonsGeneraux.lib.php).
 */
function PreloadImagesBoutons($buttons)
{
    $preloads = [];
    foreach ($buttons as $btn) {
        if (!$btn[0] || !$btn[3]) continue;
        foreach (['-over' => '_over', '-down' => '_down'] as $fileSuffix => $jsSuffix) {
            $file = EstImage($btn[0] . $fileSuffix . '.gif');
            if (file_exists(CheminAbsoluSysteme($file)))
                $preloads[] = [$btn[0] . $jsSuffix, $file];
        }
    }
    if (empty($preloads)) return;

    Ligne('<SCRIPT language="JavaScript">');
    Ligne('<!--');
    Ligne('window.addEventListener("load", function() {');
    Ligne('if (document.images) {');
    foreach ($preloads as [$jsName, $file]) {
        Ligne("$jsName = newImage(\"$file\");");
    }
    Ligne('}');
    Ligne('});');
    Ligne('// -->');
    Ligne('</SCRIPT>');
}

/**
 * Closes </HEAD>. The <BODY> tag is written explicitly by each page.
 *
 * @param int $printable  Set to 1 to also load print stylesheets.
 */
function FinEnTete($printable = 0)
{
    FeuilleStyle('barre.css',        $printable ? 'screen' : '');
    FeuilleStyle('general.css',      $printable ? 'screen' : '');
    FeuilleStyle('always_print.css', 'print');
    if ($printable) {
        FeuilleStyle('general_print.css', 'print');
        FeuilleStyle('barre_print.css',   'print');
    }

    Ligne('</HEAD>');
}

// ---------------------------------------------------------------------------
// Page chrome
// ---------------------------------------------------------------------------

/** Injects the Google Translate widget into the top banner area. */
function InsereGoogleTranslate()
{
    echo <<<HTML
<div class=GoogleTranslate>
<div id="google_translate_element"></div>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement(
    {pageLanguage: 'fr', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, multilanguagePage: true},
    'google_translate_element'
  );
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</div>
HTML;
}

/**
 * Renders the full navigation chrome (corner image, banner, side buttons)
 * and opens the main content <DIV class="ZoneCentre">.
 * Must be called inside <body>.
 *
 * @param string $title       Optional page subtitle shown in the top banner.
 * @param int    $activeBtn   0-based index of the currently active button, or -1.
 * @param array  $buttons     Navigation button array.
 */
function DebutPage($title, $activeBtn, $buttons)
{
    Ligne('<DIV class="ZoneCoinHG">'  . LienImage('', 'ZoneCoinHG.gif',   '', 'Barre') . '</DIV>');
    Ligne('<DIV class="ZoneHaut">'    . LienImage('', 'ZoneHaut.gif',     '', 'Barre'));
    InsereGoogleTranslate();
    Ligne('</DIV>');

    if ($title)
        Ligne('<DIV class="ZoneHaut"><H1 class="Titre">' . $title . '</H1></DIV>');

    Ligne('<DIV class="ZoneBordDroit">' . LienImage('', 'ZoneBordDroit.gif', '', 'Barre') . '</DIV>');
    Ligne('<DIV class="ZoneBas">'       . LienImage('', 'ZoneBas.gif',       '', 'Barre') . '</DIV>');

    foreach ($buttons as $i => $btn) {
        if (!$btn[0]) continue;
        Ligne('<DIV class="Btn' . ($i + 1) . '">');
        if ($btn[3]) {
            // Active page: show the pressed state with no link.
            // Other pages: show the normal state with rollover.
            if ($i === $activeBtn) {
                Ligne(LienImage('', $btn[0] . '-down.gif', '', 'Barre'));
            } else {
                Ligne(LienImage($btn[2], $btn[0] . '.gif', $btn[1], 'Barre',
                    'Btn' . ($i + 1), '', $btn[0] . '.gif',
                    $btn[0] . '-over.gif', $btn[0] . '-down.gif'));
            }
        } else {
            Ligne(LienImage('', $btn[0] . '.gif', '', 'Barre'));
        }
        Ligne('</DIV>');
    }

    Ligne('<DIV class="ZoneCentre">');
}

/**
 * Closes the content area and renders the page footer (April.org banner,
 * copyright notice, visitor counter, home button).
 * The </BODY> and </HTML> tags are written explicitly by each page.
 */
function FinPage()
{
    Ligne('<DIV class="FondPage">');
    Ligne('<table><tr><td>');
    Ligne(LienImage(
        'https://www.april.org/adherer?referent=Philippe+POILBARBE+%28ppoilbarbe%29',
        'http://www.april.org/files/association/documents/bannieres/bouton_web_soutien_88x31.gif',
        'Promouvoir et soutenir le logiciel libre'
    ));
    Ligne('</td><td>');
    Ligne('©<a href="mailto:webmaster@cardolan.net">Marcel Spock 1999-2026</a>');
    LigneBR('Tous droits réservés');
    Ligne('Vous êtes le visiteur n°<img src="http://perso0.proxad.net/cgi-bin/wwwcount.cgi?dd=D&df=RANDOM&md=12&ft=2"><br>Compteur complètement libre de ses opinions');
    Ligne('</td><td>');
    Ligne(LienImage('/', 'home.gif', 'Retour à la page racine'));
    Ligne('</td></tr></table>');
    Ligne('</DIV>');
    Ligne('</DIV>');
}

/** Displays an "under construction" placeholder. */
function EnConstruction()
{
    Ligne("<BR><BR><H1>Cette page est en cours d'écriture</H1>");
    Ligne('<P align=center>' . LienImage('', 'Construction.gif') . '<BR><BR></P>');
}
