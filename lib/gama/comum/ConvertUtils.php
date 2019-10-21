<?php // $Rev: $ $Author: $ $Date: $//


interface G3Conversor {
	public static function toNormal($valor);
	public static function toBD($valor);
} // G3Conversor


/**
 * Classe que realiza as convers�es de Valor Monet�rio
 */
class ConversorValorMonetario implements G3Conversor {

	/**
	 * Converte o valor que est� no formato do Banco de
	 * dados, para o formato do usu�rio.
	 *
	 * @param string $valor
	 * @return string
	 */
	public static function toNormal($valor) {
		return number_format($valor,2,',','.');
	} // toNormal


	/**
	 * Converte o valor, que est� no formato do usu�rio,
	 * para o padr�o do banco de dados.
	 *
	 * @param string $valor
	 * @return string
	 */
	public static function toBD($valor) {
		return str_replace(',','.',str_replace('.','',$valor));
	} // toBD

} // ConversorValorMonetario



/**
 * Classe que realiza as convers�es de Timestamp
 */
class ConversorDataHora implements G3Conversor {


	 /**
	  * @var mixed GMT
	  */
	 private static $GMT = 0;

//--------------------------------------------

	/**
	 * Retorna o valor de GMT
	 * @return mixed
	 */
	public static function getGMT () {
		return self::$GMT;
	} // eof getGMT



//--------------------------------------------

	/**
	 * Define o valor de GMT
	 * @param mixed $GMT
	 */
	public function setGMT ($GMT) {
		self::$GMT = $GMT;
	} // eof setGMT





	protected static function quebraDataHora($dataIn,$separador="/[\/\- :)]/",$formato='YmdHIS') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		$itens = preg_split($separador,$dataIn);
		$arr = array('Y'=>0,'m'=>0,'d'=>0,'H'=>0,'I'=>0,'S'=>0);
		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = intval($item);
			$contador++;
		}
		return $arr;
	} // quebraDataHora

	/**
	 * Converte o valor que estao no formato do Banco de
	 * dados, para o formato do usuario.
	 *
	 * @param string $valor
	 * @return string
	 */
	public static function toNormal($dataIn,$gmt=false,$separador="/[\/\- :]/",$formato='YmdHIS') {
		$arr = ConversorDataHora::quebraDataHora($dataIn,$separador,$formato);
		if (is_null($arr)) {
			return null;
		} else {
			if (isset($arr['H'])) {
				if (!$gmt) {
					$gmt = self::getGMT();
				}
				$arr['H'] += $gmt;
				$dh = mktime($arr['H'],$arr['I'],$arr['S'],$arr['m'],$arr['d'],$arr['Y']);
				return strftime("%d/%m/%Y %H:%M:%S",$dh);
			} else {
				return sprintf("%02d/%02d/%04d",$arr['d'],$arr['m'],$arr['Y']);
			}
		}
	} // toNormal


	/**
	 * Converte o valor, que est� no formato do usu�rio,
	 * para o padr�o do banco de dados.
	 *
	 * @param string $valor
	 * @return string
	 */
	public static function toBD($dataIn,$gmt=false,$separador="/[\-\/ :]/",$formato='dmYHIS') {
		$arr = ConversorDataHora::quebraDataHora($dataIn,$separador,$formato);
		if (is_null($arr)) {
			return null;
		} else {
			if (isset($arr['H'])) {
				if (!$gmt) {
					$gmt = self::getGMT();
				}
				$arr['H'] += $gmt;
				$dh = mktime($arr['H'],$arr['I'],$arr['S'],$arr['m'],$arr['d'],$arr['Y']);
				return strftime("%Y-%m-%d %H:%M:%S",$dh);
			} else {
				return sprintf("%04d-%02d-%02d",$arr['Y'],$arr['m'],$arr['d']);
			}
		}
	} // toBD



	/**
	 * Retorna a string com o valor por extenso.
	 *
	 * @param string $dataIn
	 * @param string $separador
	 * @param string $formato
	 * @return string
	 */
	public static function toExtenso($dataIn,$separador="/[\-\/]/",$formato='Ymd') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}

		$arr = ConversorDataHora::quebraDataHora($dataIn,$separador,$formato);

		switch(intval($arr["m"])) {
			case 1:case "1": $mes = "janeiro";break;
			case 2:case "2": $mes = "fevereiro";break;
			case 3:case "3": $mes = "mar�o";break;
			case 4:case "4": $mes = "abril";break;
			case 5:case "5": $mes = "maio";break;
			case 6:case "6": $mes = "junho";break;
			case 7:case "7": $mes = "julho";break;
			case 8:case "8": $mes = "agosto";break;
			case 9:case "9": $mes = "setembro";break;
			case 10:case "10": $mes = "outubro";break;
			case 11:case "11": $mes = "novembro";break;
			case 12:case "12": $mes = "dezembro";break;
		}

		return "$arr[d] de $mes de $arr[Y]";
	} // toExtenso


} // ConversorDataHora



/**
 * Classe que realiza as convers�es de Valor Monet�rio
 */
class ConversorCEP implements G3Conversor {

	/**
	 * Converte o valor que est� no formato do Banco de
	 * dados, para o formato do usu�rio.
	 *
	 * @param string $valor
	 * @return string
	 */
	public static function toNormal($valor) {
		$cep = str_replace("-","",str_replace(" ","",trim($valor)));
		$aux1 = substr($cep,0,5);
		$aux2 = substr($cep,5,3);
		return "$aux1-$aux2";
	} // toNormal


	/**
	 * Converte o valor, que est� no formato do usu�rio,
	 * para o padr�o do banco de dados.
	 *
	 * @param string $valor
	 * @return string
	 */
	public static function toBD($valor) {
		$cep = substr(str_replace("-","",str_replace(" ","",trim($valor))),0,8);
		return $cep;
	} // toBD

} // ConversorCEP


//ConversorDataHora::setGMT(-3);
//
//echo  ConversorDataHora::toNormal('2009-05-01 10:15');
//echo "\n";
//echo  ConversorDataHora::toBD('01/05/2009 10:15');
//
//
//echo  ConversorDataHora::toExtenso('2008-03-01');
//echo "\n";
//echo  ConversorDataHora::toBD('01/05/2009');

//
//echo  ConversorCEP::toNormal('880707701');
//echo "\n";
//echo  ConversorCEP::toBD('88070-7701');



?>