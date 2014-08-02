------------------------
|  Fin du Jeu - Joueur |
------------------------

que se passe-t-il à la fin, quand une alliance n'a plus de secteur, quand un joueur n'a plus de base ?

Fin d'un serveur
----------------
Système d'objectifs à avoir pour gagner :
	exemple : si les rouges et les verts sont décimés, les bleus gagnent le serveur

Les objectifs peuvent/doivent être différents pour chaque alliance

Ce système permet de faire des serveurs courts et des serveurs longs

On peut imaginer se faire sponsoriser un serveur par une entreprise qui offrerai 2000 à l'alliance qui gagne
--> pubs, revenus, etc.


Fin d'une alliance
------------------
Une alliance meurt lorsqu'elle ne possède plus aucun secteur.

Que faire des joueurs de l'alliance morte ?
--> lors de la prochaine connexion,  le joueur de l'alliance morte doit va être réaffecter à une nouvelle alliance
	sa nouvelle alliance est celle dans laquelle il a le plus de planète.
	en cas d'égalité, nous choisissons (rand() p.ex)



Joueur
------
- on peut le bannir du serveur (ou des serveurs)
- il peut arrêter de jouer
- PAS de mode vacance
- s'il ne joue pas pendant un certain temps :
	- il entre en mode 'inactif' : sur ses bases et son profil c'est marqué qu'il est inactif
	- quand on lui envoie un message, on reçoit une notif comme quoi il est inactif
	- on ne supprime pas ses planètes, elles se feront prendre par des gens au fil du temps
- il peut supprimer son compte :
	- son nom est renommé avec une croix devant son nom (ex : Bertrand --> T Bertrand)
- il peut mourrir :
	- son nom est renommé avec une croix devant son nom
	- le joueur bindkey est modifié, comme ça il peut recréer un perso avec son bindkey
	- il faut lui envoyer un mail pour dire qu'il est mort, et il peut recréer un compte sur ce serveur


Super-crédits
-------------
Si un joueur arrête de jouer, qu'il se fait tuer ou que le serveur est fini.
Ses super-crédits restants sont transférés sur le portail. Il reçoit un mail en disant qu'il lui reste des crédits et qu'il peut les réaffecter à un de ses comptes dans n'importe quel serveur.

pourquoi : incite le joueur à revenir jouer ou à le rendre content :) 
