{literal}
<script>

function processar(executar) {
	document.frm.doTestar.value = executar;
	document.frm.submit();
}


</script>

<style>

BODY {
	font-family:Arial
}

.tbl {
	font-size:12px;
}

.erro {
	color:red;
}

.ok {
	color:navy;
}

</style>
{/literal}
<h1>Suite de testes - {$m}</h1>
<form name='frm' id='frm' method="POST" action="index.php?cmdGamaAdmin=ShowPainelTest" >
<input type="hidden" name="doTestar" value="S" />
<table >
	<tr>
		<td>Parametros obrigatorios</td>
		<td>
			<table>
				<tr>
					<td>M</td>
					<td>U</td>
					<td>Suite</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><input type="text" size="20" name="m" value="{$m}"></td>
					<td><input type="text" size="20" name="u" value="{$u}"></td>
					<td>
						<select name="suite">
						{html_options values=$lsSuites output=$lsSuites selected=$suite}
						</select>
					</td>
					<td><input type="button" value="Executar teste" onclick="processar('S')"></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2"><input type="button" value="Atualizar" onclick="processar('N')"></td>
	</tr>

</table>
{$msg}

<h3>Executando suite '{$suite}'</h3>
<table width="100%" class="tbl">
	<tr>
		<th>Nome do metodo</th>
		<th>Descricao</th>
		<th>Codigo</th>
		<th>Resultado</th>
	</tr>
{foreach from=$respostas item=teste}
{if ($teste->getCodigo() === false) || ($teste->getCodigo() === 0)}
	<tr class="ok">
{else}
	<tr class="erro">
{/if}
		<td>{$teste->getNomeMetodo()}</td>
		<td>{$teste->getDescricao()}</td>
		<td>{$teste->getCodigo()}</td>
		<td>{$teste->getResultado()}</td>
	</tr>
{/foreach}
</table>

</form>