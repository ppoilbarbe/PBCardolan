<?php
if (isset($_NAV_BUTTONS_LIB_INC)) return;
$_NAV_BUTTONS_LIB_INC = 1;

// Navigation buttons: [imageBaseName, tooltip, url, active, title]
// imageBaseName resolves to images/name.png (hover/active states are pure CSS, see barre.css).
// title is the display label shown in the page banner.
// Set active to 0 to display the button without a link (placeholder).
$navButtons = [
    ['mahjong',     'Quelques informations sur le mah-jong',    '/mahjong/',    1, 'Le Mah-Jong'   ],
    ['cocktails',   'Cocktails: Pour les assoiffés',            '/cocktails/',  1, 'Cocktails'  ],
    ['recettes',    'Recettes: Pour les affamés',               '/recettes/',   1, 'Recettes de cuisine'   ],
    ['programmes',  'Quelques programmes maison à récupérer',   '/download/',   1, 'Programmes' ],
    ['liens',       'Quelques liens plus ou moins utiles',      '/links/',      1, 'Quelques liens'      ],
];
