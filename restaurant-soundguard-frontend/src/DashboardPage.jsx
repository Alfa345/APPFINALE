// src/pages/DashboardPage.jsx

import React, { useState, useEffect } from 'react';
import './DashboardPage.css';

const DashboardPage = () => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Fetch data from our new backend endpoint
    fetch('http://localhost:3001/api/dashboard/latest')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(fetchedData => {
        setData(fetchedData);
        setLoading(false);
      })
      .catch(error => {
        console.error("Error fetching data:", error);
        setError("Could not load data. Is the backend server running?");
        setLoading(false);
      });
  }, []); // The empty array [] means this effect runs only once when the component mounts

  // Show a loading message
  if (loading) {
    return <div className="dashboard-status">Loading...</div>;
  }

  // Show an error message
  if (error) {
    return <div className="dashboard-status error">{error}</div>;
  }

  // Display the fetched data
  return (
    <div className="dashboard">
      <header className="dashboard-header">
        <h1>Dashboard Sonore</h1>
        <h2>{data.restaurant_nom}</h2>
      </header>

      <main className="dashboard-main">
        <div className="realtime-panel">
          <h3>Niveau Actuel</h3>
          <div className={`level-indicator ${data.status_color}`}>
            <span className="level-value">{parseFloat(data.niveau_db).toFixed(1)} dB</span>
            <span className="level-text">{data.status_text}</span>
          </div>
          <p className="timestamp">Dernière mesure: {new Date(data.timestamp_mesure).toLocaleTimeString()}</p>
        </div>

        <div className="chart-panel">
          <h3>Activité en Temps Réel</h3>
          <div className="chart-placeholder">
            <p>Real-time chart will be displayed here.</p>
          </div>
        </div>

        <div className="history-panel">
          <h3>Historique (24h)</h3>
          <div className="chart-placeholder">
            <p>24-hour history chart will be displayed here.</p>
          </div>
        </div>
      </main>
    </div>
  );
};

export default DashboardPage;