DROP DATABASE IF EXISTS tech_storage_leve;
CREATE DATABASE tech_storage_leve CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE tech_storage_leve;

-- =========================
-- 1) Usuários (adicionado coluna papel)
-- =========================
CREATE TABLE usuarios (
  id_usuario     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome_completo  VARCHAR(120)    NOT NULL,
  email          VARCHAR(120)    NOT NULL,
  senha_hash     VARCHAR(255)    NOT NULL,
  salt           VARCHAR(255)    NOT NULL,
  papel          ENUM('ADMIN','GESTOR','FUNCIONARIO') NOT NULL DEFAULT 'FUNCIONARIO',
  status         ENUM('ATIVO','INATIVO') NOT NULL DEFAULT 'ATIVO',
  criado_em      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB;

-- =========================
-- 2) Armazéns e Locais (Localizações)
-- =========================
CREATE TABLE armazens (
  id_armazem   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome         VARCHAR(100)    NOT NULL,
  codigo       VARCHAR(30)     NOT NULL,
  ativo        TINYINT(1)      NOT NULL DEFAULT 1,
  criado_em    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_armazem),
  UNIQUE KEY uq_armazem_nome (nome),
  UNIQUE KEY uq_armazem_codigo (codigo)
) ENGINE=InnoDB;

CREATE TABLE locais_armazenagem (
  id_local         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_armazem       BIGINT UNSIGNED NOT NULL,
  codigo_local     VARCHAR(60)     NOT NULL,
  tipo             ENUM('PALETE','PRATELEIRA','CAIXA','PICKING','OUTRO') NOT NULL,
  ativo            TINYINT(1)      NOT NULL DEFAULT 1,
  PRIMARY KEY (id_local),
  UNIQUE KEY uq_local_por_armazem (id_armazem, codigo_local),
  KEY idx_locais_armazem (id_armazem),
  CONSTRAINT fk_local_armazem FOREIGN KEY (id_armazem)
    REFERENCES armazens(id_armazem) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================
-- 3) Catálogos mínimos
-- =========================
CREATE TABLE unidades_medida (
  id_unidade  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  sigla       VARCHAR(10)     NOT NULL,
  descricao   VARCHAR(80)     NOT NULL,
  PRIMARY KEY (id_unidade),
  UNIQUE KEY uq_unidade_sigla (sigla)
) ENGINE=InnoDB;

-- =========================
-- 4) Produtos
-- =========================
CREATE TABLE produtos (
  id_produto        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  sku               VARCHAR(60)  NOT NULL,
  nome              VARCHAR(160) NOT NULL,
  descricao         VARCHAR(255),
  id_unidade        BIGINT UNSIGNED NOT NULL,
  ativo             TINYINT(1)   NOT NULL DEFAULT 1,
  criado_em         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_produto),
  UNIQUE KEY uq_prod_sku (sku),
  KEY idx_prod_unidade (id_unidade),
  CONSTRAINT fk_prod_unidade FOREIGN KEY (id_unidade)
    REFERENCES unidades_medida(id_unidade)
) ENGINE=InnoDB;

-- =========================
-- 5) Estoques (saldo por Produto x Local)
-- =========================
CREATE TABLE estoques (
  id_produto        BIGINT UNSIGNED NOT NULL,
  id_local          BIGINT UNSIGNED NOT NULL,
  quantidade_atual  DECIMAL(18,3)   NOT NULL DEFAULT 0,
  PRIMARY KEY (id_produto, id_local),
  KEY idx_est_local (id_local),
  CONSTRAINT fk_est_prod   FOREIGN KEY (id_produto) REFERENCES produtos(id_produto) ON DELETE CASCADE,
  CONSTRAINT fk_est_local  FOREIGN KEY (id_local)   REFERENCES locais_armazenagem(id_local) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================
-- 6) Movimentações (histórico mínimo)
-- =========================
CREATE TABLE movimentacoes_estoque (
  id_mov            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo              ENUM('ENTRADA','SAIDA','TRANSFERENCIA','AJUSTE') NOT NULL,
  id_produto        BIGINT UNSIGNED NOT NULL,
  id_local_origem   BIGINT UNSIGNED NULL,
  id_local_destino  BIGINT UNSIGNED NULL,
  quantidade        DECIMAL(18,3)   NOT NULL,
  realizado_por     BIGINT UNSIGNED NOT NULL,
  realizado_em      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  observacao        VARCHAR(255),
  PRIMARY KEY (id_mov),
  KEY idx_mov_produto (id_produto),
  KEY idx_mov_origem (id_local_origem),
  KEY idx_mov_destino (id_local_destino),
  KEY idx_mov_usuario (realizado_por),
  CONSTRAINT fk_mov_produto  FOREIGN KEY (id_produto)       REFERENCES produtos(id_produto),
  CONSTRAINT fk_mov_origem   FOREIGN KEY (id_local_origem)  REFERENCES locais_armazenagem(id_local),
  CONSTRAINT fk_mov_destino  FOREIGN KEY (id_local_destino) REFERENCES locais_armazenagem(id_local),
  CONSTRAINT fk_mov_usuario  FOREIGN KEY (realizado_por)    REFERENCES usuarios(id_usuario),
  CHECK (quantidade > 0)
) ENGINE=InnoDB;

-- =========================
-- 7) Views úteis (opcional)
-- =========================
CREATE OR REPLACE VIEW vw_saldo_produto AS
SELECT
  p.id_produto,
  p.sku,
  p.nome,
  SUM(e.quantidade_atual) AS saldo_total
FROM produtos p
LEFT JOIN estoques e ON e.id_produto = p.id_produto
GROUP BY p.id_produto, p.sku, p.nome;

CREATE OR REPLACE VIEW vw_saldo_produto_armazem AS
SELECT
  a.id_armazem,
  a.nome AS armazem,
  p.id_produto,
  p.sku,
  p.nome AS produto,
  SUM(e.quantidade_atual) AS saldo_armazem
FROM estoques e
JOIN locais_armazenagem l ON l.id_local = e.id_local
JOIN armazens a ON a.id_armazem = l.id_armazem
JOIN produtos p ON p.id_produto = e.id_produto
GROUP BY a.id_armazem, a.nome, p.id_produto, p.sku, p.nome;

-- =========================
-- 8) Dados de exemplo rápidos
-- =========================
INSERT INTO usuarios (nome_completo, email, senha_hash, salt, papel) VALUES
('Admin', 'admin@techstorage.local', 'hash_demo', 'salt_demo', 'ADMIN');

INSERT INTO armazens (nome, codigo) VALUES
('Matriz', 'ARM-001');

INSERT INTO locais_armazenagem (id_armazem, codigo_local, tipo) VALUES
(1, 'RUA-A/PRAT-01/POS-01', 'PRATELEIRA'),
(1, 'RUA-A/PRAT-01/POS-02', 'PRATELEIRA');

INSERT INTO unidades_medida (sigla, descricao) VALUES
('UN','Unidade'),
('CX','Caixa');

INSERT INTO produtos (sku, nome, id_unidade) VALUES
('SKU-001','Mouse Óptico', 1),
('SKU-002','Teclado Mecânico', 1);

INSERT INTO estoques (id_produto, id_local, quantidade_atual) VALUES
(1, 1, 25.000),
(2, 2, 10.000);
