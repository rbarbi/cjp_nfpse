<?php// $Rev: 84 $ - $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $
/**
 * Classe que agrupa algumas funcionalidades para facilitar as operações de
 * manipulação de datas.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils.datatime
 */
class DataUtil {


	private $dia = null;
	private $mes = null;
	private $ano = null;


	/**
	 * Construtor.
	 * Recebe como parâmetro um array no formato dd, mm, aaaa
	 *
	 * @param array $data
	 * @return DataUtil
	 */
	function DataUtil($data=null) {
		if (is_array($data)) {
			list($this->dia, $this->mes, $this->ano) = $data;
		}
	} // eof DataUtil


	function getDia() {
		return $this->dia;
	}

	function setDia($dia) {
		$this->dia = $dia;
	}

	function getMes() {
		return intval($this->mes);
	}

	function setMes($mes) {
		$this->mes = $mes;
	}

	function getAno() {
		return intval($this->ano);
	}

	function setAno($ano) {
		$this->ano = $ano;
	}

	function getListaMeses() {
		$arr = array();
		$arr[1] = 'Janeiro';
		$arr[2] = 'Fevereiro';
		$arr[3] = htmlentities('Março');
		$arr[4] = 'Abril';
		$arr[5] = 'Maio';
		$arr[6] = 'Junho';
		$arr[7] = 'Julho';
		$arr[8] = 'Agosto';
		$arr[9] = 'Setembro';
		$arr[10] = 'Outubro';
		$arr[11] = 'Novembro';
		$arr[12] = 'Dezembro';
		return $arr;
	}

	function getNomeMes($numMes=null) {
		$listaMeses = DataUtil::getListaMeses();
		if (is_null($numMes)) {
			$nome = $listaMeses[$this->getMes()];
		} else {
			$nome = $listaMeses[$numMes];
		}
		return $nome;
	}


	function getListaAnos($ano_inicial=null,$ano_final=null) {
		if (is_null($ano_final)) {
			$ano_final = date('Y');
		}
		if (is_null($ano_inicial)) {
			$ano_inicial = $ano_final-9;
		}
		for($x =$ano_final; $x >= $ano_inicial; $x--){
			$num_anos[] = $x;
		}
		return $num_anos;
	}


	function incrementaMes($periodo) {
		$resto = (($this->getMes() + $periodo) % 12);
		$anos = (($this->getMes() + $periodo) - $resto) / 12;
		if ($resto == 0) {
			$this->setAno( $this->getAno() + $anos -1);
			$this->setMes(12);
		} else {
			$this->setAno($this->getAno() + $anos );
			$this->setMes($resto);
		}
	}

	function incrementaAno($periodo) {
		$this->setAno($this->getAno()+$periodo);
	}

	/**
	 * Retorna a diferença, em meses, entre a data do objeto atual e o objeto
	 * DataUtil passada por parâmetro.
	 *
	 * @param DataUtil $data
	 * @return int
	 */
	function getDiferencaMeses($data) {
		if ($this->getMes()==0) {
			$this->setMes(intval(date('m')));
		}
		if ($this->getAno() == 0) {
			$this->setAno(intval(date('Y')));
		}
		$dtAtual = sprintf("%04d%02d",$this->getAno(),$this->getMes());
		$dt = sprintf("%04d%02d",$data->getAno(),$data->getMes());

		$qtd_meses = 0;

		while ($dt < $dtAtual) {
			$qtd_meses++;
			$data->incrementaMes(1);
			$dt = sprintf("%04d%02d",$data->getAno(),$data->getMes());
		}
		return $qtd_meses;
	}

	/**
	 * Retorna a diferença, em anos, entre a data do objeto atual e o objeto
	 * DataUtil passada por parâmetro.
	 *
	 * @param DataUtil $data
	 * @return int
	 */
	function getDiferencaAnos($data) {
		$numMeses = $this->getDiferencaMeses($data);
		$numAnos = intval($numMeses/12);
		return $numAnos;
	}

	/**
	 * Retorna a data do objeto atual no formato aaaammdd
	 *
	 * @return string
	 */
	function getDataAMD() {
		return sprintf('%04d%02d%02d',$this->getAno(),$this->getMes(),$this->getDia());
	}

}



// *******************************




/*
$d1 = new DataUtil();
$d1->setMes(1);
$d1->setAno(2000);


for ($i=0;$i<25;$i++) {
	echo "\n " . $d1->getDataAMD();
	$d1->incrementaMes(5);
}
*/
/*
$d2 = new DataUtil();
$d2->setMes(1);
$d2->setAno(2003);


echo $d2->getDiferencaAnos($d1);

*/


?>