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

Le code source, les contenus textuels et les images de ce site sont sous
licence [CC BY-NC-SA 4.0](LICENSE).
