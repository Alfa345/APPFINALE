const mysql = require('mysql2');

const dbConfig = {
    host: 'localhost',
    user: 'root',        // Par défaut MAMP
    password: 'root',    // Par défaut MAMP  
    database: 'restaurant_soundguard',
    port: 3306       // Port par défaut MAMP
};

const connection = mysql.createConnection(dbConfig);

module.exports = connection;