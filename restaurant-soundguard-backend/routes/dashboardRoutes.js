// restaurant-soundguard-backend/routes/dashboardRoutes.js

const express = require('express');
const router = express.Router();
const dashboardController = require('../controllers/dashboardController');

// Define the route: GET /api/dashboard/latest
router.get('/latest', dashboardController.getLatestMeasurement);

module.exports = router;