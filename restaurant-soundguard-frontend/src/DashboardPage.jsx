// src/pages/DashboardPage.jsx

import React, { useState, useEffect } from 'react';
import './DashboardPage.css';
import { Line } from 'react-chartjs-2';
import {
  Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend,
} from 'chart.js';

// This now correctly imports 'logo.png'.
// Make sure you have RENAMED the file on your computer to 'logo.png'.
import logo from './assets/logo.png';

ChartJS.register(
  CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend
);

const DashboardPage = () => {
  const [latestData, setLatestData] = useState(null);
  const [historyData, setHistoryData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    Promise.all([
      fetch('http://localhost:3001/api/dashboard/latest'),
      fetch('http://localhost:3001/api/dashboard/history')
    ])
    .then(async ([latestRes, historyRes]) => {
      if (!latestRes.ok || !historyRes.ok) {
        throw new Error('Network response was not ok');
      }
      const latest = await latestRes.json();
      const history = await historyRes.json();
      return [latest, history];
    })
    .then(([latest, history]) => {
      setLatestData(latest);
      const chartData = {
        labels: history.map(d => new Date(d.timestamp_mesure).toLocaleTimeString()),
        datasets: [{
          label: 'Niveau Sonore (dB)',
          data: history.map(d => d.niveau_db),
          borderColor: '#e74c3c', 
          backgroundColor: 'rgba(231, 76, 60, 0.2)',
          fill: true,
          tension: 0.3,
        }],
      };
      setHistoryData(chartData);
      setLoading(false);
    })
    .catch(error => {
      console.error("Error fetching data:", error);
      setError("Could not load data. Is the backend server running?");
      setLoading(false);
    });
  }, []);

  if (loading) {
    return <div className="dashboard-status">Loading...</div>;
  }

  if (error) {
    return <div className="dashboard-status error">{error}</div>;
  }
  
  const chartOptions = {
    responsive: true,
    plugins: {
      legend: { display: false },
      title: { display: false },
    },
  };

  return (
    <div className="dashboard">
      <header className="dashboard-header">
        <img src={logo} alt="Gusteau's Restaurant Logo" className="header-logo" />
        <div className="header-text">
          <h1>SoundGuard Dashboard</h1>
          <h2>{latestData.restaurant_nom.replace('Le Bistrot Français', 'Gusteau\'s')}</h2>
        </div>
      </header>

      <main className="dashboard-main">
        <div className="realtime-panel">
          <h3>Niveau Actuel</h3>
          <div className={`level-indicator ${latestData.status_color}`}>
            <span className="level-value">{parseFloat(latestData.niveau_db).toFixed(1)} dB</span>
            <span className="level-text">{latestData.status_text}</span>
          </div>
          <p className="timestamp">Dernière mesure: {new Date(latestData.timestamp_mesure).toLocaleTimeString()}</p>
        </div>

        <div className="chart-panel">
          <h3>Activité en Temps Réel</h3>
          <div className="chart-placeholder">
            <p>Real-time chart will be displayed here.</p>
          </div>
        </div>

        <div className="history-panel">
          <h3>Historique (24h)</h3>
          {historyData ? (
             <Line options={chartOptions} data={historyData} />
          ) : (
            <p>Loading chart data...</p>
          )}
        </div>
      </main>
    </div>
  );
};

export default DashboardPage;