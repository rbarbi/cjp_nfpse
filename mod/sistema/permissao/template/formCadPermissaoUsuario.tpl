{include file="../../template/comum.tpl"}
{literal}
<script>
	function gravarPermissao(formulario) {
		doGoTo_(formulario);
	}
</script>
{/literal}

<form id='frm'>

<input type="hidden" name="m" value="{$_adm_m}"/>
<input type="hidden" name="u" value="permissao"/>
<input type="hidden" name="a" value="SysPermissaoUsuario"/>
<input type="hidden" name="acao" value="doCadPermissaoUsuario"/>


Usu&aacute;rio:
<select name='usuID'>
	{html_options values=$lsUsuarios->getKeys() selected=$lsUsuarios->getChave() output=$lsUsuarios->getValues()}
</select>
 <br>


Transa&ccedil;&atilde;o:
<select name='trID'>
	{html_options values=$lsTransacoes->getKeys() selected=$lsTransacoes->getChave() output=$lsTransacoes->getValues()}
</select>
 <br>

Permiss&atilde;o:
<select name='permissao'>
	{html_options values=$lsPermissoes->getKeys() selected=$lsPermissoes->getChave() output=$lsPermissoes->getValues()}
</select>

 <br>

<input type="button" value="Gravar" onclick="gravarPermissao('frm')">

</form>
{$msg}

{if isset($exception)}
{$exception->getMessage()}
{/if}
