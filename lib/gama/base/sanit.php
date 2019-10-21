<?php

//if(!function_exists("Trata_Post_Get____Vars")) {
function Trata_Post_Get____Vars($var) {


	$array_tradutor[";"] = ";_";
	$array_tradutor["&"] = " e ";
	$array_tradutor["="] = " igual a(o) ";
	$array_tradutor["'"] = " ";
	$array_tradutor["\\"] = "| ";
	$array_tradutor['"'] = " ";
	$array_tradutor["./"] = ".//. ";
	$array_tradutor[" --"] = " - - ";

	$array_tradutor2[" QUERY("] = " QUE_RY(";
	$array_tradutor2[" UNLINK("] = " UN_LINK(";
	$array_tradutor2[" EXEC("] = " EX_EC(";
	$array_tradutor2[" SYSTEM("] = " SYS_TEM(";
	$array_tradutor2[" INSERT "] = " IN_SERT ";
	$array_tradutor2[" DELETE "] = " DE_LETE ";
	$array_tradutor2[" UPDATE "] = " UP_DATE ";
	$array_tradutor2[" FROM "] = " FR_OM ";
	$array_tradutor2[" WHERE "] = " WH_ERE ";
	$array_tradutor2[" ALTER "] = " AL_TER ";
	$array_tradutor2[" DROP "] = " DR_OP ";
	$array_tradutor2[" RM "] = " R_M ";
	$array_tradutor2[" RMDIR "] = " RM_DIR ";
	$array_tradutor2[" CP "] = " C_P ";
	$array_tradutor2[" CHMOD "] = " CH_MOD ";
	$array_tradutor2[" SSH "] = " S_SH ";
	$array_tradutor2[" TAR "] = " T_AR ";
	$array_tradutor2[" WGET "] = " W_GET ";
	$array_tradutor2[" HTTP"] = " HTTP_";
	$array_tradutor2[" FTP"] = " FTP_";
	$array_tradutor2[" WWW"] = " WWW_";

	$var = addslashes( $var );

	foreach($array_tradutor2 as $key=>$val){
		$var = str_ireplace($key,$val,$var);
	}

	return $var;
}// fim da funcao trata_post_vars



$arr = array('id' => 1, 'usuario' => 'eduardo', 'senha' => ' INSERT into tabela \' "teste" ');
/*
foreach ($arr as $k => $v) {
	print_r(Trata_Post_Get____Vars($v));
	echo "\n";
}*/




print_r(array_map('Trata_Post_Get____Vars',$arr));




?>