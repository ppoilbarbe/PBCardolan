<?php
if (isset($C_BASE_LIB_INC)) return;
$C_BASE_LIB_INC = 1;

// Site-wide path constants derived from the web server environment.
define('PHP_RACINEFICHIERS', $_SERVER['DOCUMENT_ROOT']);
define('PHP_FICHIERSCRIPT',  $_SERVER['SCRIPT_FILENAME']);
define('PHP_RACINE',         '');
define('PHP_SCRIPT',         $_SERVER['PHP_SELF']);
define('PHP_SITE',           'http://' . $_SERVER['SERVER_NAME']);
define('PHP_PAGEURL',        PHP_SITE . PHP_SCRIPT);
define('PHP_SCRIPTDIR',      dirname(PHP_SCRIPT));

// ---------------------------------------------------------------------------
// Path helpers
// ---------------------------------------------------------------------------

/** Returns the filesystem path to a file inside /include/. */
function included($name)
{
    return PHP_RACINEFICHIERS . '/include/' . $name;
}

/**
 * Converts a relative URL path to an absolute one rooted at PHP_SCRIPTDIR.
 * Paths that already start with '/' are returned unchanged.
 */
function CheminAbsolu($path)
{
    if (substr($path, 0, 1) === '/') return $path;
    return rtrim(PHP_SCRIPTDIR, '/') . '/' . $path;
}

/**
 * Converts a URL path to an absolute filesystem path under the document root.
 */
function CheminAbsoluSysteme($path)
{
    return rtrim(PHP_RACINEFICHIERS, '/') . CheminAbsolu($path);
}

// ---------------------------------------------------------------------------
// Asset lookup
// ---------------------------------------------------------------------------

/**
 * Returns the flagcdn.com URL for a given ISO language/country code.
 * Size: 40×30 px.
 */
function FichierImageLangue($code)
{
    return 'https://flagcdn.com/40x30/' . $code . '.png';
}

/**
 * Searches a list of directories for a file and returns its absolute URL path.
 * Falls back to the filename relative to the current script directory.
 */
function EstFichier($filename, $searchPaths)
{
    foreach ($searchPaths as $dir) {
        if (file_exists($dir . '/' . $filename))
            return CheminAbsolu($dir . '/' . $filename);
    }
    return CheminAbsolu($filename);
}

/** Locates an image in the standard image directories. Passes through http(s) URLs. */
function EstImage($image)
{
    if (substr($image, 0, 4) === 'http') return $image;
    return EstFichier($image, ['.', 'images', '../images', '../../images']);
}

/** Locates a data file in the standard lib directories. */
function EstLib($filename)
{
    return EstFichier($filename, ['.', 'lib', '../lib', '../../lib', '../../../lib']);
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
function DateLng($format, $timestamp = 0)
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
