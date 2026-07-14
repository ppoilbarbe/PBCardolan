# Changelog

All notable changes to this project are documented in this file.
Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

Version numbers follow `YYYY.n`: the current year, followed by a sequence
number incremented for each release within that year.

## [Unreleased]

### Added
- `html/praetorians/index.php`, `html/css/praetorians.css`: new easter egg page, reachable only through the `pi.png` link on the home page, not part of the navigation menu. Browser tab title in Greek ("ὁ Κέρβερος τῶν Πραιτωριανῶν"). Illuminated-manuscript ("incunable") styling: page title ("Les Prétoriens") in large, small-caps, accent-colored type; drop cap on each prose paragraph; a credo (a tech/surveillance-flavored paraphrase of Tolkien's Ring-verse) displayed as a distinct block with its original line breaks preserved, in a Greek-capable serif font; closing signature ("οἱ Πραιτωριανοί") in large bold type, paired with `praetorians-watcher.png` (a nod to HAL 9000's red eye from *2001: A Space Odyssey*) placed to its left in a flex row, both vertically centered on each other; `praetorians-background.jpg` (a circuit-board pattern) tiled behind the manuscript via `.ZoneCentre:has(.Incunable)`, scoped to this page only.
- `html/css/pi-egg.css`, `html/index.php`: easter egg on the home page — `pi.png` (9x9, white 3px drop shadow) is shown bottom-right on every page load, then fades out and becomes unclickable after 5 seconds (pure CSS `@keyframes` + `pointer-events`). While visible, only a Ctrl/Cmd+click opens `/praetorians/`; a plain click does nothing. The click is always intercepted and, on Ctrl/Cmd, navigation is done via `window.location.href` rather than letting the browser follow the link natively — otherwise Ctrl/Cmd+click always opens a new tab regardless of `target`. A nod to the old floating logo from `jscript/pi.js` (1999).
- `tools/update_icons.py`: `pi.png` and `praetorians-background.jpg` added to the icons synced from PBIcons (`cardolan/pi-small.png`, `cardolan/praetorians-background.jpg`, both copied as-is).
- `README.md`: new "Easter egg — π / Prétoriens" section documenting the feature, noting it revives an idea already present in the original 1999 site (`pi.js`'s floating logo) that had been broken for years, and its references to the film *Traque sur Internet* (*The Net*, 1995) and, in keeping with the site's Tolkien identity, to *The Lord of the Rings*' Ring-verse.

### Changed
- `html/css/barre.css`: sidebar nav button boxes switched from a non-square footprint (`53x64`, `44x52` on ≤420px screens) to square (`64x64`, `32x32` on ≤420px screens), since all button icons are now square 128x128 images synced from PBIcons and any future one will be too; the two `--barre-btn-w`/`--barre-btn-h` variables are merged into a single `--barre-btn-size`. `object-fit: contain` is kept as a safety net in case a future icon isn't square.
- `tools/update_icons.py`: `praetorians-watcher.png` entry moved to keep the `ICONS` dict alphabetically sorted by destination filename.

### Removed
- `html/jscript/pi.js` and `html/images/pi.gif`, `pi2.gif`: antique 1999 floating-logo script and its images, replaced by the CSS-only easter egg (see Added); `pi.js` was already effectively dead code (relied on `document.layers`/`document.all`, long-gone browser APIs).

### Fixed
- `LICENSE`: removed the custom copyright/summary preamble that preceded the official CC BY-NC-SA 4.0 legal text, which was preventing GitHub's license detector (`licensee`) from recognizing the license; the copyright notice and contact address are now in `README.md` instead.

## [2026.8] - 2026-07-11

### Added
- `html/css/barre.css`: sidebar nav buttons now use a single 64x64 image per button (`cocktails.png`, `liens.png`, `mahjong.png`, `programmes.png`, `recettes.png`); the normal/hover/active-section looks (drop shadow, "pressed" look, grayscale) are recreated purely in CSS instead of three baked-in image variants.
- `tools/update_icons.py`, `Makefile`: `btn-cocktails`, `btn-links`, `btn-mahjong`, `btn-programs`, `btn-recipes` added to the list of icons synced from PBIcons (`cardolan/` directory) and resized to 128x128.
- `Makefile`: `favicon` target, generates a multi-size `html/images/favicon.ico` (16 to 256px) from `html/images/pbcardolan-full.png`; depends on that file, itself fetched via `update-icons` if missing.
- `tools/update_icons.py`: `home-hobbit.png` added to the icons synced from PBIcons, resized to a 128px width (not square) from `cardolan/hobbit-home-button-big.png`.
- `environment.yml`: `python` and `imagemagick` added as conda dependencies, used by `tools/update_icons.py` and the `favicon` target.
- `README.md`: new "Dépendances" section under "Développement", documenting the tools required for each `Makefile` target.

### Changed
- `html/images/cocktails.png`, `liens.png`, `mahjong.png`, `programmes.png`, `recettes.png`: the antique 1999 button images — already reduced from three variants (`-up`/`-over`/`-down`) to one per button — are now replaced altogether by newer 128x128 icons synced from PBIcons, renamed with an English `btn-` prefix (`btn-cocktails.png`, `btn-links.png`, `btn-mahjong.png`, `btn-programs.png`, `btn-recipes.png`); `html/include/nav_buttons.lib.php` updated accordingly.
- `html/css/barre.css`: sidebar nav button images are now sized to the button box with `object-fit: contain` to avoid distortion now that source icons (128x128) no longer match the button's non-square footprint (53x64); `overflow: hidden` dropped from `.Btn1`…`.Btn5` so the hover/active "pressed" translation no longer crops icons whose artwork reaches the edges; shadow offset/blur increased from 3px to 5px for visibility.
- `html/images/PB-Soft.ico` renamed to `html/images/favicon.ico` (standard favicon name), regenerated in multiple sizes (16 to 256px) from `pbcardolan-full.png`; `html/index.php` updated accordingly.
- `tools/update_icons.sh` rewritten as `tools/update_icons.py`: pure Python 3 (standard library only, no dependency), a single `ICONS` dict keyed by destination filename processed in one loop instead of three near-identical bash loops; prints the number of icons actually updated at the end.
- `Makefile`: `update-icons` and `favicon` targets now run through `$(CONDA_RUN)` like the other targets, for consistency (Python and ImageMagick are now conda dependencies, see Added).

### Removed
- `html/jscript/boutons.js` and the `-up`/`-over`/`-down` nav button image variants (`cocktails`, `liens`, `mahjong`, `programmes`, `recettes`): replaced by CSS-driven states on a single image (see Added).
- `preloadNavImages()` (`layout.lib.php`) and the `buttons` option of `do_header()`: no longer needed once there are no `-over`/`-down` images to preload.
- `html/images/cocktails.xcf`, `liens.xcf`, `mahjong.xcf`, `programmes.xcf`, `recettes.xcf`: antique 1999 GIMP sources for the retired `-up`/`-over`/`-down` button variants, no longer needed.
- `html/images/PB-Soft.jpg`, `LogoPhP_150x100_Paypal.jpg`: unused, unreferenced anywhere in the project.
- `html/images/home.png`: antique icon, superseded by `home-hobbit.png`, unreferenced anywhere in the project.
- `tools/update_icons.sh`: replaced by `tools/update_icons.py` (see Changed).

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
