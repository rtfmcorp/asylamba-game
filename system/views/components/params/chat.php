<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Container\Params;

$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');

echo '<div class="component new-message">';
    echo '<div class="head skin-5">';
        echo '<h2>Paramètres du chat</h2>';
    echo '</div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::REDIRECT_CHAT]) . '" class="on-off-button ' . ($request->cookies->get('p' . Params::REDIRECT_CHAT, Params::$params[Params::REDIRECT_CHAT]) ? null : 'disabled') . '">';
                echo 'Ouvrir le chat directement';
            echo '</a>';

            echo '<p>Ce paramètre permet, si il est activé, de rediriger automatiquement l\'icône <em>chat</em> de la barre de navigation vers le serveur de <em>chat</em> externe.</p>';
            echo '<p>Dans le cas contraire, vous serez dirigé vers la page d\'explication du fonctionnement du chat (ici).</p>';

            echo '<hr />';

            echo '<h4>Accès</h4>';
            echo '<p>Si vous avez déjà fait la manoeuvre, vous pouvez accéder à Discord en cliquant sur le bouton ci-dessous. Sinon, passez à l\'étape suivante.</p>';
            echo '<a href="https://discordapp.com/channels/132106417703354378/132106417703354378" target="_blank" class="on-off-button">';
                echo 'Accéder à Discord';
            echo '</a>';

            echo '<hr />';

            echo '<h4>Activation du chat</h4>';
            echo '<p>Premièrement, cliquez sur le lien suivant pour accéder au serveur Asylamba sur Discord :</p>';
            echo '<a href="https://discord.gg/0jkYebnseIt82lqY" target="_blank" class="on-off-button">';
                echo 'Accéder au serveur Asylamba sur Discord';
            echo '</a>';

            echo '<p>Vous pourrez y créer un compte. Une fois cette étape faite, vous vous trouvez sur le serveur. Vous n\'avez pas accès à tous les channels car vous n\'êtes pas un utilisateur vérifié. Pour cela, il faut que vous envoyiez un message privé à l\'utilisateur <strong>Chicken Bot</strong>.</p>';
            echo '<p>Une fois la discussion ouverte, écrivez "!me" à Chicken Bot. Il vous répondra alors avec un identifiant (longue suite de chiffres).</p>';
            echo '<p>Copiez cet identifiant dans le champ de texte ci-dessous et cliquez sur le bouton.</p>';
            
            echo '<form action="' . Format::actionBuilder('discordrequest', $sessionToken, []) . '" method="post">';
                echo '<p class="input input-text">';
                    echo '<input name="discord-id" type="text" placeholder="votre ID Discord" />';
                echo '</p>';
                echo '<p><button type="submit">Se connecter</button></p>';
            echo '</form>';
            echo '<p>Voilà, Chicken Bot vous a donné accès à tous les channels Asylamba ainsi qu\'aux channels privés à votre faction. Bon chat !</p>';
            
            echo '<h4>Un soucis ?</h4>';
            echo '<p>Si vous n\'arrivez pas à y accéder, contactez un administrateur, ou demandez directement de l\'aide dans le channel "accueil".</p>';

            echo '<h4>C\'est quoi Discord ?</h4>';
            echo '<p>Discord est un outil fait pour les joueurs de jeux vidéo coopératifs. Il permet de discuter avec vos collègues, tant manuscritement que vocalement.</p>';
            echo '<p>Cet outil est disponible via navigateur, mais vous pouvez aussi l\'installer sur votre Mac ou PC si vous le souhaitez. Une application Android et iOS est également disponible pour rester connecté en tout temps.</p>';
        echo '</div>';
    echo '</div>';
echo '</div>';
