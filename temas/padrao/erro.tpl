<div  style=" background-color: rgb(252, 232, 232);font-family: Arial; font-size:12px">
<div style="font-weight: bold; color: rgb(255, 0, 0); font-size: 16px; background-color: rgb(252, 200, 200);">
Erro: {$exception->getMessage()} ({$exception->getCode()})
</div>
<div>
{$exception->getDescricao()|escape:"htmlall"}
</div>
{if $debug < 2}
<div>
<table border="0">
	<tr>
		<td><b>Modulo:</b></td>
		<td>{$exception->getM()}</td>
	</tr>
	<tr>
		<td><b>SubModulo:</b></td>
		<td>{$exception->getU()}</td>
	</tr>
	<tr>
		<td><b>Action:</b></td>
		<td>{$exception->getA()}</td>
	</tr>
	<tr>
		<td><b>A&ccedil;&atilde;o:</b> </td>
		<td>{$exception->getAcao()}</td>
	</tr>
	<tr>
		<td><b>Arquivo:</b> </td>
		<td>{$exception->getFile()}</td>
	</tr>
	<tr>
		<td><b>Linha:</b> </td>
		<td>{$exception->getLine()}</td>
	</tr>
	<tr>
		<td colspan="2">
		<pre>
{$exception->getTraceAsString()}
</pre>
		</td>
	</tr>
</table>

</div>
{/if}
<div align="right">
{literal}
<input type="button" id="btnDetalhes" value="(+) Exibir mais detalhes" style="border:solid 1px black;" 
onclick="if (document.getElementById('erro_det_adicionais').style.display=='none') {
document.getElementById('erro_det_adicionais').style.display='block'; 
document.getElementById('btnDetalhes').value='(-) Ocultar detalhes'; 
} else { 
document.getElementById('erro_det_adicionais').style.display='none';
document.getElementById('btnDetalhes').value='(+) Exibir mais detalhes'; 
}">
{/literal}
</div>
<div id="erro_det_adicionais" style="display:none">
<span style="font-size:14px;font-weight:bold;color:red">Detalhes adicionais</span><br>
<b>
Para suporte, copie todo o conte&uacute;do da caixa de texto abaixo, cole em um email
endere&ccedil;ado para <a href="mailto:suporte@iasoft.com.br">suporte@iasoft.com.br</a>, informando detalhes do ocorrido.</b>
<a href="#" onclick="document.getElementById('taDetalhes').select();document.getElementById('taDetalhes').focus()" style="font-size:10px">selecionar conte&uacute;do</a>
<textarea rows="10" cols="82" id="taDetalhes" style="font-size:8px;background-color:#fff0f0;"  readonly>
----------------------------------------------------------------------------
{$dump}
----------------------------------------------------------------------------
</textarea>
</div>
</div>