ASYLAMBA : Expansion Stellaire
==============================

Multiplayer online strategy game. Visit [asylamba.com](http://asylamba.com) to try the game. It's only in French for the moment.

![Asylamba logo](http://asylamba.com/public/media/files/sources/asylambacom.png)

Read this file in other languages : [Français](README.md), [English](README.en.md) 

Dependencies
------------

- Apache 2.4.7
- PHP 5.5.9
- MySQL 5.5.49
- Composer

Installation
------------

If you wish, you can use the [Docker repository](https://github.com/rtfmcorp/asylamba-docker) to install your environment.

This section is only about the game install and assume your environment is ready.

First, you must clone the game repository. To do so, please use the following command :

```sh
git clone git@github.com:rtfmcorp/asylamba-game.git
```

Then, go in the created folder and install the project dependencies with [Composer](https://getcomposer.org/) :

```sh
cd asylamba-game
composer install
```

You're now able to follow the install procedure :	

- copy the file `config/parameters.dist.yml` and rename it `parameters.yml`
- copy the file `system/config/app.config.local.default.php` and rename it `app.config.local.php`, modify the `APP_ROOT` constant and some others if needed (connection to database, and so on.) and also the `PUBLICR` constant with your own path
- create a database (the name must be the same as the `DEFAULT_SQL_DTB` constant from the last point file)

From there, you can access an interface that permits to create all the SQL tables of the DB : `http://localhost/[votre chemin]/script`. To do the install, click on the first button : "deploy.dbinstall".

If no errors are displayed, you can go to `http://localhost/[votre chemin]/buffer` and create your first players. This interface allows you to connect to each and every player created.

Three scripts are available to update the game on a daily basis, it the ones under the title "Tâches Cron" (cron tasks). You can either launch them by hand or call the related URLs with crons.


Structure of the project
------------------------

The game was developed without any framework for performance and specific needs reasons. There are not many dependencies at that time. We can notice the use of jQuery and LESS for CSS compilation. The rest is in pure HTML/CSS/JavaScript/PHP.

The project contains mainly two folders :

- `public/` : for images, CSS, JavaScript and logs
- `system/` : for views and game core 

The `system/` folder contains a lot of sub-folders. It's pretty easy to guess what's inside for most of them. However the `system/modules` folder requires some explanations. It contains the main classes of the game that are grouped by modules. Each one of these modules is called with a greek god name. The list with explanations is just down there.


| Module    | Goal |
|-----------|----------|
| Ares      | the war (commanders, fights, fleets) |
| Artemis   | the spy |
| Athena    | the orbital base (orbital base, buildings, commercial shippings, recycling, transactions) |
| Atlas     | the rankings (faction, player) |
| Demeter   | the factions (elections, law, forums) |
| Gaia      | the galaxy (sectors, solar systems, planets) |
| Hermes    | the communication (message, notification) |
| Promethee | the technology (research, technologies) |
| Zeus      | the joueur (tutorial, bonus management, credit sendings) |


Contribute
----------

If you want to contribute to the project, it's possible ! Thanks to read the [instructions](CONTRIBUTING.en.md) before starting.


Team
----

Game creators:

* [abdelaz3r](https://github.com/abdelaz3r)
* [acknowledge](https://github.com/acknowledge)
* [N03](https://github.com/N03)

Contributors:

* [Kern046](https://github.com/Kern046)
* [PapyRusky](https://github.com/PapyRuski)
* [Liador](https://github.com/Liador)
* You ? :)


License
-------

[WIP]