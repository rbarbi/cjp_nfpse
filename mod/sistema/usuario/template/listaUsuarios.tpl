<table>
	{foreach from=$lista item=bo}
	<tr>
		<td><a href='javascript:excluir("{$bo->getID()}")'>excluir</a></td>
		<td><a href='javascript:alterar("{$bo->getID()}")'>alterar</a></td>
		<td><a href='javascript:exibir("{$bo->getID()}")'>exibir</a></td>
		<td>{$bo->getUsername()}</td>
		<td>{$bo->getNome()}</td>
	</tr>
	{/foreach}
</table>
