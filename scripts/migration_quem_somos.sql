-- Migration: 4 colunas para fotos "Quem Somos" no cadastro de empresa
-- Rodar no banco de producao do sistemameucar.
-- Revert no rodape (comentado).

ALTER TABLE empresas
  ADD COLUMN path_quem_somos_1 VARCHAR(255) NULL DEFAULT NULL AFTER path_frente_loja_2,
  ADD COLUMN path_quem_somos_2 VARCHAR(255) NULL DEFAULT NULL AFTER path_quem_somos_1,
  ADD COLUMN path_quem_somos_3 VARCHAR(255) NULL DEFAULT NULL AFTER path_quem_somos_2,
  ADD COLUMN path_quem_somos_4 VARCHAR(255) NULL DEFAULT NULL AFTER path_quem_somos_3;

-- ===== REVERT (se necessario) =====
-- ALTER TABLE empresas
--   DROP COLUMN path_quem_somos_1,
--   DROP COLUMN path_quem_somos_2,
--   DROP COLUMN path_quem_somos_3,
--   DROP COLUMN path_quem_somos_4;
