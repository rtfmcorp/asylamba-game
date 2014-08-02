----------
|  Lois  |
----------

Hiérarchie :

N4 - 1 joueur : chef de l'alliance, 'empereur'
	Il peut proposer des lois sans en référer aux autres
	2 ou 3 lois qui coûtent pire cher
N3 - 4 joueurs : ambassadeur
	--> trésorier : impots des secteurs, routes commerciales
	--> défense : force d'attaque, subvention militaire, armée
	--> intérieur : modérer le forum, tout ce qui a rapport aux planète (techno, contre-espionnage)
	--> propagande : radios et marchés, voix du peuple

N2 - % de l'alliance : parlementaires
	les N2 qui veulent se présenter en N3 mettent leur préférence (je veux être trésorier, sinon progagande, sinon...)
	Ensuite entre les 4 élus, ils prennent le poste qu'il ont préféré, sinon le 2e, sinon le 3e etc. (algorithme de sélection)
	ils n'ont pas de lois. Mais ils doivent voter pour élire les N3 et voter pour accepter les lois des N3

N1 - tout le peuple de l'alliance
	selon certaines conditions (différentes pour chaque alliance), ils peuvent se présenter au parlement


Lois :
toutes les lois coutent des crédits
Il y a des lois infinies (qui modifient qqch, comme les impôts) et des lois à durée (lois active durant x semaines)

Certaines lois ont des options : par exemple effort de guerre avec option "contre l'alliance bleue, sur tel secteur" -> le prix sera affiché en conséquence




Lois pour chaque rang :
N4 :


----- N3 ---------------
Trésorier :
	- ajustement de l'imposition sectoriel :
		options : 	- secteur
					- nouvelle taxe
	- accords commerciaux :
		options : 	- durée
					- avec quelle alliance
					- niveau (pourcentage de bonus)
	- placement financier : (prêt virtuel : ex: placement de 10M pendant 2 semaines, au bout de 2 semaines, l'alliance récupère gagne 12M)
		options :	- montant
					- durée

Défense :
	- subvention militaire (longue période, faible coût, baisse les coûts des vaisseaux pour les gens de l'alliance)
		options : 	- durée
					- niveau (pourcentage de la baisse du cout des vaisseaux)
	- force de frappe : augmente l'attaque des vaisseaux
		options :	- temps
					- niveau
					- classe de vaisseaux
	- stratège : augmente la défense des vaisseaux
		options :	- temps
					- niveau
					- classe de vaisseaux
	- navigation : augmente la vitesse des vaisseaux
		options :	- temps
					- niveau
					- classe de vaisseaux

Intérieur :
	- décret sur l'éducation : augmente l'expérience des commandants à l'école
		options : 	- durée
					- niveau
	- subvention universitaire : accélère les recherches (investi plus que le montant déterminé par le joueur)
		options : 	- durée
					- niveau
	- transfert technologique : accélère la vitesse de développement des technologies
		options :	- durée
					- niveau
	- e : étend le rayon de contre-espionnage : augmente l'investissement dans le contre-espionnage (plus que le montant déterminé par le joueur)
		options : 	- durée
					- niveau
	- [il doit gérer et modérer le forum d'alliance]

Propagande :
	- loi sur le commerce : ajustement de la taxe d'un marché
		options : 	- secteur
					- taux (nouveau pourcentage de taxe)
	- [il peut gérer les offres qui sont faites automatiquement (taux de commandants, taux de cartes, etc.)]
	- [voix du peuple : récupère les requetes des gens (ceux qui ralent, etc.) et peut en faire part au conseil]
	- [il gère et modère les radios]


--- N4 ------------
Empereur :
	- déclaration de guerre : une alliance est en guerre contre une autre, les barrrières qui empechaient un joueur d'attaqué un joueur plus faible que lui est annulée pendant cette durée; route commerciale entre les deux sont cassées. Tous les joueurs de la galaxie reçoivent un communiqué comme quoi telle alliance a déclaré la guerre à une autre. Si une des deux alliance meurt durant la guerre, tous les joueurs de l'alliance morte vont dans l'alliance gagnante.
		options : 	- alliance cible
					- durée
	- traité de paix : [visible seulement en temps de guerre ]une alliance qui s'est faite déclarée la guerre peut payer ce traité pour annuler la guerre. L'autre alliance doit accepter pour que ça soit validé.	
[à partir d'ici demander le fichier à Gil]		
	- effort de guerre : réduction du prix des vaiseaux et du temps de construction
		options : 	- durée
					- 
	- protectionnisme : toutes routes commerciales break; augmentation des ressources / impot.

--- N3 ------------
intérieur :
- gérer et modérer le forum
- décret sur l'éducation : augmente l'expérience des commandants a l'école

propagande :
- gérer les stations
- loi-anti collabo : coupe les canaux de communication entre 2 alliances









	Une faction est composée de 'membres'.
Chaque membre a un rang :
	-n1, le rang standard (simple citoyen électeur)
	-n2, les parlementaires, sont élus par les n1
	-n3, les ministre, sont élus par les n2
	-n4, le chef, est élu par les n2

L'interaction avec sa Faction pourra se dérouler comme suit :

	Les n1 aura accès à :
		la vision d'informations diverses
			-le journal
			-le descriptif rp
			-les membres dirigeant (n2, n3, n4)
			-les classements
			-les lois en cours
			-les taux d'impositions
			-la bibliothèque

	Les n1 pourront effectuer les actions suivantes :
		-la lecture des comptes de la Faction
		-vendre une carte historique à la bibliothèque
		-aller sur le forum de Faction en lecture et écriture sur les partie publiques et éventuellement créer des sujet.
		-voter(pour les n2) en période d'élection
		-se présenter comme candidat en période de campagne sous réserve de conditions.
			les conditions à remplir relève de la particularité de la factions, pour chaque factions, plusieurs critères sont à remplir mais un élémen central concernant son rp est mis en avant :
				-synelle : ancienneté
				-kovak : victoires
				-négore : tune
				-empire : planète
				-nerve : ressources
				-aphéra : technologie
				-cardan : planète sur des secteurs qui n'appartiennent pas à la faction
			Les politiciens en cours de mandat ont la possibilité de se représenter sans avoir à remplir ces conditions.
			Dès le lancement de la période de campagne, les candidat ont un laps de temps défini pour se présenter et publier leur programme politique

	Les n2 pourront faire tout ce que peuvent faire les n1, à cela est ajouté :
		-La possibilité de voter pour élir les n3 et n4
			on éli parmis les n2 cinq n3 et un n4
		-La possibilité de voter pour l'acceptation ou non d'une loi proposée par un n3 (une fois la loi proposée, un temps, temps précédent la mise en pratique de la loi, est laissé pour les votations)
		-La possibilité de lire les discussions dans les ambassades

	Les n3 auront la possibilité (en plus de celles qu'ont les n1 et les n2) de proposer au parlement des lois concernant leur dicastère (ex : modification des impots pour le trésorier). Ainsi que la possibilité de :
		-publier un communiqué de presse
		-envoyer un mail à tous les citoyens de la Faction
		-Seul l'ambassadeur à la possibilité d'écrire des messages dans l'embassade

	Le n4 peut (en plus de ce que peuvent les n1, n2 et n3)
		-appliquer des lois (sans qu'elles soient voter par le parlement)

	Les n3 et n4 n'ont pas besoin d'être réélus par les n1 pour faire partie de n2 lors de la législation suivant la léglislation courante (par contre ils doivent être réélu par les n2 pour rester n3 ou n4)

	Les joueurs étranger peuvent interargir avec une Faction de la manière suivante :
		-les n2, n3 et n4 ont la lecture sur l'ambassade
		-seul le n3 'ambassadeur' à les droit d'écriture sur l'ambassade
		-tous (n1, 2, 3, 4) peuvent lire le journal de la Faction et voire les lois en éxecution concernant directement leur propre Faction




nomenclature des grades
-----------------------

1. générale

chaque nation a quatre niveau de joueur :
n1 = simple joueur, pas impliqué politiquement
n2 = joueur élu par les n1, à la possibilité d'élir à son tour les n3 et les n4 parmis les n2, doit aussi avaliser les lois des n3
n3 = 5 joueurs élu a des postes importants, peuvent faire voter des lois
    n3.a = trésorier :
     	- gère les taxe de chaque secteur sous controle de la nation
     	- débloque des fonds à usage des lois
    n3.b = dep. de la défense : propose des lois influant sur les batailles
    n3.d = dep. de l'intérieur : gère et modère le forum d'alliance
    n3.c = propagande : gère les station radios sous contrôle
n4 = "empereur", joueur au dessus de tous les autres. Peut déclarer la guerre et dispose de quelques lois qu'il n'a pas besoin de faire voter

2. idée en vrac

empire, kovakh	: dynaste, dynastaire, grand dynaste
négore		: vizir, vistear, viziDuc, vistaer
synelle		: consul
marche		: épistan, épistar, épitaphe, viquaire, diacre
aphéra, nerve	: chancelier, sénateur, sénati, héliocrate, patricien

dynaste, prince, roi, baron, calife, vizir, héritier, viziDuc

3. nom pour chaque nation

empire
	n4 : empereur
	n3 : dynastaire
	n2 : dynaste
	n1 : 

kovakh
	n4 : hérault
	n3 : dynastaire
	n2 : dynaste
	n1 : 

négore
	n4 : vizir
	n3 : gestionnaire
	n2 : négociant
	n1 : 

synelle
	n4 : fédérant
	n3 : consul
	n2 : conseiller
	n1 : 

marche
	n4 : 
	n3 : 
	n2 : 
	n1 : 

aphéra
	n4 :
	n3 : 
	n2 : 
	n1 : 

nerve
	n4 :
	n3 :
	n2 : 
	n1 : 