-- Insertion des données de base (tables sans dépendances)
INSERT INTO etatActif (libelle) VALUES 
('Actif'),
('Inactif'),
('Suspendu'),
('Fermé');

INSERT INTO etatValidation (libelle) VALUES 
('En attente'),
('Approuvé'),
('Rejeté');

INSERT INTO modePaiement (libelle) VALUES 
('Virement bancaire'),
('Espèces'),
('Chèque'),
('Carte bancaire'),
('Prélèvement automatique'),
('Mobile Money');

INSERT INTO typeTransaction (libelle) VALUES 
('Entrée'),
('Sortie');

INSERT INTO type_retour (libelle, nbJourEspacant) VALUES
('Mensuel', 30),
('Trimestriel', 90),
('Semestriel', 180),
('Annuel', 365);

-- Insertion des données avec premières dépendances
INSERT INTO type_pret (libelle, taux, dateCreation, pretmin, pretmax, dureeMoisMax) VALUES
('Prêt Personnel', 12.50, CURDATE(), 100000, 5000000, 24),
('Prêt Immobilier', 6.75, CURDATE(), 5000000, 50000000, 240),
('Prêt Auto', 9.25, CURDATE(), 1000000, 15000000, 60),
('Prêt Étudiant', 5.50, CURDATE(), 500000, 3000000, 120),
('Crédit Revolving', 15.90, CURDATE(), 200000, 2000000, 12);

INSERT INTO agent (nom, prenom, motdepasse, email, role, etatActif) VALUES
('Rabe', 'Jean', 'password123', 'jean.rabe@banque.mg', 'Administrateur', 1),
('Rakoto', 'Paul', 'securepass456', 'paul.rakoto@banque.mg', 'Conseiller', 1),
('Andry', 'Lova', 'admin789', 'lova.andry@banque.mg', 'Agent de crédit', 2);

INSERT INTO client (nom, prenom, motdepasse, email, telephone, adresse, dateNaissance, profession, revenuMensuel, etatActif) VALUES
('Rasoa', 'Marie', 'pass1', 'marie.rasoa@email.com', '0321234567', 'Antananarivo', '1990-05-10', 'Comptable', 850000.00, 1),
('Randri', 'Lina', 'pass2', 'lina.randri@email.com', '0347654321', 'Fianarantsoa', '1988-11-25', 'Infirmière', 650000.00, 1),
('Rahari', 'Tiana', 'pass3', 'tiana.rahari@email.com', '0339988776', 'Tamatave', '1995-08-19', 'Enseignant', 500000.00, 1);

INSERT INTO fondEntrant (montant, descri, datefond) VALUES
(20000000.00, 'Capital initial', CURDATE()),
(5000000.00, 'Apport du partenaire', CURDATE());

-- INSERT INTO compteClient (idClient, numeroCompte, solde, etatActif) VALUES
-- (1, 'CPT0001', 1500000.00, 1),
-- (2, 'CPT0002', 1000000.00, 1),
-- (3, 'CPT0003', 500000.00, 1);

-- INSERT INTO transaction (idCompte, typeTransaction, montant, description) VALUES
-- (1, 1, 200000.00, 'Versement initial'),
-- (1, 2, 50000.00, 'Paiement facture eau'),
-- (2, 1, 100000.00, 'Salaire'),
-- (3, 2, 25000.00, 'Retrait guichet');

-- INSERT INTO pret (idDemande, idClient, idTypePret, montantAccorde, dureeMois, montantTotal, dateAccepte, dateDebutRemboursement, dateFinRemboursement, modePaiement, etat) VALUES
-- (1, 1, 1, 3000000.00, 12, 3360000.00, CURDATE(), CURDATE(), DATE_ADD(CURDATE(), INTERVAL 12 MONTH), 1, 2),
-- (2, 2, 2, 25000000.00, 60, 29250000.00, CURDATE(), CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 MONTH), 2, 1);

-- INSERT INTO remboursement (idPret, montantPaye, modePaiement, reference) VALUES
-- (1, 280000.00, 1, 'VIR2025-001'),
-- (1, 280000.00, 1, 'VIR2025-002'),
-- (2, 500000.00, 2, 'ESP2025-001');
