# Examen projet final s4 : Operateur mobile money 
## Partie 1 : Etape 1
- Lecture et comprehension du sujet : Rivaldo, Tommy 
- Creation de repository git : Rivaldo(ok)
- Initialisation de Codeigniter 4 : Rivaldo(ok)
- Configuaration de base SQLite3 : Tommy(ok)
- Envoye de lien git sur le formualire : Tommy(ok)

## Partie 1: Etape 2 : Conception du base 


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
    -  


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

## Depot
