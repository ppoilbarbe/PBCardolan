<?php
if (isset($C_DOWNLOAD_LIB_INC)) return;
$C_DOWNLOAD_LIB_INC = 1;

require_once included('LireParam.lib.php');
require_once included('Edition.lib.php');

// Maps single-letter OS codes (as used in data files) to display names.
const OS_NAMES = ['W' => 'Windows', 'L' => 'Linux', 'M' => 'MacOS'];

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
        $key     = strtoupper($entry);
        $filename = $data[$key . '.FICHIER'][0];
        $counted  = $data[$key . '.COMPTER'][0];
        $sysPath  = $counted
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
        Ligne('<TD class="Fichier"><a href="' . $href . '">' . $filename . '</a></TD>');
        Ligne('<TD class="Texte">' . $description . '</TD>');
        Ligne('</TR>');

        // OS logos, language flags, and file metadata.
        Ligne('<TR><TD class="Infos" colspan=2>');
        foreach ($data[$key . '.SYSTEME'] ?? [] as $osCode) {
            $osName = OS_NAMES[strtoupper($osCode)] ?? '';
            if ($osName) Ligne(LienImage('', 'Logo' . $osName . '.gif', $osName));
        }
        foreach ($data[$key . '.LANGUES'] ?? [] as $langCode) {
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
