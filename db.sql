-- ==============================================
-- PAGTREM - Database Schema
-- Execute este arquivo no MySQL/phpMyAdmin
-- ==============================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS PAGTREM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE PAGTREM;

-- ==============================================
-- Tabela: usuarios
-- ==============================================
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    nome_completo VARCHAR(120) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20) DEFAULT NULL,
    cep VARCHAR(10) DEFAULT NULL,
    cpf VARCHAR(20) DEFAULT NULL,
    senha VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- ==============================================
-- Tabela: trens
-- ==============================================
DROP TABLE IF EXISTS trens;
CREATE TABLE trens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    status ENUM('ativo', 'inativo', 'manutencao') NOT NULL DEFAULT 'ativo',
    capacidade INT NOT NULL DEFAULT 0,
    notas TEXT DEFAULT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ==============================================
-- Tabela: notificacoes
-- ==============================================
DROP TABLE IF EXISTS notificacoes;
CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    mensagem TEXT NOT NULL,
    data_notificacao DATE NOT NULL,
    status ENUM('ativa', 'inativa') NOT NULL DEFAULT 'ativa',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_data (data_notificacao)
) ENGINE=InnoDB;

-- ==============================================
-- SEED: Usuario Admin
-- Username: admin
-- Senha: lucas123
-- ==============================================
INSERT INTO usuarios (username, nome_completo, email, senha, role, status) VALUES
('admin', 'Administrador do Sistema', 'admin@pagtrem.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ativo');

-- ==============================================
-- SEED: Dados de exemplo - Trens
-- ==============================================
INSERT INTO trens (nome, status, capacidade, notas) VALUES
('Trem Expresso 001', 'ativo', 200, 'Linha principal - horário comercial'),
('Trem Regional 002', 'ativo', 150, 'Linha secundária'),
('Trem Manutenção 003', 'manutencao', 100, 'Em revisão programada');

-- ==============================================
-- SEED: Dados de exemplo - Notificações
-- ==============================================
INSERT INTO notificacoes (titulo, mensagem, data_notificacao, status) VALUES
('Bem-vindo ao PAGTREM', 'Sistema de gerenciamento de trens inicializado com sucesso.', CURDATE(), 'ativa'),
('Manutenção Programada', 'Manutenção no Trem 003 agendada para esta semana.', CURDATE(), 'ativa');
