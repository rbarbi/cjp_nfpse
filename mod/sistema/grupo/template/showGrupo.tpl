{include file="../../template/comum.tpl"}

{literal}
<script>
	function exibeFormCadPermissao() {
		window.alert('Ok');
	}
</script>
{/literal}


<h1>Visualizar grupo</h1>

<br>

ID: {$grupo->getID()} <br>
Nome: {$grupo->getNome()} <br>
Descricao: {$grupo->getDescricao()} <br>

{assign var="listaUsuarios" value=$grupo->getLsMembros()}
<div>
<ul id="userTabs" class="shadetabs">
<li><a href="#" rel="#default" >Grupos</a></li>
<li><a href="{$_rootScript}?m={$_adm_m}&u=grupo&a=SysGrupo&acao=showFormIncluirUsuarioGrupo&grupoID={$grupo->getID()}" rel="#iframe">Incluir usuario</a></li>
<li><a href="{$_rootScript}?m={$_adm_m}&u=grupo&a=SysGrupo&acao=doListarUsuariosGrupo&grupoID={$grupo->getID()}" rel="#iframe" class="selected">Listar Usuarios</a></li>
<li><a href="{$_rootScript}?m={$_adm_m}&u=grupo&a=SysPermissaoGrupo&acao=showFormIncluirUsuarioGrupo&grupoID={$grupo->getID()}" rel="#iframe">Incluir permissao</a></li>
<li><a href="{$_rootScript}?m={$_adm_m}&u=grupo&a=SysPermissaoGrupo&acao=doListarUsuariosGrupo&grupoID={$grupo->getID()}" rel="#iframe">Listar permissoes</a></li>
</ul>
<div id="userDivContainer" style="border:1px solid gray; width:650px; margin-bottom: 1em; padding: 10px; height: 350px">
<p>---</p>
<p>
{foreach from=$listaUsuarios item=usuario}
{$usuario->getNome()}<br>
{/foreach}
</p>
</div>


</div>


{literal}
<script type="text/javascript">

var users=new ddajaxtabs("userTabs", "userDivContainer")
users.setpersist(true)
users.setselectedClassTarget("link") //"link" or "linkparent"
users.init()


</script>
{/literal}
