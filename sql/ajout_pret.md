AJOUT PRET : 
-Regles de gestion :
    type_tour : par defaut mensuel ?
    nb_de_remboursement = dureeMois / type_retour
    
    -typePret : pretmin <= montantAccorde <= pretmax
                dureeMois <= dureeMoisMax
    -infos client : revenuMensuel*type_retour >= montantTotal / nb_de_remboursement
    => si oui, on ajoute le pret sinon, message d'erreur precis.

    -dateAccepte : now

    -calcul automatique du montant total montantTotal = montantAccorde + (montantAccorde * taux/nb_de_remboursement * nb_de_remboursement)

=> miajouter remboursement any amin base du mois de dateDebutRemb a mois de dateFinRemb
    Manao anle annuite constante (mois, captial restant, taux interet, montant a payer, capital rembourse)