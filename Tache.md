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
## Etape 1: Rivaldo
- base.sql
  - creation du table operateurs (ok)
  - Creations du table configurations_commissions(ok)
  - insertions du donnes (ok)

## Commission inter-opérateur pour les transferts vers un autre opérateur : Rivaldo
  - [ok] Détection du préfixe et de l'opérateur du numéro destinataire : Rivaldo
  - [ok] Calcul du frais de transfert de base + commission additionnelle selon l'opérateur destinataire : Rivaldo
  - [ok] Controller : calcul dynamique des frais avec le numéro destinataire : Rivaldo
  - [ok] Vue formulaire de transfert : affichage du frais de base, de la commission et du total débité : Rivaldo
  - [ok] Model Client.php : normalisation des numéros pour accepter les formats avec séparateurs : Rivaldo
  - [ok] Configuration SQL : table configurations_commissions et données de préfixes/opérateurs :Rivaldo
        

## Situation gain via les frais (retrait + transfert) : Tommy
- [ok] Model : getStatsFrais() avec regroupement par operateur (retraits, transferts meme operateur, transferts autre operateur)
- [ok] Controller : gainsFrais() avec filtrage par date et restriction admin
- [ok] Vue : admin/gains_frais.php avec 4 tableaux separes (Retraits, Transferts meme operateur, Transferts autre operateur, Recapitulatif general)
- [ok] Route : GET /admin/gains-frais
- [ok] Lien navigation admin : "Gains Frais" dans le layout client.php
- [ok] Filtre par periode (date debut / date fin)
- [ok] Cartes de synthese (total frais, nombre operations, montant brut)

## Gestion des commissions par operateur : Rivaldo
- [ok] Controller : CommissionController (index, store, update, delete)
- [ok] Vue : admin/commissions/index.php avec tableau + modal CRUD
- [ok] Routes : admin/commissions (GET, POST store, POST update/:id, GET delete/:id)
- [ok] Lien navigation admin : "Commissions" dans le layout client.php

## Gestion des prefixes des operateurs : Tommy
- [ok] Controller : PrefixeController (index, store, update, delete, getByOperateurApi)
- [ok] Vue : admin/prefixes/index.php avec tableau + modal CRUD
- [ok] Routes : admin/prefixes (GET, POST store, POST update/:id, GET delete/:id, GET api/operateur/:id)
- [ok] Lien navigation admin : "Préfixes" dans le layout client.php


## cote client :Tommy
- Envoi multiple vers plusieurs numéros ( divisé le montant pour chaque numéro)
    - [ok] Fonction js (addRecipientField, updateRepartition, calcul frais dynamique)
    - [ok] Vue multiple.php (formulaire avec champs dynamiques, suppression, ajout)
    - [ok] Controller : methodes multiple() et transfererMultiple()
    - [ok] Model : effectuerTransfertsMultiple()
    - [ok] Routes : GET/POST /transfert/multiple
    - [ok] Lien navigation depuis formulaire simple

## Alea 2 : Tommy
- Epargne
  - Base de donnee
    - [ok] Creation d'un nouveau table
    - [ok] Donnee de test
  - Fonction concerner : 
      - [ok] effectuerTransfert
        - [ok] Modificaton : ajouter une requete qui transfere l'argent vers le table epargen qui contient l'epargne du client en fonction du pourcentage d'epargne
  - Fonction a cree :
      - [Pas fini] getepargneclient(id_client)
  - Erreur :
      - Table 'promotion_transfert non trouver'