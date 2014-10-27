----------
|  Lois  |
----------

Hiérarchie :

N6 - 1 joueur : chef de l'alliance, 'empereur', diplomate
	Il peut proposer des lois sans en référer aux autres
	2 ou 3 lois qui coûtent pire cher
Ministres - 4 joueurs :
N5	--> trésorier : impots des secteurs, routes commerciales
N4	--> défense : force d'attaque, subvention militaire, armée
N3	--> intérieur : modérer le forum, tout ce qui a rapport aux planète (techno, contre-espionnage)

N2 (status = 2) - % de l'alliance : parlementaires
	les N2 qui veulent se présenter en N3 mettent leur préférence (je veux être trésorier, sinon progagande, sinon...)
	Ensuite entre les 4 élus, ils prennent le poste qu'il ont préféré, sinon le 2e, sinon le 3e etc. (algorithme de sélection)
	ils n'ont pas de lois. Mais ils doivent voter pour élire les N3 et voter pour accepter les lois des N3

N1 (status = 1) - tout le peuple de l'alliance
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
X		options : 	- secteur
					- nouvelle taxe
T	- accords commerciaux :
					- avec quelle alliance
					- niveau (pourcentage de bonus)
	- loi sur le commerce : ajustement de la taxe d'un marché
X		options : 	- secteur
					- taux (nouveau pourcentage de taxe)
T	- placement financier : (prêt virtuel : ex: placement de 10M pendant 2 semaines, au bout de 2 semaines, l'alliance récupère gagne 12M)
		options :	- montant

Défense :
T	- subvention militaire diminue de 10% le cout de construction des vaisseaux
T	- force de frappe : augmente l'attaque des vaisseaux
		options :	- temps
					- niveau
					- classe de vaisseaux
T	- stratège : augmente la défense des vaisseaux
		options :	- temps
					- niveau
					- classe de vaisseaux
T	- navigation : augmente la vitesse des vaisseaux
		options :	- temps
					- niveau
					- classe de vaisseaux

Intérieur :
T	- décret sur l'éducation : augmente l'expérience des commandants à l'école
					- niveau
T	- subvention universitaire : accélère les recherches (investi plus que le montant déterminé par le joueur)
					- niveau
T	- transfert technologique : accélère la vitesse de développement des technologies de 10%
	- e : étend le rayon de contre-espionnage : augmente l'investissement dans le contre-espionnage (plus que le montant déterminé par le joueur)
					- niveau
	- [il doit gérer et modérer le forum d'alliance]


--- N4 ------------
Empereur :
	- guerre
		options : 	- alliance cible
	- traité de paix : [visible seulement en temps de guerre ]une alliance qui s'est faite déclarée la guerre peut payer ce traité pour annuler la guerre. L'autre alliance doit accepter pour que ça soit validé.	
[à partir d'ici demander le fichier à Gil]		
	- effort de guerre : réduction du prix des vaiseaux et du temps de construction
					- 
	- protectionnisme : toutes routes commerciales break; augmentation des ressources / impot.

X	- changer le nom d'un secteur





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
