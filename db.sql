
``sql
-- Banco de dados e tabelas básicas do PAGTREM
CREATE DATABASE IF NOT EXISTS PAGTREM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE PAGTREM;

-- tabela usuario
CREATE TABLE IF NOT EXISTS usuario (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nome_completo VARCHAR(120) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  telefone VARCHAR(20) DEFAULT NULL,
  cep VARCHAR(10) DEFAULT NULL,
  cpf VARCHAR(20) DEFAULT NULL,
  senha VARCHAR(255) NOT NULL,
  tipo_usuario TINYINT NOT NULL DEFAULT 1, -- 1 = usuario, 2 = admin
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- tabela trem
CREATE TABLE IF NOT EXISTS trem (
  id_trem INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  horario TIME NOT NULL,
  parada VARCHAR(120) DEFAULT NULL,
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- tabela sensor (exemplo)
CREATE TABLE IF NOT EXISTS sensor (
  id_sensor INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  status VARCHAR(20) DEFAULT 'ATIVO',
  localizacao VARCHAR(120) DEFAULT NULL,
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- usuario inicial (senha: 1234) - gerada com password_hash PHP (bcrypt)
-- hash da senha 1234 gerada por PHP: password_hash('1234', PASSWORD_DEFAULT)
-- substitua a hash abaixo se preferir gerar a nova
INSERT INTO usuario (nome_completo,email,telefone,senha,tipo_usuario) VALUES
('Math','math@example.com','11999999999','$2y$10$SOMEPLACEHOLDERHASH',2);

-- nota: caso queira, após importar, execute um script php para resetar a senha corretamente.
