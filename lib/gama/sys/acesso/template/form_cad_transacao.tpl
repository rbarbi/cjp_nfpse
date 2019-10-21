<div name ='dlgCadastro' id="dlgCadastro">
		<div class="hd">Cadastrar Transacao</div>
		<div class="bd">
			<form method="POST" action="{$_rootScript}" name="form">
				<input type="hidden" name="admin" value="1">
				<input type="hidden" name="m" value="acesso">
				<input type="hidden" name="a" value="SysTransacao.action">
				<input type="hidden" name="acao" value="doCadTransacao">

				<div>
					<label>Nome:</label>
					<input type="text" id="nomeTransacao" name="nomeTransacao">
				</div>

				<div class="clear"></div>

				<div>
					<label>Descricao:</label>
					<textarea id="descricao" name="descricao" rows="4" cols="60"></textarea>
				</div>

				<div class="clear"></div>

				<div>
					<label for="modulo">M&oacute;dulo:</label>
					<input type="text" id="modulo" name="m" size="32"/>
				</div>

				<div class="clear"></div>

				<div>
					<label for="submodulo">SubM&oacute;dulo:</label>
					<input type="text" id="submodulo" name="u" size="32"/>
				</div>

				<div class="clear"></div>

				<div>
					<label>Action:</label>
					<input type="text" id="action" name="a" size="32"/>
				</div>

				<div class="clear"></div>

				<div>
					<label for="action">A&ccedil;&atilde;o:</label>
					<input type="text" id="acao" name="acao" size="32"/>
				</div>

				<div class="clear"></div>

				<div>
					<label for="senha">Senha:</label>
					<input type="text" name="senha" />
				</div>
			</form>
		</div>
</div>