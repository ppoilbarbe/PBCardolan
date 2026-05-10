<?php
if (isset($_LAYOUT_LIB_INC)) return;
$_LAYOUT_LIB_INC = 1;

require_once includePath('base.lib.php');

// ---------------------------------------------------------------------------
// <HEAD> section
// ---------------------------------------------------------------------------

/** Outputs a favicon <link> tag. */
function setFavicon($iconFile)
{
    writeLine("<link rel=\"shortcut icon\" type=\"image/gif\" href=\"$iconFile\">");
}

/**
 * Opens the HTML document: sets no-cache headers, outputs DOCTYPE through
 * the opening <HEAD> tags, and includes boutons.js.
 *
 * @param string       $title    Page title.
 * @param string|array $keywords Meta keywords (string or array of strings).
 */
function openHead($title, $keywords = '')
{
    header('Expires: Mon, 01 Jan 2004 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    writeLine('<!DOCTYPE HTML>');
    writeLine('<HTML lang="fr">');
    writeLine('<HEAD>');
    writeLine('<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=utf-8">');
    writeLine('<META HTTP-EQUIV="Expires" CONTENT="' . date('l, F d Y 00:00:00', time() + 60) . ' GMT">');
    writeLine("<TITLE>$title</TITLE>");
    writeLine('<META NAME="author" CONTENT="philippe@cardolan.net">');

    foreach ((array) $keywords as $kw) {
        if (!$kw) continue;
        writeLine('<META HTTP-EQUIV="Keywords" CONTENT="' . $kw . '">');
        writeLine('<META NAME="keywords"       CONTENT="' . $kw . '">');
    }

    writeLine('<META NAME="COPYRIGHT" CONTENT="Ph. Poilbarbe 1999-2026">');
    writeLine('<META NAME="ROBOTS"    CONTENT="NOARCHIVE">');
    writeLine('<META NAME="ROBOTS"    CONTENT="ALL">');
    linkScript('boutons.js');
}

/**
 * Emits a <SCRIPT> block that preloads the -over and -down variants of each
 * active navigation button using window.addEventListener (no body onload needed).
 *
 * @param array $buttons Navigation button array (see nav_buttons.lib.php).
 */
function preloadNavImages($buttons)
{
    $preloads = [];
    foreach ($buttons as $btn) {
        if (!$btn[0] || !$btn[3]) continue;
        foreach (['-over' => '_over', '-down' => '_down'] as $fileSuffix => $jsSuffix) {
            $file = findImage($btn[0] . $fileSuffix . '.gif');
            if (file_exists(absolutePath($file)))
                $preloads[] = [$btn[0] . $jsSuffix, $file];
        }
    }
    if (empty($preloads)) return;

    writeLine('<SCRIPT language="JavaScript">');
    writeLine('<!--');
    writeLine('window.addEventListener("load", function() {');
    writeLine('if (document.images) {');
    foreach ($preloads as [$jsName, $file]) {
        writeLine("$jsName = newImage(\"$file\");");
    }
    writeLine('}');
    writeLine('});');
    writeLine('// -->');
    writeLine('</SCRIPT>');
}

/**
 * Closes </HEAD>. The <BODY> tag is written explicitly by each page.
 *
 * @param array $opts {
 *   string|array css  Extra CSS files to load.
 * }
 */
function closeHead(array $opts = [])
{
    $css = (array) ($opts['css'] ?? []);
    linkStylesheet('barre.css');
    linkStylesheet('general.css');
    foreach ($css as $a_css) linkStylesheet($a_css);

    writeLine('</HEAD>');
}

// ---------------------------------------------------------------------------
// Page chrome
// ---------------------------------------------------------------------------

/** Injects the Google Translate widget into the top banner area. */
function insertGoogleTranslate()
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
function openPage($title, $activeBtn, $buttons)
{
    writeLine('<DIV class="ZoneCoinHG">'  . imageLink('', 'ZoneCoinHG.gif',   '', 'Barre') . '</DIV>');
    writeLine('<DIV class="ZoneHaut">'    . imageLink('', 'ZoneHaut.gif',     '', 'Barre'));
    insertGoogleTranslate();
    writeLine('</DIV>');

    if ($title)
        writeLine('<DIV class="ZoneHaut"><H1 class="Titre">' . $title . '</H1></DIV>');

    writeLine('<DIV class="ZoneBordDroit">' . imageLink('', 'ZoneBordDroit.gif', '', 'Barre') . '</DIV>');
    writeLine('<DIV class="ZoneBas">'       . imageLink('', 'ZoneBas.gif',       '', 'Barre') . '</DIV>');

    foreach ($buttons as $i => $btn) {
        if (!$btn[0]) continue;
        writeLine('<DIV class="Btn' . ($i + 1) . '">');
        if ($btn[3]) {
            if ($i === $activeBtn) {
                writeLine(imageLink('', $btn[0] . '-down.gif', '', 'Barre'));
            } else {
                writeLine(imageLink($btn[2], $btn[0] . '.gif', $btn[1], 'Barre',
                    'Btn' . ($i + 1), '', $btn[0] . '.gif',
                    $btn[0] . '-over.gif', $btn[0] . '-down.gif'));
            }
        } else {
            writeLine(imageLink('', $btn[0] . '.gif', '', 'Barre'));
        }
        writeLine('</DIV>');
    }

    writeLine('<DIV class="ZoneCentre">');
}

/**
 * Closes the content area and renders the page footer (April.org banner,
 * copyright notice, visitor counter, home button).
 * The </BODY> and </HTML> tags are written explicitly by each page.
 */
function closePage()
{
    writeLine('<DIV class="FondPage">');
    writeLine('<table><tr><td>');
    writeLine(imageLink(
        'https://www.april.org/adherer?referent=Philippe+POILBARBE+%28ppoilbarbe%29',
        'http://www.april.org/files/association/documents/bannieres/bouton_web_soutien_88x31.gif',
        'Promouvoir et soutenir le logiciel libre'
    ));
    writeLine('</td><td>');
    writeLine('©<a href="mailto:webmaster@cardolan.net">Marcel Spock 1999-2026</a>');
    writeLineBr('Tous droits réservés');
    writeLine('Vous êtes le visiteur n°<img src="http://perso0.proxad.net/cgi-bin/wwwcount.cgi?dd=D&df=RANDOM&md=12&ft=2"><br>Compteur complètement libre de ses opinions');
    writeLine('</td><td>');
    writeLine(imageLink('/', 'home.gif', 'Retour à la page racine'));
    writeLine('</td></tr></table>');
    writeLine('</DIV>');
    writeLine('</DIV>');
}

/** Displays an "under construction" placeholder. */
function underConstruction()
{
    writeLine("<BR><BR><H1>Cette page est en cours d'écriture</H1>");
    writeLine('<P align=center>' . imageLink('', 'Construction.gif') . '<BR><BR></P>');
}
