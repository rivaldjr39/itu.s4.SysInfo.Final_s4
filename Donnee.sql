INSERT INTO prefixes (prefixe) VALUES ('033'), ('037');

INSERT INTO types_operations (code, libelle) VALUES
('DEPOT', 'Dépôt'),
('RETRAIT', 'Retrait'),
('TRANSFERT', 'Transfert');

-- Exemple de barème par tranche pour le retrait (id type_operation = 2)
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais_fixe, frais_pourcentage) VALUES
(2, 0,      5000,    200,  0),
(2, 5001,   20000,   500,  0),
(2, 20001,  100000,  0,    2.0),
(2, 100001, 999999999, 0,  1.5);


-- ============================================================
-- VUES UTILES POUR LE CÔTÉ OPÉRATEUR
-- ============================================================

-- Situation des gains (frais perçus sur retrait/transfert)
CREATE VIEW vue_gains_operateur AS
SELECT
    t.libelle           AS type_operation,
    DATE(o.date_operation) AS jour,
    SUM(o.frais)         AS total_frais,
    COUNT(*)             AS nombre_operations
FROM operations o
JOIN types_operations t ON t.id = o.type_operation_id
WHERE o.statut = 'REUSSI' AND t.code IN ('RETRAIT', 'TRANSFERT')
GROUP BY t.libelle, DATE(o.date_operation);

-- Situation des comptes clients
CREATE VIEW vue_situation_comptes AS
SELECT
    cl.numero_telephone,
    c.solde,
    c.date_creation,
    (SELECT COUNT(*) FROM operations o
     WHERE o.compte_source_id = c.id OR o.compte_destination_id = c.id) AS nombre_operations
FROM comptes c
JOIN clients cl ON cl.id = c.client_id;