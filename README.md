# ASYLAMBA : Expansion Stellaire

Jeu de stragégie en ligne multi-joueur. [http://asylamba.com](http://asylamba.com)


## Dépendances

- Apache 2.4.7
- PHP 5.5.9
- MySQL 5.5.49

## Installation

Procédure pour une installation locale :

- copier `index.default.php` et le renommer en `index.php`, y modifier la constante PUBLICR avec votre chemin
- copier `system/config/app.config.local.default.php` et le renommer `app.config.local.php`, y modifier la constante APP_ROOT et d'autres infos (connexion à la base de données, etc.)
- créer une base de données (nom correspondant à la constante DEFAULT_SQL_DTB du fichier du point précédent)

A partir de là, vous pouvez accéder à une interface qui permet de créer toutes les tables de la base de données : http://localhost/[ votre chemin ]/script. Pour faire l'installation, cliquez sur le premier bouton "deploy.dbinstall". 

Si aucune erreur s'affiche, vous pouvez ensuite créer des personnages en allant sur http://localhost/[ votre chemin ]/buffer. Cette interface permet de se connecter à tous les personnages créés.

Trois scripts permettent de mettre à jour le jeu quotidiennement, il s'agit des trois boutons sous le titre "Tâches Cron". Vous pouvez soit les lancer à la main, soit appeler leurs URLs avec des crons.


## Contributeurs

* [abdelaz3r](https://github.com/abdelaz3r)
* [acknowledge](https://github.com/acknowledge)
* [N03](https://github.com/N03)

## Licence

[WIP]