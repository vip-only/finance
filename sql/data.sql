-- Insertion des donnees de base (tables sans dependances)
INSERT INTO etatActif (libelle) VALUES 
('Actif'),
('Inactif'),
('Suspendu'),
('Ferme');

INSERT INTO etatValidation (libelle) VALUES 
('En attente'),
('Approuve'),
('Rejete');

INSERT INTO modePaiement (libelle) VALUES 
('Virement bancaire'),
('Especes'),
('Cheque'),
('Carte bancaire'),
('Prelevement automatique'),
('Mobile Money');

INSERT INTO typeTransaction (libelle) VALUES 
('Entree'),
('Sortie');

INSERT INTO type_retour (libelle, nbJourEspacant) VALUES
('Mensuel', 30),
('Trimestriel', 90),
('Semestriel', 180),
('Annuel', 365);

-- Insertion des types de prêts
INSERT INTO type_pret (libelle, taux, assurance, dateCreation, pretmin, pretmax, dureeMoisMax) VALUES
('Pret Personnel', 12.50, 1.50, '2025-01-01', 100000, 5000000, 24),
('Pret Immobilier', 6.75, 2.00, '2025-01-01', 5000000, 50000000, 240),
('Pret Auto', 9.25, 1.75, '2025-01-01', 1000000, 15000000, 60),
('Pret Etudiant', 5.50, 0.50, '2025-01-01', 500000, 3000000, 120),
('Credit Revolving', 15.90, 0.0, '2025-01-01', 200000, 2000000, 12),
('Micro-credit', 18.00, 0.0, '2025-01-01', 50000, 500000, 6);

-- Insertion des agents
INSERT INTO agent (nom, prenom, motdepasse, email, role, etatActif) VALUES
('Rabe', 'Jean', 'admin123', 'jean.rabe@banque.mg', 'Administrateur', 1),
('Rakoto', 'Paul', 'conseiller456', 'paul.rakoto@banque.mg', 'Conseiller', 1),
('Andry', 'Lova', 'agent789', 'lova.andry@banque.mg', 'Agent de credit', 1),
('Razafy', 'Sophie', 'sophie2025', 'sophie.razafy@banque.mg', 'Gestionnaire', 1),
('Rasolofo', 'Michel', 'michel123', 'michel.rasolofo@banque.mg', 'Directeur', 1);

-- Insertion des clients
INSERT INTO client (nom, prenom, motdepasse, email, telephone, adresse, dateNaissance, profession, revenuMensuel, etatActif) VALUES
('Rasoa', 'Marie', 'marie2025', 'marie.rasoa@email.com', '0321234567', 'Lot IVA 15 Antananarivo', '1990-05-10', 'Comptable', 850000.00, 1),
('Randri', 'Lina', 'lina123', 'lina.randri@email.com', '0347654321', 'Soarano Fianarantsoa', '1988-11-25', 'Infirmiere', 650000.00, 1),
('Rahari', 'Tiana', 'tiana456', 'tiana.rahari@email.com', '0339988776', 'Tanamakoa Tamatave', '1995-08-19', 'Enseignant', 500000.00, 1),
('Rakoto', 'Hery', 'hery789', 'hery.rakoto@email.com', '0324567890', 'Antsirabe Centre', '1992-03-15', 'Entrepreneur', 1200000.00, 1),
('Ranaivo', 'Soa', 'soa2025', 'soa.ranaivo@email.com', '0331122334', 'Mahajanga Be', '1987-12-08', 'Medecin', 1500000.00, 1),
('Andriamanana', 'Koto', 'koto123', 'koto.andriamanana@email.com', '0338877665', 'Antsiranana Centre', '1993-07-22', 'Ingenieur', 950000.00, 1),
('Rabemananjara', 'Fidy', 'fidy456', 'fidy.rabemananjara@email.com', '0325544332', 'Toliara Be', '1991-09-14', 'Pharmacien', 1100000.00, 1);

-- Insertion des fonds entrants
INSERT INTO fondEntrant (montant, descri, datefond) VALUES
(50000000.00, 'Capital initial de la banque', '2025-01-01'),
(15000000.00, 'Apport des investisseurs', '2025-01-15'),
(8000000.00, 'Subvention gouvernementale', '2025-02-01'),
(5500000.00, 'Interets percus mois precedent', '2025-02-15'),
(3200000.00, 'Remboursements anticipes', '2025-03-01'),
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
(1, 2, 50000.00, 'Paiement facture electricite'),
(1, 1, 150000.00, 'Salaire mensuel'),
(2, 1, 100000.00, 'Transfert familial'),
(2, 2, 25000.00, 'Retrait distributeur'),
(3, 1, 80000.00, 'Honoraires consultation'),
(3, 2, 30000.00, 'Achat medicaments'),
(4, 1, 300000.00, 'Vente produits'),
(4, 2, 75000.00, 'Paiement fournisseur'),
(5, 1, 120000.00, 'Consultation medicale'),
(5, 2, 40000.00, 'Frais bancaires'),
(6, 1, 95000.00, 'Salaire ingenieur'),
(7, 1, 110000.00, 'Vente pharmacie');



INSERT INTO pret (idClient, idTypePret, montantAccorde, dureeMois, montantTotal, dateAccepte, DELAI, dateDebutRemboursement, dateFinRemboursement, modePaiement) VALUES
(1, 1, 3000000.00, 12, 3375000.00, '2025-01-15', 30, '2025-02-15', '2026-01-15', 1),
(2, 2, 25000000.00, 60, 30375000.00, '2025-01-20', 30, '2025-02-20', '2030-01-20', 2),
(3, 4, 1500000.00, 24, 1665000.00, '2025-02-01', 30, '2025-03-01', '2027-02-01', 3),
(4, 3, 8000000.00, 36, 9480000.00, '2025-02-10', 30, '2025-03-10', '2028-02-10', 1),
(5, 1, 2000000.00, 18, 2250000.00, '2025-02-15', 30, '2025-03-15', '2026-08-15', 5),
(6, 6, 800000.00, 6, 872000.00, '2025-03-01', 30, '2025-04-01', '2025-09-01', 4),
(7, 1, 4000000.00, 24, 4500000.00, '2025-03-05', 30, '2025-04-05', '2027-03-05', 1);

-- Insertion des etats des prêts
INSERT INTO etat_pret (idPret, etat) VALUES
(1, 2), -- Prêt 1 approuve
(2, 2), -- Prêt 2 approuve
(3, 2), -- Prêt 3 approuve
(4, 1), -- Prêt 4 en attente
(5, 2), -- Prêt 5 approuve
(6, 2), -- Prêt 6 approuve
(7, 1); -- Prêt 7 en attente

INSERT INTO amortissement (idPret, numMois, datePaiementPrevue, montantMensuel, interet, assurance, capitalRembourse, capitalRestant) VALUES
(1, 1, '2025-02-15', 281250.00, 31250.00, 3750.00, 250000.00, 2750000.00),
(1, 2, '2025-03-15', 281250.00, 28645.83, 3750.00, 252604.17, 2497395.83),
(1, 3, '2025-04-15', 281250.00, 26015.58, 3750.00, 255234.42, 2242161.41),
(1, 4, '2025-05-15', 281250.00, 23358.98, 3750.00, 257891.02, 1984270.39),
(1, 5, '2025-06-15', 281250.00, 20675.73, 3750.00, 260574.27, 1723696.12),
(1, 6, '2025-07-15', 281250.00, 17965.58, 3750.00, 263284.42, 1460411.70),
(1, 7, '2025-08-15', 281250.00, 15228.29, 3750.00, 266021.71, 1194389.99),
(1, 8, '2025-09-15', 281250.00, 12463.60, 3750.00, 268786.40, 925603.59),
(1, 9, '2025-10-15', 281250.00, 9671.29, 3750.00, 271578.71, 654024.88),
(1, 10, '2025-11-15', 281250.00, 6851.09, 3750.00, 274398.91, 379625.97),
(1, 11, '2025-12-15', 281250.00, 4002.77, 3750.00, 277247.23, 102378.74),
(1, 12, '2026-01-15', 103447.40, 1066.07, 1281.33, 102378.74, 0.00);

-- Insertion des amortissements pour le prêt 3 (Tiana - 1.5M sur 24 mois à 5.5% + 0.5% assurance)
INSERT INTO amortissement (idPret, numMois, datePaiementPrevue, montantMensuel, interet, assurance, capitalRembourse, capitalRestant) VALUES
(3, 1, '2025-03-01', 69375.00, 6875.00, 625.00, 62500.00, 1437500.00),
(3, 2, '2025-04-01', 69375.00, 6671.88, 625.00, 62703.12, 1374796.88),
(3, 3, '2025-05-01', 69375.00, 6467.82, 625.00, 62907.18, 1311889.70),
(3, 4, '2025-06-01', 69375.00, 6262.79, 625.00, 63112.21, 1248777.49),
(3, 5, '2025-07-01', 69375.00, 6056.77, 625.00, 63318.23, 1185459.26),
(3, 6, '2025-08-01', 69375.00, 5849.73, 625.00, 63525.27, 1121934.00),
(3, 7, '2025-09-01', 69375.00, 5641.66, 625.00, 63733.34, 1058200.66),
(3, 8, '2025-10-01', 69375.00, 5432.53, 625.00, 63942.47, 994258.18),
(3, 9, '2025-11-01', 69375.00, 5222.33, 625.00, 64152.67, 930105.52),
(3, 10, '2025-12-01', 69375.00, 5011.03, 625.00, 64363.97, 865741.55),
(3, 11, '2026-01-01', 69375.00, 4798.61, 625.00, 64576.39, 801165.16),
(3, 12, '2026-02-01', 69375.00, 4585.05, 625.00, 64789.95, 736375.21);

-- Insertion des amortissements pour le prêt 5 (Soa - 2M sur 18 mois à 12.5% + 1.5% assurance)
INSERT INTO amortissement (idPret, numMois, datePaiementPrevue, montantMensuel, interet, assurance, capitalRembourse, capitalRestant) VALUES
(5, 1, '2025-03-15', 125000.00, 20833.33, 2500.00, 104166.67, 1895833.33),
(5, 2, '2025-04-15', 125000.00, 19784.03, 2500.00, 105215.97, 1790617.36),
(5, 3, '2025-05-15', 125000.00, 18717.68, 2500.00, 106282.32, 1684335.04),
(5, 4, '2025-06-15', 125000.00, 17634.53, 2500.00, 107365.47, 1576969.57),
(5, 5, '2025-07-15', 125000.00, 16534.27, 2500.00, 108465.73, 1468503.84),
(5, 6, '2025-08-15', 125000.00, 15416.62, 2500.00, 109583.38, 1358920.46),
(5, 7, '2025-09-15', 125000.00, 14281.25, 2500.00, 110718.75, 1248201.71),
(5, 8, '2025-10-15', 125000.00, 13127.85, 2500.00, 111872.15, 1136329.56),
(5, 9, '2025-11-15', 125000.00, 11956.13, 2500.00, 113043.87, 1023285.69),
(5, 10, '2025-12-15', 125000.00, 10765.80, 2500.00, 114234.20, 909051.49),
(5, 11, '2026-01-15', 125000.00, 9556.57, 2500.00, 115443.43, 793608.06),
(5, 12, '2026-02-15', 125000.00, 8328.17, 2500.00, 116671.83, 676936.23),
(5, 13, '2026-03-15', 125000.00, 7080.29, 2500.00, 117919.71, 559016.52),
(5, 14, '2026-04-15', 125000.00, 5812.67, 2500.00, 119187.33, 439829.19),
(5, 15, '2026-05-15', 125000.00, 4525.05, 2500.00, 120474.95, 319354.24),
(5, 16, '2026-06-15', 125000.00, 3217.23, 2500.00, 121782.77, 197571.47),
(5, 17, '2026-07-15', 125000.00, 1888.95, 2500.00, 123111.05, 74460.42),
(5, 18, '2026-08-15', 75243.76, 783.34, 1000.00, 74460.42, 0.00);

-- Insertion des amortissements pour le prêt 6 (Koto - 800K sur 6 mois à 18% + 3% assurance)
INSERT INTO amortissement (idPret, numMois, datePaiementPrevue, montantMensuel, interet, assurance, capitalRembourse, capitalRestant) VALUES
(6, 1, '2025-04-01', 145333.33, 12000.00, 2000.00, 133333.33, 666666.67),
(6, 2, '2025-05-01', 145333.33, 10000.00, 2000.00, 135333.33, 531333.34),
(6, 3, '2025-06-01', 145333.33, 7970.00, 2000.00, 137363.33, 393970.01),
(6, 4, '2025-07-01', 145333.33, 5909.55, 2000.00, 139423.78, 254546.23),
(6, 5, '2025-08-01', 145333.33, 3818.19, 2000.00, 141515.14, 113031.09),
(6, 6, '2025-09-01', 114727.75, 1695.47, 1331.28, 113031.09, 0.00);

-- Insertion des remboursements effectues AVEC assurance
INSERT INTO remboursement (idPret, idAmortissement, numMois, montantPaye, capital_restant, capital_rembourse, interet, assurance, modePaiement, reference) VALUES
-- Prêt 1 (Marie) - 3 premiers mois payes
(1, 1, 1, 281250.00, 2750000.00, 250000.00, 31250.00, 3750.00, 1, 'VIR2025-001'),
(1, 2, 2, 281250.00, 2497395.83, 252604.17, 28645.83, 3750.00, 1, 'VIR2025-002'),
(1, 3, 3, 281250.00, 2242161.41, 255234.42, 26015.58, 3750.00, 1, 'VIR2025-003'),

-- Prêt 3 (Tiana) - 2 premiers mois payes
(3, 13, 1, 69375.00, 1437500.00, 62500.00, 6875.00, 625.00, 3, 'CHQ2025-001'),
(3, 14, 2, 69375.00, 1374796.88, 62703.12, 6671.88, 625.00, 3, 'CHQ2025-002'),

-- Prêt 5 (Soa) - 4 premiers mois payes
(5, 21, 1, 125000.00, 1895833.33, 104166.67, 20833.33, 2500.00, 5, 'MOB2025-001'),
(5, 22, 2, 125000.00, 1790617.36, 105215.97, 19784.03, 2500.00, 5, 'MOB2025-002'),
(5, 23, 3, 125000.00, 1684335.04, 106282.32, 18717.68, 2500.00, 5, 'MOB2025-003'),
(5, 24, 4, 125000.00, 1576969.57, 107365.47, 17634.53, 2500.00, 5, 'MOB2025-004'),

-- Prêt 6 (Koto) - 1er mois paye
(6, 31, 1, 145333.33, 666666.67, 133333.33, 12000.00, 2000.00, 4, 'CB2025-001');

-- Remboursements de base (AVEC point-virgule à la fin)
INSERT INTO remboursement (idPret, idAmortissement, numMois, montantPaye, capital_restant, capital_rembourse, interet, assurance, modePaiement, reference) VALUES
-- Prêt 1 (Marie) - 3 premiers mois payes
(1, 1, 1, 281250.00, 2750000.00, 250000.00, 31250.00, 3750.00, 1, 'VIR2025-001'),
(1, 2, 2, 281250.00, 2497395.83, 252604.17, 28645.83, 3750.00, 1, 'VIR2025-002'),
(1, 3, 3, 281250.00, 2242161.41, 255234.42, 26015.58, 3750.00, 1, 'VIR2025-003'),

-- Prêt 3 (Tiana) - 2 premiers mois payes
(3, 13, 1, 69375.00, 1437500.00, 62500.00, 6875.00, 625.00, 3, 'CHQ2025-001'),
(3, 14, 2, 69375.00, 1374796.88, 62703.12, 6671.88, 625.00, 3, 'CHQ2025-002'),

-- Prêt 5 (Soa) - 4 premiers mois payes
(5, 21, 1, 125000.00, 1895833.33, 104166.67, 20833.33, 2500.00, 5, 'MOB2025-001'),
(5, 22, 2, 125000.00, 1790617.36, 105215.97, 19784.03, 2500.00, 5, 'MOB2025-002'),
(5, 23, 3, 125000.00, 1684335.04, 106282.32, 18717.68, 2500.00, 5, 'MOB2025-003'),
(5, 24, 4, 125000.00, 1576969.57, 107365.47, 17634.53, 2500.00, 5, 'MOB2025-004'),

-- Prêt 6 (Koto) - 1er mois paye
(6, 31, 1, 145333.33, 666666.67, 133333.33, 12000.00, 2000.00, 4, 'CB2025-001');

-- DEUXIeME INSERT avec remboursements supplementaires
INSERT INTO remboursement (idPret, idAmortissement, numMois, montantPaye, capital_restant, capital_rembourse, interet, assurance, datePaiement, modePaiement, reference) VALUES
-- Remboursements supplementaires pour le prêt 1 (Marie) - mois 4 et 5
(1, 4, 4, 281250.00, 1984270.39, 257891.02, 23358.98, 3750.00, '2025-05-20', 1, 'VIR2025-004'),
(1, 5, 5, 281250.00, 1723696.12, 260574.27, 20675.73, 3750.00, '2025-06-18', 1, 'VIR2025-005'),

-- Remboursements supplementaires pour le prêt 3 (Tiana) - mois 3, 4 et 5
(3, 15, 3, 69375.00, 1311889.70, 62907.18, 6467.82, 625.00, '2025-05-05', 3, 'CHQ2025-003'),
(3, 16, 4, 69375.00, 1248777.49, 63112.21, 6262.79, 625.00, '2025-06-02', 3, 'CHQ2025-004'),
(3, 17, 5, 69375.00, 1185459.26, 63318.23, 6056.77, 625.00, '2025-07-08', 3, 'CHQ2025-005'),

-- Remboursements supplementaires pour le prêt 5 (Soa) - mois 5, 6, 7 et 8
(5, 25, 5, 125000.00, 1468503.84, 108465.73, 16534.27, 2500.00, '2025-07-20', 5, 'MOB2025-005'),
(5, 26, 6, 125000.00, 1358920.46, 109583.38, 15416.62, 2500.00, '2025-08-25', 5, 'MOB2025-006'),
(5, 27, 7, 125000.00, 1248201.71, 110718.75, 14281.25, 2500.00, '2025-09-22', 5, 'MOB2025-007'),
(5, 28, 8, 125000.00, 1136329.56, 111872.15, 13127.85, 2500.00, '2025-10-28', 5, 'MOB2025-008'),

-- Remboursements supplementaires pour le prêt 6 (Koto) - mois 2, 3 et 4
(6, 32, 2, 145333.33, 531333.34, 135333.33, 10000.00, 2000.00, '2025-05-10', 4, 'CB2025-002'),
(6, 33, 3, 145333.33, 393970.01, 137363.33, 7970.00, 2000.00, '2025-06-12', 4, 'CB2025-003'),
(6, 34, 4, 145333.33, 254546.23, 139423.78, 5909.55, 2000.00, '2025-07-15', 4, 'CB2025-004');

-- TROISIeME INSERT pour les remboursements de 2024
INSERT INTO remboursement (idPret, numMois, montantPaye, capital_restant, capital_rembourse, interet, assurance, datePaiement, modePaiement, reference) VALUES
-- Janvier 2024
(1, 10, 120000.00, 800000.00, 110000.00, 10000.00, 1500.00, '2024-01-15', 1, 'VIR2024-001'),
(3, 8, 85000.00, 600000.00, 78000.00, 7000.00, 800.00, '2024-01-22', 3, 'CHQ2024-001'),

-- Fevrier 2024
(5, 6, 95000.00, 750000.00, 88000.00, 7000.00, 1200.00, '2024-02-10', 5, 'MOB2024-001'),
(6, 3, 75000.00, 400000.00, 70000.00, 5000.00, 900.00, '2024-02-28', 4, 'CB2024-001'),

-- Mars 2024
(1, 11, 115000.00, 650000.00, 105000.00, 10000.00, 1400.00, '2024-03-12', 1, 'VIR2024-002'),
(3, 9, 82000.00, 520000.00, 75000.00, 7000.00, 750.00, '2024-03-25', 3, 'CHQ2024-002'),

-- Avril 2024
(5, 7, 98000.00, 680000.00, 90000.00, 8000.00, 1100.00, '2024-04-08', 5, 'MOB2024-002'),

-- Mai 2024
(6, 4, 78000.00, 320000.00, 72000.00, 6000.00, 850.00, '2024-05-15', 4, 'CB2024-002'),
(1, 12, 110000.00, 540000.00, 100000.00, 10000.00, 1300.00, '2024-05-30', 1, 'VIR2024-003'),

-- Juin 2024
(3, 10, 80000.00, 440000.00, 73000.00, 7000.00, 700.00, '2024-06-18', 3, 'CHQ2024-003'),
(5, 8, 102000.00, 578000.00, 94000.00, 8000.00, 1000.00, '2024-06-25', 5, 'MOB2024-003'),

-- Juillet 2024
(6, 5, 76000.00, 244000.00, 70000.00, 6000.00, 800.00, '2024-07-20', 4, 'CB2024-003'),

-- Août 2024
(1, 13, 108000.00, 432000.00, 98000.00, 10000.00, 1200.00, '2024-08-14', 1, 'VIR2024-004'),
(3, 11, 79000.00, 361000.00, 72000.00, 7000.00, 650.00, '2024-08-28', 3, 'CHQ2024-004'),

-- Septembre 2024
(5, 9, 105000.00, 473000.00, 97000.00, 8000.00, 950.00, '2024-09-16', 5, 'MOB2024-004'),

-- Octobre 2024
(6, 6, 74000.00, 170000.00, 68000.00, 6000.00, 750.00, '2024-10-22', 4, 'CB2024-004'),
(1, 14, 106000.00, 326000.00, 96000.00, 10000.00, 1100.00, '2024-10-31', 1, 'VIR2024-005'),

-- Novembre 2024
(3, 12, 77000.00, 284000.00, 70000.00, 7000.00, 600.00, '2024-11-12', 3, 'CHQ2024-005'),
(5, 10, 108000.00, 365000.00, 100000.00, 8000.00, 900.00, '2024-11-27', 5, 'MOB2024-005'),

-- Decembre 2024
(1, 15, 104000.00, 222000.00, 94000.00, 10000.00, 1000.00, '2024-12-10', 1, 'VIR2024-006'),
(6, 7, 72000.00, 98000.00, 66000.00, 6000.00, 700.00, '2024-12-20', 4, 'CB2024-005');

