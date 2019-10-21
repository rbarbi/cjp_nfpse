CREATE TABLE "public"."tb_sys_usuario_grupo" (
  "ug_usu_id" BIGINT NOT NULL, 
  "ug_gu_id" BIGINT NOT NULL, 
  "ug_nivel" INTEGER, 
  "ug_usu_resp_cad_id" BIGINT, 
  "ug_dh_cadastro" TIMESTAMP WITHOUT TIME ZONE DEFAULT now(), 
  CONSTRAINT "tb_sys_usuario_grupo_pkey" PRIMARY KEY("ug_usu_id", "ug_gu_id"), 
  CONSTRAINT "tb_sys_usuario_grupo_ug_gu_id_key" UNIQUE("ug_gu_id")
) WITH OIDS;

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_usu_id"
IS 'Identificação do usuário que está ingressando neste grupo.';

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_gu_id"
IS 'Identificação do grupo em que este usuário está ingressando.';

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_nivel"
IS 'Nível que este usuário assume ao acessar as transações associadas a este grupo.';

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_usu_resp_cad_id"
IS 'ID do usuário responsavel pela inclusao deste usuário neste grupo.';

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_dh_cadastro"
IS 'Data em que foi realizado o cadastro.';