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
    pretmin INT,
    pretmax INT,
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
    etat INT DEFAULT 1,
    FOREIGN KEY (idClient) REFERENCES client(idClient),
    FOREIGN KEY (idTypePret) REFERENCES type_pret(idTypePret),
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
    etat INT DEFAULT 1,
    FOREIGN KEY (idDemande) REFERENCES demande_pret(idDemande),
    FOREIGN KEY (idClient) REFERENCES client(idClient),
    FOREIGN KEY (idTypePret) REFERENCES type_pret(idTypePret),
    FOREIGN KEY (etat) REFERENCES etatValidation(idEtat)
);

CREATE TABLE modePaiement(
    idmodePaiement INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50)
);

CREATE TABLE paiement(
    idPaiement INT AUTO_INCREMENT PRIMARY KEY,
    idPret INT NOT NULL,
    montantPaye DECIMAL(10,2) NOT NULL,
    datePaiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modePaiement INT, -- Espèces, Chèque, Virement, etc.
    reference VARCHAR(50), -- Numéro de chèque, référence virement
    FOREIGN KEY (idPret) REFERENCES pret(idPret),
    FOREIGN KEY (modePaiement) REFERENCES modePaiement(idmodePaiement)
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

INSERT INTO etatValidation (libelle) VALUES 
('En attente'),
('Approuvé'),
('Rejeté');

INSERT INTO type_retour (libelle, nbJourEspacant) VALUES
('Mensuel', 30),
('Trimestriel', 90),
('Semestriel', 180),
('Annuel', 365);

INSERT INTO type_pret (libelle, taux, type_retour, dateCreation) VALUES
('Prêt Personnel', 12.50, 1, CURDATE()),
('Prêt Immobilier', 6.75, 1, CURDATE()),
('Prêt Auto', 9.25, 1, CURDATE()),
('Prêt Étudiant', 5.50, 1, CURDATE()),
('Crédit Revolving', 15.90, 1, CURDATE());

