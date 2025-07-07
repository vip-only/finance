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

-- Insertion des types de prêts
INSERT INTO type_pret (libelle, taux, assurance, dateCreation, pretmin, pretmax, dureeMoisMax) VALUES
('Prêt Personnel', 12.50, 1.50, '2025-01-01', 100000, 5000000, 24),
('Prêt Immobilier', 6.75, 2.00, '2025-01-01', 5000000, 50000000, 240),
('Prêt Auto', 9.25, 1.75, '2025-01-01', 1000000, 15000000, 60),
('Prêt Étudiant', 5.50, 0.50, '2025-01-01', 500000, 3000000, 120),
('Crédit Revolving', 15.90, 0.0, '2025-01-01', 200000, 2000000, 12),
('Micro-crédit', 18.00, 0.0, '2025-01-01', 50000, 500000, 6);

-- Insertion des agents
INSERT INTO agent (nom, prenom, motdepasse, email, role, etatActif) VALUES
('Rabe', 'Jean', 'admin123', 'jean.rabe@banque.mg', 'Administrateur', 1),
('Rakoto', 'Paul', 'conseiller456', 'paul.rakoto@banque.mg', 'Conseiller', 1),
('Andry', 'Lova', 'agent789', 'lova.andry@banque.mg', 'Agent de crédit', 1),
('Razafy', 'Sophie', 'sophie2025', 'sophie.razafy@banque.mg', 'Gestionnaire', 1),
('Rasolofo', 'Michel', 'michel123', 'michel.rasolofo@banque.mg', 'Directeur', 1);

-- Insertion des clients
INSERT INTO client (nom, prenom, motdepasse, email, telephone, adresse, dateNaissance, profession, revenuMensuel, etatActif) VALUES
('Rasoa', 'Marie', 'marie2025', 'marie.rasoa@email.com', '0321234567', 'Lot IVA 15 Antananarivo', '1990-05-10', 'Comptable', 850000.00, 1),
('Randri', 'Lina', 'lina123', 'lina.randri@email.com', '0347654321', 'Soarano Fianarantsoa', '1988-11-25', 'Infirmière', 650000.00, 1),
('Rahari', 'Tiana', 'tiana456', 'tiana.rahari@email.com', '0339988776', 'Tanamakoa Tamatave', '1995-08-19', 'Enseignant', 500000.00, 1),
('Rakoto', 'Hery', 'hery789', 'hery.rakoto@email.com', '0324567890', 'Antsirabe Centre', '1992-03-15', 'Entrepreneur', 1200000.00, 1),
('Ranaivo', 'Soa', 'soa2025', 'soa.ranaivo@email.com', '0331122334', 'Mahajanga Be', '1987-12-08', 'Médecin', 1500000.00, 1),
('Andriamanana', 'Koto', 'koto123', 'koto.andriamanana@email.com', '0338877665', 'Antsiranana Centre', '1993-07-22', 'Ingénieur', 950000.00, 1),
('Rabemananjara', 'Fidy', 'fidy456', 'fidy.rabemananjara@email.com', '0325544332', 'Toliara Be', '1991-09-14', 'Pharmacien', 1100000.00, 1);

-- Insertion des fonds entrants
INSERT INTO fondEntrant (montant, descri, datefond) VALUES
(50000000.00, 'Capital initial de la banque', '2025-01-01'),
(15000000.00, 'Apport des investisseurs', '2025-01-15'),
(8000000.00, 'Subvention gouvernementale', '2025-02-01'),
(5500000.00, 'Intérêts perçus mois précédent', '2025-02-15'),
(3200000.00, 'Remboursements anticipés', '2025-03-01'),
(7800000.00, 'Donation partenaire international', '2025-03-10');

-- Insertion des comptes clients
INSERT INTO compteClient (idClient, numeroCompte, solde, etatActif) VALUES
(1, 'CPT000001', 1500000.00, 1),
(2, 'CPT000002', 1000000.00, 1),
(3, 'CPT000003', 500000.00, 1),
(4, 'CPT000004', 2000000.00, 1),
(5, 'CPT000005', 1800000.00, 1),
(6, 'CPT000006', 750000.00, 1),
(7, 'CPT000007', 1300000.00, 1);

-- Insertion des transactions
INSERT INTO transaction (idCompte, typeTransaction, montant, description) VALUES
(1, 1, 200000.00, 'Versement initial'),
(1, 2, 50000.00, 'Paiement facture électricité'),
(1, 1, 150000.00, 'Salaire mensuel'),
(2, 1, 100000.00, 'Transfert familial'),
(2, 2, 25000.00, 'Retrait distributeur'),
(3, 1, 80000.00, 'Honoraires consultation'),
(3, 2, 30000.00, 'Achat médicaments'),
(4, 1, 300000.00, 'Vente produits'),
(4, 2, 75000.00, 'Paiement fournisseur'),
(5, 1, 120000.00, 'Consultation médicale'),
(5, 2, 40000.00, 'Frais bancaires'),
(6, 1, 95000.00, 'Salaire ingénieur'),
(7, 1, 110000.00, 'Vente pharmacie');

