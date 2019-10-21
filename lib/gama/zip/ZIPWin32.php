<?php // $Rev: $ $Author: $ $Date: $//

class G3ZIPWin32 extends G3ZIP {

	function __construct() {		
		$this->setPathExecutavel('/bin/7-ZipPortable/App/7-Zip/7z.exe');
	} // eof __construct
	
	
	
	function getLinhaComando($nomeArquivoZipado,$pathInclude,$lsExtArquivosIncluir=array('rtf','jpg')) {
		$s = $this->getPathExecutavel();
		$s .= " a $nomeArquivoZipado  -tzip  $pathInclude ";
		foreach ($lsExtArquivosIncluir as $ext) {
			$s .= " -ai!$ext ";
		}
		return $s;
	}
	
	public function exec($linhaComando) {
		echo $linhaComando;
		system($linhaComando);
	} // eof exec	
}


?>