# Examen projet final s4 : Operateur mobile money 
## Partie 1 : Etape 1
- Lecture et comprehension du sujet : Rivaldo, Tommy 
- Creation de repository git : Rivaldo(ok)
- Initialisation de Codeigniter 4 : Rivaldo(ok)
- Configuaration de base SQLite3 : Tommy(ok)
- Envoye de lien git sur le formualire : Tommy(ok)

## Partie 1: Etape 2 : Conception du base 


## Transfert : Tommy
- Function (php)
    - effectuerTransfert ()
        - [ok] Fonction qui enregistre les transfert dans la table operations
    - getHistorique (id_client)
        - [ok] Fonction qui recupere tout les historique d'un client a partir de son id
    - getFrais(montant)
        - [ok] Fonction utiliser dans le js 
- Function (js)
    - getFrais(montant)
        - [ok] Fonction qui recupere le Frais de transfert au moment ou on insere le montant
- Verification 
    - Verifier si le solde n'est pas vide ou inferieur a la montant envoyer
    - Verifier que le numero destinataire existe vraiment
    - Verifier pour que le numero destinataire ne soit pas le numero de l'envoye
- Page 
    - Page 1 :
        - Contient l'historique des transferts d'un client
        - Bouton "Faire un transfert"
    - Page 2 :
        - Contient le formulaire de transfert 
    
## Retrait : Tommy
- Function (php)
    - [ok] effectuerRetrait()
    - [ok] getBaremeFrais
    - [ok] getSolde
    - [ok] calculFrais
    - [ok] getCompteParNumero
    - [ok] getHistorique
- Page
    - [ok] Retrait
    - [ok] Formulaire de retrait
    - [ok] Historique


## Login
- Creation de vue login.php
  - input insertion numero :Rivaldo(ok)
  - affichage d'erreur :Rivaldo(ok)
  
- creation du model Client.php
  - declaration du table client:Rivaldo(ok)
  - fonction de recherche client par telephone : Rivaldo(ok)

- Controller login.php
  - fonction authentification par numero de telephone :Rivaldo
  - fonction prendre le session de l'utilisateur connecter :Rivaldo(ok)
  - Fonction pour la deconnexion :Rivaldo(ok)

## Depot
- vue depot/depot.php
  - Affichage du solde actuel :Rivaldo(ok)
  - Input du montant a deposer : Rivaldo(ok)
  - fonction js pour calculer et afficher le frais du depot dynamique :Rivaldo(ok)
- Controller DepotController.php
  - fonction pour prendre l'info du client connecter resolveCurrentClient():Rivaldo(ok)
  - redirection vers le vue et la session index() :Rivaldo(ok)
  - Comparaison et calcul de frais par rapport au montant deposer pour le js dans le vue calculerFraisApi() : Rivaldo(ok)

- Model Depot.php
  - function pour prendre les baremes de depot getBaremeFrais(float $montant):Rivaldo(ok)
  - function pour prendre le compte et solde du client  getCompteParClientId() et vgetSolde(int $clientId) :Rivaldo(ok)
  - fonction pour efectuer le depot  effectuerDepot(int $clientId, float $montant): Rivaldo(ok)

# V2

## cote client
- Envoi multiple vers plusieurs numéros ( divisé le montant pour chaque numéro)
    - [x] Fonction js (addRecipientField, updateRepartition, calcul frais dynamique)
    - [x] Vue multiple.php (formulaire avec champs dynamiques, suppression, ajout)
    - [x] Controller : methodes multiple() et transfererMultiple()
    - [x] Model : effectuerTransfertsMultiple()
    - [x] Routes : GET/POST /transfert/multiple
    - [x] Lien navigation depuis formulaire simple

## Situation gain via les frais (retrait + transfert) : Tommy
- [x] Model : getStatsFrais() avec regroupement par operateur (retraits, transferts meme operateur, transferts autre operateur)
- [x] Controller : gainsFrais() avec filtrage par date et restriction admin
- [x] Vue : admin/gains_frais.php avec 4 tableaux separes (Retraits, Transferts meme operateur, Transferts autre operateur, Recapitulatif general)
- [x] Route : GET /admin/gains-frais
- [x] Lien navigation admin : "Gains Frais" dans le layout client.php
- [x] Filtre par periode (date debut / date fin)
- [x] Cartes de synthese (total frais, nombre operations, montant brut)

## Gestion des commissions par operateur : Tommy
- [x] Controller : CommissionController (index, store, update, delete)
- [x] Vue : admin/commissions/index.php avec tableau + modal CRUD
- [x] Routes : admin/commissions (GET, POST store, POST update/:id, GET delete/:id)
- [x] Lien navigation admin : "Commissions" dans le layout client.php

## Gestion des prefixes des operateurs : Tommy
- [x] Controller : PrefixeController (index, store, update, delete, getByOperateurApi)
- [x] Vue : admin/prefixes/index.php avec tableau + modal CRUD
- [x] Routes : admin/prefixes (GET, POST store, POST update/:id, GET delete/:id, GET api/operateur/:id)
- [x] Lien navigation admin : "Préfixes" dans le layout client.php