{include file="../../template/comum.tpl"}
<h1>Cadastro de Transacao</h1>
<form method="post" action="{$_rootScript}" name="frm">

{literal}
<script>
	function gravar() {
		var s;
		s = "nome="+document.getElementById('nome').value;
		s += gett('descricao');
		s += gett('_m');
		s += gett('_u');
		s += gett('_a');
		s += gett('_acao');
		s += gett('nivel');
		s += gett('permissao');
		doGoTo('{/literal}{$_adm_m}{literal}','transacao','SysTransacao','doCadTransacao',s);
	}
</script>
{/literal}

<input type="hidden" id="id" value=""/>

Nome: <input type="text" id="nome" value=""/> <br>

Descricao: <input type="text" id="descricao" value=""/> <br>
Módulo: <input type="text" id="_m" value=""/> <br>
SubMódulo: <input type="text" id="_u" value=""/> <br>
Action: <input type="text" id="_a" value=""/> <br>
Ação: <input type="text" id="_acao" value=""/> <br>
Nível Minimo: <input type="text" id="nivel" value=""/> <br>
Permissao default: <input type="text" id="permissao" value=""/> <br>


<input type="button" value="Gravar" onclick="gravar()">

</form>
{$msg}

{if isset($exception)}
{$exception->getMessage()}
{/if}
