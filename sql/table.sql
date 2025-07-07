CREATE DATABASE IF NOT EXISTS banque;
USE banque;

CREATE TABLE etatActif(
    idEtat INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50)
);
CREATE TABLE etatValidation(
    idEtat INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);
CREATE TABLE agent(
    idAgent INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    motdepasse VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role VARCHAR(50) NOT NULL, -- Administrateur, Conseiller, etc.
    etatActif INT DEFAULT 1, -- Actif, Inactif
    FOREIGN KEY (etatActif) REFERENCES etatActif(idEtat)
);
CREATE TABLE fondEntrant(
    idfond INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(15,2) NOT NULL,
    descri VARCHAR(100),
    datefond DATE NOT NULL
);
CREATE TABLE type_retour(
    idTypeRetour INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL,
    nbJourEspacant INT NOT NULL
);
CREATE TABLE type_pret(
    idTypePret INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    taux DECIMAL(5,2) NOT NULL, 
    type_retour INT,
    dateCreation DATE NOT NULL,
    dateAbolition DATE,
    pretmin INT NOT NULL,
    pretmax INT NOT NULL,
    dureeMoisMax INT NOT NULL,
    FOREIGN KEY (type_retour) REFERENCES type_retour(idTypeRetour)
);
CREATE TABLE client(
    idClient INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    motdepasse VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    dateNaissance DATE,
    profession VARCHAR(100),
    revenuMensuel DECIMAL(10,2),
    dateInscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    etatActif INT DEFAULT 1,
    FOREIGN KEY (etatActif) REFERENCES etatActif(idEtat)
);
CREATE TABLE demande_pret(
    idDemande INT AUTO_INCREMENT PRIMARY KEY,
    idClient INT NOT NULL,
    idTypePret INT NOT NULL,
    montantDemande DECIMAL(15,2) NOT NULL,
    dureeMois INT NOT NULL,
    motif TEXT NOT NULL,
    dateDemande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modePaiement INT, -- Virement, espèce, etc. (makaiza le vola indraminy)
    etat INT DEFAULT 1,
    FOREIGN KEY (idClient) REFERENCES client(idClient),
    FOREIGN KEY (idTypePret) REFERENCES type_pret(idTypePret),
    FOREIGN KEY (modePaiement) REFERENCES modePaiement(idmodePaiement),
    FOREIGN KEY (etat) REFERENCES etatValidation(idEtat)
);
CREATE TABLE pret(
    idPret INT AUTO_INCREMENT PRIMARY KEY,
    idDemande INT NOT NULL,
    idClient INT NOT NULL,
    idTypePret INT NOT NULL,
    montantAccorde DECIMAL(15,2) NOT NULL,
    dureeMois INT NOT NULL,
    montantTotal DECIMAL(15,2) NOT NULL, 
    dateAccepte DATE NOT NULL,
    dateDebutRemboursement DATE NOT NULL,
    dateFinRemboursement DATE NOT NULL,
    modePaiement INT, -- Espèces, Chèque, Virement, etc. (makaiza le vola indraminy)
    etat INT DEFAULT 1,
    FOREIGN KEY (idDemande) REFERENCES demande_pret(idDemande),
    FOREIGN KEY (idClient) REFERENCES client(idClient),
    FOREIGN KEY (idTypePret) REFERENCES type_pret(idTypePret),
    FOREIGN KEY (modePaiement) REFERENCES modePaiement(idmodePaiement),
    FOREIGN KEY (etat) REFERENCES etatValidation(idEtat)
);

CREATE TABLE modePaiement(
    idmodePaiement INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50)
);

CREATE TABLE remboursement(
    idPaiement INT AUTO_INCREMENT PRIMARY KEY,
    idPret INT NOT NULL,
    montantPaye DECIMAL(10,2) NOT NULL,
    datePaiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modePaiement INT, -- Espèces, Chèque, Virement, etc.
    reference VARCHAR(50), -- Numéro de chèque, référence virement
    FOREIGN KEY (idPret) REFERENCES pret(idPret),
    FOREIGN KEY (modePaiement) REFERENCES modePaiement(idmodePaiement)
);

CREATE TABLE compteClient(
    idCompte INT AUTO_INCREMENT PRIMARY KEY,
    idClient INT NOT NULL,
    numeroCompte VARCHAR(20) UNIQUE NOT NULL,
    solde DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    etatActif INT DEFAULT 1, 
    FOREIGN KEY (idClient) REFERENCES client(idClient),
    FOREIGN KEY (etatActif) REFERENCES etatActif(idEtat)
);

CREATE TABLE typeTransaction(
    idTypeTransaction INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL 
);

CREATE TABLE transaction(
    idTransaction INT AUTO_INCREMENT PRIMARY KEY,
    idCompte INT NOT NULL,
    typeTransaction INT NOT NULL, 
    montant DECIMAL(15,2) NOT NULL,
    dateTransaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    FOREIGN KEY (idCompte) REFERENCES compteClient(idCompte)
);

CREATE TABLE document_client(
    idDocument INT AUTO_INCREMENT PRIMARY KEY,
    idClient INT NOT NULL,
    idDemande INT,
    typeDocument VARCHAR(50) NOT NULL, -- CNI, Justificatif revenus, etc.
    nomFichier VARCHAR(255),
    cheminFichier VARCHAR(500),
    dateUpload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idClient) REFERENCES client(idClient),
    FOREIGN KEY (idDemande) REFERENCES demande_pret(idDemande)
);
