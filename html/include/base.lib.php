<?php
if (isset($_BASE_LIB_INC)) return;
$_BASE_LIB_INC = 1;

// Site-wide path constants derived from the web server environment.
define('DOC_ROOT',   $_SERVER['DOCUMENT_ROOT']);
define('SCRIPT_FILE', $_SERVER['SCRIPT_FILENAME']);
define('BASE_PATH',  '');
define('SELF_PATH',  $_SERVER['PHP_SELF']);
define('SITE_URL',   'http://' . $_SERVER['SERVER_NAME']);
define('PAGE_URL',   SITE_URL . SELF_PATH);
define('SCRIPT_DIR', dirname(SELF_PATH));

// ---------------------------------------------------------------------------
// Path helpers
// ---------------------------------------------------------------------------

/** Returns the filesystem path to a file inside /include/. */
function includePath($name)
{
    return DOC_ROOT . '/include/' . $name;
}

/**
 * Converts a relative URL path to an absolute one rooted at SCRIPT_DIR.
 * Paths that already start with '/' are returned unchanged.
 */
function absoluteUrl($path)
{
    if (substr($path, 0, 1) === '/') return $path;
    return rtrim(SCRIPT_DIR, '/') . '/' . $path;
}

/**
 * Converts a URL path to an absolute filesystem path under the document root.
 */
function absolutePath($path)
{
    return rtrim(DOC_ROOT, '/') . absoluteUrl($path);
}

// ---------------------------------------------------------------------------
// Asset lookup
// ---------------------------------------------------------------------------

/**
 * Returns the flagcdn.com URL for a given ISO language/country code.
 * Size: 40×30 px.
 */
function flagUrl($code)
{
    return 'https://flagcdn.com/40x30/' . $code . '.png';
}

/**
 * Searches a list of directories for a file and returns its absolute URL path.
 * Falls back to the filename relative to the current script directory.
 */
function findFile($filename, $searchPaths)
{
    foreach ($searchPaths as $dir) {
        if (file_exists($dir . '/' . $filename))
            return absoluteUrl($dir . '/' . $filename);
    }
    return absoluteUrl($filename);
}

/** Locates an image in the standard image directories. Passes through http(s) URLs. */
function findImage($image)
{
    if (substr($image, 0, 4) === 'http') return $image;
    return findFile($image, ['.', 'images', '../images', '../../images']);
}

/** Locates a data file in the standard lib directories. */
function findLib($filename)
{
    return findFile($filename, ['.', 'lib', '../lib', '../../lib', '../../../lib']);
}

// ---------------------------------------------------------------------------
// Date formatting
// ---------------------------------------------------------------------------

/**
 * Formats a timestamp like PHP's date(), with 'F', 'M', 'l', 'D' replaced by
 * French month and day names. All other format characters are passed to date().
 *
 * @param string $format    Format string (same syntax as date()).
 * @param int    $timestamp Unix timestamp; defaults to now.
 */
function frenchDate($format, $timestamp = 0)
{
    static $months = [
        1  => ['Janvier', 'jan'], ['Février',   'fév'], ['Mars',     'mar'],
               ['Avril',   'avr'], ['Mai',       'mai'], ['Juin',     'jun'],
               ['Juillet', 'jul'], ['Août',      'aou'], ['Septembre','sep'],
               ['Octobre', 'oct'], ['Novembre',  'nov'], ['Décembre', 'déc'],
    ];
    static $days = [
        ['Dimanche','dim'], ['Lundi',   'lun'], ['Mardi',   'mar'],
        ['Mercredi','mer'], ['Jeudi',   'jeu'], ['Vendredi','ven'], ['Samedi','sam'],
    ];

    $t   = $timestamp ?: time();
    $d   = getdate($t);
    $out = '';

    for ($i = 0; $i < strlen($format); $i++) {
        switch ($format[$i]) {
            case 'F': $out .= $months[$d['mon']][0];  break;
            case 'M': $out .= $months[$d['mon']][1];  break;
            case 'l': $out .= $days[$d['wday']][0];   break;
            case 'D': $out .= $days[$d['wday']][1];   break;
            default:  $out .= date($format[$i], $t);
        }
    }

    return $out;
}

// ---------------------------------------------------------------------------
// Output helpers
// ---------------------------------------------------------------------------

/** Outputs a line of HTML followed by a newline. */
function writeLine($html) { echo $html, "\n"; }

/** Outputs a line of HTML followed by <BR> and a newline. */
function writeLineBr($html) { echo $html, "<BR>\n"; }

/** Outputs an <h1> heading. */
function h1($text) { echo "<h1>$text</h1>\n"; }

/** Outputs an <h2> heading. */
function h2($text) { echo "<h2>$text</h2>\n"; }

// ---------------------------------------------------------------------------
// Asset path resolution
// ---------------------------------------------------------------------------

/**
 * Walks up the directory tree (up to 4 levels) to locate a file by name.
 * Used to resolve CSS and JS paths from deeply nested pages.
 */
function findAsset($name)
{
    if (substr($name, 0, 1) === "/") return $name;
    foreach (['.', '..', '../..', '../../..'] as $dir) {
        if (file_exists($dir . '/' . $name))
            return absoluteUrl($dir . '/' . $name);
    }
    return absoluteUrl($name);
}

// ---------------------------------------------------------------------------
// HTML asset tags
// ---------------------------------------------------------------------------

/**
 * Outputs a <LINK> stylesheet tag.
 *
 * @param string $name  Filename inside the css/ directory.
 */
function linkStylesheet($name)
{
    $name = substr($name, 0, 1) === "/" ? $name : "css/$name";
    writeLine('<LINK rel="stylesheet" href="' . findAsset($name) . '" type="text/css">');
}

/**
 * Outputs a <SCRIPT> tag for a JavaScript file inside the jscript/ directory.
 */
function linkScript($name)
{
    writeLine('<SCRIPT LANGUAGE="javascript" src="' . findAsset("jscript/$name") . '"></SCRIPT>');
}

// ---------------------------------------------------------------------------
// Image rendering
// ---------------------------------------------------------------------------

/**
 * Returns a "width=x height=y" attribute string for a local image, or '' if
 * the image cannot be found.
 */
function imageSizeAttr($image)
{
    $path = absolutePath($image);
    if (!file_exists($path)) return '';
    $size = getimagesize($path);
    return ' ' . $size[3];
}

/**
 * Builds an <img> tag, optionally wrapped in an <a> with rollover behaviour.
 *
 * @param string $link        URL for the anchor; empty string for no link.
 * @param string $image       Image filename (resolved via findImage()).
 * @param string $text        Alt text and status-bar text on hover.
 * @param string $class       CSS class for the <img>.
 * @param string $imgName     Name attribute for the <img> (needed for JS swaps).
 * @param string $imgTarget   Name of the image to swap (defaults to $imgName).
 * @param string $mouseOut    Image filename to swap to on mouseout.
 * @param string $mouseOver   Image filename to swap to on mouseover.
 * @param string $mouseDown   Image filename to swap to on mousedown.
 * @param string $linkTarget  Anchor target attribute (e.g. "_blank" to open in a new tab).
 */
function imageLink($link, $image, $text = '', $class = '', $imgName = '',
                   $imgTarget = '', $mouseOut = '', $mouseOver = '', $mouseDown = '',
                   $title = '', $linkTarget = '')
{
    $src    = findImage($image);
    $target = $imgTarget ?: $imgName;
    $events = '';

    if ($mouseOver) {
        $over    = findImage($mouseOver);
        $status  = $text ? "window.status='$text'; " : '';
        $events .= " onmouseover=\"{$status}changeImages('$target', '$over'); return true;\"";
    }
    if ($mouseOut) {
        $out     = findImage($mouseOut);
        $status  = $text ? "window.status=''; " : '';
        $events .= " onmouseout=\"{$status}changeImages('$target', '$out'); return true;\"";
    }
    if ($mouseDown) {
        $down    = findImage($mouseDown);
        $up      = $mouseOver ? findImage($mouseOver) : $src;
        $events .= " onmousedown=\"changeImages('$target', '$down'); return true;\"";
        $events .= " onmouseup=\"changeImages('$target', '$up'); return true;\"";
    }

    $nameAttr  = $imgName ? " name=\"$imgName\"" : '';
    $classAttr = $class   ? " class=\"$class\""  : '';
    $altAttr   = $text    ? " alt=\"$text\""     : '';
    $titleAttr = $title   ? " title=\"$title\""  : '';
    $targetAttr = $linkTarget ? " target=\"$linkTarget\" rel=\"noopener\"" : '';

    $html = $link ? "<a href=\"$link\" class=Image$targetAttr$events>\n" : '';
    $html .= "<img src=\"$src\"" . imageSizeAttr($src) . $classAttr . $altAttr . $titleAttr;
    $html .= $link ? '' : $events;
    $html .= "$nameAttr>";
    $html .= $link ? '</a>' : '';

    return $html;
}
