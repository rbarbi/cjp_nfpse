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
IS 'Identifica��o do usu�rio que est� ingressando neste grupo.';

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_gu_id"
IS 'Identifica��o do grupo em que este usu�rio est� ingressando.';

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_nivel"
IS 'N�vel que este usu�rio assume ao acessar as transa��es associadas a este grupo.';

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_usu_resp_cad_id"
IS 'ID do usu�rio responsavel pela inclusao deste usu�rio neste grupo.';

COMMENT ON COLUMN "public"."tb_sys_usuario_grupo"."ug_dh_cadastro"
IS 'Data em que foi realizado o cadastro.';