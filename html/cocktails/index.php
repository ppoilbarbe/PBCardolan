<?php
/**
 * cocktails/index.php — Cocktail recipes (under construction).
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Base.lib.php';

require_once included('MiseEnPage.lib.php');
require_once included('BoutonsGeneraux.lib.php');

DebutEnTete('Cocktails: Pour les assoiffés', 'Cocktails,Français');
FinEnTete();
DebutPage('Cocktails', 1, $BoutonsGeneraux);
EnConstruction();
FinPage();
