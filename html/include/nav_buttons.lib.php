<?php
if (isset($_NAV_BUTTONS_LIB_INC)) return;
$_NAV_BUTTONS_LIB_INC = 1;

// Navigation buttons: [imageBaseName, tooltip, url, active]
// imageBaseName resolves to images/BtnN.gif / BtnN-over.gif / BtnN-down.gif.
// Set active to 0 to display the button without a link (placeholder).
$navButtons = [
    ['Btn1', 'Quelques informations sur le mah-jong',    '/mahjong/',    1],
    ['Btn2', 'Cocktails: Pour les assoiffés',            '/cocktails/',  1],
    ['Btn3', 'Recettes: Pour les affamés',               '/recettes/',   1],
    ['Btn4', 'Quelques programmes maison à récupérer',   '/download/',   1],
    ['Btn5', 'Quelques liens plus ou moins utiles',      '/links/',      1],
];
