ASYLAMBA : Expansion Stellaire
==============================

[![Build Status](https://travis-ci.org/rtfmcorp/asylamba-game.svg?branch=master)](https://travis-ci.org/rtfmcorp/asylamba-game)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rtfmcorp/asylamba-game/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rtfmcorp/asylamba-game/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/rtfmcorp/asylamba-game/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/rtfmcorp/asylamba-game/?branch=master)

Jeu de stragégie en ligne multi-joueur. Visitez [asylamba.com](http://asylamba.com) pour tester le jeu.

![logo Asylamba](http://asylamba.com/public/media/files/sources/asylambacom.png)

For the english version, follow this link : [English version](README.en.md) 

Dépendances
-----------

- Apache 2.4.7
- PHP 5.5.9
- MySQL 5.5.49
- Composer

Installation
------------

Si vous le souhaitez, vous pouvez utiliser le [dépôt Docker](https://github.com/rtfmcorp/asylamba-docker) du projet pour installer votre environnement.

Cette section traite uniquement de l'installation du jeu et suppose que votre environnement est prêt.

Tout d'abord, vous devez cloner le dépôt du jeu. Pour ce faire, ouvrez une invite de commandes, et entrez la commande suivante :

```sh
git clone git@github.com:rtfmcorp/asylamba-game.git
```

Ensuite, rendez-vous dans le dossier nouvellement créé et installez les dépendances du projet à l'aide de [Composer](https://getcomposer.org/) :

```sh
cd asylamba-game
composer install
```

Vous pouvez ensuite suivre la procédure suivante :

- copier `config/parameters.dist.yml` et le renommer en `parameters.yml`
- copier `system/config/app.config.local.default.php` et le renommer `app.config.local.php`, y modifier la constante `APP_ROOT` et d'autres infos (connexion à la base de données, etc.) ainsi que la constante `PUBLICR` avec votre chemin
- créer une base de données (nom correspondant à la constante `DEFAULT_SQL_DTB` du fichier du point précédent)

A partir de là, vous pouvez accéder à une interface qui permet de créer toutes les tables de la base de données : `http://localhost/[votre chemin]/script`. Pour faire l'installation, cliquez sur le premier bouton "deploy.dbinstall". 

Si aucune erreur s'affiche, vous pouvez ensuite créer des personnages en allant sur `http://localhost/[votre chemin]/buffer`. Cette interface permet de se connecter à tous les personnages créés.

Trois scripts permettent de mettre à jour le jeu quotidiennement, il s'agit des trois boutons sous le titre "Tâches Cron". Vous pouvez soit les lancer à la main, soit appeler leurs URLs avec des crons.


Structure du projet
-------------------

Le jeu a été développé sans framework aucun, cela pour des raisons de performances et de besoins spécifiques pour un jeu de ce type. Il y a donc vraiment peu de dépendances à des librairies externes. Les dépendances sont citées plus haut, nous ajoutons à cela jQuery ainsi que LESS pour la compilation CSS. Tout le reste est en pur HTML/CSS/JavaScript/PHP.

Le projet contient deux dossiers principaux :

- `public/` : pour les images, le CSS, le JavaScript et les logs
- `system/` : pour les vues, le cœur du jeu et tout ce qui est "mécanique"

Le dossier system est plutôt fourni mais les noms des dossiers qu'il contient sont assez explicites. Le sous-dossier `system/modules` mérite toutefois quelques précisions. Il contient toutes les classes principales du jeu qui sont regroupées en modules. Chacun de ces modules possède un nom de dieu grec. La liste se trouve ci-dessous.


| Module    | Fonction |
|-----------|----------|
| Arès      | la guerre (commandants, combats, flottes) |
| Artémis   | l'espionnage |
| Athéna    | la base ortibale (base orbitale, bâtiments, envois commerciaux, recyclage, constructions, transactions) |
| Atlas     | les classements (faction, joueur) |
| Déméter   | les factions (élections, lois, forums) |
| Gaïa      | la galaxie (secteurs, systèmes solaires, planètes) |
| Hermès    | la communication (messagerie, notification) |
| Prométhée | la technologie (recherches, technologies) |
| Zeus      | le joueur (tutoriel, gestion des bonus, envois de crédit) |


Contribuer
----------

Si vous souhaitez contribuer au projet, c'est possible ! Merci de prendre connaissance des [instructions](CONTRIBUTING.md) avant de commencer.


Contributeurs
-------------

* [abdelaz3r](https://github.com/abdelaz3r)
* [acknowledge](https://github.com/acknowledge)
* [N03](https://github.com/N03)


Licence
-------

[WIP]