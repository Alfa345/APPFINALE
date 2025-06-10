// restaurant-soundguard-backend/controllers/dashboardController.js

const db = require('../config/db');

// Get the latest measurement for a specific restaurant
// For simplicity, we'll hardcode the restaurant and main sensor for now.
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