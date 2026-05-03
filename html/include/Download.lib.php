<?php
if (isset($C_DOWNLOAD_LIB_INC)) return;
$C_DOWNLOAD_LIB_INC = 1;

require_once included('LireParam.lib.php');
require_once included('Edition.lib.php');

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
function DoDownload($paramFile)
{
    $data = LireParam($paramFile);

    foreach ($data['TELECHARGE'] as $entry) {
        $key      = strtoupper($entry);
        $filename = $data[$key . '.FICHIER'][0];
        $isUrl    = (bool) preg_match('#^https?://#i', $filename);
        $counted  = !$isUrl && $data[$key . '.COMPTER'][0];
        $linkText = $data[$key . '.TEXTE'][0] ?? '';

        if ($isUrl) {
            $description = '';
            foreach ($data[$key . '.DESCRIPTION'] ?? [] as $line) {
                $description .= $line === '' ? "<BR>\n" : autoLinkUrls($line) . "\n";
            }
            $version = $data[$key . '.VERSION'][0] ?? '';
            $label   = $linkText !== '' ? htmlspecialchars($linkText) : htmlspecialchars($filename);

            Ligne('<TR>');
            Ligne('<TD class="Fichier"><a href="' . htmlspecialchars($filename) . '">' . $label . '</a></TD>');
            Ligne('<TD class="Texte">' . $description . '</TD>');
            Ligne('</TR>');

            Ligne('<TR><TD class="Infos" colspan=2>');
            foreach (splitMultiValue($data[$key . '.SYSTEME'] ?? []) as $osCode) {
                $osName = OS_NAMES[strtoupper($osCode)] ?? '';
                if ($osName) Ligne(LienImage('', 'Logo' . $osName . '.gif', $osName));
            }
            foreach (splitMultiValue($data[$key . '.LANGUES'] ?? []) as $langCode) {
                Ligne('<img src="' . FichierImageLangue($langCode) . '" border=0>');
            }
            if ($version !== '') Ligne('&nbsp;Version: ' . $version);
            Ligne('</TD></TR>');
            continue;
        }

        $sysPath = $counted
            ? CheminAbsoluSysteme('/sendfiles/' . $filename)
            : CheminAbsoluSysteme($filename);

        if (!file_exists($sysPath)) {
            Ligne("<TR><TD>$filename<BR>$sysPath</TD><TD><B>PAS TROUVE</B></TD></TR>");
            continue;
        }

        // Build the multi-line description (empty lines become <BR>).
        $description = '';
        foreach ($data[$key . '.DESCRIPTION'] ?? [] as $line) {
            $description .= $line === '' ? "<BR>\n" : $line . "\n";
        }

        $date    = DateLng('j-M-Y', filemtime($sysPath));
        $size    = filesize($sysPath) / 1024.0;
        $version = $data[$key . '.VERSION'][0] ?? '';

        // Read the download count if the counter file is newer than the download.
        $downloads = '';
        if ($counted) {
            $logFile = $sysPath . '.count';
            if (file_exists($logFile) && filemtime($sysPath) <= filemtime($logFile)) {
                $count = (int) file_get_contents($logFile);
                if ($count > 1)
                    $downloads = sprintf('&nbsp;&nbsp;&nbsp;Téléchargé %d fois', $count);
            }
        }

        // File link and description.
        $href = $counted
            ? PHP_RACINE . '/envoyer.php?FICHIER=' . rawurlencode($filename)
              . " onmouseover=\"window.status='$filename'; return true;\""
              . " onmouseout=\"window.status=''; return true;\""
            : CheminAbsolu($filename);
        Ligne('<TR>');
        $label = $linkText !== '' ? htmlspecialchars($linkText) : $filename;
        Ligne('<TD class="Fichier"><a href="' . $href . '">' . $label . '</a></TD>');
        Ligne('<TD class="Texte">' . $description . '</TD>');
        Ligne('</TR>');

        // OS logos, language flags, and file metadata.
        Ligne('<TR><TD class="Infos" colspan=2>');
        foreach (splitMultiValue($data[$key . '.SYSTEME'] ?? []) as $osCode) {
            $osName = OS_NAMES[strtoupper($osCode)] ?? '';
            if ($osName) Ligne(LienImage('', 'Logo' . $osName . '.gif', $osName));
        }
        foreach (splitMultiValue($data[$key . '.LANGUES'] ?? []) as $langCode) {
            Ligne('<img src="' . FichierImageLangue($langCode) . '" border=0>');
        }
        Ligne(sprintf(
            '&nbsp;Version: %s&nbsp;&nbsp;&nbsp;Size:&nbsp;<B>%1.1fKb</B>&nbsp;&nbsp;&nbsp;<em>Posted on %s</em>',
            $version, $size, $date
        ) . $downloads);
        Ligne('</TD></TR>');
    }
}

/**
 * Renders a complete download table wrapped in a styled <DIV>.
 *
 * @param string $paramFile       Data filename (resolved via EstLib()).
 * @param string $cssClass        CSS class for the wrapping <DIV>.
 */
function DoTableDownload($paramFile, $cssClass)
{
    Ligne('<DIV class="' . $cssClass . '"><BR><TABLE>');
    DoDownload(EstLib($paramFile));
    Ligne('</TABLE><BR></DIV>');
}
