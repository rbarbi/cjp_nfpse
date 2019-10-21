{include file="../../template/comum.tpl"}
<h1>Cadastro de usuarios</h1>


{literal}
<script>
	function gravar(formulario) {
		doGoTo_(formulario);
	}
</script>
{/literal}

<form method="post" action="{$_rootScript}" name="frm" id="frm">



<input type="hidden" name="m" value="{$_adm_m}"/>
<input type="hidden" name="u" value="usuario"/>
<input type="hidden" name="a" value="SysUsuario"/>
<input type="hidden" name="acao" value="doCadUsuario"/>

<input type="hidden" id="id" name="id" value=""/>

Nome: <input type="text" id="nome" name="nome" value=""/> <br>

Username: <input type="text" id="username"  name="username" value=""/> <br>

Senha: <input type="password" id="senha" name="senha" value=""/> <br>

<input type="button" value="Gravar" onclick="gravar('frm')">

</form>
{$msg}

{if isset($exception)}
{$exception->getMessage()}
{/if}
