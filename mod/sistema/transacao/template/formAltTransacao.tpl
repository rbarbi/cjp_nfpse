{include file="../../template/comum.tpl"}
<h1>Altera Transacao</h1>
<form method="post" action="{$_rootScript}" name="frm">


<script>
{literal}
	function gravar() {
		var s;
		s = "nome="+document.getElementById('nome').value;
		s += gett('id');
		s += gett('descricao');
		s += gett('_m');
		s += gett('_u');
		s += gett('_a');
		s += gett('_acao');
		s += gett('nivel');
		s += gett('permissao');
		doGoTo('{/literal}{$_adm_m}{literal}','transacao','SysTransacao','doAltTransacao',s);
	}
{/literal}
</script>


<input type="hidden" id="id" value="{$bo->getID()}"/>

Nome: <input type="text" id="nome" value="{$bo->getNome()}"/> <br>

Descricao: <input type="text" id="descricao" value="{$bo->getDescricao()}"/> <br>
Módulo: <input type="text" id="_m" value="{$bo->getM()}"/> <br>
SubMódulo: <input type="text" id="_u" value="{$bo->getU()}"/> <br>
Action: <input type="text" id="_a" value="{$bo->getA()}"/> <br>
Ação: <input type="text" id="_acao" value="{$bo->getAcao()}"/> <br>
Nível Minimo: <input type="text" id="nivel" value="{$bo->getNivelMinimo()}"/> <br>
Permissao default: <input type="text" id="permissao" value="{$bo->getPermissaoDefault()}"/> <br>


<input type="button" value="Gravar" onclick="gravar()">

</form>
{$msg}

{if isset($exception)}
{$exception->getMessage()}
{/if}
