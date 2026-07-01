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
    writeLine('<META NAME="viewport" CONTENT="width=device-width, initial-scale=1">');
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
            $file = findImage($btn[0] . $fileSuffix . '.png');
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
 * Renders the page chrome and opens the main content area.
 * Must be called inside <body>.
 *
 * HTML structure emitted:
 *   div.page-layout
 *   ├── header.site-header
 *   │   ├── a.site-name
 *   │   ├── h1.Titre              (optionnel)
 *   │   └── div.header-translate
 *   └── div.body-layout
 *       ├── nav.sidebar           (Btn1 … Btn5)
 *       └── div.ZoneCentre        ← le contenu de la page va ici
 *
 * @param string $title       Titre de la page affiché dans l'en-tête ('' = aucun).
 * @param int    $activeBtn   Index 0-based du bouton actif, ou -1.
 * @param array  $buttons     Tableau des boutons de navigation.
 */
function openPage($title, $activeBtn, $buttons)
{
    writeLine('<DIV class="page-layout">');

    // ── En-tête ──────────────────────────────────────────────────────────────
    writeLine('<HEADER class="site-header">');
    if ($activeBtn === -1) {
        writeLine('<A href="https://lotr.fandom.com/fr/wiki/Cardolan" class="site-name" target="_blank" rel="noopener">Cardolan</A>');
    } else {
        writeLine('<A href="/" class="site-name">Cardolan</A>');
    }
    if ($title) {
        $sectionUrl = ($activeBtn >= 0 && !empty($buttons[$activeBtn][2]))
            ? $buttons[$activeBtn][2] : '';
        if ($sectionUrl)
            writeLine('<H1 class="Titre"><A href="' . htmlspecialchars($sectionUrl) . '">' . $title . '</A></H1>');
        else
            writeLine('<H1 class="Titre">' . $title . '</H1>');
    }
    writeLine('<DIV class="header-translate">');
    insertGoogleTranslate();
    writeLine('</DIV>');
    writeLine('</HEADER>');

    // ── Corps : sidebar + contenu ─────────────────────────────────────────────
    writeLine('<DIV class="body-layout">');
    writeLine('<NAV class="sidebar">');

    foreach ($buttons as $i => $btn) {
        if (!$btn[0]) continue;
        writeLine('<DIV class="Btn' . ($i + 1) . '">');
        if ($btn[3]) {
            if ($i === $activeBtn) {
                writeLine(imageLink('', $btn[0] . '-down.png', '', 'Barre'));
            } else {
                writeLine(imageLink($btn[2], $btn[0] . '-up.png', $btn[1], 'Barre',
                    'Btn' . ($i + 1), '', $btn[0] . '-up.png',
                    $btn[0] . '-over.png', $btn[0] . '-down.png'));
            }
        } else {
            writeLine(imageLink('', $btn[0] . '-up.png', '', 'Barre'));
        }
        writeLine('</DIV>');
    }

    writeLine('</NAV>');
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
        'Promouvoir et soutenir le logiciel libre',
        linkTarget: '_blank'
    ));
    writeLine('</td><td>');
    writeLine('©<a href="mailto:webmaster@cardolan.net">Marcel Spock 1999-2026</a>');
    writeLineBr('<a href="/licence/">CC BY-NC-SA</a>');
    writeLine('Vous êtes le visiteur n°<img src="http://perso0.proxad.net/cgi-bin/wwwcount.cgi?dd=D&df=RANDOM&md=12&ft=2"><br>Compteur complètement libre de ses opinions');
    writeLine('</td><td>');
    writeLine(imageLink('/', 'home-hobbit.png', 'Retour à la page racine', '', '', '', '', '', '', 'Cul-de-Sac'));
    writeLine('</td></tr></table>');
    writeLine('</DIV>'); // FondPage
    writeLine('</DIV>'); // ZoneCentre
    writeLine('</DIV>'); // body-layout
    writeLine('</DIV>'); // page-layout
}

/** Displays an "under construction" placeholder. */
function underConstruction()
{
    writeLine("<BR><BR><H1>Cette page est en cours d'écriture</H1>");
    writeLine('<P align=center>' . imageLink('', 'Construction.gif') . '<BR><BR></P>');
}
