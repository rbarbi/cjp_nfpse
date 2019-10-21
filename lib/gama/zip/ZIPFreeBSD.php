<?php // $Rev: $ $Author: $ $Date: $//

class G3ZIPFreeBSD extends G3ZIP {

	function __construct() {		
		$this->setPathExecutavel('/usr/local/bin/zip');
	} // eof __construct
	
	
	
	function getLinhaComando($nomeArquivoZipado,$pathInclude,$lsExtArquivosIncluir=array('\*.rtf','\*.jpg')) {
//		$s = "/usr/local/bin/zip -j $NomeArquivoZipado arquivos/$ano/$mes/$dia/ -r $NomeArquivoZipado arquivos/$ano/$mes/$dia/ -i \*.rtf -i \*.jpg";
		$s = $this->getPathExecutavel();
		$s .= " -j $nomeArquivoZipado    $pathInclude ";
		$s .= " -r $nomeArquivoZipado    $pathInclude ";
		foreach ($lsExtArquivosIncluir as $ext) {
			$s .= " -i $ext ";
		}
//		$s .= " -i \*.rtf -i \*.jpg ";
		return $s;
	}
	
	
	
	
} // eof G3ZIPFreeBSD


?>