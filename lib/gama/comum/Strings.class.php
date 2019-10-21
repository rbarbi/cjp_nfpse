<?php // $Rev: $ $Author: $ $Date: $//


/**
 * Classe que encapsula um conjunto de funcionalidades para tratar strings;
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @created 2010-03-10
 */
class String {

	const ENC_ISO_8859_1 = 'ISO-8859-1';
	const ENC_UTF_8 = 'UTF-8';


	/**
	  * @var string locale
	  */
	protected  $_locale = "PT_BR";

	/**
	  * @var string valor
	  */
	protected  $_valor;

	/**
	  * @var string encode
	  */
	protected  $_encode;

	//--------------------------------------------

	/**
	 * Retorna o valor de locale
	 * @return mixed
	 */
	public function getLocale () {
		return $this->_locale;
	} // eof getLocale

	/**
	 * Retorna o valor de valor
	 * @return mixed
	 */
	public function getValor () {
		return $this->_valor;
	} // eof getValor

	/**
	 * Retorna o valor de encode
	 * @return mixed
	 */
	public function getEncode () {
		return $this->_encode;
	} // eof getEncode



	//--------------------------------------------

	/**
	 * Define o valor de locale
	 * @param mixed $locale
	 */
	public function setLocale ($locale) {
		$this->_locale = $locale;
	} // eof setLocale

	/**
	 * Define o valor de valor
	 * @param mixed $valor
	 */
	public function setValor ($valor) {
		$this->_valor = $valor;
	} // eof setValor

	/**
	 * Define o valor de encode
	 * @param mixed $encode
	 */
	public function setEncode ($encode) {
		$this->_encode = $encode;
	} // eof setEncode




	/**
	 * Construtor.
	 *
	 * @param string $valor
	 */
	public function __construct($valor=false,$encode=String::ENC_ISO_8859_1) {
		@setlocale (LC_CTYPE, $this->getLocale());
		$this->setEncode($encode);
		if ($valor) {
			$this->setValor($valor);
		}
	} // __construct



	/**
	 * Retorna o valor da string, caso seja exigido o valor do
	 * objeto, pelo interpretador.
	 *
	 * @return string
	 */
	public function __tostring() {
		return $this->getValor();
	} // __tostring


	/**
	 * Converte a string para min�sculo;
	 *
	 * @param string $valor
	 * @return string
	 */
	public function toLowerCase($valor=false) {
		if ($valor) {
			return String::_toLowerCase($valor);
		} elseif (is_object($this)) {
			return String::_toLowerCase($this->getValor());
		}
	} // toLower



	protected static function _toLowerCase($valor) {
		return new String(strtolower($valor));
	} // _toLower




	/**
	 * Converte o conte�do da string para mai�sculo;
	 *
	 * @param string $valor
	 * @return string
	 */
	public function toUpperCase($valor=false) {
		if ($valor) {
			return String::_toUpperCase($valor);
		} elseif (is_object($this)) {
			return String::_toUpperCase($this->getValor());
		}
	} // toLower



	protected static function _toUpperCase($valor) {
		return new String(strtoupper($valor));
	} // _toLower




	/**
	 * Remove os caracteres especiais, retornando a string apenas com
	 * os valores ASCII normais;
	 *
	 * @param string $valor
	 * @return String
	 */
	public function normalize($valor=false) {
		$ts = array("/[�-�]/","/�/","/�/","/[�-�]/","/[�-�]/","/�/","/�/","/[�-��]/","/�/","/[�-�]/","/[�-�]/","/[�-�]/","/�/","/�/","/[�-�]/","/[�-�]/","/�/","/�/","/[�-��]/","/�/","/[�-�]/","/[�-�]/");
		$tn = array("A","AE","C","E","I","D","N","O","X","U","Y","a","ae","c","e","i","d","n","o","x","u","y");
		if ($valor === false) {
			$valor = $this->getValor();
		}
		return new String(preg_replace($ts,$tn, $valor));
	} // normalize




	/**
	 * Remove parte do valor da string.
	 *
	 * @param string $valor
	 * @return String
	 */
	public function remove($valor) {
		return new String(str_replace($valor,"",$this->getValor()));
	} // remove






	/**
	 * Remove todos os \r e \n da string
	 *
	 * @return String
	 */
	public function chomp() {
		return new String(preg_replace("/[\n\r]/","", $this->getValor()));
	} // chomp



	/**
     * Compara duas string.
     *
     * @param string String que se deseja comparar.
     * @param bool   Se TRUE, a compara��o � case-sensitive.
     * @return int
     */
	public function compareTo( $anotherString, $caseSensitive = true ) {
		$function = $caseSensitive ? 'strcmp' : 'strcasecmp';
		return $function( $this->getValor(), $anotherString );
	} // compareTo



	/**
	 * Retorna uma substring.
	 *
	 * @param int $indiceInicial
	 * @param int $comprimento
	 * @return String
	 */
	public function substr($indiceInicial,$comprimento=false) {

		if ($comprimento !== false) {
			return new String(substr($this->getValor(),$indiceInicial,$comprimento));
		} else {
			return new String(substr($this->getValor(),$indiceInicial));
		}

	} // substr





	/**
     * Concatenates the specified string to the end of this string.
     *
     * @param string String that will be concatenated.
     * @return void
     */
	public function concat( $str ) {
		return new String($this->getValor() . $str);
	} // concat


	/**
     * Returns true if and only if this string contains the specified sequence
     * of char values.
     *
     * @param string Char Sequence that is being searched.
     * @return bool
     */
	public function contains( $charSequence ) {
		return preg_match( $charSequence, $this->getValor() );
	} // contains


	public function copyValueOf( &$data ) {
		if ( is_a($data, 'String') ) {
			$this->_copyValueOfString( &$data );
		} else if (is_string($data)) {
			$this->setValor($data);
		}
	} // copyValueOf


	/**
     * Private method that copies the value of a String object to this one.
     *
     * @param String object reference.
     * @return void
     */
	protected function _copyValueOfString( &$data ) {
		$this->setValor( $data->getValor() );
	} // _copyValueOfString


	/**
     * Returns the character that is in position $pos. If this position is out
     * of bounds, it returns FALSE.
     *
     * @param int Position of the character that will be returned.
     * @return char
     */
	public function getCharAt( $pos ) {
		if ( $pos < 0 || $pos > $this->lenght() ) {
			return false;
		} else {
			return $this->_valor[$pos];
		}
	} // getCharAt




	public function length() {
		return strlen( $this->getValor() );
	} // lenght





	public function trim() {
		return new String(trim($this->getValor()));
	} // trim



	public function split($separador) {
		return explode($separador,$this->getValor());
	} // split


	public function join($separador) {
		return new String(join($separador,$this->getValor()));
	} // join



	/**
	 * @return String
	 */
	public function toUTF8() {
		if ($this->getEncode() != String::ENC_UTF_8  ) {
			return new String(utf8_encode($this->getValor()),String::ENC_UTF_8);
		} else {
			return new String($this->getValor());
		}
	} // toUTF8






    /**
     * Replace a string for another in actual string
     */
    function replace($find,$replace,$ignore_case=false) {

        if($ignore_case) {
            return new String(str_ireplace($find,$replace,$this->getValor()));
        } else {
            return new String(str_replace($find,$replace,$this->getValor()));
        }
    } // replace

    /**
     * Same as replace, but ereg .
     */
    function eregReplace($find,$replace,$ignore_case=false) {

        if($ignore_case) {
            return new String(preg_replace($find,$replace,$this->getValor()));
        } else {
			// Aqui deve ser feito uma verificacao...
            return new String(preg_replace($find,$replace,$this->getValor()));
        }
    } // eregReplace



    /**
     * Parse string as a URL valid.
     */
       function toURL() {
        return new String(urlencode(utf8_decode($this->getValor())));
    } // toURL

    /**
     * Parse string as HTML encoded, NOT WORKING AS I THINK
     */
    function htmlEncode($ent=null) {
        return new String(htmlentities(html_entity_decode(utf8_decode($this->getValor()))));
    } // htmlEncode

    /**
     * Uncode string into html
     */
    function htmlDecode($ent=null) {
        return new String(html_entity_decode(utf8_decode($this->getValor())));
    }

    /**
     * Codify as html code just words, ignoes quotes and tags
     */
    function htmlEncodeWords() {
        $x = $this->htmlEncode(ENT_NOQUOTES);
        $x->replace("&lt;","<")->replace("&gt;",">")->replace("/&gt;",">");
        return $x ;
    } // htmlEncodeWords

    /**
     * Cleans up the string, good for filenames, logins and etc
     *
     * @return String
     */
    function clean() {
    	$x = $this->stripTags();
        $x->htmlEncode()->eregReplace("&([a-zA-Z])([a-zA-Z]*);","\\1")->replace(" ","_")->toURL()->eregReplace("%([a-zA-Z0-9]{2})","_");
        return $x;
    } // clean




    /**
     * Strip tags
     */
    function stripTags($tagsAllowed=null) {
        return new String(strip_tags($this->getValor(),$tagsAllowed));
    } // stripTags

    /**
     * Remove slashes
     */
    function removeSlashes() {
    	return new String(stripslashes($this->getValor()));
    }



    /**
     * Turns line breaks into <br />
     */
    function nlToBR() {
        return new String(nl2br($this->getValor()));
    }

    /**
     * <br /> back to line breaks
     */
    function BRToNl() {
        return new String(str_replace("<br />","\n",$this->getValor()));
    }

    /**
     * Alias for nlToBR()
     */
    function nl2br() {
        return $this->nlToBR();
    }

       /**
        * Alias for BRToNl()
        */
    function br2nl() {
        return $this->BRToNl();

    }


    /**
     * Return this string as xml, must whatout for special chars and tags.
     */
    function __toXml() {
        $xml = new String("<string length=\"{$this->length()}\">\n\t<value>{$this->getValor()}</value>\n</string>");
        return $xml ;
    }

    /**
     * A alias to __toXMl()
     *
     * @return String
     */
    function toXML() {
        return $this->__toXML();
    }











} // String


/*
$s = new String("�SSIM IN �NORIS c�us�");
$t = new String("<i href='xpto?a=3&b=3'>aaa</i>");

	echo $t->stripTags('<a><i>');
	exit;*/


//echo $s->toLower('Andr�');
//echo String::toLowerCase('Andr�');
//echo $s->normalize()->toLowerCase()->remove('onoris');

//echo $s->toXML();
//echo $s->compareTo($t,true);
//echo $s->substr(6,5);
//$s->normalize();

?>