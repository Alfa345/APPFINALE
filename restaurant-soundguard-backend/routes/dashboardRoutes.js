// restaurant-soundguard-backend/routes/dashboardRoutes.js

const express = require('express');
const router = express.Router();
const dashboardController = require('../controllers/dashboardController');

// Existing route
router.get('/latest', dashboardController.getLatestMeasurement);

// NEW ROUTE for 24h history
router.get('/history', dashboardController.get24HourHistory);

module.exports = router;
