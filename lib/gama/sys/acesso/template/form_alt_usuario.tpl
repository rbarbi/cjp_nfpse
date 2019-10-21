<div name ='dlgAlterar' id="dlgAlterar">				
		<div class="hd">Alterar Usuario</div>				
		<div class="bd">				
			<form method="POST" action="{$_rootScript}" name="form">	
				<input type="hidden" name="admin" value="1">
				<input type="hidden" name="m" value="acesso">
				<input type="hidden" name="a" value="SysUsuario.action">
				<input type="hidden" name="acao" value="doAltUsuario">				
				<label for="id">id:</label><input type="text" name="oid"  value="{$usuario->getId()}" readonly />
				<div class="clear"></div>				
				<label for="nome">Nome:</label><input type="text" name="nome" value="{$usuario->getNome()}"/>
				<div class="clear"></div>
				<label for="username">Username:</label><input type="text" name="username" value="{$usuario->getUsername()}"/>	
				<div class="clear"></div>
				<label for="senha">Senha:</label><input type="text" name="senha" value="{$usuario->getSenha()}"/>	
				<div class="clear"></div>
				<label for="nivelAcesso">Nivel:</label><input type="text" name="nivelAcesso" value="{$usuario->getNivelAcesso()}"/>	
				<div class="clear"></div>
				<label for="statusRegistro">Status:</label>
				<select name="statusRegistro">
					{html_options values=$lsKFiltroStatus selected=$usuario->getStatusRegistro() output=$lsVFiltroStatus}
				</select>	
			</form>	
		</div>				
</div>	