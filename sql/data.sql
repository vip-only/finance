
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

INSERT INTO etatActif (libelle) VALUES 
('Actif'),
('Inactif'),
('Suspendu'),
('Fermé');

INSERT INTO modePaiement (libelle) VALUES 
('Virement bancaire'),
('Espèces'),
('Chèque'),
('Carte bancaire'),
('Prélèvement automatique'),
('Mobile Money'),
('Crypto-monnaie');

INSERT INTO typeTransaction (libelle) VALUES 
('Entrée'),
('Sortie');
