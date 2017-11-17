## [Prochainement]
### Ajouté
- Gestionnaire de projet avec tableau de bord pour les retours
- Commentaires pour les bugs et évolutions
- Nouvelle configuration de carte de la galaxie
- Journal et gazette
- Connexions WebSocket entre navigateurs et serveur
- Partage en temps réel via WebSocket
- Partage en temps réel des nouvelles
- Optimisation et animation de la recherche de routes commerciales
- support de l'HTTPS
- Gestionnaire de budget
- Outil de donation

### Modifié
- Le report de bugs ainsi que la proposition d'amélioration se font maintenant en jeu au lieu du forum
- Compteur d'expérience sur la page profil

### Corrigé
- Espace faction quand aucun classement n'a été fait
- Flottes allant au hangar et au mess après un déplacement
- Chargement des bonus de joueur
- Nettoyage des notifications
- Validation de transactions sur la plateforme commerciale
- Gain de ressources à la relève

## [2.1.3] - 2017-08-11
### Ajouté
- Message quand un dirigeant conserve sa place après une élection sans candidats
- Gestion d'une élection sans candidat lorsqu'aucun gouvernement n'existe

### Modifié
- Les comptes inactifs sont maintenant accessibles en environnement de développement

### Corrigé
- Elections sans candidats (messages vides et blocage de cycle électoral)
- Limite de ressources des endroits vides
- Différence de position de classement

### Supprimé
- Journalisation réccurrente d'erreurs utilisateur

## [2.1.2] - 2017-08-09
### Ajouté
- Stockage des données d'appartenance territoriale dans Redis

### Corrigé
- Désertion des commandants quand leur base est conquise
- Affichage des attaques entrantes dans l'amirauté
- Données d'appartenance territoriale dans le registre tactique
- Fin de mission de recyclage
- Conversion des lieux de recyclage lorsqu'ils sont vides

## [2.1.1] - 2017-07-23
### Ajouté
- Mise en évidence des rapports de combat et d'espionnage visualisés dans les listes
- Execution quotidienne du nettoyage de notifications et de comptes inactifs
- Execution quotidienne des calculs de classement
- Paramètre INI supprimant la limite de temps maximal d'exécution
- Gestionnaire de session via Redis

### Corrigé
- Invitations extérieures à l'inscription
- Expérience issue de l'école de commandement
- Accès à l'interface d'administration
- Investissements vides
- Message d'erreur d'une conversation sans destinataires
- Informations du bâtiment de stockage
- Alertes pour les fins de construction
- Recyclage de vaisseaux
- Récompense de parrainage
- Délais de livraison depuis la plateforme commerciale
- Abandon de capitale
- Pertes en défense après une défense victorieuse
- Accès aux données de session par les processus

## [2.1.0] - 2017-06-01
### Ajouté
- Classe Container pour les services et paramètres
- Classe centrale Application
- Autoload PSR-4 pour les tests
- Raccourci de lancement de la suite de tests
- Fichiers de configuration des paramètres et des services
- Classe principale par module avec un fichier de configuration dédié
- Patron de conception Unit of Work
- Gestionnaire d'entités modèle
- Gestionnaire de routes
- Moteur de rendu de pages
- Moteur temps réel avec serveur persistant
- Evenements et points d'écoute pour la prise territoriale
- Gestionnaire d'actions en temps réel
- Gestionnaire d'actions cycliques
- Gestionnaire de processus
- Gestionnaire de tâches
- Gestionnaire de répartition de charge pour les processus

### Modifié
- Le système de sessions de données a été supprimé de plusieurs managers
- Système de prise territoriale

### Supprimé
- Support de HHVM
- Support de PHP 5.5

## [2.0.0] - 2016-11-01
### Ajouté
* Fichier de configuration Composer
* Fichier de configuration PHPUnit
* Chargement automatique selon norme PSR-4
* Fichier de configuration Travis CI
* Fichier de configuration Scrutinizer CI
* Fichier de configuration d'éditeur
* Fichiers de configuration de NPM et Gulp

### Modifié
* Les classes sont chargées par nom de domaine plutôt que par inclusion manuelle
* Les points de nom de script remplacés par des underscores

### Supprimé
* Inclusion manuelle des fichiers de jeu
* Constantes de localisation des modules

