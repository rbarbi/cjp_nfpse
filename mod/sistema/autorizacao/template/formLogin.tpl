{include file="../../template/comum.tpl"}
<h1>Login</h1>
<form method="post" action="{$_rootScript}" name="frm" id="frm">

{literal}
<script>
	function logar(formulario) {
		document.getElementById(formulario).submit();
//		_doGoTo(formulario);
	}
</script>
{/literal}

<input type="hidden" name="m" value="{$_adm_m}"/>
<input type="hidden" name="u" value="autorizacao"/>
<input type="hidden" name="a" value="SysAutorizacao"/>
<input type="hidden" name="acao" value="doLogin"/>

{'username'|traduz}: <input type="text" name="username" id="username"  value=""/> <br>

{'senha'|traduz}: <input type="password" name="senha" value=""/> <br>

<input type="button" value="Entrar" onclick="logar('frm')">

</form>
{$msg|traduz}

{if isset($exception)}
{$exception->getMessage()}
{/if}
<script>
document.getElementById('username').focus();
</script>
