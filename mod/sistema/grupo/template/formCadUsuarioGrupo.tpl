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
<input type="hidden" name="u" value="grupo"/>
<input type="hidden" name="a" value="SysGrupo"/>
<input type="hidden" name="acao" value="doIncluirUsuarioGrupo"/>

<input type="hidden" name="grupoID" value="{$grupo->getID()}"/>


Usu&aacute;rio:
<select name='usuID'>
	{html_options values=$lsUsuarios->getKeys() selected=$lsUsuarios->getChave() output=$lsUsuarios->getValues()}
</select>
 <br>



<input type="button" value="Gravar" onclick="gravarPermissao('frm')">

</form>
{$msg}

{if isset($exception)}
{$exception->getMessage()}
{/if}
