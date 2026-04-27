<?php
if (isset($C_EDITION_LIB_INC)) return;
$C_EDITION_LIB_INC = 1;

// ---------------------------------------------------------------------------
// Output helpers
// ---------------------------------------------------------------------------

/** Outputs a line of HTML followed by a newline. */
function Ligne($html) { echo $html, "\n"; }

/** Outputs a line of HTML followed by <BR> and a newline. */
function LigneBR($html) { echo $html, "<BR>\n"; }

/** Outputs an <h1> heading. */
function Titre($text) { echo "<h1>$text</h1>\n"; }

/** Outputs an <h2> heading. */
function SousTitre($text) { echo "<h2>$text</h2>\n"; }

// ---------------------------------------------------------------------------
// Asset path resolution
// ---------------------------------------------------------------------------

/**
 * Walks up the directory tree (up to 4 levels) to locate a file by name.
 * Used to resolve CSS and JS paths from deeply nested pages.
 */
function TrouveFichierUtilisateur($name)
{
    foreach (['.', '..', '../..', '../../..'] as $dir) {
        if (file_exists($dir . '/' . $name))
            return CheminAbsolu($dir . '/' . $name);
    }
    return CheminAbsolu($name);
}

// ---------------------------------------------------------------------------
// HTML asset tags
// ---------------------------------------------------------------------------

/**
 * Outputs a <LINK> stylesheet tag.
 *
 * @param string $name  Filename inside the css/ directory.
 * @param string $media Optional media attribute (e.g. 'print', 'screen').
 */
function FeuilleStyle($name, $media = '')
{
    $mediaAttr = $media ? " media=\"$media\"" : '';
    Ligne('<LINK rel="stylesheet"' . $mediaAttr . ' href="' . TrouveFichierUtilisateur("css/$name") . '" type="text/css">');
}

/**
 * Outputs a <SCRIPT> tag for a JavaScript file inside the jscript/ directory.
 */
function JavaScript($name)
{
    Ligne('<SCRIPT LANGUAGE="javascript" src="' . TrouveFichierUtilisateur("jscript/$name") . '"></SCRIPT>');
}

// ---------------------------------------------------------------------------
// Image rendering
// ---------------------------------------------------------------------------

/**
 * Returns a "width=x height=y" attribute string for a local image, or '' if
 * the image cannot be found.
 */
function TailleImage($image)
{
    $path = CheminAbsoluSysteme($image);
    if (!file_exists($path)) return '';
    $size = getimagesize($path);
    return ' ' . $size[3];
}

/**
 * Builds an <img> tag, optionally wrapped in an <a> with rollover behaviour.
 *
 * @param string $link        URL for the anchor; empty string for no link.
 * @param string $image       Image filename (resolved via EstImage()).
 * @param string $text        Alt text and status-bar text on hover.
 * @param string $class       CSS class for the <img>.
 * @param string $imgName     Name attribute for the <img> (needed for JS swaps).
 * @param string $imgTarget   Name of the image to swap (defaults to $imgName).
 * @param string $mouseOut    Image filename to swap to on mouseout.
 * @param string $mouseOver   Image filename to swap to on mouseover.
 * @param string $mouseDown   Image filename to swap to on mousedown.
 */
function LienImage($link, $image, $text = '', $class = '', $imgName = '',
                   $imgTarget = '', $mouseOut = '', $mouseOver = '', $mouseDown = '')
{
    $src    = EstImage($image);
    $target = $imgTarget ?: $imgName;
    $events = '';

    if ($mouseOver) {
        $over    = EstImage($mouseOver);
        $status  = $text ? "window.status='$text'; " : '';
        $events .= " onmouseover=\"{$status}changeImages('$target', '$over'); return true;\"";
    }
    if ($mouseOut) {
        $out     = EstImage($mouseOut);
        $status  = $text ? "window.status=''; " : '';
        $events .= " onmouseout=\"{$status}changeImages('$target', '$out'); return true;\"";
    }
    if ($mouseDown) {
        $down    = EstImage($mouseDown);
        $up      = $mouseOver ? EstImage($mouseOver) : $src;
        $events .= " onmousedown=\"changeImages('$target', '$down'); return true;\"";
        $events .= " onmouseup=\"changeImages('$target', '$up'); return true;\"";
    }

    $nameAttr  = $imgName ? " name=\"$imgName\"" : '';
    $classAttr = $class   ? " class=\"$class\""  : '';
    $altAttr   = $text    ? " alt=\"$text\""     : '';

    $html = $link ? "<a href=\"$link\" class=Image$events>\n" : '';
    $html .= "<img src=\"$src\"" . TailleImage($src) . $classAttr . $altAttr;
    $html .= $link ? '' : $events;
    $html .= "$nameAttr>";
    $html .= $link ? '</a>' : '';

    return $html;
}
