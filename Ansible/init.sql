-- Configuración de la base de datos y tabla
CREATE DATABASE IF NOT EXISTS mydatabase;

USE mydatabase;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,  -- Aquí está 'usuario'
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mfa_code VARCHAR(10),  -- Agregada la columna para el código MFA
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



