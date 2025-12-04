DROP DATABASE IF EXISTS pagtrem;

CREATE DATABASE IF NOT EXISTS pagtrem
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE pagtrem;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  phone VARCHAR(30),
  department VARCHAR(120),
  job_title VARCHAR(120),
  avatar VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_users_email UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  city VARCHAR(120),
  state CHAR(2),
  cep VARCHAR(9),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS routes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  origin VARCHAR(100),
  destination VARCHAR(100),

  extra_info TEXT,
  status ENUM('ativa','manutencao') NOT NULL DEFAULT 'ativa',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS route_stations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  route_id INT NOT NULL,
  station_id INT NOT NULL,
  stop_order INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rs_route FOREIGN KEY (route_id)
    REFERENCES routes(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_rs_station FOREIGN KEY (station_id)
    REFERENCES stations(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT uq_route_stop UNIQUE (route_id, stop_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cameras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  location VARCHAR(200),
  status ENUM('online','offline') DEFAULT 'online',
  train_code VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  tag ENUM('Manutenção','Novidades','Sistema') DEFAULT 'Sistema',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chat_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_chat_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM users WHERE email IN ('admin@pagtrem.com','usuario@pagtrem.com');
INSERT INTO users (name, email, password, role, avatar)
VALUES
('Administrador', 'admin@pagtrem.com',
  '$2y$10$jXMvLkH8bsM1E7e8qcWjj.KVsSXNFxyCG3raoycw2O66OvtlCz9jO', 'admin', NULL),
('Usuário de Teste', 'usuario@pagtrem.com',
  '$2y$10$jXMvLkH8bsM1E7e8qcWjj.KVsSXNFxyCG3raoycw2O66OvtlCz9jO', 'user', NULL);

INSERT IGNORE INTO stations (name, city, state, cep) VALUES
('Estação Central - Plataforma 1','São Paulo','SP','01001-000'),
('Estação Central - Plataforma 2','São Paulo','SP','01001-000'),
('Estação Norte - Entrada','São Paulo','SP','02000-000'),
('Estação Sul - Plataforma 1','São Paulo','SP','04000-000'),
('Túnel KM 45','Campinas','SP','13010-000'),
('Ponte Rio Grande','Sorocaba','SP','18010-000');


INSERT IGNORE INTO routes (name, origin, destination, extra_info, status) VALUES
('São Paulo → Rio de Janeiro', 'São Paulo', 'Rio de Janeiro', 'Wi-Fi disponível', 'ativa'),
('Campinas → Santos', 'Campinas', 'Santos', NULL, 'ativa'),
('Belo Horizonte → São Paulo', 'Belo Horizonte', 'São Paulo', 'Manutenção na via', 'manutencao'),
('Curitiba → Florianópolis', 'Curitiba', 'Florianópolis', 'Vista panorâmica', 'ativa');


INSERT IGNORE INTO route_stations (route_id, station_id, stop_order) VALUES
(1,1,1),(1,3,2),(1,2,3),
(2,5,1),(2,6,2),
(3,4,1),(3,1,2),
(4,3,1),(4,6,2);

INSERT IGNORE INTO cameras (name, location, status, train_code) VALUES
('Câmera #1','Estação Central - Plataforma 1','online','1234'),
('Câmera #2','Estação Central - Plataforma 2','online','5678'),
('Câmera #3','Estação Norte - Entrada','online',NULL),
('Câmera #4','Estação Sul - Plataforma 1','offline',NULL),
('Câmera #5','Túnel KM 45','online','9012'),
('Câmera #6','Ponte Rio Grande','online','3456');

INSERT IGNORE INTO notices (title, body, tag) VALUES
('Manutenção Programada',
 'Linha São Paulo-Rio será interditada dia 15/01 das 02h às 06h para manutenção preventiva.',
 'Manutenção'),
('Nova Rota Disponível',
 'A partir de amanhã, nova rota Campinas-Sorocaba estará operando das 06h às 22h.',
 'Novidades'),
('Atualização de Sistema',
 'Sistema de câmeras atualizado com novos recursos de detecção automática.',
 'Sistema');

INSERT IGNORE INTO chat_messages (user_id, message) VALUES
( (SELECT id FROM users WHERE email='usuario@pagtrem.com' LIMIT 1), 'Olá, gostaria de informações sobre a rota SP-RJ.' ),
( (SELECT id FROM users WHERE email='admin@pagtrem.com' LIMIT 1), 'Mensagem de boas-vindas do administrador.' );

DROP DATABASE IF EXISTS pagtrem;
CREATE DATABASE pagtrem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pagtrem;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  phone VARCHAR(30),
  department VARCHAR(120),
  job_title VARCHAR(120),
  avatar VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE stations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  city VARCHAR(120),
  state CHAR(2),
  cep VARCHAR(9),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE routes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  origin VARCHAR(100),
  destination VARCHAR(100),

  extra_info TEXT,
  status ENUM('ativa','manutencao') DEFAULT 'ativa',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE route_stations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  route_id INT NOT NULL,
  station_id INT NOT NULL,
  stop_order INT NOT NULL,
  FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE,
  FOREIGN KEY (station_id) REFERENCES stations(id) ON DELETE RESTRICT
);

CREATE TABLE cameras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  location VARCHAR(200),
  status ENUM('online','offline') DEFAULT 'online',
  train_code VARCHAR(50)
);

CREATE TABLE notices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  tag ENUM('Manutenção','Novidades','Sistema') DEFAULT 'Sistema',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE chat_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


INSERT INTO users (name,email,password,role) VALUES
('Administrador','admin@pagtrem.com','$2y$10$jXMvLkH8bsM1E7e8qcWjj.KVsSXNFxyCG3raoycw2O66OvtlCz9jO','admin'),
('Usuário de Teste','usuario@pagtrem.com','$2y$10$jXMvLkH8bsM1E7e8qcWjj.KVsSXNFxyCG3raoycw2O66OvtlCz9jO','user');


CREATE TABLE employees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  role VARCHAR(120) NOT NULL,
  phone VARCHAR(30),
  cep VARCHAR(12),
  street VARCHAR(200),
  neighborhood VARCHAR(150),
  city VARCHAR(120),
  uf VARCHAR(4),
  photo VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO employees (name, role, phone, cep, street, neighborhood, city, uf, photo) VALUES
('Carlos Silva', 'Maquinista', '(11) 98765-4321', '01001-000', 'Rua das Flores, 123', 'Centro', 'São Paulo', 'SP', NULL),
('Ana Souza', 'Agente de Estação', '(11) 91234-5678', '02002-000', 'Av. Paulista, 1000', 'Bela Vista', 'São Paulo', 'SP', NULL),
('Roberto Oliveira', 'Segurança', '(21) 99876-5432', '20040-002', 'Rua do Ouvidor, 50', 'Centro', 'Rio de Janeiro', 'RJ', NULL),
('Fernanda Lima', 'Atendente', '(31) 98765-1234', '30130-000', 'Av. Afonso Pena, 500', 'Centro', 'Belo Horizonte', 'MG', NULL),
('João Pereira', 'Manutenção', '(41) 91234-8765', '80020-000', 'Rua XV de Novembro, 200', 'Centro', 'Curitiba', 'PR', NULL);

INSERT INTO routes (name, origin, destination, extra_info, status) VALUES
('Expresso Leste', 'São Paulo (Luz)', 'Guaianases', 'Alta demanda', 'ativa'),
('Linha Turística da Serra', 'Campos do Jordão', 'Santo Antônio do Pinhal', 'Guia turístico incluso', 'manutencao'),
('Intercidades Campinas-SP', 'Campinas', 'São Paulo (Barra Funda)', 'Serviço expresso', 'ativa'),
('Conexão Aeroporto', 'São Paulo (Luz)', 'Aeroporto Guarulhos', 'Saídas a cada 15min', 'ativa');

INSERT INTO notices (title, body, tag) VALUES
('Atrasos na Linha 1', 'Devido a chuvas fortes, a Linha 1 opera com velocidade reduzida.', 'Sistema'),
('Novo Horário de Atendimento', 'A bilheteria da Estação Central agora funciona até as 23h.', 'Novidades'),
('Manutenção de Escadas Rolantes', 'As escadas rolantes da Estação Norte passarão por manutenção neste fim de semana.', 'Manutenção');
