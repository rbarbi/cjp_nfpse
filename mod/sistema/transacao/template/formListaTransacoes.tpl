{include file="../../template/comum.tpl"}
{literal}
<script>
	function pesquisar() {
		var s;
//		s = "nome="+document.getElementById('nome').value;
//		s += "&username="+document.getElementById('username').value;
//		s += "&username="+document.getElementById('username').value;
//		s += "&senha="+document.getElementById('senha').value;
		doGoTo('{/literal}{$_adm_m}{literal}','transacao','SysTransacao','doListarTransacoes',s);
	}


	function alterar(oid) {
		doGoTo('{/literal}{$_adm_m}{literal}','transacao','SysTransacao','showFormAltTransacao','id='+oid);
	}

	function exibir(oid) {
		doGoTo('{/literal}{$_adm_m}{literal}','transacao','SysTransacao','showTransacao','id='+oid);
	}

	function excluir(oid) {
		if (confirm('Deseja mesmo excluir este registro?')) {
			doGoTo('{/literal}{$_adm_m}{literal}','transacao','SysTransacao','doDelTransacao','id='+oid);
		}
	}

</script>
{/literal}


<h1>Listagem de transacoes</h1>
<hr>
{$msg}

<form method="post" action="{$_rootScript}" name="frm">

<input type="button" value="Listar" onclick="pesquisar()">
</form>

<div>
<div id='corpo'>{$listagem}</div><br>
<div id='nav'></div>
</div>




