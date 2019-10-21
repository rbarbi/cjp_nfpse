CREATE TABLE "public"."tb_sys_grupo_usuarios" (
  "gu_id" BIGSERIAL,
  "gu_nome" VARCHAR(64),
  "gu_descricao" VARCHAR(250),
  "gu_usuario_admin_id" BIGINT,
  "gu_status_registro" CHAR(1) DEFAULT 'A'::bpchar NOT NULL,
  CONSTRAINT "pk_sys_grupo_usuarios" PRIMARY KEY("gu_id"),
  CONSTRAINT "fk_usuario_admin_grupo" FOREIGN KEY ("gu_usuario_admin_id")
    REFERENCES "public"."tb_sys_usuario"("usu_id")
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
    NOT DEFERRABLE
) WITH OIDS;
