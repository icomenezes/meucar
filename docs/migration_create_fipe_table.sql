-- Tabela fipe: referência local substituindo as APIs externas
-- Populada mensalmente via importação de CSV
-- Não possui FK com outras tabelas (tabela de referência pura)

CREATE TABLE IF NOT EXISTS fipe (
  id            INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
  segmento      VARCHAR(20)      NOT NULL COMMENT 'carros | caminhoes | motos',
  marca_id      INT              NOT NULL,
  marca         VARCHAR(100)     NOT NULL,
  modelo_id     INT              NOT NULL,
  modelo        VARCHAR(200)     NOT NULL,
  codigo        VARCHAR(20)      NOT NULL COMMENT 'year code ex: 2020-1',
  ano_label     VARCHAR(50)      NOT NULL COMMENT 'ex: 1991 Gasolina (label do select)',
  ano_modelo    VARCHAR(10)      NOT NULL COMMENT 'somente o ano ex: 1991',
  cod_fipe      VARCHAR(20)      NOT NULL,
  combustivel_letra CHAR(1)      NULL,
  combustivel   VARCHAR(50)      NULL,
  preco         DECIMAL(12,2)    NULL,
  mes_referencia VARCHAR(30)     NULL,

  INDEX idx_fipe_marcas  (segmento, marca_id, marca),
  INDEX idx_fipe_modelos (segmento, marca_id, modelo_id, modelo),
  INDEX idx_fipe_anos    (marca_id, modelo_id, codigo),
  INDEX idx_fipe_precos  (marca_id, modelo_id, codigo, segmento),
  INDEX idx_fipe_codfipe (cod_fipe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
