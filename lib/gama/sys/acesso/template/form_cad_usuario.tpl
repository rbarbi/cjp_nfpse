<div name ='dlgCadastro' id="dlgCadastro">				
		<div class="hd">Cadastrar Usuario</div>				
		<div class="bd">				
			<form method="POST" action="index.php" name="form">	
				<input type="hidden" name="admin" value="1">
				<input type="hidden" name="m" value="acesso">
				<input type="hidden" name="a" value="SysUsuario.action">
				<input type="hidden" name="acao" value="doCadUsuario">				
				<label for="nome">Nome:</label><input type="text" name="nome" />
				<div class="clear"></div>
				<label for="username">Username:</label><input type="text" name="username" />	
				<div class="clear"></div>
				<label for="senha">Senha:</label><input type="text" name="senha" />	
			</form>	
		</div>				
</div>	