// restaurant-soundguard-backend/server.js

const express = require('express');
const cors = require('cors');
const dashboardRoutes = require('./routes/dashboardRoutes');

const app = express();
const PORT = 3001; // Port for our backend server

// Middleware
app.use(cors()); // Allows requests from our frontend
app.use(express.json()); // Allows us to read JSON in request bodies

// Routes
app.use('/api/dashboard', dashboardRoutes);

// Start the server
app.listen(PORT, () => {
  console.log(`Backend server is running on http://localhost:${PORT}`);
});