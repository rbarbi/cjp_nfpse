<?php

/**
 * Converte Arquivo InlcudeJS.ini para os formatos necessários
 *
 * @author Kaleu
 * @copyright IASoft
 */
class ConversorIncludeJS {

	protected $pathIncludeJS;
	protected $smarty;

	//Expressões usadas para identificar o tipo de linha do arquivo
	//ex:  "   "
	protected static $EXP_LINHA_BRANCO = '/^[ \t\n\r]{0,}$/';
	//ex: "-- Comentário"
	protected static $EXP_COMENTARIO = '/^-.*$/';
	//ex: "[Título]"
	protected static $EXP_TITULO = '/^\[.*\][\n\r]{0,}$/';

	//Usado para remover caracteres de quebra de linha indesejados
	protected static $QUEBRA_LINHA_SEPARATOR = array("\r\n", "\n", "\r");
	//Usado para identificação de quebra de linha
	protected static $QUEBRA_LINHA_STRING = '|br|';

	public function __construct($pathIncludeJS, $smarty){
		$this->pathIncludeJS = $pathIncludeJS;
		$this->smarty = $smarty;
	}

	public function toStringScript(){

		$linhas = $this->toArray();

		$strFinal = "";
		foreach($linhas as $linha){
			if($this->ehLinhaEmBranco($linha)){
				$strFinal .= "\n";
			} else if ($this->ehComentario($linha)){
				$linha = $this->removeQuebraLinha($linha);
				$strFinal .= "<!".$linha." -->\n";
			} else if ($this->ehTitulo($linha)){
				$linha = $this->removeQuebraLinha($linha);
				$linha = $this->removeColchetes($linha);
				$strFinal .= "<!-- --------------------------- -->\n";
				$strFinal .= "<!-- ".$linha." -->\n";
				$strFinal .= "<!-- --------------------------- -->\n";
			} else {
				$linha = $this->removeQuebraLinha($linha);
				$strFinal .= "<script src='".$linha."' type='text/javascript'></script>\n";
			}
		}
		return $strFinal;
	}

	public function toJSB2(){

		$projeto = array();
		$projeto["name"] = MainGama::getApp()->getM();
		$projeto["deployDir"] = "./mod/".MainGama::getApp()->getM()."/interface_web/deploy";
		$projeto["licenseText"] = MainGama::getApp()->getM();
		$projeto["pkgs"] = array();
		$projeto["resources"] = array();

		$pkgAll = array();
		$pkgAll["name"] = "Projeto Completo";
		$pkgAll["file"] = "complete-project.js";
		$pkgAll["isDebug"] = false;
		$pkgAll["includeDeps"] = true;
		$pkgAll["pkgDeps"] = array();
		$pkgAll["fileIncludes"] = array();

		$linhas = $this->toArray();

		$pkg = false;
		foreach($linhas as $linha){

			if($this->ehLinhaEmBranco($linha) || $this->ehComentario($linha)){
				continue;
			} else if ($this->ehTitulo($linha)){

				if($pkg){
					$pkgAll["pkgDeps"][] = $pkg["file"];
					$projeto['pkgs'][] = $pkg;
				 }

				 $linha = $this->removeQuebraLinha($linha);
				 $linha = $this->removeColchetes($linha);

				 $pkg = array();
				 $pkg["name"] = $linha;
				 $pkg["file"] = $linha.".js";
				 $pkg["isDebug"] = false;
				 $pkg["includeDeps"] = false;
				 $pkg["pkgDeps"] = array();
				 $pkg["fileIncludes"] = array();

			} else {
				$linha = $this->removeQuebraLinha($linha);
				//Separa caminho para pegar o diretório e o nome do arquivo separados
				$text = substr($linha, strrpos($linha, "/")+1);
				$path = substr($linha, 0, strrpos($linha, "/")+1);
				$pkg["fileIncludes"][] = array(
					"text"=>$text,
					"path"=>$path
				);
			}
		}
		//Pega último pkg
		if($pkg){
			$pkgAll["pkgDeps"][] = $pkg["file"];
			$projeto['pkgs'][] = $pkg;
		 }

		$projeto['pkgs'][] = $pkgAll;

		return json_encode($projeto);
	}

	public function toArray(){

		$oldTemplateDir = $this->smarty->template_dir;
		$oldCompileDir = $this->smarty->compile_dir;

		//Configura smarty para fazer fecth no arquivo ini
		$this->smarty->assign('pathExtJS','./lib/extJS/');
		$this->smarty->assign("m", MainGama::getApp()->getM());
		$this->smarty->assign("u", MainGama::getApp()->getU());
		$this->smarty->assign("pathLibGama3Ext", './lib/gama/interface_web/gama3/');
		$this->smarty->assign("pathLibInterfaceWeb", './lib/gama/interface_web/');

		$this->smarty->template_dir = './';
		$this->smarty->compile_dir = './'.MainGama::getApp()->getModPath() . MainGama::getApp()->getM().'/template/c';

		if(!is_file($this->pathIncludeJS)){
			throw new SysException("O Arquivo <b>IncludeJS.ini</b> precisa ser criado na pasta <b>interface_web</b> do seu módulo.");
		}
		$text = $this->smarty->fetch($this->pathIncludeJS);

		$this->smarty->template_dir = $oldTemplateDir;
		$this->smarty->compile_dir = $oldCompileDir;

		//Insere um caractere para quebra de linha pois não consegui separar as linha spelo \n ou \r
		$text = str_replace(self::$QUEBRA_LINHA_SEPARATOR, self::$QUEBRA_LINHA_STRING, $text);

		//e separa linhas pelo novo caractere
	    $linhas = explode(self::$QUEBRA_LINHA_STRING,$text);

		return $linhas;
	}

	/* ---- GERA ARQUIVO ---- */



	/* ---- AUXILIARES DE LINHA ---- */

	public function ehLinhaEmBranco($linha){
		return preg_match(self::$EXP_LINHA_BRANCO, $linha);
	}

	public function ehComentario($linha){
		return preg_match(self::$EXP_COMENTARIO, $linha);
	}

	public function ehTitulo($linha){
		return preg_match(self::$EXP_TITULO, $linha);
	}

	/* ---- AUXILIARES DE STRING ---- */

	public function removeQuebraLinha($linha){
		return str_replace(self::$QUEBRA_LINHA_SEPARATOR, "", $linha);
	}

	public function removeColchetes($linha){
		return preg_replace('/[\[\]]/', "", $linha);
	}
}
?>
