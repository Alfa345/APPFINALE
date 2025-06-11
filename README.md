
# 📢 Restaurant SoundGuard

Restaurant SoundGuard est une application web de surveillance sonore conçue pour les restaurants. Elle permet de visualiser en temps réel le niveau de décibels d’un lieu et d’accéder à un historique des 24 dernières heures, afin de garantir un environnement confortable pour les clients.

## ✨ Fonctionnalités

### Tableau de bord en temps réel
- Affichage dynamique des données des capteurs sonores.
- Système d’alerte visuelle (vert/orange/rouge) basé sur des seuils prédéfinis.
- Graphique interactif de l’historique sonore des dernières 24h.

### Carte des restaurants (bientôt disponible)
- Localisation des établissements sur une carte interactive.
- Filtrage par zone géographique.
- Consultation des données en cliquant sur un établissement.

---

## 🛠️ Stack Technique
| Composant       | Technologie utilisée    |
|-----------------|-------------------------|
| Frontend        | React + Vite            |
| Visualisation   | Chart.js                |
| Backend         | Node.js + Express       |
| Base de données | MySQL                   |

---

## ⚙️ Prérequis
Avant de commencer, vous devez avoir installé :
- [**Node.js**](https://nodejs.org/en/) (version 16 ou supérieure)
- Un serveur local **MySQL** (via MAMP, WAMP ou XAMPP)
- **Git** pour la gestion de version
- Un éditeur de code comme **Visual Studio Code**

---

## 🚀 Installation & Lancement

Suivez ces étapes pour lancer le projet.

### 1. Cloner le Dépôt
```bash
git clone https://github.com/Alfa345/APPFINALE.git
cd APPFINALE
Markdown

2. Configurer la Base de Données (Étape Cruciale)

Cette étape est essentielle pour que le serveur backend puisse démarrer.
Démarrez MAMP et assurez-vous que le serveur MySQL est actif (lumière verte).
Allez sur phpMyAdmin dans votre navigateur (généralement http://localhost/phpMyAdmin).

Cliquez sur l'onglet Importer.

<!-- Optionnel: Ajoutez une capture d'écran ici -->

<!-- ![Étape 1: Onglet Importer](URL_DE_VOTRE_IMAGE_ICI) -->

Sélectionnez le fichier SQL en cliquant sur "Choisir un fichier" et en naviguant vers :
restaurant-soundguard-backend/config/database.sql
<!-- Optionnel: Ajoutez une capture d'écran ici -->

<!-- ![Étape 2: Sélection du fichier SQL](URL_DE_VOTRE_IMAGE_ICI) -->

Cliquez sur Go / Importer en bas de la page.
Cela créera la base restaurant_soundguard, toutes les tables et des données de test.

3. Installer les Dépendances

Les dépendances sont nécessaires pour le backend et le frontend.

Pour le Backend :

cd restaurant-soundguard-backend
npm install
Bash

Pour le Frontend :

# Depuis le dossier backend, retournez à la racine du projet
cd ..
cd restaurant-soundguard-frontend
npm install
Bash

4. Lancer les Serveurs

Utilisez deux terminaux distincts pour lancer le backend et le frontend simultanément.

Terminal 1 : Démarrer le Backend

cd restaurant-soundguard-backend
npm start
Bash

Vous devriez voir les messages de succès :

✅ Database connected successfully.
🚀 Backend server is running on http://localhost:3001

Laissez ce terminal ouvert.

Terminal 2 : Démarrer le Frontend

cd restaurant-soundguard-frontend
npm run dev
Bash

Le terminal affichera l'URL d'accès, par défaut http://localhost:5173.

5. Accéder à l'Application

Ouvrez votre navigateur à l'adresse suivante :
👉 http://localhost:5173

L’application est maintenant fonctionnelle ! 🎉

🤔 Dépannage (En cas de problème)

La page affiche Loading... indéfiniment :

Vérifiez que le serveur backend est bien lancé dans son terminal.
Assurez-vous que le backend a pu se connecter à la base de données (il doit afficher "✅ Database connected successfully.").

Le terminal du backend affiche DATABASE CONNECTION FAILED :
Assurez-vous que MAMP est bien lancé et que le serveur MySQL est allumé (lumière verte).

Le navigateur affiche ERR_CONNECTION_REFUSED sur le port 5173 :
C'est que votre serveur frontend n'est pas lancé. Allez dans le terminal du frontend et lancez la commande npm run dev.

🤝 Contribuer au Projet

Pour ajouter une nouvelle fonctionnalité, suivez ce workflow :
Créez une nouvelle branche depuis main : git checkout -b feature/nom-de-la-fonction
Faites vos modifications et vos commits.
Poussez votre branche sur GitHub : git push origin feature/nom-de-la-fonction
Ouvrez une Pull Request (Demande de Tirage) sur GitHub pour fusionner votre branche dans main.
---

### Le fichier `.gitignore`

Comme mentionné, ce contenu ne va pas dans le `README`. Créez un fichier nommé `.gitignore` à la racine de votre projet (`APPFINALE/.gitignore`) et mettez-y ce contenu :

Dépendances et builds
/node_modules
/dist
/build
npm-debug.log*
yarn-debug.log*
yarn-error.log*
Fichiers d'environnement
.env
.env.local
.env.development.local
.env.test.local
.env.production.local
Fichiers système
.DS_Store
Thumbs.db