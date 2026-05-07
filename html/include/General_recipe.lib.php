<?php
/**
 * General_recipe.lib.php — Integration shim for PBRecipe sub-sites.
 *
 * When PBRecipe is deployed as a sub-directory of this site it detects this
 * file via a relative path and calls the three functions below instead of
 * rendering its own standalone HTML skeleton.  All visual chrome (navigation,
 * footer, CSS) is therefore provided here for site-wide coherence.
 *
 * Usage (called by PBRecipe's index.php):
 *
 *   recipe_header($type)   — before <body>  : DOCTYPE + <head>…</head>
 *   <body>
 *   recipe_body($type)     — after  <body>  : navigation chrome
 *   … recipe content …
 *   recipe_footer($type)   — before </body> : footer
 *   </body></html>
 *
 * @param string $type  Recipe section label (e.g. the SITE_TITLE constant from
 *                      PBRecipe's config.php).  Used as the page sub-title and
 *                      meta keywords.
 */
if (isset($C_GENERAL_RECIPE_LIB_INC)) return;
$C_GENERAL_RECIPE_LIB_INC = 1;

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/Site.lib.php';

/**
 * Outputs DOCTYPE + <head>…</head>.  Call before <body>.
 */
function recipe_header(string $type): void
{
    if ($type == "recettes")
    {
      $html_title = 'Recettes — Pour les affamés';
      $keywords = "Recettes, Cuisine";
    }
    elseif ($type == "cocktails")
    {
      $html_title = 'Cocktails — Pour les assoifés';
      $keywords = "Cocktails, mocktails, long drinks, highballs, tiki drinks, short drinks, punchs";
    }
    else
    {
      $html_title = $type;
      $keywords = "recettes";
    }
    do_header($html_title, [
        'keywords' => $keywords,
        'printable' => 1,
    ]);
}

/**
 * Renders the navigation chrome just after <body>.
 * Button index 2 (0-based) = Btn3 "Recettes: Pour les affamés".
 */
function recipe_body(string $type): void
{
    do_page_open($type ?: 'Recettes', 2);
}

/**
 * Renders the page footer just before </body>.
 */
function recipe_footer(string $type): void
{
    do_footer();
}
