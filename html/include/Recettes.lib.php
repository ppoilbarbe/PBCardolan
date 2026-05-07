<?php
require_once included('LireParam.lib.php');
require_once included('Edition.lib.php');

// All recipe data, keyed by UPPERCASED "RECIPENAME.FIELD" pairs.
$DescriptionRecettes = LireParam(EstLib('recettes.txt'));

// ---------------------------------------------------------------------------
// Cross-reference helpers
// ---------------------------------------------------------------------------

/**
 * Returns an anchor linking to a recipe page.
 *
 * @param string $name  Recipe key (case-insensitive).
 * @param string $label Link text; defaults to the recipe's TITRE field.
 */
function UneReference($name, $label = '')
{
    global $DescriptionRecettes;
    $key   = strtoupper($name);
    $label = $label ?: $DescriptionRecettes[$key . '.TITRE'][0];
    return '<a href="UneRecette.php?NOMRECETTE=' . $key . '">' . $label . '</a>';
}

/**
 * Replaces inline cross-reference markers with recipe links.
 * Markers: #RECIPENAME# or #RECIPENAME|display text#
 */
function ExpandReferences($text)
{
    return preg_replace_callback(
        '/#([A-Za-z]+)(?:\|(.+?))?#/',
        function($m) { return UneReference($m[1], isset($m[2]) ? $m[2] : ''); },
        $text
    );
}

// ---------------------------------------------------------------------------
// Field accessors
// ---------------------------------------------------------------------------

/**
 * Returns the first value of a recipe field, with cross-references expanded.
 *
 * @param string $recipe Uppercase recipe key.
 * @param string $field  Field name (e.g. 'TITRE', 'CATEGORIE').
 */
function EstTexteSimple($recipe, $field)
{
    global $DescriptionRecettes;
    return ExpandReferences($DescriptionRecettes[$recipe . '.' . $field][0] ?? '');
}

/**
 * Returns all values of a recipe field joined as <P> paragraphs,
 * with empty lines converted to paragraph breaks.
 *
 * @param string $recipe Uppercase recipe key.
 * @param string $field  Field name (e.g. 'RECETTE', 'COMMENTAIRES').
 */
function EstTexte($recipe, $field)
{
    global $DescriptionRecettes;

    $out = '<P>';
    foreach ($DescriptionRecettes[$recipe . '.' . $field] ?? [] as $line) {
        $line = trim($line);
        $out .= $line !== '' ? ExpandReferences($line) . ' ' : '</P><P>';
    }
    return $out . '</P>';
}
