<?php
echo '<!DOCTYPE html>';
echo '<html lang="fr">';

echo '<head>';
    echo '<title>500 — Expansion</title>';

    echo '<meta charset="utf-8" />';
    echo '<meta name="description" content="' . APP_DESCRIPTION . '" />';
    echo '<link rel="stylesheet" media="screen" type="text/css" href="' . CSS . 'notfound/main.css" />';
echo '</head>';

echo '<body>';
    echo '<div class="container">';
        echo '<h1>500. Erreur interne.</h1>';
        echo '<h2>Une terrible erreur s\'est produite, condamnant les asylambiens à errer dans le cosmos.<br/>';
        echo 'Nous réparons au plus vite le réacteur principal pour que tout redémarre correctement.</h2>';
    echo '</div>';

    echo '<div class="boxlink">';
        echo '<a href="' . APP_ROOT . 'profil">revenir vers la page principale</a>';
        echo '<a href="' . $this->getContainer()->getParameter('getout_root') . '">où allez jeter un oeil au site</a>';
        echo '<a href="' . $this->getContainer()->getParameter('getout_root') . 'blog">où peut-être au blog</a>';
    echo '</div>';
echo '</body>';
