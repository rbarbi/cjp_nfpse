-- SQL Manager 2007 for PostgreSQL 4.3.0.4
-- ---------------------------------------
-- Host      : producao
-- Database  : gama3
-- Version   : PostgreSQL 8.1.3 on i686-pc-mingw32, compiled by GCC gcc.exe (GCC) 3.4.2 (mingw-special)


SET check_function_bodies = false;
--
-- Structure for table tb_sys_transacao (OID = 1497643) : 
--
SET search_path = public, pg_catalog;
CREATE TABLE public.tb_sys_transacao (
    tr_id bigint DEFAULT nextval(('"tb_transacao_tr_id_seq"'::text)::regclass) NOT NULL,
    tr_nome character varying(60),
    tr_descricao character varying(250),
    tr_m character varying(64),
    tr_u character varying(64),
    tr_a character varying(128),
    tr_acao character varying(64),
    tr_nivel_min_usuario integer,
    tr_permissao_default character(1),
    tr_status character(1) DEFAULT 'A'::bpchar NOT NULL,
    tr_tr_agregadora bigint
);
--
-- Structure for table tb_sys_usuario (OID = 1505501) : 
--
CREATE TABLE public.tb_sys_usuario (
    usu_id bigserial NOT NULL,
    usu_nome character varying(80),
    usu_username character varying(64) NOT NULL,
    usu_senha character varying(64),
    usu_nivel integer DEFAULT 50,
    usu_status_registro character(1) DEFAULT 'A'::bpchar NOT NULL
);
--
-- Structure for table tb_sys_grupo_usuarios (OID = 1505567) : 
--
CREATE TABLE public.tb_sys_grupo_usuarios (
    gu_id bigserial NOT NULL,
    gu_nome character varying(64),
    gu_descricao character varying(250),
    gu_usuario_admin_id bigint,
    gu_status_registro character(1) DEFAULT 'A'::bpchar NOT NULL
);
--
-- Structure for table tb_sys_permissao_usuario (OID = 1505667) : 
--
CREATE TABLE public.tb_sys_permissao_usuario (
    pu_id bigint NOT NULL,
    pu_usu_id bigint NOT NULL
);
--
-- Structure for table tb_sys_permissao (OID = 1505701) : 
--
CREATE TABLE public.tb_sys_permissao (
    pe_id bigserial NOT NULL,
    pe_tr_id bigint,
    pe_permissao character(1) DEFAULT '='::bpchar,
    pe_status character(1) DEFAULT 'A'::bpchar,
    pe_tipo character(1) NOT NULL
);
--
-- Structure for table tb_sys_usuario_grupo (OID = 1537663) : 
--
CREATE TABLE public.tb_sys_usuario_grupo (
    ug_usu_id bigint NOT NULL,
    ug_gu_id bigint NOT NULL,
    ug_nivel integer,
    ug_usu_resp_cad_id bigint,
    ug_dh_cadastro timestamp without time zone DEFAULT now()
);
--
-- Structure for table tb_sys_permissao_grupo (OID = 1537698) : 
--
CREATE TABLE public.tb_sys_permissao_grupo (
    pg_id bigint NOT NULL,
    pg_gu_id bigint NOT NULL
);


--
-- Structure for table tb_sys_registro_auditoria : 
--
CREATE TABLE "public"."tb_sys_registro_auditoria" (
  "ra_id" SERIAL, 
  "ra_usu_id" BIGINT, 
  "ra_obj_id" BIGINT, 
  "ra_dh_evento" TIMESTAMP WITHOUT TIME ZONE DEFAULT now(), 
  "ra_ip" INET, 
  "ra_username" VARCHAR(64), 
  "ra_nome_classe" VARCHAR(128), 
  "ra_acao" VARCHAR(32), 
  "ra_observacoes" VARCHAR, 
  "ra_hash" VARCHAR(64), 
  CONSTRAINT "tb_sys_registro_auditoria_pkey" PRIMARY KEY("ra_id")
) WITH OIDS;

COMMENT ON TABLE "public"."tb_sys_registro_auditoria"
IS 'Tabela de auditoria do sistema';

COMMENT ON COLUMN "public"."tb_sys_registro_auditoria"."ra_hash"
IS 'Usado para validar o conteúdo do registro de auditoria.';


--
-- Data for table public.tb_sys_transacao (OID = 1497643) (LIMIT 0,45)
--
INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (22, 'Cadastrar usuario.', 'Exibe formulario de cadastro de usuario.', 'sistema', 'usuario', 'SysUsuario', 'showFormCadUsuario', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (24, 'Listar usuarios.', 'Exibe o formulario de listagem de usuarios.', 'sistema', 'usuario', 'SysUsuario', 'showFormListaUsuarios', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (26, 'Alterar usuario.', 'Exibe o formulario de alteracao de um usuario selecionado.', 'sistema', 'usuario', 'SysUsuario', 'showFormAltUsuario', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (28, 'Excluir usuario.', 'Exclui ou desativa o usuario.', 'sistema', 'usuario', 'SysUsuario', 'doDelUsuario', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (29, 'Exibe usuario.', 'Exibe os dados do usuario selecionado.', 'sistema', 'usuario', 'SysUsuario', 'showUsuario', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (30, 'Login', 'Exibe o formulario de login.', 'sistema', 'autorizacao', 'SysAutorizacao', 'showFormLogin', 99999, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (32, 'Cadastra transacao.', 'Exibe formulario de cadastro de transacao', 'sistema', 'transacao', 'SysTransacao', 'showFormCadTransacao', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (36, 'Listar transacoes.', 'Exibe o formulario de listagem das transacoes cadastradas no sistema.', 'sistema', 'transacao', 'SysTransacao', 'showFormListaTransacoes', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (39, 'Exibe transacao.', 'Exibe os dados de uma transacao selecionada.', 'sistema', 'transacao', 'SysTransacao', 'showTransacao', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (43, 'Pagina principal.', 'Exibe a pagina principal do sistema.', 'sistema', NULL, 'SysBase', 'showIndexPrincipal', 99999, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (23, NULL, 'Realiza o cadastro do usuario.', 'sistema', 'usuario', 'SysUsuario', 'doCadUsuario', 99, 'N', 'A', 22);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (25, NULL, 'Recupera e exibe a lista de usuarios.', 'sistema', 'usuario', 'SysUsuario', 'doListarUsuarios', 99, 'S', 'A', 24);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (27, NULL, 'Atualiza os dados do usuario selecionado.', 'sistema', 'usuario', 'SysUsuario', 'doAltUsuario', 99, 'N', 'A', 26);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (31, 'Autentica', 'Realiza a autenticacao do usuario.', 'sistema', 'autorizacao', 'SysAutorizacao', 'doLogin', 99999, 'S', 'A', 30);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (33, '', 'Realiza o cadastro da transacao.', 'sistema', 'transacao', 'SysTransacao', 'doCadTransacao', 99, 'N', 'A', 32);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (38, 'Excluir transacao.', 'Realiza a exclusao ou desativacao de uma transacao selecionada.', 'sistema', 'transacao', 'SysTransacao', 'doDelTransacao', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (44, NULL, NULL, 'sistema', 'usuario', 'SysUsuario', 'showIndex', 99, 'S', 'A', 43);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (35, '', 'Realiza a alteracao dos dados da transacao selecionada.', 'sistema', 'transacao', 'SysTransacao', 'doAltTransacao', 99, 'D', 'A', 34);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (34, 'Altera transacao.', 'Exibe o formulario de alteracao de uma transacao selecionada.', 'sistema', 'transacao', 'SysTransacao', 'showFormAltTransacao', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (37, NULL, 'Recupera e exibe a lista de transacoes.', 'sistema', 'transacao', 'SysTransacao', 'doListarTransacoes', 99, 'N', 'A', 36);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (46, 'Home exportação', 'Página inicial da exportação', 'exportacao', '', 'Acesso', 'showIndex', 9, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (47, 'Index do grupo', 'Página inicial do gerenciamento de grupos de usuários', 'sistema', 'grupo', 'SysGrupo', 'showIndex', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (48, 'Exibe formulário de cadastro de grupo', 'Exibe formulário de cadastro de grupos de usuários.', 'sistema', 'grupo', 'SysGrupo', 'showFormCadGrupo', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (49, '', 'Realiza o cadastro do grupo de usuários.', 'sistema', 'grupo', 'SysGrupo', 'doCadGrupo', 99, 'S', 'A', 48);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (50, 'Exibir a página de listagem de grupos de usuários', 'Exibe o formulário para consulta de grupos de usuários.', 'sistema', 'grupo', 'SysGrupo', 'showFormListaGrupos', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (51, NULL, 'Realiza a pesquisa dos grupos de usuários', 'sistema', 'grupo', 'SysGrupo', 'doListarGrupos', 99, 'S', 'A', 50);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (52, 'Exibir Grupo', 'Exibe os dados de um dado Grupo de usuários', 'sistema', 'grupo', 'SysGrupo', 'showGrupo', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (53, 'Incluir usuário em um grupo', 'Exibe o formulário para inclusão de um usuário em um grupo.', 'sistema', 'grupo', 'SysGrupo', 'showFormIncluirUsuarioGrupo', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (54, '', 'Efetiva a inclusão do usuário selecionado no grupo especificado.', 'sistema', 'grupo', 'SysGrupo', 'doIncluirUsuarioGrupo', 99, 'S', 'A', 53);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (55, 'Exibir o formulário para listagem dos usuários de um grupo', 'Exibe o formulário de  listagem de usuários de um grupo', 'sistema', 'grupo', 'SysGrupo', 'showFormListaUsuariosGrupo', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (56, NULL, 'Recupera a lista de usuários de um grupo.', 'sistema', 'grupo', 'SysGrupo', 'doListarUsuariosGrupo', 99, 'S', 'A', 55);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (57, 'Remover usuário de um grupo', 'Remove o usuário de um dado grupo.', 'sistema', 'grupo', 'SysGrupo', 'doDelUsuarioGrupo', 99, 'S', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (40, 'Cadastra permissao de usuario.', 'Exibe formulario de cadastro de permissao de usuario', 'sistema', 'permissao', 'SysPermissaoUsuario', 'showFormCadPermissaoUsuario', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (42, 'Lista as permissoes de usuario.', 'Exibe o formulario de permissoes do usuario.', 'sistema', 'permissao', 'SysPermissaoUsuario', 'showPermissoesUsuario', 99, 'N', 'A', NULL);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (41, NULL, 'Realiza o cadastro da permissao de acesso a uma transacao para um dado usuario.', 'sistema', 'permissao', 'SysPermissaoUsuario', 'doCadPermissaoUsuario', 99, 'N', 'A', 40);

INSERT INTO tb_sys_transacao (tr_id, tr_nome, tr_descricao, tr_m, tr_u, tr_a, tr_acao, tr_nivel_min_usuario, tr_permissao_default, tr_status, tr_tr_agregadora)
VALUES (45, 'Excluir permissao', 'Exclui uma permissão.', 'sistema', 'permissao', 'SysPermissaoUsuario', 'doDelPermissaoUsuario', 99, 'S', 'A', NULL);

--
-- Definition for index pk_transacao (OID = 1497662) : 
--
ALTER TABLE ONLY tb_sys_transacao
    ADD CONSTRAINT pk_transacao PRIMARY KEY (tr_id);
--
-- Definition for index pk_sys_usuario (OID = 1505506) : 
--
ALTER TABLE ONLY tb_sys_usuario
    ADD CONSTRAINT pk_sys_usuario PRIMARY KEY (usu_id);
--
-- Definition for index tb_sys_unique_key (OID = 1505508) : 
--
ALTER TABLE ONLY tb_sys_usuario
    ADD CONSTRAINT tb_sys_unique_key UNIQUE (usu_username);
--
-- Definition for index pk_sys_grupo_usuarios (OID = 1505571) : 
--
ALTER TABLE ONLY tb_sys_grupo_usuarios
    ADD CONSTRAINT pk_sys_grupo_usuarios PRIMARY KEY (gu_id);
--
-- Definition for index fk_usuario_admin_grupo (OID = 1505573) : 
--
ALTER TABLE ONLY tb_sys_grupo_usuarios
    ADD CONSTRAINT fk_usuario_admin_grupo FOREIGN KEY (gu_usuario_admin_id) REFERENCES tb_sys_usuario(usu_id);
--
-- Definition for index fk_usu_pu (OID = 1505676) : 
--
ALTER TABLE ONLY tb_sys_permissao_usuario
    ADD CONSTRAINT fk_usu_pu FOREIGN KEY (pu_usu_id) REFERENCES tb_sys_usuario(usu_id);
--
-- Definition for index tb_sys_permissao_pkey (OID = 1505706) : 
--
ALTER TABLE ONLY tb_sys_permissao
    ADD CONSTRAINT tb_sys_permissao_pkey PRIMARY KEY (pe_id);
--
-- Definition for index fk_tr_pu (OID = 1505708) : 
--
ALTER TABLE ONLY tb_sys_permissao
    ADD CONSTRAINT fk_tr_pu FOREIGN KEY (pe_tr_id) REFERENCES tb_sys_transacao(tr_id);
--
-- Definition for index tb_sys_permissao_usuario_fk (OID = 1505713) : 
--
ALTER TABLE ONLY tb_sys_permissao_usuario
    ADD CONSTRAINT tb_sys_permissao_usuario_fk FOREIGN KEY (pu_id) REFERENCES tb_sys_permissao(pe_id);
--
-- Definition for index tb_sys_usuario_grupo_pkey (OID = 1537666) : 
--
ALTER TABLE ONLY tb_sys_usuario_grupo
    ADD CONSTRAINT tb_sys_usuario_grupo_pkey PRIMARY KEY (ug_usu_id, ug_gu_id);
--
-- Definition for index tb_sys_permissao_grupo_fk (OID = 1537700) : 
--
ALTER TABLE ONLY tb_sys_permissao_grupo
    ADD CONSTRAINT tb_sys_permissao_grupo_fk FOREIGN KEY (pg_id) REFERENCES tb_sys_permissao(pe_id);
--
-- Definition for index tb_sys_permissao_grupo_fk1 (OID = 1537705) : 
--
ALTER TABLE ONLY tb_sys_permissao_grupo
    ADD CONSTRAINT tb_sys_permissao_grupo_fk1 FOREIGN KEY (pg_gu_id) REFERENCES tb_sys_grupo_usuarios(gu_id);
--
-- Definition for index tb_sys_usuario_grupo_fk (OID = 1537716) : 
--
ALTER TABLE ONLY tb_sys_usuario_grupo
    ADD CONSTRAINT tb_sys_usuario_grupo_fk FOREIGN KEY (ug_usu_id) REFERENCES tb_sys_usuario(usu_id);
--
-- Definition for index tb_sys_usuario_grupo_fk1 (OID = 1537721) : 
--
ALTER TABLE ONLY tb_sys_usuario_grupo
    ADD CONSTRAINT tb_sys_usuario_grupo_fk1 FOREIGN KEY (ug_gu_id) REFERENCES tb_sys_grupo_usuarios(gu_id);
--
-- Definition for index tb_sys_permissao_usuario_pkey (OID = 1537726) : 
--
ALTER TABLE ONLY tb_sys_permissao_usuario
    ADD CONSTRAINT tb_sys_permissao_usuario_pkey PRIMARY KEY (pu_id);
--
-- Definition for index tb_sys_permissao_grupo_pkey (OID = 1537728) : 
--
ALTER TABLE ONLY tb_sys_permissao_grupo
    ADD CONSTRAINT tb_sys_permissao_grupo_pkey PRIMARY KEY (pg_id);
--
-- Comments
--
COMMENT ON SCHEMA public IS 'Standard public schema';
COMMENT ON COLUMN public.tb_sys_usuario_grupo.ug_usu_id IS 'Identificação do usuário que está ingressando neste grupo.';
COMMENT ON COLUMN public.tb_sys_usuario_grupo.ug_gu_id IS 'Identificação do grupo em que este usuário está ingressando.';
COMMENT ON COLUMN public.tb_sys_usuario_grupo.ug_nivel IS 'Nível que este usuário assume ao acessar as transações associadas a este grupo.';
COMMENT ON COLUMN public.tb_sys_usuario_grupo.ug_usu_resp_cad_id IS 'ID do usuário responsavel pela inclusao deste usuário neste grupo.';
COMMENT ON COLUMN public.tb_sys_usuario_grupo.ug_dh_cadastro IS 'Data em que foi realizado o cadastro.';
