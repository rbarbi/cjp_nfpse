{include file="../../template/comum.tpl"}
<h1>Alteracao de grupo</h1>
<form method="post" action="{$_rootScript}" name="frm">

{literal}
<script>
	function gravar() {
		var s;
		s = "id="+document.getElementById('id').value;
		s += "&nome="+document.getElementById('nome').value;
		s += "&descricao="+document.getElementById('descricao').value;
		doGoTo('{/literal}{$_adm_m}{literal}','grupo','SysGrupo','doAltGrupo',s);
	}
</script>
{/literal}


<input type="hidden" name="m" value="{$_adm_m}"/>
<input type="hidden" name="u" value="grupo"/>
<input type="hidden" name="a" value="SysGrupo"/>
<input type="hidden" name="acao" value="doAltGrupo"/>

<input type="hidden" id="id" value="{$bo->getID()}"/>

Nome: <input type="text" id="nome" value="{$bo->getNome()}"/> <br>

Username: <input type="text" id="descricao" value="{$bo->getDescricao()}"/> <br>

<input type="button" value="Gravar" onclick="gravar()">

</form>
{$msg}

{if isset($exception)}
{$exception->getMessage()}
{/if}
