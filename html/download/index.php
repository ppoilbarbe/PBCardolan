<?php
/**
 * download/index.php — Home-made programs available for download.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Base.lib.php';

require_once included('MiseEnPage.lib.php');
require_once included('BoutonsGeneraux.lib.php');
require_once included('Download.lib.php');

DebutEnTete('Quelques programmes maison', '');
FinEnTete();
DebutPage('Programmes', 3, $BoutonsGeneraux);
?>
<p>Voici quelques programmes développés à la maison pour un usage personnel.
Si cela intéresse quelqu'un, ils sont disponibles pour tout un chacun.</p>
<p>Si vous les appréciez ou si vous désirez des modifications, je serais
heureux que vous m'en fassiez part.</p>
<?php
DoTableDownload('file_download.txt', 'Download');
FinPage();
