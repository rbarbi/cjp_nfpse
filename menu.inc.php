<?php

/*
		$arr[] = array( 'txLabel' => 'Processamentos',
						'txImg' => './temas/progem/img/menu/processamentos.png',
						'txURL'=>null,
						'itensFilhos' => array(
							 array (
								'txLabel' => 'Importações',
								'txImg' => null,
								'txURL'=>null,
								'itensFilhos' => array(
										array(	'txLabel' => 'Importar arquivo do Plano de Contas',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('showFormImportaArqPlanoContas',
												'iss_bancos.action')"),
									)
								)

							)

					  );*/


		$arr[] = array(	'txLabel' => 'RELATORIOS',
						'txImg' => './temas/progem/img/menu/relatorios.png',
						'txURL'=>null,
						'itensFilhos' => array(
							 array (
								'txLabel' => 'ISS',
								'txImg' => null,
								'txURL'=>null,
								'itensFilhos' => array(
									    array(	'txLabel' => 'Relatorio 1',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormParmsRelatoriosImpostos',
												'iss_bancos.action')"),
										array(	'txLabel' => 'Relatorio 2',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormParmsRelatoriosImpostos2',
												'iss_bancos.action')"),
										array(	'txLabel' => 'Apuração Anual',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormParmsRelatorioResumo',
												'iss_bancos.action')"),
										array(	'txLabel' => 'Calcular apuração',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormParmsRelatorioApuracao',
												'apuracao_iss.action')"),
										array(	'txLabel' => 'Alterar apuração',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormParmsAlteracaoApuracao',
												'apuracao_iss.action')"),
										array('txLabel' => 'Relatorio Imposto Devido',
												 'txImg' => 'null',
												 'txURL' => "javascript:acessa_menu4('showFormParmsRelatoriosImpostos','iss_bancos.action')"
												)
										)
								)
							)
						);




		$arr[] = array(	'txLabel' => 'ISS BANCOS',
						'txImg' => './temas/progem/img/menu/iss_bancos.png',
						'txURL'=>null,
						'itensFilhos' => array(
							 array (
								'txLabel' => 'Ferramentas',
								'txImg' => null,
								'txURL'=>null,
								'itensFilhos' => array(
									    array(	'txLabel' => 'Importar arq Plano Contas',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormImportaArqPlanoContas',
												'iss_bancos.action')"),
										array(	'txLabel' => 'Importar arq Balancete',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormImportaArqBalancete',
												'iss_bancos.action')"),
										array(	'txLabel' => 'Importar Indices',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormImportarTabelaIndices',
												'iss_bancos.action')"),
										array(	'txLabel' => 'Importar Declaracoes',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormImportarArqDeclaracao',
												'iss_bancos.action')"),
										array(	'txLabel' => 'Converter Arquivos',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormXLS2CSV',
												'iss_bancos.action')"),
										array(	'txLabel' => 'Enviar Arquivos',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('showFormUpload',
												'fileExplorer.action')"),
										array(	'txLabel' => 'Navegar Arquivos',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu4('doExibirExplorer',
												'fileExplorer.action')")

									)
								)
							)
						);


		$arr[] = array(	'txLabel' => 'Cadastros',
						'txImg' => './temas/progem/img/menu/cadastro.png',
						'txURL'=>null,
						'itensFilhos' => array(
							 array (
								'txLabel' => 'Ferramentas',
								'txImg' => null,
								'txURL'=>null,
								'itensFilhos' => array(
										array(	'txLabel' => 'Cad. Plano Contas - Bacen',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('showFormCadPlanoContasBacen',
												'iss_bancos.action')"),

										array(	'txLabel' => 'Lista do Plano de Contas',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('doListarPlanoContasBacen',
												'iss_bancos.action')"),

										array(	'txLabel' => 'Importar arq. contabilidade',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('showFormImportaArqContabilidade',
												'iss_bancos.action')"),

										array(	'txLabel' => 'Cadastrar Leis',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('showFormCadLei',
												'lei.action')"),

										array(	'txLabel' => 'Listar Leis',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('doListarLeis',
												'lei.action')"),

										array(	'txLabel' => 'Cadastrar Contribuinte',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu3('showFormCadContribuinte',
												'contribuinte.action')"),


										array(	'txLabel' => 'Listar Contribuintes',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu3('doListarContribuinte',
												'contribuinte.action')"),
											/*
										array(	'txLabel' => 'Lista do Plano de Contas',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('doListarPlanoContasBacen',
												'iss_bancos.action')")*/
										array(	'txLabel' => 'Cadastro Tipo Fator Ajuste',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('showIndex',
												'tipo_fator_ajuste.action')"),

									   array(	'txLabel' => 'Listar Tipo Fator Ajuste',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('doListarTipoFatorAjuste',
												'tipo_fator_ajuste.action')"),

									   array(	'txLabel' => 'Cadastro Fator Ajuste',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('showIndex',
												'fator_ajuste.action')"),

									   array(	'txLabel' => 'Listar Fator Ajuste',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('doListarFatorAjuste',
												'fator_ajuste.action')"),

									    array(	'txLabel' => 'Cadastro Valor Fator Ajuste',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('showIndex',
												'valor_fator_ajuste.action')"),

									    array(	'txLabel' => 'Listar Valor Fator Ajuste',
												'txImg' => null,
												'txURL'=>"javascript:acessa_menu('doListarValorFatorAjuste',
												'valor_fator_ajuste.action')")

									)
								)
							)
						);


		$arr[] = array(	'txLabel' => 'Sair do Sistema',
						'txImg' => './temas/progem/img/menu/sairdosistema.png',
						'txURL'=>'?doLogout=1');

