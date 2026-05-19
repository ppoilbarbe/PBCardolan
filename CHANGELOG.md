# Changelog

Tous les changements notables sont documentés ici.
Format : [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/).

## [2026.2] - 2026-05-19

### Added
- `$navButtons` : colonne `title` (index 4) pour le titre affiché dans le bandeau, indépendamment de l'identifiant technique du sous-site.
- `Makefile` : `livetest` dépend désormais de `stop` — l'ancien serveur est toujours arrêté avant d'en démarrer un nouveau.
- `Makefile` : note dans l'aide sur les paquets système requis (`php-mysql`, `php-mbstring`) pour `make livetest NOCONDA=1`.

### Changed
- Images de boutons de navigation converties de GIF en PNG ; état normal renommé de `Btn N.gif` en `name-up.png` (suffixes `-up`, `-over`, `-down`).
- Images logos OS (Linux, Windows, MacOS) et `home` converties de GIF en PNG.
- `recipe_integration.lib.php` : `recipe_body()` déduit le titre affiché et l'index du bouton actif depuis `$navButtons` au lieu de valeurs codées en dur.
- `html/css/recipes.css` : suppression de l'écrasement erroné de `--clr-primary` par `#D0D0D0` (gris illisible sur fond beige) ; ajout des variables `--radius` et `--shadow` manquantes.
- Palette CSS : bandeau d'en-tête et barre de navigation éclaircis (`--clr-header` : `#3A2E26` → `#8C5238` ; `--clr-sidebar` : `#26211D` → `#6B3F2B` ; dégradé ajusté) tout en conservant un contraste suffisant avec le texte ivoire.

### Removed
- Anciennes images GIF de boutons (`Btn1`–`Btn5` et variantes), logos OS GIF, `home.gif`, et images de mise en page obsolètes (`ZoneHaut`, `ZoneBas`, etc.).

## [2026.1] - 2026-01-01

- Version initiale modernisée : remise au goût du jour du PHP et des CSS, normalisation des noms de fichiers et variables, intégration du module recettes PBRecipe.
