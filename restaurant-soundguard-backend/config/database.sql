-- =========================================
-- BASE DE DONNÉES RESTAURANT SOUNDGUARD
-- =========================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS restaurant_soundguard 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE restaurant_soundguard;

-- =========================================
-- TABLE DES RESTAURANTS
-- =========================================
CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse TEXT NOT NULL,
    ville VARCHAR(50) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    telephone VARCHAR(20),
    email VARCHAR(100),
    type_cuisine ENUM('francaise', 'italienne', 'asiatique', 'mexicaine', 'autre') DEFAULT 'autre',
    capacite_max INT DEFAULT 50,
    surface_m2 INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_ville (ville),
    INDEX idx_localisation (latitude, longitude)
);

-- =========================================
-- TABLE DES CAPTEURS SONORES
-- =========================================
CREATE TABLE capteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    nom_capteur VARCHAR(50) NOT NULL,
    position_description VARCHAR(100), -- "Salle principale", "Terrasse", "Bar"
    modele VARCHAR(50),
    numero_serie VARCHAR(100),
    date_installation DATE,
    calibrage_offset DECIMAL(4,2) DEFAULT 0.00, -- Ajustement calibrage
    statut ENUM('actif', 'maintenance', 'hors_service') DEFAULT 'actif',
    derniere_communication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    INDEX idx_restaurant (restaurant_id),
    INDEX idx_statut (statut)
);

-- =========================================
-- TABLE DES SEUILS ET LIMITES
-- =========================================
CREATE TABLE seuils_sonores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    -- Seuils réglementaires (en dB)
    limite_legale_jour DECIMAL(5,2) DEFAULT 70.00,    -- Limite légale jour
    limite_legale_nuit DECIMAL(5,2) DEFAULT 65.00,    -- Limite légale nuit
    limite_legale_weekend DECIMAL(5,2) DEFAULT 75.00, -- Weekend/événements
    
    -- Seuils de confort client
    seuil_confort_min DECIMAL(5,2) DEFAULT 45.00,     -- Trop silencieux
    seuil_confort_max DECIMAL(5,2) DEFAULT 65.00,     -- Confortable
    seuil_inconfort DECIMAL(5,2) DEFAULT 75.00,       -- Inconfortable
    seuil_nuisible DECIMAL(5,2) DEFAULT 85.00,        -- Nuisible
    
    -- Seuils d'alerte
    alerte_niveau_1 DECIMAL(5,2) DEFAULT 70.00,       -- Attention
    alerte_niveau_2 DECIMAL(5,2) DEFAULT 80.00,       -- Alerte
    alerte_niveau_3 DECIMAL(5,2) DEFAULT 90.00,       -- Critique
    
    -- Périodes d'application
    heure_debut_jour TIME DEFAULT '08:00:00',
    heure_fin_jour TIME DEFAULT '22:00:00',
    
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    UNIQUE KEY unique_restaurant (restaurant_id)
);

-- =========================================
-- TABLE DES MESURES SONORES
-- =========================================
CREATE TABLE mesures_sonores (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    capteur_id INT NOT NULL,
    niveau_db DECIMAL(5,2) NOT NULL,
    timestamp_mesure TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Métadonnées contextuelles
    temperature DECIMAL(4,1), -- Température ambiante
    humidite DECIMAL(4,1),    -- Humidité %
    nb_clients_estime INT,    -- Estimation du nombre de clients
    
    -- Classification automatique
    categorie_niveau ENUM('silencieux', 'normal', 'eleve', 'excessif') NOT NULL,
    est_pic_sonore BOOLEAN DEFAULT FALSE, -- Pic détecté
    
    FOREIGN KEY (capteur_id) REFERENCES capteurs(id) ON DELETE CASCADE,
    INDEX idx_capteur_timestamp (capteur_id, timestamp_mesure),
    INDEX idx_timestamp (timestamp_mesure),
    INDEX idx_niveau (niveau_db),
    INDEX idx_categorie (categorie_niveau)
);

-- =========================================
-- TABLE DES ALERTES
-- =========================================
CREATE TABLE alertes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    capteur_id INT NOT NULL,
    type_alerte ENUM('seuil_depasse', 'pic_sonore', 'capteur_offline', 'maintenance') NOT NULL,
    niveau_gravite ENUM('info', 'attention', 'alerte', 'critique') NOT NULL,
    niveau_db_declencheur DECIMAL(5,2),
    message TEXT,
    timestamp_alerte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Gestion des alertes
    statut ENUM('nouvelle', 'vue', 'traitee', 'ignoree') DEFAULT 'nouvelle',
    timestamp_traitement TIMESTAMP NULL,
    traite_par VARCHAR(100),
    commentaire_traitement TEXT,
    
    FOREIGN KEY (capteur_id) REFERENCES capteurs(id) ON DELETE CASCADE,
    INDEX idx_capteur_timestamp (capteur_id, timestamp_alerte),
    INDEX idx_statut (statut),
    INDEX idx_gravite (niveau_gravite)
);

-- =========================================
-- TABLE DES STATISTIQUES QUOTIDIENNES
-- =========================================
CREATE TABLE stats_quotidiennes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    capteur_id INT NOT NULL,
    date_stat DATE NOT NULL,
    
    -- Statistiques de base
    niveau_moyen DECIMAL(5,2),
    niveau_min DECIMAL(5,2),
    niveau_max DECIMAL(5,2),
    niveau_median DECIMAL(5,2),
    
    -- Temps passé par catégorie (en minutes)
    temps_silencieux INT DEFAULT 0,
    temps_normal INT DEFAULT 0,
    temps_eleve INT DEFAULT 0,
    temps_excessif INT DEFAULT 0,
    
    -- Compteurs d'événements
    nb_pics_sonores INT DEFAULT 0,
    nb_depassements_seuil INT DEFAULT 0,
    nb_alertes_generees INT DEFAULT 0,
    
    -- Périodes spécifiques
    niveau_moyen_service DECIMAL(5,2), -- Pendant les heures de service
    niveau_moyen_pause DECIMAL(5,2),   -- Pendant les pauses
    
    FOREIGN KEY (capteur_id) REFERENCES capteurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_capteur_date (capteur_id, date_stat),
    INDEX idx_date (date_stat)
);

-- =========================================
-- TABLE DES ÉVÉNEMENTS RESTAURANT
-- =========================================
CREATE TABLE evenements_restaurant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    nom_evenement VARCHAR(100) NOT NULL,
    type_evenement ENUM('concert', 'anniversaire', 'fete', 'promotion', 'autre') DEFAULT 'autre',
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    seuil_autorise_temporaire DECIMAL(5,2), -- Seuil spécial pour l'événement
    description TEXT,
    
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    INDEX idx_restaurant_dates (restaurant_id, date_debut, date_fin)
);

-- =========================================
-- DONNÉES DE TEST RÉALISTES
-- =========================================

-- Restaurants d'exemple
INSERT INTO restaurants (nom, adresse, ville, code_postal, latitude, longitude, telephone, email, type_cuisine, capacite_max, surface_m2) VALUES
('Le Bistrot Français', '123 Rue de la Paix', 'Paris', '75001', 48.8566, 2.3522, '01.42.33.44.55', 'contact@bistrot-francais.fr', 'francaise', 80, 120),
('Pizza Roma', '456 Avenue des Champs', 'Lyon', '69001', 45.7640, 4.8357, '04.78.12.34.56', 'info@pizza-roma.fr', 'italienne', 60, 90),
('Dragon d\'Or', '789 Boulevard Saint-Michel', 'Marseille', '13001', 43.2965, 5.3698, '04.91.55.66.77', 'contact@dragon-or.fr', 'asiatique', 100, 150);

-- Capteurs pour chaque restaurant
INSERT INTO capteurs (restaurant_id, nom_capteur, position_description, modele, numero_serie, date_installation) VALUES
(1, 'CAPTEUR_SALLE_PRINCIPALE', 'Salle principale', 'SoundMeter Pro X1', 'SM2024-001', '2024-01-15'),
(1, 'CAPTEUR_TERRASSE', 'Terrasse extérieure', 'SoundMeter Pro X1', 'SM2024-002', '2024-01-15'),
(2, 'CAPTEUR_CENTRE', 'Centre de la salle', 'SoundMeter Pro X2', 'SM2024-003', '2024-02-01'),
(3, 'CAPTEUR_PRINCIPAL', 'Salle principale', 'SoundMeter Pro X1', 'SM2024-004', '2024-01-20');

-- Seuils par restaurant
INSERT INTO seuils_sonores (restaurant_id, limite_legale_jour, limite_legale_nuit, seuil_confort_max, seuil_inconfort, seuil_nuisible, alerte_niveau_1, alerte_niveau_2, alerte_niveau_3) VALUES
(1, 70.00, 65.00, 65.00, 75.00, 85.00, 70.00, 80.00, 90.00),
(2, 75.00, 70.00, 70.00, 80.00, 90.00, 75.00, 85.00, 95.00),
(3, 72.00, 67.00, 68.00, 78.00, 88.00, 72.00, 82.00, 92.00);

-- Mesures d'exemple récentes (dernières 24h)
INSERT INTO mesures_sonores (capteur_id, niveau_db, timestamp_mesure, temperature, humidite, nb_clients_estime, categorie_niveau, est_pic_sonore) VALUES
-- Restaurant 1 - Aujourd'hui
(1, 45.5, DATE_SUB(NOW(), INTERVAL 2 HOUR), 22.5, 45, 5, 'silencieux', FALSE),
(1, 62.3, DATE_SUB(NOW(), INTERVAL 1 HOUR), 23.0, 47, 25, 'normal', FALSE),
(1, 78.9, DATE_SUB(NOW(), INTERVAL 30 MINUTE), 24.5, 50, 45, 'eleve', TRUE),
(1, 55.2, DATE_SUB(NOW(), INTERVAL 15 MINUTE), 24.0, 48, 20, 'normal', FALSE),

-- Restaurant 2 - Aujourd'hui  
(3, 48.7, DATE_SUB(NOW(), INTERVAL 3 HOUR), 21.5, 52, 8, 'silencieux', FALSE),
(3, 71.2, DATE_SUB(NOW(), INTERVAL 2 HOUR), 22.8, 55, 35, 'eleve', FALSE),
(3, 85.6, DATE_SUB(NOW(), INTERVAL 1 HOUR), 25.2, 58, 55, 'excessif', TRUE),
(3, 67.4, DATE_SUB(NOW(), INTERVAL 45 MINUTE), 24.8, 56, 40, 'normal', FALSE);

-- Alertes d'exemple
INSERT INTO alertes (capteur_id, type_alerte, niveau_gravite, niveau_db_declencheur, message, statut) VALUES
(1, 'seuil_depasse', 'attention', 78.9, 'Seuil de confort dépassé pendant le service du soir', 'nouvelle'),
(3, 'seuil_depasse', 'alerte', 85.6, 'Niveau sonore critique détecté - intervention recommandée', 'nouvelle');

-- =========================================
-- VUES UTILES POUR L'APPLICATION
-- =========================================

-- Vue des dernières mesures par restaurant
CREATE VIEW vue_dernieres_mesures AS
SELECT 
    r.nom as restaurant_nom,
    c.nom_capteur,
    m.niveau_db,
    m.timestamp_mesure,
    m.categorie_niveau,
    s.seuil_confort_max,
    s.seuil_inconfort,
    s.seuil_nuisible,
    CASE 
        WHEN m.niveau_db <= s.seuil_confort_max THEN 'vert'
        WHEN m.niveau_db <= s.seuil_inconfort THEN 'orange' 
        ELSE 'rouge'
    END as couleur_alerte
FROM restaurants r
JOIN capteurs c ON r.id = c.restaurant_id
JOIN mesures_sonores m ON c.id = m.capteur_id
JOIN seuils_sonores s ON r.id = s.restaurant_id
WHERE c.statut = 'actif'
AND m.timestamp_mesure = (
    SELECT MAX(timestamp_mesure) 
    FROM mesures_sonores m2 
    WHERE m2.capteur_id = c.id
);

-- Vue des statistiques temps réel
CREATE VIEW vue_stats_temps_reel AS
SELECT 
    r.id as restaurant_id,
    r.nom as restaurant_nom,
    COUNT(DISTINCT c.id) as nb_capteurs_actifs,
    AVG(m.niveau_db) as niveau_moyen_actuel,
    MAX(m.niveau_db) as niveau_max_actuel,
    COUNT(CASE WHEN a.statut = 'nouvelle' THEN 1 END) as alertes_actives
FROM restaurants r
LEFT JOIN capteurs c ON r.id = c.restaurant_id AND c.statut = 'actif'
LEFT JOIN mesures_sonores m ON c.id = m.capteur_id 
    AND m.timestamp_mesure > DATE_SUB(NOW(), INTERVAL 1 HOUR)
LEFT JOIN alertes a ON c.id = a.capteur_id 
    AND a.statut = 'nouvelle'
    AND a.timestamp_alerte > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY r.id, r.nom;

-- =========================================
-- INDEX POUR OPTIMISATION
-- =========================================

-- Index pour les requêtes temps réel
CREATE INDEX idx_mesures_recent ON mesures_sonores(timestamp_mesure DESC, capteur_id);
CREATE INDEX idx_alertes_recent ON alertes(timestamp_alerte DESC, statut);

-- =========================================
-- PROCÉDURES STOCKÉES UTILES
-- =========================================

DELIMITER //

-- Procédure pour nettoyer les anciennes données
CREATE PROCEDURE CleanOldData(IN days_to_keep INT)
BEGIN
    -- Supprimer les mesures de plus de X jours
    DELETE FROM mesures_sonores 
    WHERE timestamp_mesure < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
    
    -- Supprimer les alertes traitées de plus de 30 jours
    DELETE FROM alertes 
    WHERE statut IN ('traitee', 'ignoree') 
    AND timestamp_alerte < DATE_SUB(NOW(), INTERVAL 30 DAY);
END //

-- Procédure pour calculer les stats quotidiennes
CREATE PROCEDURE CalculateStatsQuotidiennes(IN target_date DATE)
BEGIN
    INSERT INTO stats_quotidiennes (
        capteur_id, date_stat, niveau_moyen, niveau_min, niveau_max,
        temps_silencieux, temps_normal, temps_eleve, temps_excessif,
        nb_pics_sonores, nb_depassements_seuil
    )
    SELECT 
        m.capteur_id,
        DATE(m.timestamp_mesure) as date_stat,
        AVG(m.niveau_db) as niveau_moyen,
        MIN(m.niveau_db) as niveau_min,
        MAX(m.niveau_db) as niveau_max,
        COUNT(CASE WHEN m.categorie_niveau = 'silencieux' THEN 1 END) as temps_silencieux,
        COUNT(CASE WHEN m.categorie_niveau = 'normal' THEN 1 END) as temps_normal,
        COUNT(CASE WHEN m.categorie_niveau = 'eleve' THEN 1 END) as temps_eleve,
        COUNT(CASE WHEN m.categorie_niveau = 'excessif' THEN 1 END) as temps_excessif,
        COUNT(CASE WHEN m.est_pic_sonore = TRUE THEN 1 END) as nb_pics_sonores,
        COUNT(CASE WHEN m.niveau_db > (SELECT alerte_niveau_1 FROM seuils_sonores s JOIN capteurs c ON s.restaurant_id = c.restaurant_id WHERE c.id = m.capteur_id) THEN 1 END) as nb_depassements_seuil
    FROM mesures_sonores m
    WHERE DATE(m.timestamp_mesure) = target_date
    GROUP BY m.capteur_id, DATE(m.timestamp_mesure)
    ON DUPLICATE KEY UPDATE
        niveau_moyen = VALUES(niveau_moyen),
        niveau_min = VALUES(niveau_min),
        niveau_max = VALUES(niveau_max),
        temps_silencieux = VALUES(temps_silencieux),
        temps_normal = VALUES(temps_normal),
        temps_eleve = VALUES(temps_eleve),
        temps_excessif = VALUES(temps_excessif),
        nb_pics_sonores = VALUES(nb_pics_sonores),
        nb_depassements_seuil = VALUES(nb_depassements_seuil);
END //

DELIMITER ;

-- =========================================
-- TRIGGERS POUR AUTOMATISATION
-- =========================================

DELIMITER //

-- Trigger pour classifier automatiquement les niveaux sonores
CREATE TRIGGER classify_sound_level 
BEFORE INSERT ON mesures_sonores
FOR EACH ROW
BEGIN
    DECLARE seuil_confort DECIMAL(5,2);
    DECLARE seuil_inconfort DECIMAL(5,2);
    DECLARE seuil_nuisible DECIMAL(5,2);
    
    -- Récupérer les seuils du restaurant
    SELECT s.seuil_confort_max, s.seuil_inconfort, s.seuil_nuisible
    INTO seuil_confort, seuil_inconfort, seuil_nuisible
    FROM seuils_sonores s
    JOIN capteurs c ON s.restaurant_id = c.restaurant_id
    WHERE c.id = NEW.capteur_id;
    
    -- Classifier le niveau
    IF NEW.niveau_db <= seuil_confort THEN
        SET NEW.categorie_niveau = 'normal';
    ELSEIF NEW.niveau_db <= seuil_inconfort THEN
        SET NEW.categorie_niveau = 'eleve'; 
    ELSEIF NEW.niveau_db <= seuil_nuisible THEN
        SET NEW.categorie_niveau = 'excessif';
    ELSE
        SET NEW.categorie_niveau = 'excessif';
    END IF;
    
    -- Détecter les pics sonores (>15dB au-dessus du seuil confort)
    IF NEW.niveau_db > (seuil_confort + 15) THEN
        SET NEW.est_pic_sonore = TRUE;
    END IF;
END //

DELIMITER ;

-- Message de fin
SELECT 'Base de données Restaurant SoundGuard créée avec succès!' as message;