# Cardolan

Site personnel de Philippe Poilbarbe, développé en PHP. Le nom et l'identité
visuelle du site font référence à Cardolan, l'un des royaumes issus du
partage d'Arnor dans l'œuvre de J.R.R. Tolkien (voir la page [/blason/](html/blason/index.php)
pour l'explication du blason).

## Contenu du site

- **Accueil** (`/`) — page de garde.
- **Mah-Jong** (`/mahjong/`) — informations sur le jeu.
- **Cocktails** (`/cocktails/`) — recueil de recettes de cocktails.
- **Recettes** (`/recettes/`) — recueil de recettes de cuisine.
- **Téléchargements** (`/download/`) — programmes maison à récupérer.
- **Liens** (`/links/`) — liens externes.
- **Blason** (`/blason/`) — présentation et explication du blason de Cardolan.
- **Licence** (`/licence/`) — conditions de licence du site (CC BY-NC-SA 4.0).

## Sous-sites — PBRecipe

Les sections **Cocktails** et **Recettes** sont propulsées par
[PBRecipe](https://github.com/ppoilbarbe/PBRecipe), un moteur de gestion de
recettes indépendant (base de données SQLite/MariaDB/PostgreSQL, import/export
YAML) développé dans un dépôt séparé. Elles sont intégrées à Cardolan via le
shim `html/include/recipe_integration.lib.php`, qui leur fournit l'en-tête,
le pied de page et la charte graphique communs au reste du site.

## Easter egg — π / Prétoriens

Un clin d'œil à la version originale du site (1999) : l'ancien script
`html/jscript/pi.js` affichait déjà un logo flottant sur la page d'accueil,
mais son implémentation reposait sur des API de navigateur (`document.layers`,
`document.all`) abandonnées depuis longtemps — il ne fonctionnait donc plus
depuis des années. L'idée a été reprise et proprement réimplémentée : sur la
page d'accueil, l'image `pi.png` apparaît discrètement en bas à droite à
chaque chargement, puis disparaît au bout de 5 secondes (animation CSS pure,
sans JavaScript pour le minutage). Un Ctrl/Cmd+clic dessus pendant ce court
instant ouvre `/praetorians/`, une page cachée (absente du menu de
navigation) mise en forme comme un manuscrit enluminé.

Le thème de la page (surveillance, déduction, traque numérique) fait
référence au film *Traque sur Internet* (*The Net*, 1995). Son crédo, dans la
continuité de l'identité tolkienienne du reste du site (Cardolan, le blason,
Sindarin…), est une paraphrase du poème de l'Anneau du *Seigneur des Anneaux*,
transposée au vocabulaire de la surveillance informatique. La signature finale
est accompagnée de `praetorians-watcher.png`, l'icône du programme
[PBRegisterActivity](https://github.com/ppoilbarbe/PBRegisterActivity)
réutilisée ici pour son œil rouge évoquant HAL 9000 (*2001, l'Odyssée de
l'espace*) — un clin d'œil à l'auto-surveillance.

## Développement

Le projet utilise un `Makefile` pour les tâches courantes :

```sh
make help          # liste des cibles disponibles
make venv          # crée l'environnement conda (PHP, gh, Python, ImageMagick)
make livetest      # démarre le serveur PHP local et ouvre le navigateur
make test          # vérifie la syntaxe PHP de tous les fichiers du site
make update-icons  # synchronise les icônes depuis PBIcons
make favicon       # régénère favicon.ico depuis pbcardolan-full.png
make deploy        # déploie le site sur le serveur
```

### Dépendances

- **PHP**, **gh** (GitHub CLI), **Python 3** (bibliothèque standard
  uniquement, aucun paquet à installer) et **ImageMagick** (`convert`) :
  installés via `make venv` (environnement conda `site_web`, voir
  `environment.yml`). Python et ImageMagick sont utilisés par
  `tools/update_icons.py` (cible `make update-icons`) et par la cible
  `make favicon` (redimensionnement des icônes).
- **sitecopy** : requis pour `make deploy`.

## Licence

Copyright (c) 1999-2026 Marcel Spock

Le code source (HTML/site), les contenus textuels (recettes de cuisine, de
cocktails et autres contenus éditoriaux) et les images/icônes de ce site sont
sous licence [CC BY-NC-SA 4.0](LICENSE).

Contact : webmaster@cardolan.net
