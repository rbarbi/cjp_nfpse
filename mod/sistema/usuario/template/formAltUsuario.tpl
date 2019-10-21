{include file="../../template/comum.tpl"}
<h1>Alteracao de usuario</h1>
<form method="post" action="{$_rootScript}" name="frm">

{literal}
<script>
	function gravar() {
		var s;
		s = "id="+document.getElementById('id').value;
		s += "&nome="+document.getElementById('nome').value;
		s += "&username="+document.getElementById('username').value;
		s += "&username="+document.getElementById('username').value;
		s += "&senha="+document.getElementById('senha').value;
		doGoTo('{/literal}{$_adm_m}{literal}','usuario','SysUsuario','doAltUsuario',s);
	}
</script>
{/literal}


<input type="hidden" name="m" value="{$_adm_m}"/>
<input type="hidden" name="u" value="usuario"/>
<input type="hidden" name="a" value="SysUsuario"/>
<input type="hidden" name="acao" value="doAltUsuario"/>

<input type="hidden" id="id" value="{$bo->getID()}"/>

Nome: <input type="text" id="nome" value="{$bo->getNome()}"/> <br>

Username: <input type="text" id="username" value="{$bo->getUsername()}"/> <br>

Senha: <input type="password" id="senha" value=""/> <br>

<input type="button" value="Gravar" onclick="gravar()">

</form>
{$msg}

{if isset($exception)}
{$exception->getMessage()}
{/if}
