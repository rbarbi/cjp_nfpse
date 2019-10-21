{include file="../../template/comum.tpl"}
<h1>Login</h1>
<form method="post" action="{$_rootScript}" name="frm">

{literal}
<script>
	function gravar() {
		var s;
		s = "username="+document.getElementById('username').value;
		s += "&senha="+document.getElementById('senha').value;
		doGoTo('{/literal}{$_adm_m}{literal}','usuario','SysUsuario','doLogin',s);
	}
</script>
{/literal}




Username: <input type="text" id="username" value=""/> <br>

Senha: <input type="password" id="senha" value=""/> <br>

<input type="button" value="Login" onclick="gravar()">

</form>
{$msg}

{if isset($exception)}
{$exception->getMessage()}
{/if}
