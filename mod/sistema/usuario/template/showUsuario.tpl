{include file="../../template/comum.tpl"}

{literal}
<script>
	function exibeFormCadPermissao() {
		window.alert('Ok');
	}
</script>
{/literal}


<h1>Visualizar usuario</h1>

<br>

ID: {$usuario->getID()} <br>
Nome: {$usuario->getNome()} <br>
Username: {$usuario->getUsername()} <br>




<div>
<ul id="userTabs" class="shadetabs">
<li><a href="#" rel="#default" >Grupos</a></li>
<li><a href="{$_rootScript}?m={$_adm_m}&u=permissao&a=SysPermissaoUsuario&acao=showFormCadPermissaoUsuario&usuID={$usuario->getID()}" rel="#iframe">Cadastrar Permissões</a></li>
<li><a href="{$_rootScript}?m={$_adm_m}&u=permissao&a=SysPermissaoUsuario&acao=showPermissoesUsuario&usuID={$usuario->getID()}" rel="#iframe" class="selected">Listar Permissões</a></li>
</ul>
<div id="userDivContainer" style="border:1px solid gray; width:550px; margin-bottom: 1em; padding: 10px; height: 300px">
<p>---</p>
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
