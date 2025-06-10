// restaurant-soundguard-backend/server.js

const express = require('express');
const cors = require('cors');
const dashboardRoutes = require('./routes/dashboardRoutes');
const db = require('./config/db'); // Import the database connection

const app = express();
const PORT = 3001; // Port for our backend server

// Middleware
app.use(cors()); // Allows requests from our frontend
app.use(express.json()); // Allows us to read JSON in request bodies

// Routes
app.use('/api/dashboard', dashboardRoutes);

// --- MODIFIED SECTION ---
// Attempt to connect to the database BEFORE starting the Express server.
db.connect(err => {
  if (err) {
    // If connection fails, log the error and DO NOT start the server.
    console.error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
    console.error('!!! DATABASE CONNECTION FAILED !!!');
    console.error('!!! IS YOUR MAMP/MYSQL SERVER RUNNING? !!!');
    console.error('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
    console.error('Error details:', err.stack);
    return;
  }

  // If connection is successful, log it and start the server.
  console.log('✅ Database connected successfully.');

  // Start the server
  app.listen(PORT, () => {
    console.log(`🚀 Backend server is running on http://localhost:${PORT}`);
  });
});