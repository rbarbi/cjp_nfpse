<?php // $Rev: $ $Author: $ $Date: $//


/**
 * Classe que centraliza as operaчѕes de compactaчуo e descompactaчуo 
 * de arquivos, bem como a inclusуo de arquivos em outros compactados.
 * 
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.zip
 */
abstract class G3ZIP {

	 /**
	  * @var mixed pathExecutavel 
	  */
	 private $pathExecutavel;

//--------------------------------------------

	/**
	 * Retorna o valor de pathExecutavel
	 * @return mixed
	 */
	public function getPathExecutavel () {
		return $this->pathExecutavel;
	} // eof getPathExecutavel 



//--------------------------------------------

	/**
	 * Define o valor de pathExecutavel
	 * @param mixed $pathExecutavel
	 */
	public function setPathExecutavel ($pathExecutavel) {
		$this->pathExecutavel = $pathExecutavel;
	} // eof setPathExecutavel 

	
	/**
	 * Executa a compactacao do arquivo
	 *
	 * @param string $nomeArquivoZipado
	 * @param string $pathInclude
	 * @param array  $lsExtArquivosIncluir
	 */
	function execCompactacao($nomeArquivoZipado,$pathInclude,$lsExtArquivosIncluir=false) {
		if (is_array($lsExtArquivosIncluir)) {
			$l = $this->getLinhaComando($nomeArquivoZipado,$pathInclude,$lsExtArquivosIncluir);
		} else {
			$l = $this->getLinhaComando($nomeArquivoZipado,$pathInclude);
		}
		$this->exec($l);
	} // eof execCompactacao
	
	/**
	 * Executa a linha de comando passada como parтmetro.
	 *
	 * @param string $linhaComando
	 */
	public function exec($linhaComando) {
		echo $linhaComando;
		exec($linhaComando);
	} // eof exec
	

} // eof G3ZIP


?>