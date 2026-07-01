<?php
/**
 * site.lib.php — Main site library; include this in every page.
 *
 * Canonical page structure:
 *
 *   do_header($htmlTitle, $opts);
 *   ?>
 *   <body>
 *   <?php do_page_open($pageTitle, $activeBtn); ?>
 *
 *   ... content ...
 *
 *   <?php do_footer(); ?>
 *   </body>
 *   </html>
 *
 * Sub-projects include this file and follow the same structure, passing their
 * own navigation buttons via $opts['buttons'].
 */
if (isset($_SITE_LIB_INC)) return;
$_SITE_LIB_INC = 1;

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/base.lib.php';
require_once includePath('layout.lib.php');
require_once includePath('nav_buttons.lib.php');

/**
 * Outputs the complete <head>…</head> section.
 *
 * @param string $htmlTitle  Text for the HTML <TITLE> tag.
 * @param array  $opts {
 *   string       keywords    Meta keywords.
 *   string       rss         URL of an RSS feed (emits <link rel="alternate">).
 *   string       favicon     Favicon image filename.
 *   string|array javascript  Extra JS files to load from jscript/.
 *   array        buttons     Nav buttons used for preloading; defaults to $navButtons.
 *   array        css         Extra CSS files to load from css/.
 * }
 */
function do_header(string $htmlTitle, array $opts = [])
{
    global $navButtons;

    $keywords   = $opts['keywords']   ?? '';
    $rss        = $opts['rss']        ?? '';
    $favicon    = $opts['favicon']    ?? '';
    $javascript = (array) ($opts['javascript'] ?? []);
    $css        = (array) ($opts['css'] ?? []);
    $buttons    = $opts['buttons']    ?? $navButtons;

    openHead($htmlTitle, $keywords);
    if ($favicon) setFavicon(findImage($favicon));
    if ($rss)     writeLine('<link rel="alternate" type="application/rss+xml" title="RSS" href="'
                        . htmlspecialchars($rss) . '">');
    preloadNavImages($buttons);
    closeHead(['css' => $css]);
    foreach ($javascript as $js) linkScript($js);
}

/**
 * Renders the navigation chrome and opens the main content area.
 * Must be called immediately after <body>.
 *
 * @param string $pageTitle  Subtitle shown in the top banner ('' = none).
 * @param int    $activeBtn  0-based index of the active nav button, or -1.
 * @param array  $opts {
 *   array buttons  Navigation buttons; defaults to $navButtons.
 *   bool  logo     If true, shows the pbcardolan.png logo instead of the "Cardolan" text.
 * }
 */
function do_page_open(string $pageTitle, int $activeBtn, array $opts = [])
{
    global $navButtons;
    openPage($pageTitle, $activeBtn, $opts['buttons'] ?? $navButtons, $opts['logo'] ?? false);
}

/** Outputs the page footer and closes the content area. */
function do_footer()
{
    closePage();
}
