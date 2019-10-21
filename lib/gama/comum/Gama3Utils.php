<?php // $Rev: 700 $ - $Author: eduluz $ $Date: 2015-03-12 10:43:16 -0300 (qui, 12 mar 2015) $


/**
 * Classe que agrupa funcionalidades comuns a v�rios sistemas.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils
 */
class Gama3Utils {


	/**
	 * Serializa um objeto passado por par�metro.
	 *
	 * @param mixed $x
	 * @return string Base64 encoded
	 */
	public static function serializa($x) {
		return base64_encode(serialize($x));
	}

	/**
	 * Deserializa uma string passada por par�metro,
	 * recuperando o objeto original.
	 *
	 * @param string $x
	 * @return mixed
	 */
	public static function deserializa($x) {
		return unserialize(base64_decode($x));
	}



	/**
	 * Converte a data vinda do banco de dados para um formato
	 * leg�vel, dentro do padr�o "humano".
	 *
	 * @param string $dataIn String com a data original
	 * @param string $separador Opcional - express�o regular do separador dos itens da data
	 * @param string $formato Opcional - string com o formato de entrada. Padr�o Ymd - 'ano-mes-dia'
	 * @return String Data formatada em 'd-m-Y'
	 */
	public static function convertDataDB2Normal($dataIn,$separador="-",$formato='Ymd') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		$itens = explode($separador,$dataIn);
		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = $item;
			$contador++;
		}
		return "$arr[d]/$arr[m]/$arr[Y]";
	}

	/**
	 * Converte a datahora vinda do banco de dados para um formato
	 * leg�vel, dentro do padr�o "humano".
	 *
	 * @param string $dataIn String com a datahora original
	 * @param string $separador Opcional - express�o regular do separador dos itens da data
	 * @param string $formato Opcional - string com o formato de entrada. Padr�o Ymd - 'ano-mes-dia'
	 * @return String Data formatada em 'd/m/Y h:i:s'
	 */
	public static function convertDataHoraDB2Normal($dataIn,$separador="/[- :.]/",$formato='YmdHisx') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		list($dataIn,$horaIn) = explode(" ",$dataIn);
		$itens = preg_split($separador,$dataIn);

		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = $item;
			$contador++;
		}
		$itens = preg_split($separador,$horaIn);
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = $item;
			$contador++;
		}
		return "$arr[d]/$arr[m]/$arr[Y] $arr[H]:$arr[i]:$arr[s]";
	} // convertDataHoraDB2Normal

	/**
	 * Converte a datahora vinda do banco de dados para um formato
	 * leg�vel, dentro do padr�o "humano".
	 *
	 * @param string $dataIn String com a datahora original
	 * @param string $separador Opcional - express�o regular do separador dos itens da data
	 * @param string $formato Opcional - string com o formato de entrada. Padr�o Ymd - 'ano-mes-dia'
	 * @return String Data formatada em 'd-m-Y'
	 */
	public static function convertDataHoraDB2MesExtenso($dataIn,$separador="/[- ]/",$formato='Ymd') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		list($dataIn,$horaIn) = explode(" ",$dataIn);
		$itens = preg_split($separador,$dataIn);
		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = $item;
			$contador++;
		}

		switch($arr["m"])
		{
			case "01":case "1": $mes = "Janeiro";break;
			case "02":case "2": $mes = "Fevereiro";break;
			case "03":case "3": $mes = "Março";break;
			case "04":case "4": $mes = "Abril";break;
			case "05":case "5": $mes = "Maio";break;
			case "06":case "6": $mes = "Junho";break;
			case "07":case "7": $mes = "Julho";break;
			case "08":case "8": $mes = "Agosto";break;
			case "09":case "9": $mes = "Setembro";break;
			case "10":case "10": $mes = "Outubro";break;
			case "11":case "11": $mes = "Novembro";break;
			case "12":case "12": $mes = "Dezembro";break;
		}

		return "$arr[d] de $mes de $arr[Y]";
	}

	/**
	 * Converte a datahora vinda do banco de dados para um formato
	 * leg�vel, dentro do padr�o "humano".
	 *
	 * @param string $dataIn String com a datahora original
	 * @param string $separador Opcional - express�o regular do separador dos itens da data
	 * @param string $formato Opcional - string com o formato de entrada. Padr�o Ymd - 'ano-mes-dia'
	 * @return String Data formatada em 'd-m-Y'
	 */
	public static function convertDataDB2MesExtenso($dataIn,$separador="/[- ]/",$formato='Ymd') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		//list($dataIn,$horaIn) = explode(" ",$dataIn);
		$itens = preg_split($separador,$dataIn);
		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = $item;
			$contador++;
		}

		switch($arr["m"])
		{
			case "01":case "1": $mes = "Janeiro";break;
			case "02":case "2": $mes = "Fevereiro";break;
			case "03":case "3": $mes = "Março";break;
			case "04":case "4": $mes = "Abril";break;
			case "05":case "5": $mes = "Maio";break;
			case "06":case "6": $mes = "Junho";break;
			case "07":case "7": $mes = "Julho";break;
			case "08":case "8": $mes = "Agosto";break;
			case "09":case "9": $mes = "Setembro";break;
			case "10":case "10": $mes = "Outubro";break;
			case "11":case "11": $mes = "Novembro";break;
			case "12":case "12": $mes = "Dezembro";break;
		}

		return "$arr[d] de $mes de $arr[Y]";
	}



	/**
	 * M�todo que converte a data do formato humano para do banco de dados.
	 *
	 * @param string $dataIn
	 * @param string $separador
	 * @param string $formato
	 * @return string
	 */
	public static function convertDataNormal2DB($dataIn,$separador="/[-\/ ]/",$formato='dmY') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		$itens = preg_split($separador,$dataIn);
		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = $item;
			$contador++;
		}
		return "$arr[Y]-$arr[m]-$arr[d]";
	}

	/**
	 * m�todo que converte o cep do formato humano para o banco de dados
	 * @param string $cep
	 */
	public static function converteCepNormal2DB($cep)
	{
		$cep = str_replace("-","",str_replace(" ","",trim($cep)));
		return $cep;
	} // eof converteCepNormal2DB

	/**
	 * m�todo que converte o cep vindo do banco de dados para o padrao humano
	 * @param string $cep
	 */
	public static function converteCepDB2Normal($cep)
	{
		$cep = str_replace("-","",str_replace(" ","",trim($cep)));
		$aux1 = substr($cep,0,5);
		$aux2 = substr($cep,5,3);
		return "$aux1-$aux2";
	} // eof converteCepDB2Normal

	/**
      * m�todo que ir� limpar a string, retirando aspas e caracteres estranhos
      *
      * @param string $string
      */
	public static function limpaString($string)
	{
		$string = trim(strtoupper(strtr($string,"�������������������������:,;()-%+@*][#!^~<>{}=","RAAASAIEIDECEEONOOOSUUUUCC                     ")));
		return $string;
	} // eof limpaString

	public static function getCumprimentoPeriodo()
	{
		$hora = date("G");
		if (($hora>=0)&&($hora<=12))
		{
			$cumprimento =  "Bom dia";
		}
		else if (($hora>12)&&($hora<=18))
		{
			$cumprimento = "Boa Tarde";
		}
		else if(($hora>18)&&($hora<=23))
		{
			$cumprimento= "Boa noite";
		}
		$cumprimento .= " Dr.(a) ";

		return $cumprimento;
	} // eof getCumprimentoPeriodo



	public static function convertTimestampNormal2DB($dataIn,$separador="/[-\/ :.]/",$formato='dmYHisx') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		$itens = preg_split($separador,$dataIn);
		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = $item;
			$contador++;
		}
		return "$arr[Y]-$arr[m]-$arr[d] $arr[H]:$arr[i]:$arr[s]";
	}


	public static function convertTimestampDB2Normal($dataIn,$separador="/[- :.]/",$formato='YmdHisx') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		$itens = preg_split($separador,$dataIn);
		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = $item;
			$contador++;
		}
		return "$arr[d]/$arr[m]/$arr[Y] $arr[H]:$arr[i]:$arr[s]";
	}


	protected static function quebraDataHora($dataIn,$separador="/[- :.]/",$formato='YmdHisx') {
		if (is_null($dataIn) || (strlen(trim($dataIn))==0) || ($dataIn === false)) {
			return null;
		}
		$itens = preg_split($separador,$dataIn);
		$arr = array();
		$contador = 0;
		foreach ($itens as $item) {
			$k = $formato[$contador];
			$arr[$k] = intval($item);
			$contador++;
		}
		return $arr;
	}




	public static function convertTimestampDB2NormalGMT($dataIn,$gmt=-3,$separador="[- :.]",$formato='YmdHis') {
		$arr = Gama3Utils::quebraDataHora($dataIn,$separador,$formato);
//		$dh = strtotime($dataIn);
		if (is_null($arr)) {
			return null;
		} else {
//			print_r($arr);
//			MainGama::getDebug(true)->log($dataIn,'convertTimestampDB2NormalGMT');
//			MainGama::getDebug(true)->log($arr,'convertTimestampDB2NormalGMT');
//			$arr['H'] += $gmt;
			$arr['H'] += $gmt;
			$dh = mktime($arr['H'],$arr['i'],$arr['s'],$arr['m'],$arr['d'],$arr['Y']);
			return strftime("%d/%m/%Y %H:%M:%S",$dh);

//			return "$arr[d]/$arr[m]/$arr[Y] $arr[H]";
		}
	}



	public static function convertTimestampNormal2DB_GMT($dataIn,$gmt=-3,$separador="[-/ :.]",$formato='dmYHisx') {
		$arr = Gama3Utils::quebraDataHora($dataIn,$separador,$formato);
		if (is_null($arr)) {
			return null;
		} else {
//			print_r($arr);
//			MainGama::getDebug(true)->log($dataIn,'convertTimestampDB2NormalGMT');
//			MainGama::getDebug(true)->log($arr,'convertTimestampDB2NormalGMT');
//			$arr['H'] += $gmt;
			$arr['H'] += $gmt;
			$dh = mktime($arr['H'],$arr['i'],$arr['s'],$arr['m'],$arr['d'],$arr['Y']);
			return strftime("%Y-%m-%d %H:%M:%S",$dh);
		}
	}



	/**
	 * M�todo usado para converter o timestamp vindo do banco de dados para o formato
	 * "natural", considerando a data GMT em rela��o a local. Por isso � 'reversa';
	 * Na pr�tica, significa que eu pego a data/hora da minha localiza��o atual, e
	 * quero saber qual a hora GMT correspondente.
	 *
	 * @param string $dataIn
	 * @param int $gmt
	 * @param string $separador
	 * @param string $formato
	 * @return string
	 */
	public static function convertTimestampDB2NormalGMT_Reversa($dataIn,$gmt=-3,$separador="[-/ :.]",$formato='YmdHisx') {
		$gmt *= -1;
		return Gama3Utils::convertTimestampDB2NormalGMT($dataIn,$gmt,$separador,$formato);
	} // eof convertTimestampDB2NormalGMTReversa


	/**
	 *
	 * @param unknown_type $dataIn
	 * @param unknown_type $gmt
	 * @param unknown_type $separador
	 * @param unknown_type $formato
	 * @return unknown
	 */
	public static function convertTimestampNormal2DB_GMT_Reversa($dataIn,$gmt=-3,$separador="[-/ :.]",$formato='dmYHisx') {
		$gmt *= -1;
		return Gama3Utils::convertTimestampNormal2DB_GMT($dataIn,$gmt,$separador,$formato);
	} // eof convertTimestampNormal2DBGMTReversa


	public static function convertTimestampDB2DB_GMT($dataIn,$gmt=-3,$separador="[-/ :.]",$formato='YmdHisx') {
		$arr = Gama3Utils::quebraDataHora($dataIn,$separador,$formato);
		if (is_null($arr)) {
			return null;
		} else {
			$arr['H'] += $gmt;
			$dh = mktime($arr['H'],$arr['i'],$arr['s'],$arr['m'],$arr['d'],$arr['Y']);
			return strftime("%Y-%m-%d %H:%M:%S",$dh);
		}
	} // eof convertTimestampDB2DB_GMT


        public static function getContentMimeType($filename){
            $mime_types = array(

                'txt' => 'text/plain',
                'htm' => 'text/html',
                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'swf' => 'application/x-shockwave-flash',
                'flv' => 'video/x-flv',

                // images
                'png' => 'image/png',
                'jpe' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'ico' => 'image/vnd.microsoft.icon',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'svgz' => 'image/svg+xml',

                // archives
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                'exe' => 'application/x-msdownload',
                'msi' => 'application/x-msdownload',
                'cab' => 'application/vnd.ms-cab-compressed',

                // audio/video
                'mp3' => 'audio/mpeg',
                'qt' => 'video/quicktime',
                'mov' => 'video/quicktime',

                // adobe
                'pdf' => 'application/pdf',
                'psd' => 'image/vnd.adobe.photoshop',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'ps' => 'application/postscript',

                // ms office
                'doc' => 'application/msword',
                'docx' => 'application/msword',
                'rtf' => 'application/rtf',
                'xls' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',

                // open office
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            );

            $ext = strtolower(array_pop(explode('.',$filename)));
            if (array_key_exists($ext, $mime_types)) {
                return $mime_types[$ext];
            }
            elseif (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME);
                $mimetype = finfo_file($finfo, $filename);
                finfo_close($finfo);
                return $mimetype;
            }
            else {
                return 'application/octet-stream';
            }
        }

        /**
         * Esta fun��o faz o download de um arquivo para o diret�rio passado por par�metro.
         * Se o arquivo j� existir e o parametro overwrite for true (default) sobreescreve o arquivo existente.
         * Se o arquivo j� existir e o parametro overwrite for false salva o arquivo com outro nome.
         * O retorno desta fun��o � nome do arquivo salvo.
         *
         * @param string $directory
         * @param boolean $overwrite
         * @return string $file_name
         */
        public static function doUploadFile($directory,$overwrite=true,$utf8=false){
            $MAX_FILENAME_LENGTH = 260;

            $max_file_size_in_bytes = 2147483647; // 2GB in bytes

            $POST_MAX_SIZE = ini_get('post_max_size');
            $unit = strtoupper(substr($POST_MAX_SIZE, -1));
            $multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

            if((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE)
            {
                    throw new Exception('Tamanho do arquivo excede o m�ximo permitido');
            }

            $file_name = '';
            $uploadErrors = array(
                    0 => 'Enviado com sucesso',
                    1 => 'Tamanho do arquivo excede o máximo permitido',
                    2 => 'Tamanho do arquivo excede o máximo permitido',
                    3 => 'Arquivo corrompido',
                    4 => 'Nenhum arquivo enviado',
                    6 => 'Faltando diretório temporário'
            );

            if(!isset($_FILES["Filedata"]))
                    throw new Exception("Arquivo não encontrado");
            else if(isset($_FILES["Filedata"]["error"]) && $_FILES["Filedata"]["error"] != 0)
                    throw new Exception($uploadErrors[$_FILES["Filedata"]["error"]]);
            else if(!isset($_FILES["Filedata"]["tmp_name"]) || !@is_uploaded_file($_FILES["Filedata"]["tmp_name"]))
                    throw new Exception('Arquivo n�o encontrado');
            else if(!isset($_FILES["Filedata"]['name']))
                    throw new Exception('Arquivo com nome inv�lido');

            $file_size = @filesize($_FILES["Filedata"]["tmp_name"]);
            if(!$file_size || $file_size > $max_file_size_in_bytes)
                    throw new Exception('Tamanho do arquivo excede o m�ximo permitido');

            if($file_size <= 0)
                    throw new Exception('Arquivo inv�lido ou corrompido');

			$file_name = $_FILES["Filedata"]['name'];
            if($utf8)
				$file_name = utf8_decode($file_name);
			$file_name = strtolower(Gama3Utils::limpaString(basename($file_name)));
            $file_name = str_replace(' ', '_', $file_name);

            if(strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH)
                    throw new Exception('Nome de arquivo inválido');

            if(!$overwrite){
                // Se o arquivo j� existe, ele adiciona um n�mero no final do arquivo
                $counter = 0;
                while(file_exists($directory . $file_name))
                {
                    $counter++;
                    $file_name = $counter == 1 ? str_replace('.', '(1).', $file_name) : str_replace('('.($counter-1).')','('.($counter).')', $file_name);
                }
            }

            if(!@move_uploaded_file($_FILES["Filedata"]["tmp_name"], $directory.$file_name))
                    throw new Exception("Erro ao salvar arquivo");

            return $file_name;
        }

        /**
         * Esta fun��o move um arquivo de um diret�rio para outro diret�rio.
         * Se o arquivo j� existir no diret�rio destino e o parametro overwite for false (default) salva o arquivo com nome diferente.
         * Se o parametro overwrite dor true substitui o arquivo existente.
         * Esta fun��o retorno o nome do arquivo salvo.
         *
         * @param string $sourcePath //Caminho completo do arquivo de origem
         * @param string $fileName //Nome do arquivo de origem
         * @param string $targetDirectory //Diret�rio destino.
         * @param boolean $overwrite //Define se o arquivo deve ser sobrescrito se j� existir no diret�rio destino.
         * @param boolean $removeSource //Define se o arquivo de origem deve ser exclu�do ap�s ser copiado.
         * @return string $fileName //Nome do arquivo salvo.
         */
        public static function moveFile($sourcePath,$fileName,$targetDirectory,$overwrite=false,$removeSource=true){
            if(!file_exists($sourcePath))
                throw new Exception ("Arquivo n�o encontrado.", -999, "");
            if(!$overwrite){
                // Se o arquivo j� existe, ele adiciona um n�mero no final do arquivo
                $counter = 0;
                while(file_exists($targetDirectory . $fileName))
                {
                    $counter++;
                    if(preg_match("/(\(\d+\))(\.\w+)$/", $fileName)){
                        $fileName = preg_replace("/(\(\d+\))(\.\w+)$/", "($counter)$2", $fileName);
                    }else{
                        $fileName = preg_replace("/(\.\w+)$/", "($counter)$1", $fileName);
                    }
                }
            }
            copy($sourcePath, $targetDirectory.$fileName);
            if($removeSource){
                unlink($sourcePath);
            }
            return $fileName;
        }




        public static function print_r($var,$xml=false) {

            for ($i=0;$i<10;$i++) {
                ob_end_clean();
            }

            if (is_array($var) && $xml) {
                $c = new _multidi_array2xml();
                $s = $c->array2xml($var);
                header('Content-type: text/xml');
                echo $s;
                exit;
            } else {
                echo '<pre>';
                print_r($var);
                exit;
            }
        }



} // eoc Gama3Utils

//
//$dt = '2009-10-25 15:32:48';
//
//echo Gama3Utils::convertDataHoraDB2Normal($dt);


//
//$dtDB = '2009-04-30 22:35:05';
//$dtN = '30/04/2009 22:35:05';

//echo Gama3Utils::convertTimestampDB2DB_GMT($dtDB);

/*

//echo Gama3Utils::convertTimestampDB2NormalGMT($dt);
//echo Gama3Utils::convertTimestampNormal2DB_GMT($dt);

echo Gama3Utils::convertTimestampNormal2DB($dtN);
echo "\n";
echo Gama3Utils::convertTimestampNormal2DB_GMT_Reversa($dtN);
echo "\n";
echo Gama3Utils::convertTimestampNormal2DB_GMT($dtN);
echo "\n------------------\n";

echo Gama3Utils::convertTimestampDB2Normal($dtDB);
echo "\n";
echo Gama3Utils::convertTimestampDB2NormalGMT($dtDB);
echo "\n";
echo Gama3Utils::convertTimestampDB2NormalGMT_Reversa($dtDB);

*/




class _multidi_array2xml {
    /**
     * Parse multidimentional array to XML.
     *
     * @param array $array
     * @return string    XML
     */
    var $XMLtext;

    public function array2xml($array, $output=true) {
        //star and end the XML document
        $this->XMLtext="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<array>\n";
        $this->array_transform($array);
        $this->XMLtext .="</array>";
        if($output) return $this->XMLtext;
    }
    public function SaveXml($src){
        $myFile = "testFile.txt";
        $fh = @fopen($src, 'w');
        if($fh){
            fwrite($fh, $this->XMLtext);
            fclose($fh);
            return true;
        }else {
            return false;
        }

    }
    private function array_transform($array){
        static $Depth;

        foreach($array as $key => $value){
            $key = strtr($key, '# @+-$%&!?*()/\\', '_________________');
            if(is_scalar($value)){
                    unset($Tabs);
                    for($i=1;$i<=$Depth+1;$i++) $Tabs .= "\t";
                    if(preg_match("/^[0-9]+\$/",$key)) $key = "N_$key";
//                    $value = trim($value);
                    $this->XMLtext .= "$Tabs<$key>$value</$key>\n";
            } else {

                $Depth += 1;
                unset($Tabs);
                for($i=1;$i<=$Depth;$i++) $Tabs .= "\t";

                if (is_object($value)) {



                    //$obj = array();

                    $Depth++;
                    $novoArray = array( get_class($value) =>  get_object_vars($value));

//                    echo '<pre>';
//                    print_r($novoArray);
//                    exit;

                    $this->array_transform($novoArray);
                    $Depth--;
                    /*
                    if ($key == 'xpto') {
                    echo '<pre>';
                    echo "\n $key  \n";
                    print_r($novoArray);
                    exit;
                    }

                    //$value = get_object_vars($object);
                    $key = get_class($value);
                    $value = get_object_vars($value);

//                    $this->array_transform($arr);

                    for($i=1;$i<=$Depth+1;$i++) $Tabs .= "\t";

                    if(!preg_match("/(-ATTR)\$/", $key)) {
                        if(preg_match("/^[0-9]+\$/",$key)) $keyval = "N_$key"; else $keyval = $key;
                        $closekey = $keyval;
                        if(is_array($array[$key."-ATTR"])){
                            foreach ($array[$key."-ATTR"] as $atrkey => $atrval ) $keyval .= " ".$atrkey."=\"$atrval\"";
                        }
                        $this->XMLtext.="$Tabs<$keyval>\n";
                        $this->array_transform($value);
                        $this->XMLtext.="$Tabs</$closekey>\n";
                        $Depth -= 1;

                    } */

                } else {



                    //search for atribut like [name]-ATTR to put atributs to some object
                    if(!preg_match("/(-ATTR)\$/", $key)) {
                        if(preg_match("/^[0-9]+\$/",$key)) $keyval = "N_$key"; else $keyval = $key;
                        $closekey = $keyval;
                        if(is_array($array[$key."-ATTR"])){
                            foreach ($array[$key."-ATTR"] as $atrkey => $atrval ) $keyval .= " ".$atrkey."=\"$atrval\"";
                        }

                        if (is_null($value) || is_string($value) || is_numeric($value)) {
                            $this->XMLtext.="$Tabs<$keyval>";
                            $this->XMLtext.= trim($value) .  "</$keyval>\n";
                        } else {
                            $this->XMLtext.=  "$Tabs<$keyval>\n";
//                            echo '<pre>';
//                            echo $keyval;
//                            echo '<hr>';
//
//                            print_r($value);
//                            exit;

                            $this->array_transform($value);
                            $this->XMLtext.="$Tabs</$closekey>\n";
                        }

                    }
                }
                $Depth -= 1;
            }
        }
        return true;
    }
}


?>