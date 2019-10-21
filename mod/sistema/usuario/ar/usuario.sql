CREATE TABLE "public"."tb_sys_usuario" (
  "usu_id" BIGSERIAL,
  "usu_nome" VARCHAR(80),
  "usu_username" VARCHAR(64) NOT NULL,
  "usu_senha" VARCHAR(64),
  "usu_nivel" INTEGER DEFAULT 50,
  "usu_status_registro" CHAR(1) DEFAULT 'A'::bpchar NOT NULL,
  CONSTRAINT "pk_sys_usuario" PRIMARY KEY("usu_id"),
  CONSTRAINT "tb_sys_unique_key" UNIQUE("usu_username")
) WITH OIDS;