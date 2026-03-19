CREATE DATABASE IF NOT EXISTS academico_simples CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE academico_simples;

DROP TABLE IF EXISTS pauta_notas;
DROP TABLE IF EXISTS pautas;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS curso_ucs;
DROP TABLE IF EXISTS ucs;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS cursos;

CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_utilizador VARCHAR(30) NOT NULL UNIQUE,
    nome VARCHAR(150) NOT NULL,
    data_nascimento DATE NULL,
    foto VARCHAR(255) NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('aluno','funcionario','gestor') NOT NULL,
    curso_id INT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_curso FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE SET NULL
);

CREATE TABLE ucs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    ativo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE curso_ucs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    uc_id INT NOT NULL,
    ano INT DEFAULT 1,
    semestre INT DEFAULT 1,
    UNIQUE KEY unique_curso_uc_semestre (curso_id, uc_id, ano, semestre),
    CONSTRAINT fk_curso_ucs_curso FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
    CONSTRAINT fk_curso_ucs_uc FOREIGN KEY (uc_id) REFERENCES ucs(id) ON DELETE CASCADE
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    tipo_pedido ENUM('matricula','mudanca_curso','certificado') NOT NULL,
    descricao TEXT NULL,
    curso_origem_id INT NULL,
    curso_destino_id INT NULL,
    estado ENUM('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
    observacoes TEXT NULL,
    decidido_por INT NULL,
    data_pedido DATETIME NOT NULL,
    data_decisao DATETIME NULL,
    CONSTRAINT fk_pedido_aluno FOREIGN KEY (aluno_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_pedido_curso_origem FOREIGN KEY (curso_origem_id) REFERENCES cursos(id) ON DELETE SET NULL,
    CONSTRAINT fk_pedido_curso_destino FOREIGN KEY (curso_destino_id) REFERENCES cursos(id) ON DELETE SET NULL,
    CONSTRAINT fk_pedido_decisor FOREIGN KEY (decidido_por) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE pautas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    uc_id INT NOT NULL,
    ano_letivo VARCHAR(20) NOT NULL,
    epoca VARCHAR(50) NOT NULL,
    criado_por INT NOT NULL,
    data_criacao DATETIME NOT NULL,
    CONSTRAINT fk_pauta_curso FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
    CONSTRAINT fk_pauta_uc FOREIGN KEY (uc_id) REFERENCES ucs(id) ON DELETE CASCADE,
    CONSTRAINT fk_pauta_criador FOREIGN KEY (criado_por) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE pauta_notas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pauta_id INT NOT NULL,
    aluno_id INT NOT NULL,
    nota_final DECIMAL(4,1) NULL,
    CONSTRAINT fk_pauta_nota_pauta FOREIGN KEY (pauta_id) REFERENCES pautas(id) ON DELETE CASCADE,
    CONSTRAINT fk_pauta_nota_aluno FOREIGN KEY (aluno_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO cursos (nome, ativo) VALUES
('Engenharia Informática', 1),
('Gestão', 1),
('Design', 1);

INSERT INTO ucs (nome, codigo, ativo) VALUES
('Programação Web', 'PW01', 1),
('Bases de Dados', 'BD01', 1),
('Matemática', 'MAT01', 1);

INSERT INTO curso_ucs (curso_id, uc_id, ano, semestre) VALUES
(1, 1, 1, 2),
(1, 2, 1, 2),
(2, 3, 1, 1);

INSERT INTO users (numero_utilizador, nome, data_nascimento, foto, password, role, curso_id, ativo) VALUES
('gestor1', 'Gestor Principal', '1985-01-10', NULL, '$2y$12$MfXwROJNYOhsdJ74veCpdevkBvOlDWkh27jqdX3.5Sem0c4oJLeeq', 'gestor', NULL, 1),
('func1', 'Funcionário Académico', '1990-03-15', NULL, '$2y$12$mMXwV2E8FmSjMoX2Pu5yzuEw1gIOym2ku0uFqwjf0hdatfQWjHmbO', 'funcionario', NULL, 1);
