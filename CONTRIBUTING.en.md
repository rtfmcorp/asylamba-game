Guide for the contributor
=========================

You can contribute in two different ways :

- Open an issue : If you find something wrong or something that can be done better, just open an [*issue*](https://github.com/rtfmcorp/asylamba-game/issues) and describe as good as you can the problem.
- Propose a PR : If you want to resolve something, you can do it, just follow the precedure down below.


What type of problem can I solve ?
----------------------------------

You can search for an issue you want to solve. In this case, please comment the issue so that we know you're working on it. If there's no issues you can create it.

Here are the things you can do :

- correct orthograph or typing errors
- make refactoring, optimizations
- work on the interface (make it responsive for example)
- create documentation
- optimize the installation process

If you want to add or modify game features, it's likely that the PR will be rejected. If you want to do it, please start a discussion with the dev team first, we'll talk together about what you want to do (open a topic in Asylamba's forum).


How to contribute ?
-------------------

First you need to fork the repo. Then you can work on your version of the game and make commits. When it's done you can submit a pull request on the *dev* branch of the main repo.

The main branch is *master*, the *dev* branch is used for development. We'll not accept PRs on the *master* branch.

*Hint : Make a branch on your fork to do modifications so that you can work on another issue while waiting for the approval/rejection of your PR. Don't forget that all the commits you do on the same branch after the PR will be added to the PR.*

An official guide for the contributions is available [here](https://guides.github.com/activities/contributing-to-open-source/#contributing). If you have any question, don't hesitate to contact us by mail, Asylamba or Discord.


Good practices
--------------

- respect the code conventions (spacing, new lines, tabs, naming)
- the code must be in english (variables names, ...)
- the comments are in english or french
- the commits names must be clear (in french or english)
- a commit modifies/improves one thing, it must be clear and concise


Modify the front-end
--------------------

If you want to modify the CSS, you must edit the [LESS](http://lesscss.org) files that are located in `public/css/less`. To compile them, [Gulp](http://gulpjs.com) is used. First you need to install NPM and GULP on your system, then you have to install the dependencies of the project by typing :

    npm install

The dependencies will be installed in the `node_modules/` folder. To compile the CSS, just type :

    gulp less
