-- Script de criação da base de dados FelixBus (versão correta)
-- Linguagens Web - 2024/2025

CREATE DATABASE IF NOT EXISTS felixbus;
USE felixbus;

-- -----------------------------
-- Perfis de Utilizador
-- -----------------------------
-- Perfis que podem ser atribuídos aos utilizadores: visitante, cliente, funcionario, administrador
CREATE TABLE perfil (
    idPerfil INT AUTO_INCREMENT PRIMARY KEY,
    designacao VARCHAR(30) NOT NULL
);

-- Inserção dos perfis obrigatórios na base de dados
INSERT INTO perfil (designacao) VALUES
('visitante'),
('cliente'),
('funcionario'),
('administrador');

-- -----------------------------
-- Utilizadores
-- -----------------------------
-- Tabela para armazenar dados dos utilizadores: username, password, nome, etc.
CREATE TABLE utilizador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    nome VARCHAR(80),
    email VARCHAR(80),
    morada VARCHAR(100),
    telemovel VARCHAR(15),
    idPerfil INT NOT NULL,
    FOREIGN KEY (idPerfil) REFERENCES perfil(idPerfil)
);

-- Inserção dos utilizadores obrigatórios para login (cliente/cliente, funcionario/funcionario, admin/admin)
INSERT INTO utilizador (username, password, idPerfil)
VALUES 
('cliente', 'cliente', 2),    -- Cliente
('funcionario', 'funcionario', 3), -- Funcionario
('admin', 'admin', 4);    -- Administrador

-- -----------------------------
-- Carteiras
-- -----------------------------
-- Cada utilizador tem uma carteira associada para a gestão de saldo
CREATE TABLE carteira (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idUtilizador INT NOT NULL,
    saldo DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (idUtilizador) REFERENCES utilizador(id)
);

-- Inserção de carteira inicial para todos os utilizadores obrigatórios (saldo inicial 0.00)
INSERT INTO carteira (idUtilizador, saldo)
SELECT id, 0.00 FROM utilizador;

-- Carteira da empresa FelixBus (única)
CREATE TABLE carteira_empresa (
    id INT PRIMARY KEY,
    saldo DECIMAL(10,2) DEFAULT 0.00
);

-- Inserção da carteira da empresa FelixBus
INSERT INTO carteira_empresa (id, saldo) VALUES (1, 0.00);

-- -----------------------------
-- Auditoria de Operações
-- -----------------------------
-- Tabela para registrar todas as operações realizadas nas carteiras (adicionar, levantar, compra)
CREATE TABLE auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operacao ENUM('adicionar', 'levantar', 'compra') NOT NULL,
    dataOperacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    valor DECIMAL(10,2),
    idOrigem INT,
    idDestino INT,
    descricao TEXT
);

-- -----------------------------
-- Rotas
-- -----------------------------
-- Definir as rotas de viagem, com origem, destino, data da viagem, hora, preço e capacidade
CREATE TABLE rota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origem VARCHAR(80) NOT NULL,
    destino VARCHAR(80) NOT NULL,
    dataViagem DATE NOT NULL,
    hora TIME NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    capacidade INT NOT NULL
);

-- -----------------------------
-- Bilhetes
-- -----------------------------
-- Relacionamento entre utilizadores e viagens (bilhetes comprados)
CREATE TABLE bilhete (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idUtilizador INT NOT NULL,
    idRota INT NOT NULL,
    dataCompra DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUtilizador) REFERENCES utilizador(id),
    FOREIGN KEY (idRota) REFERENCES rota(id)
);

-- -----------------------------
-- Alertas / Promoções / Informações
-- -----------------------------
-- Tabela para gerir alertas, promoções e informações que serão apresentadas no site
CREATE TABLE alerta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100),
    mensagem TEXT,
    dataPublicacao DATETIME DEFAULT CURRENT_TIMESTAMP
);
