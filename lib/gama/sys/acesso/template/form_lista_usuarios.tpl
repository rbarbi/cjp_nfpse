{literal}
<div id="dlgLista">
	<div class="hd">Lista de Usuários</div>
	<div class="bd">	
		<center>
			<input type="hidden" id="idFiltroAtual">
			Filtro:
			<select id="idFiltro">
  				<option value ="A">Ativo</option>
				<option value ="I">Inativo</option>
  				<option value ="">Ambos</option>  				
			</select>
			<input type="button" value="Filtrar" id="bFiltrar">			
			<div class="clear"></div>			
			<div id="tabela"></div>			
			<div id="paginador"></div>
		</center>
	</div>			
</div>	
{/literal}