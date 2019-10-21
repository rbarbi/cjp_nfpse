{include file="../../template/comum.tpl"}
<h1>Menu de administracao geral</h1> <hr>
{$_usuario->getUsername()}

<ul>
	<li><b>USUARIOS</b></li>
	<ul>
	<li> <a href="javascript:doGoTo('{$_adm_m}','usuario','SysUsuario','showFormCadUsuario')">Incluir usuario</a>
	<li> <a href="javascript:doGoTo('{$_adm_m}','usuario','SysUsuario','showFormListaUsuarios')">Listar usuarios</a>
	</ul>
	<li><b>TRANSACOES</b></li>
	<ul>
	<li> <a href="javascript:doGoTo('{$_adm_m}','transacao','SysTransacao','showFormCadTransacao')">Incluir transacao</a>
	<li> <a href="javascript:doGoTo('{$_adm_m}','transacao','SysTransacao','showFormListaTransacoes')">Listar transacoes</a>
	</ul>
	<li><b>GRUPOS</b></li>
	<ul>
	<li> <a href="javascript:doGoTo('{$_adm_m}','grupo','SysGrupo','showFormCadGrupo')">Incluir grupo</a>
	<li> <a href="javascript:doGoTo('{$_adm_m}','grupo','SysGrupo','showFormListaGrupos')">Listar grupos</a>
	</ul>
</ul>
