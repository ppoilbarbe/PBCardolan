<?php
if (isset($_PARAMS_LIB_INC)) return;
$_PARAMS_LIB_INC = 1;

/**
 * Parses a KEY>VALUE data file into an associative array.
 *
 * File format:
 *   - Lines starting with '#' are comments and are ignored.
 *   - Each data line has the form: KEY>VALUE
 *   - Keys are stored uppercased; duplicate keys produce an array of values.
 *
 * @param  string     $filename Absolute filesystem path to the data file.
 * @return array|null           Parsed data, or null if the file cannot be opened.
 */
function readParams($filename)
{
    $fh = fopen(absolutePath($filename), 'r');
    if (!$fh) return null;

    $result = [];
    while (($line = fgets($fh, 4096)) !== false) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;

        $sep   = strpos($line, '>');
        $key   = strtoupper(trim(substr($line, 0, $sep)));
        $value = trim(substr($line, $sep + 1));
        $result[$key][] = $value;
    }
    fclose($fh);

    return $result;
}
