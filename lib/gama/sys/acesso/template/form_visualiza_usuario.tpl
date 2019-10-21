<div name ='dlgVisualizar' id="dlgVisualizar">				
		<div class="hd">Visualizar Usuario</div>				
		<div class="bd">				
				<label for="id">id:</label>{$usuario->getId()}
				<div class="clear"></div>				
				<label for="nome">Nome:</label>{$usuario->getNome()}
				<div class="clear"></div>
				<label for="username">Username:</label>{$usuario->getUsername()}	
				<div class="clear"></div>
				<label for="senha">Senha:</label>{$usuario->getSenha()}	
				<div class="clear"></div>
				<label for="nivelAcesso">Nivel:</label>{$usuario->getNivelAcesso()}	
				<div class="clear"></div>
				<label for="statusRegistro">Status:</label> {$usuario->getStatusRegistro()}	
		</div>				
</div>