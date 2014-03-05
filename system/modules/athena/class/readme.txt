> ID	RPLAYER	RBASE	TYPE	RESS	PRICE	TRUC	RATE
> ----------------------------------------------------------
> 1		0		0		0		0		0 		--		0.1
> 2		1 		1 		0		1000	12000	--		0.2			1
> 2		1 		1 		0		2000	12000	--		--
> 2		1 		1 		0		1000 	20000   --		1.2
> 2		1 		1 		0
> 2		1 		1 		0
> 2		1 		1 		0


load(array('rplayer' => 2));
	-> Transaction

load(array('rplayer' => 2), array(), array(), Transaction::TYP_COMMANDER);
	-> TransactionCommander