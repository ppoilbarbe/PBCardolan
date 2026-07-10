# Changelog

All notable changes to this project are documented in this file.
Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

Version numbers follow `YYYY.n`: the current year, followed by a sequence
number incremented for each release within that year.

## [Unreleased]

### Added
- `html/css/barre.css`: sidebar nav buttons now use a single 64x64 image per button (`cocktails.png`, `liens.png`, `mahjong.png`, `programmes.png`, `recettes.png`); the normal/hover/active-section looks (drop shadow, "pressed" look, grayscale) are recreated purely in CSS instead of three baked-in image variants.

### Changed
- `imageLink()` (`base.lib.php`): dropped the `mouseOver`/`mouseOut`/`mouseDown`/`imgTarget` parameters, unused now that nav-button rollovers are CSS-only.

### Removed
- `html/jscript/boutons.js` and the `-up`/`-over`/`-down` nav button image variants (`cocktails`, `liens`, `mahjong`, `programmes`, `recettes`): replaced by CSS-driven states on a single image (see Added).
- `preloadNavImages()` (`layout.lib.php`) and the `buttons` option of `do_header()`: no longer needed once there are no `-over`/`-down` images to preload.

## [2026.7] - 2026-07-10

### Added
- `Makefile`: `update-icons` target to refresh `html/images/*.png` from the PBIcons repository.
- `tools/update_icons.sh`: downloads the source images from `ppoilbarbe/PBIcons` (via Git LFS), resizes any file larger than 128x128 down to that size, and skips files whose source content hasn't changed since the last run (SHA-256 hash cached in `html/images/<file>.sha256`, version-controlled).
- `tools/update_icons.sh`: also downloads `html/images/pbcardolan-full.png` and `Sindarin.jpg` as-is (no resizing), from the PBIcons repository.
- `html/images/pbpicat.png`, `pbprompt.png`, `pbrecipe.png`, `pbregisteractivity.png`, `pbrenamer.png`: new icons synced from PBIcons.

### Changed
- `html/images/pbcardolan.png`, `pbicons.png`: resynced from PBIcons and resized to 128x128.

### Fixed
- `html/images/pbcardolan-full.png`: restored, wrongly removed in 2026.6 while still referenced by the `/blason/` page.

## [2026.6] - 2026-07-10

### Added
- `html/download/`: new `PBIcons` entry (raster images/icons and their SVG vectorization), with `pbicons.png` image.
- `html/download/lib/file_download.txt`: `PBRegisterActivity` entry now links to its GitHub releases page.

### Changed
- `LICENSE`: replaced with the full CC BY-NC-SA 4.0 legal text (was a hand-written summary), so GitHub correctly detects the license instead of showing "Other".
- `/blason/` page: fixed Sindarin place names — "Fontgrise (Hoarwell)" corrected to "la Mitheithel (la Fontgrise)", "Hauts des Galgals" corrected to "Tyrn Gorthad (les Hauts-des-Galgals)".
- Home page: removed the obsolete "Optimisé pour une résolution minimale de 1024x768" mention.
- `html/download/lib/file_download.txt`: fixed incorrect releases links for `PBPicat` (was pointing to `PBRenamer`), `PBRecipe` (was pointing to `PBBoule`), and `PBPrompt` (wording).

### Removed
- `html/images/pbcardolan-full.png`: original-size image now lives in the `PBIcons` repository, it does not belong here.

## [2026.5] - 2026-07-02

### Added
- `html/download/`: `pbcardolan.png` image for the PBCardolan download entry.
- Home page: `pbcardolan.png` logo (64px) in the header, replacing the "Cardolan" text, linking externally to the Fandom wiki page.
- New `/blason/` page explaining the Cardolan emblem's symbolism (tree, stars, mountains, crown), displaying `pbcardolan-full.png` linked to the Fandom wiki. The footer's Cul-de-Sac icon now links here instead of the home page.
- `README.md`: site and sub-sites overview (reference to PBRecipe).
- Home page: clicking the Sindarin banner image now opens a modal with the French translation of the Tengwar phonetic-English text. No visual indication (no link, no cursor change) hints that the image is clickable.
- `general.css`: modal (overlay + box) styling, aligned with the site palette.

### Changed
- Home page: `Sindarin.jpg` (new high-resolution version) now displayed at 60% of the page width.
- `html/images/Sindarin.jpg` replaced again by an even higher-resolution version.

### Fixed
- `/download` page: several links (Github, flagpedia.net, README, releases, tinyMediaManager) used `<a src="...">` instead of `<a href="...">`, making them non-clickable and unstyled.

### Removed
- `html/images/FondFondPage.jpg` (unused).
- Unused `html/images/Sindarin-16x9.jpg` and `Sindarin-21x9.jpg` crop variants.

## [2026.4] - 2026-07-01

### Added
- `LICENSE` file at the repository root: HTML/site source code, text content (recipes, cocktails, editorial content) and all images/icons are licensed under CC BY-NC-SA 4.0.
- `html/licence/index.php`: dedicated page detailing the licensing terms, linked from the footer.
- `imageLink()` (`base.lib.php`): new `$linkTarget` parameter to open a link in a new tab (`target="_blank" rel="noopener"`).
- `openPage()` (`layout.lib.php`): section title in the header is now a clickable link to the active sub-site.
- `openHead()` (`layout.lib.php`): `viewport` meta tag for responsive rendering.
- `Makefile`: `deploy` target (`sitecopy`).

### Changed
- Footer copyright notice: "Tous droits réservés" replaced by "CC BY-NC-SA", linking to `/licence/`.
- Home page: "Cardolan" link now points to the Fandom wiki page (`lotr.fandom.com`) instead of a local anchor.
- April.org banners (`index.php`, footer) now open in a new tab.
- Footer (`FondPage`): reduced padding and top margin for a more compact layout.
- Home icon changed from `home.png` to `home-hobbit.png`.
- `.gitignore`: exclude `html/cocktails/`.

## [2026.3] - 2026-05-19

### Removed
- Sous-site cocktails retiré de ce dépôt : `html/cocktails/index.php` et les images GIF associées (cocktails migrés en sous-site indépendant, comme les recettes).

### Fixed
- `download.lib.php` : attributs `onmouseover`/`onmouseout` concaténés par erreur dans la valeur `href`, cassant les liens de téléchargement (`ReglesMahJong.pdf`, `CalculMahjong.zip`).

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
