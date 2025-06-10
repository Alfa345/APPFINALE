// restaurant-soundguard-backend/controllers/dashboardController.js

const db = require('../config/db');

// THIS IS THE MISSING CODE
// Get the latest measurement for a specific restaurant
exports.getLatestMeasurement = (req, res) => {
  // This query uses the excellent view you created in your SQL file!
  const sql = `
    SELECT 
        restaurant_nom,
        niveau_db,
        timestamp_mesure,
        couleur_alerte AS status_color,
        CASE 
            WHEN couleur_alerte = 'vert' THEN 'Normal'
            WHEN couleur_alerte = 'orange' THEN 'Élevé'
            ELSE 'Critique'
        END AS status_text
    FROM vue_dernieres_mesures
    WHERE restaurant_nom = 'Le Bistrot Français' AND nom_capteur = 'CAPTEUR_SALLE_PRINCIPALE'
    LIMIT 1;
  `;

  db.query(sql, (err, results) => {
    if (err) {
      console.error("Database query error:", err);
      return res.status(500).json({ error: "Error fetching data from database" });
    }
    if (results.length === 0) {
      return res.status(404).json({ message: "No measurement found for this restaurant." });
    }
    res.json(results[0]);
  });
};


// Get measurements from the last 24 hours
exports.get24HourHistory = (req, res) => {
  // For now, we use the hardcoded sensor ID for 'CAPTEUR_SALLE_PRINCIPALE' which is 1
  const sensorId = 1;

  const sql = `
    SELECT 
        niveau_db,
        timestamp_mesure
    FROM mesures_sonores
    WHERE capteur_id = ?
      AND timestamp_mesure >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ORDER BY timestamp_mesure ASC;
  `;

  db.query(sql, [sensorId], (err, results) => {
    if (err) {
      console.error("Database query error:", err);
      return res.status(500).json({ error: "Error fetching history data" });
    }
    res.json(results);
  });
};