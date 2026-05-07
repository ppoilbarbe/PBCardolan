<?php
/**
 * Site.lib.php — Main site library; include this in every page.
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
if (isset($C_SITE_LIB_INC)) return;
$C_SITE_LIB_INC = 1;

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Base.lib.php';
require_once included('MiseEnPage.lib.php');
require_once included('BoutonsGeneraux.lib.php');

/**
 * Outputs the complete <head>…</head> section.
 *
 * @param string $htmlTitle  Text for the HTML <TITLE> tag.
 * @param array  $opts {
 *   string       keywords    Meta keywords.
 *   string       rss         URL of an RSS feed (emits <link rel="alternate">).
 *   string       favicon     Favicon image filename.
 *   int          printable   1 to include print stylesheets.
 *   string|array javascript  Extra JS files to load from jscript/.
 *   array        buttons     Nav buttons used for preloading; defaults to $BoutonsGeneraux.
 * }
 */
function do_header(string $htmlTitle, array $opts = [])
{
    global $BoutonsGeneraux;

    $keywords  = $opts['keywords']   ?? '';
    $rss       = $opts['rss']        ?? '';
    $favicon   = $opts['favicon']    ?? '';
    $printable = $opts['printable']  ?? 0;
    $javascript = (array) ($opts['javascript'] ?? []);
    $buttons   = $opts['buttons']    ?? $BoutonsGeneraux;

    DebutEnTete($htmlTitle, $keywords);
    if ($favicon) SetBookmarkIcon(EstImage($favicon));
    if ($rss)     Ligne('<link rel="alternate" type="application/rss+xml" title="RSS" href="'
                        . htmlspecialchars($rss) . '">');
    PreloadImagesBoutons($buttons);
    FinEnTete($printable);
    foreach ($javascript as $js) JavaScript($js);
}

/**
 * Renders the navigation chrome and opens the main content area.
 * Must be called immediately after <body>.
 *
 * @param string $pageTitle  Subtitle shown in the top banner ('' = none).
 * @param int    $activeBtn  0-based index of the active nav button, or -1.
 * @param array  $opts {
 *   array buttons  Navigation buttons; defaults to $BoutonsGeneraux.
 * }
 */
function do_page_open(string $pageTitle, int $activeBtn, array $opts = [])
{
    global $BoutonsGeneraux;
    DebutPage($pageTitle, $activeBtn, $opts['buttons'] ?? $BoutonsGeneraux);
}

/** Outputs the page footer and closes the content area. */
function do_footer()
{
    FinPage();
}
