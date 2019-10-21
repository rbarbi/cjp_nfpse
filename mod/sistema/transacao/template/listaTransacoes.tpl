<table border="1">
	<tr>
		<th colspan="3">&nbsp;</th>
		<th>Nome</th>
		<th>Descricao</th>
	</tr>
	{foreach from=$lista item=bo}
	<tr>
		<td><a href='javascript:excluir("{$bo->getID()}")'>excluir</a></td>
		<td><a href='javascript:alterar("{$bo->getID()}")'>alterar</a></td>
		<td><a href='javascript:exibir("{$bo->getID()}")'>exibir</a></td>
		<td>{$bo->getNome()}</td>
		<td>{$bo->getDescricao()}</td>
	</tr>
	{/foreach}
</table>
