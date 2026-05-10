<?php
if (isset($_DOWNLOAD_LIB_INC)) return;
$_DOWNLOAD_LIB_INC = 1;

require_once includePath('params.lib.php');
require_once includePath('base.lib.php');

// Maps single-letter OS codes (as used in data files) to display names.
const OS_NAMES = ['W' => 'Windows', 'L' => 'Linux', 'M' => 'MacOS'];

// Wraps bare URLs in <a> tags, leaving already-linked URLs untouched.
function autoLinkUrls(string $text): string
{
    return preg_replace('#(?<!["\'])https?://[^\s<>"\']+#', '<a href="$0">$0</a>', $text);
}

// Flattens an array of potentially space-separated values into individual tokens.
function splitMultiValue(array $values): array
{
    $result = [];
    foreach ($values as $v) {
        foreach (preg_split('/\s+/', trim($v), -1, PREG_SPLIT_NO_EMPTY) as $token)
            $result[] = $token;
    }
    return $result;
}

/**
 * Renders the rows of a download table for a single param file.
 * Intended to be called from within a <TABLE> context.
 *
 * @param string $paramFile Absolute filesystem path to the data file.
 */
function renderDownloadRows($paramFile)
{
    $data = readParams($paramFile);

    foreach ($data['TELECHARGE'] as $entry) {
        $key      = strtoupper($entry);
        $filename = $data[$key . '.FICHIER'][0];
        $isUrl    = (bool) preg_match('#^https?://#i', $filename);
        $counted  = !$isUrl && $data[$key . '.COMPTER'][0];
        $linkText = $data[$key . '.TEXTE'][0] ?? '';

        $image     = $data[$key . '.IMAGE'][0] ?? '';
        $imageHtml = $image !== '' ? '<img src="' . findImage($image) . '" style="max-width:64px;max-height:64px" alt="">' . "<BR>\n" : '';

        if ($isUrl) {
            $description = '';
            foreach ($data[$key . '.DESCRIPTION'] ?? [] as $line) {
                $description .= $line === '' ? "<BR>\n" : autoLinkUrls($line) . "\n";
            }
            $version = $data[$key . '.VERSION'][0] ?? '';
            $label   = $linkText !== '' ? htmlspecialchars($linkText) : htmlspecialchars($filename);

            writeLine('<TR>');
            writeLine('<TD class="Fichier">' . $imageHtml . '<a href="' . htmlspecialchars($filename) . '">' . $label . '</a></TD>');
            writeLine('<TD class="Texte">' . $description . '</TD>');
            writeLine('</TR>');

            writeLine('<TR><TD class="Infos" colspan=2>');
            foreach (splitMultiValue($data[$key . '.SYSTEME'] ?? []) as $osCode) {
                $osName = OS_NAMES[strtoupper($osCode)] ?? '';
                if ($osName) writeLine(imageLink('', 'Logo' . $osName . '.gif', $osName));
            }
            foreach (splitMultiValue($data[$key . '.LANGUES'] ?? []) as $langCode) {
                writeLine('<img src="' . flagUrl($langCode) . '" border=0>');
            }
            if ($version !== '') writeLine('&nbsp;Version: ' . $version);
            writeLine('</TD></TR>');
            continue;
        }

        $sysPath = $counted
            ? absolutePath('/sendfiles/' . $filename)
            : absolutePath($filename);

        if (!file_exists($sysPath)) {
            writeLine("<TR><TD>$filename<BR>$sysPath</TD><TD><B>PAS TROUVE</B></TD></TR>");
            continue;
        }

        $description = '';
        foreach ($data[$key . '.DESCRIPTION'] ?? [] as $line) {
            $description .= $line === '' ? "<BR>\n" : $line . "\n";
        }

        $date    = frenchDate('j-M-Y', filemtime($sysPath));
        $size    = filesize($sysPath) / 1024.0;
        $version = $data[$key . '.VERSION'][0] ?? '';

        $downloads = '';
        if ($counted) {
            $logFile = $sysPath . '.count';
            if (file_exists($logFile) && filemtime($sysPath) <= filemtime($logFile)) {
                $count = (int) file_get_contents($logFile);
                if ($count > 1)
                    $downloads = sprintf('&nbsp;&nbsp;&nbsp;Téléchargé %d fois', $count);
            }
        }

        $href = $counted
            ? BASE_PATH . '/send_file.php?FICHIER=' . rawurlencode($filename)
              . " onmouseover=\"window.status='$filename'; return true;\""
              . " onmouseout=\"window.status=''; return true;\""
            : absoluteUrl($filename);
        writeLine('<TR>');
        $label = $linkText !== '' ? htmlspecialchars($linkText) : $filename;
        writeLine('<TD class="Fichier">' . $imageHtml . '<a href="' . $href . '">' . $label . '</a></TD>');
        writeLine('<TD class="Texte">' . $description . '</TD>');
        writeLine('</TR>');

        writeLine('<TR><TD class="Infos" colspan=2>');
        foreach (splitMultiValue($data[$key . '.SYSTEME'] ?? []) as $osCode) {
            $osName = OS_NAMES[strtoupper($osCode)] ?? '';
            if ($osName) writeLine(imageLink('', 'Logo' . $osName . '.gif', $osName));
        }
        foreach (splitMultiValue($data[$key . '.LANGUES'] ?? []) as $langCode) {
            writeLine('<img src="' . flagUrl($langCode) . '" border=0>');
        }
        writeLine(sprintf(
            '&nbsp;Version: %s&nbsp;&nbsp;&nbsp;Size:&nbsp;<B>%1.1fKb</B>&nbsp;&nbsp;&nbsp;<em>Posted on %s</em>',
            $version, $size, $date
        ) . $downloads);
        writeLine('</TD></TR>');
    }
}

/**
 * Renders a complete download table wrapped in a styled <DIV>.
 *
 * @param string $paramFile  Data filename (resolved via findLib()).
 * @param string $cssClass   CSS class for the wrapping <DIV>.
 */
function renderDownloadTable($paramFile, $cssClass)
{
    writeLine('<DIV class="' . $cssClass . '"><BR><TABLE>');
    renderDownloadRows(findLib($paramFile));
    writeLine('</TABLE><BR></DIV>');
}
