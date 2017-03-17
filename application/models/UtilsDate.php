<?php
/**
 * Classe de manipulação de Datas
 * @author Aptus Solutions <aptussolutions@aptussolutions.com.br>
 * @version 0.5.32
 * @since 01/10/2006
 * @copyright Copyright � 2006, R & G Consultoria e Desenvolvimento de Sistemas Ltda.
 * @access public
 * @package saps
 */

class UtilsDate {

	private function __construct(){
	}
        
        function formata_data($_data) {
    // o array $partes é um dos parâmetros de preg_match(), e retorna os padrões encontrados na string.
    if (preg_match("/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/", $_data, $partes)) {
	$data_formatada = $partes[3]."-".$partes[2]."-".$partes[1];
	return $data_formatada;
    } 
}

	public static function dias_NaoUteis($datainicial, $datafinal){
		if (!isset($datainicial)) return false;
		if (!isset($datafinal)) $datafinal=time();
		
		$segundos_datainicial = strtotime(preg_replace('#(\d{2})/(\d{2})/(\d{4})#','$3/$2/$1',$datainicial));
		$segundos_datafinal = strtotime(preg_replace('#(\d{2})/(\d{2})/(\d{4})#','$3/$2/$1',$datafinal));
		$dias = abs(floor(floor(($segundos_datafinal-$segundos_datainicial)/3600)/24 ) );
		
		
		$aWhile = array();
		$uteis=0;
		$nao_uteis=0;
		for($i=1;$i<=$dias;$i++){
			$diai = $segundos_datainicial+($i*3600*24);	
			$w = date('w',$diai);
			if ($w==0){
				//print(date('d/m/Y',$diai).' Domingo<br />');
				$aWhile[] = date('d/m/Y',$diai);				
				$nao_uteis++;
			}elseif($w==6){
				//print(date('d/m/Y',$diai).' Sabado<br />');
				$aWhile[] = date('d/m/Y',$diai);
				$nao_uteis++;
			}else{
			$uteis++;
			
			}
		}
		//printvardie($aWhile);
		return $aWhile;
	}

	public static function formata_data_extenso($strDate) {
	// Array com os dia da semana em português;
	$arrDaysOfWeek = array('Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','S&aacute;bado');
	// Array com os meses do ano em português;
	$arrMonthsOfYear = array(1 => 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
	// Descobre o dia da semana
	$intDayOfWeek = date('w',strtotime($strDate));
	// Descobre o dia do mês
	$intDayOfMonth = date('d',strtotime($strDate));
	// Descobre o mês
	$intMonthOfYear = date('n',strtotime($strDate));
	// Descobre o ano
	$intYear = date('Y',strtotime($strDate));
	// Formato a ser retornado
	return $arrDaysOfWeek[$intDayOfWeek] . ', ' . $intDayOfMonth . ' de ' . $arrMonthsOfYear[$intMonthOfYear] . ' de ' . $intYear;
}
	
	public static function dias_Uteis($datainicial, $datafinal){
            
		if (!isset($datainicial)) return false;
		if (!isset($datafinal)) $datafinal=time();
		
		$segundos_datainicial = strtotime(preg_replace('#(\d{2})/(\d{2})/(\d{4})#','$3/$2/$1',$datainicial));
		$segundos_datafinal = strtotime(preg_replace('#(\d{2})/(\d{2})/(\d{4})#','$3/$2/$1',$datafinal));
		$dias = abs(floor(floor(($segundos_datafinal-$segundos_datainicial)/3600)/24 ) );
		
		
		$aWhile = array();
		$uteis=0;
		$nao_uteis=0;
		for($i=1;$i<=$dias;$i++){
			$diai = $segundos_datainicial+($i*3600*24);	
			$w = date('w',$diai);
			
			if ($w==0){
				//print(date('d/m/Y',$diai).' Domingo<br />');
				$aWhile[] = date('d/m/Y',$diai);				
				$nao_uteis++;
			}elseif($w==6){
				//print(date('d/m/Y',$diai).' Sabado<br />');
				$aWhile[] = date('d/m/Y',$diai);
				$nao_uteis++;
			}else{
			$dias_uteis[] = date('d/m/Y',$diai);
			$uteis++;
			
			}
		}
		//printvardie($dias_uteis);
		return $dias_uteis;
	}
	
	public static function mesExtensoPortugues($mesNumero){
		$nomeDosMeses = array(NULL,
			'Janeiro', 'Fevereiro', 'Março',
			'Abril'  , 'Maio'     , 'Junho',
			'Julho'  , 'Agosto'   , 'Setembro',
			'Outubro', 'Novembro' , 'Dezembro');
	 
		if(array_key_exists($mesNumero, $nomeDosMeses)){
			return $nomeDosMeses[$mesNumero];
		}
		return "Mes Incorreto";	
	}
	
	public static function FeriadoPascoa($ano=false, $form="d/m/y") {
        $ano=$ano?$ano:date("Y");        
        if ($ano<1583) {
                $A = ($ano % 4);
                $B = ($ano % 7);
                $C = ($ano % 19);
                $D = ((19 * $C + 15) % 30);
                $E = ((2 * $A + 4 * $B - $D + 34) % 7);
                $F = (int)(($D + $E + 114) / 31);
                $G = (($D + $E + 114) % 31) + 1;
                return date($form, mktime(0,0,0,$F,$G,$ano));
        }
        else {
                $A = ($ano % 19);
                $B = (int)($ano / 100);
                $C = ($ano % 100);
                $D = (int)($B / 4);
                $E = ($B % 4);
                $F = (int)(($B + 8) / 25);
                $G = (int)(($B - $F + 1) / 3);
                $H = ((19 * $A + $B - $D - $G + 15) % 30);
                $I = (int)($C / 4);
                $K = ($C % 4);
                $L = ((32 + 2 * $E + 2 * $I - $H - $K) % 7);
                $M = (int)(($A + 11 * $H + 22 * $L) / 451);
                $P = (int)(($H + $L - 7 * $M + 114) / 31);
                $Q = (($H + $L - 7 * $M + 114) % 31) + 1;
              
                return date($form, mktime(0,0,0,$P,$Q,$ano));
        }
	} 
	
	public static function FeriadoCarnaval($ano=false, $form="d/m/Y") {
	    $ano=$ano?$ano:date("Y");
	    
	    $a=explode("/", UtilsDate::FeriadoPascoa($ano));
	    return date($form, mktime(0,0,0,$a[1],$a[0]-47,$a[2]));
	}
	
	public static function FeriadoCorpusChristi($ano=false, $form="d/m/Y") {
	    $ano=$ano?$ano:date("Y");	
	    $a=explode("/", UtilsDate::FeriadoPascoa($ano));
	    return date($form, mktime(0,0,0,$a[1],$a[0]+60,$a[2]));
	}
	
	public static function FeriadoSextaSanta($ano=false, $form="d/m/Y") {
	    $ano=$ano?$ano:date("Y");
	    $a=explode("/", UtilsDate::FeriadoPascoa($ano));
	    return date($form, mktime(0,0,0,$a[1],$a[0]-2,$a[2]));
	} 
	
	
	public static function DateToStrDMY($data){
		setlocale(LC_ALL, "pt_BR", "ptb");
		if($data == ""){
			$dia1 = date("d");
			$mes1 = date("m");
			$ano1 = date("Y");
		}else{
			$aData = explode("-",$data);
			$dia1 = $aData[2];
			$mes1 = $aData[1];
			$ano1 = $aData[0];
		}
		$dia = strftime("%d",mktime(0,0,0,$mes1,$dia1,$ano1));
		$mes = substr(ucwords(strftime("%B",mktime(0,0,0,$mes1,$dia1,$ano1))),0,3);
		$ano = strftime("%Y",mktime(0,0,0,$mes1,$dia1,$ano1));
		return $dia."/".$mes."/".$ano;
	}

	public static function DateToExtenso($data){
		setlocale(LC_ALL, "pt_BR", "ptb");
		if($data == ""){
			$dia = date("d");
			$mes = date("m");
			$ano = date("Y");
		}else{
			$aData = explode("-",$data);
			$dia = $aData[2];
			$mes = $aData[1];
			$ano = $aData[0];
		}
		return strftime("Belém, %A, %d de %B de %Y", mktime(0,0,0,$mes,$dia,$ano));
	}
	
	/*Essa é uma pog para resover o problema de letras com caracteres especiais que não ficam em caixa alta
	 * By PAulo Moura
	 * */
	public static function ConverteAcentos($string){
		$retorno = UtilsDate::StrReplaceChar(array(utf8_decode("ç")=>utf8_decode("Ç"),utf8_decode("á")=>utf8_decode("Á"),utf8_decode("é")=>utf8_decode("É"),
		utf8_decode("í")=>utf8_decode("Í"),utf8_decode("ó")=>utf8_decode("Ó"), utf8_decode("ú")=>utf8_decode("Ú"),utf8_decode("ã")=>utf8_decode("Ã"),
		utf8_decode("õ")=>utf8_decode("Õ"),utf8_decode("à")=>utf8_decode("À"),utf8_decode("è")=>utf8_decode("È"),utf8_decode("ì")=>utf8_decode("Ì"),
		utf8_decode("ò")=>utf8_decode("ò"),utf8_decode("ù")=>utf8_decode("Ù"),utf8_decode("ê")=>utf8_decode("Ê"),utf8_decode("â")=>utf8_decode("Â"),
		utf8_decode("ô")=>utf8_decode("Ô")),$string);
		
		return $retorno;
	}

	public static function StrReplaceChar($array,$string){
		foreach ($array as $key=> $value)
			$string = str_replace($key, $value, $string);
		return $string;
	}

	public static function StrDataToShow($string, $mask){
		if ($string=='') return '';
		$sizeMask = strlen($mask)-1;
		$j = 0;
		$aux = '';

		for ($i=0; $i<=$sizeMask; $i++)
			if ($mask{$i}=='#') {
				$aux .= $string{$j};
				$j++;
			} else {
				$aux .= $mask{$i};
			}
		return $aux;
	}
	
	public static function StrAddChar($string, $char, $length, $position){
		for ($i=strlen($string); $i<=($length-1); $i++)
			$string = ($position=='left') ? $char.$string : $string.$char;

		return $string;
	}
	
	public static function formataDataToBd($data){
		$explodeData = explode('/',$data);
		$hrPadrao = '00:00:00';
		return $explodeData[2].'-'.$explodeData[1].'-'.$explodeData[0].' '.$hrPadrao;
	}
	
	public static function formataDataToShow($data){
		//$data = explode('-',$data);
		$explodeData = explode('-',$data);
		return $explodeData[2].'/'.$explodeData[1].'/'.$explodeData[0];
	}
	public static function formataDataSemHoraToShow($data){
		if ($data=='') return '';
		$time = explode(":",$data);
		$formatTime = explode(" ",$time[0]);
		$dataFormatada = explode("-",$formatTime[0]);		
		return $dataFormatada[2]."/".$dataFormatada[1]."/".$dataFormatada[0];
	}
	
	public static function formataDataToShowWithHour($data){
		if ($data=='') return '';
		$data = explode(' ',$data);
		$explodeData = explode('-',$data[0]);
		return $explodeData[2].'/'.$explodeData[1].'/'.$explodeData[0].' '.$data[1];
	}

	public static function formataHora($data){
		if ($data=='') return '';
		$data = explode(' ',$data);
		$explodeData = explode('-',$data[0]);
		return $data[1];
	}
	public static function formataHoraMinuto($data){
		if ($data=='') return '';
		$data = explode(' ',$data);
		$explodeData = explode('-',$data[0]);
		$horMin = explode(':',$data[1]);
		return $horMin[0].':'.$horMin[1];
//		return $data[1];
	}
	public static function somaData($data,$meses) {
		$data = explode("/",$data);
		$dia = $data[0];
		$mes = $data[1];
		$ano = $data[2];
		
		return date('d/m/Y',mktime(0,0,0,$mes+$meses,$dia,$ano));
	}
	
    public static function somaDiasDataAtual($dias){
		return date('d/m/Y',mktime(0,0,0,date('m'),date('d')+$dias,date('Y')));
	}
    
    public static function somaMesDataAtual($mes){
		return date('d/m/Y',mktime(0,0,0,date('m')+$mes,date('d'),date('Y')));
	}
    
    public static function somaAnoDataAtual($ano){
		return date('d/m/Y',mktime(0,0,0,date('m'),date('d')+$dias,date('Y')+$ano));
	}

	/*nova funcao modificada por Ismael */
	public static function remove_acentos( $texto ){
	$array1 = array(   "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "ẽ", "í", "ì", "î", "ï", "ĩ", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ũ", "ç"
                     , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Ẽ", "Í", "Ì", "Î", "Ï", "Ĩ", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ũ", "Ç" );
 	$array2 = array(   "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "c"
                     , "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "C" );
  		return str_replace( $array1, $array2, $texto );
	} 
	
	/* Adiciona de a quantidade necessaria de anos, ex: "+1 year", "+2 year" e "+3 year" */
	public static function calculaAno($dt,$opcao) {
		$data = new DateTime("$dt");
		$data->modify($opcao);
		return $data->format("d/m/Y");
	}
	
	/* Diferença entre datas (M/D/Y H:i:s) */
	public static function difData($dtInicial, $dtFinal) {
		$dtInicial = strtotime($dtInicial);
		$dtFinal = strtotime($dtFinal);
		$dif = $dtFinal - $dtInicial;
		$data = gmdate("d-H:i:s", $dif);
		$dia = explode("-", $data);
		$dia[0] = $dia[0] - 1;
		($dia[0] > 1) ? $qtDia = $dia[0] . " dias" : $qtDia = $dia[0] . " dia";
		if ($qtDia > 0) {
			return $qtDia . " " . $dia[1];
		} else {
			return $dia[1];
		}
	}

	public static function gamDataQueryString($str){
		if (trim($str) != '') {
			if (!strpos($str, '/')) {
				$str = substr($str,0,2).'/'.substr($str,2,2).'/'.substr($str,4,4);	
			}
		}
		return $str;
	}

	public static function formataMoeda($valor){
		// ======== Sabendo que o separador ï¿½ o ponto(.) ========
		$aPartes = explode(".",$valor);
		// ==== Tratando os Centavos =======
		if($aPartes[1] == 0)
					$aPartes[1] = "00";
		if(strlen($aPartes[1]) == 1)
					$aPartes[1] = $aPartes[1]."0";
		// ==== Tratando o Milhar =======
		switch(strlen($aPartes[0])){
					case 4://0.000
								$milhar = substr($aPartes[0],0,1).".".
														substr($aPartes[0],1,3);
								break;
					case 5://00.000
								$milhar = substr($aPartes[0],0,2).".".
														substr($aPartes[0],2,3);
								break;
					case 6://000.000
								$milhar = substr($aPartes[0],0,3).".".
														substr($aPartes[0],3,3);
								break;
					case 7://0.000.000
								$milhar = substr($aPartes[0],0,1).".".
														substr($aPartes[0],1,3).".".
														substr($aPartes[0],4,3);
								break;
					case 8://00.000.000
								$milhar = substr($aPartes[0],0,2).".".
														substr($aPartes[0],2,3).".".
														substr($aPartes[0],5,3);
								break;
					case 9://000.000.000
								$milhar = substr($aPartes[0],0,3).".".
														substr($aPartes[0],3,3).".".
														substr($aPartes[0],6,3);
								break;
					default:
								$milhar = $aPartes[0];
								break;
		}
		return        $milhar.",".$aPartes[1];
	}

	public static function formataMoedaBD($valor){
		
		$valor = str_replace(',','.',str_replace('.','',$valor));
		
		return $valor;
		
	}
	
	
	//adicionado por Myller
	//fun��o que converte caracteres acentuados para mai�sculas e/ou minusculas
	public static function convertem($term, $tp) {
		
    	if ($tp == "1") $palavra = strtr(strtoupper($term),"àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ","ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß");

    	elseif ($tp == "0") $palavra = strtr(strtolower($term),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß","àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ");

    	return $palavra;

	} 
	
	
	/**
	 * Funcao que soma ou subtrai, dias, meses ou anos de uma data qualquer
	 * @param $date
	 * @param $operation
	 * @param $where
	 * @param $quant
	 * @param $return_format
	 * @return unknown_type
	 */
	public static function operations($date, $operation, $where = FALSE, $quant, $return_format = FALSE)
	{
		// Verifica erros
		$warning = "<br>Warning! Date Operations Fail... ";
		if(!$date || !$operation) {
			return "$warning invalid or inexistent arguments<br>";
		}else{
			if(!($operation == "sub" || $operation == "-" || $operation == "sum" || $operation == "+")) return "<br>$warning Invalid Operation...<br>";
			else {
				// Separa dia, mes e ano
				list($day, $month, $year) = split("/", $date);

				// Determina a operacao (Soma ou Subtra��o)
				($operation == "sub" || $operation == "-") ? $op = "-" : $op = '';

				// Determina aonde sera efetuada a operação (dia, m�s, ano)
				if($where == "day")   $sum_day	 = $op."$quant";
				if($where == "month") $sum_month = $op."$quant";
				if($where == "year")  $sum_year	 = $op."$quant";
				
				// Gera o timestamp
				$date = mktime(0, 0, 0, $month + $sum_month, $day + $sum_day, $year + $sum_year);
				
				// Retorna o timestamp ou extended
				($return_format == "timestamp" || $return_format == "ts") ? $date = $date : $date = date("d/m/Y", "$date");

				// Retorna a data
				return $date;
			}
		}
	}

    public static function formataContaCorrente($cc){
        $cc = trim($cc);
        $cc1 = substr($cc, -1);
        $cc2 = substr_replace($cc, '-', -1);
        return $cc3 = $cc2.$cc1;

    }

public static function extenso($valor = 0, $maiusculas = false) {

        $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
        "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
        "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
        "dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
        "sete", "oito", "nove");

        $z = 0;
        $rt = "";
        
        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);       

        for($i=0;$i<count($inteiro);$i++)
        for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
            $inteiro[$i] = "0".$inteiro[$i];

        $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
        for ($i=0;$i<count($inteiro);$i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
            $ru) ? " e " : "").$ru;
            $t = count($inteiro)-1-$i;
            $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";

            if ($valor == "000")$z++; elseif ($z > 0) $z--;
            if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
            if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        if(!$maiusculas){
            return($rt ? $rt : "zero");
        } else {
            if ($rt) $rt=ereg_replace(" E "," e ",ucwords($rt));
                return (($rt) ? ($rt) : "Zero");
        }

	}
	
	function nomeSobrenome($nomeCompleto){
		$vNome = explode(" ",$nomeCompleto);
		$ultimo = count($vNome) - 1;
		return $vNome[0]." ".$vNome[$ultimo];
	}
	
function whois($domain, $tipo) {
	$server[0] = "whois.registro.br"; //--> Domínios Nacionais
	$server[1] = "whois.internic.net"; //--> Domínios .com, .net, .org, .edu
	$server[2] = "whois.networksolutions.com"; //--> Domínios .aero, .arpa, .biz, .coop, .info, .int, .museum

	$domain = strtolower($domain);
	if (trim($domain) <> "") {
		$domain = trim($domain);
		$final = substr($domain, -4);
		// Verifica dominios do brasil
		if (substr("$domain", -3) == ".br") {
			$br = fsockopen($server[0], 43, $errno, $errstr, 30);
			if (!$br) {
				$dados .= "$errstr ($errno)";
			} else {
				fputs($br, "$domain\r\n");
				while (!feof($br)) {
					$buffer .= fread($br,128);
				}
				if ($tipo == 1) {
					if (strpos($buffer, "No match for") > 0) $dados .= false; else $dados .= true;
				} else {
					$dados .= str_replace("\n","<BR>\n",trim($buffer));

				}
				fclose ($br);
			}
		}
		// Verifica domínios .com, .net, .org, .edu
		elseif (($final == '.com') OR ($final == '.net') OR ($final == '.org') OR ($final == '.edu')) {
			$internic = fsockopen($server[1], 43, $errno, $errstr, 30);
			if (!$internic) {
				$dados .= "$errstr ($errno)";
			}
			else {
				fputs($internic, "$domain\r\n");
				while (!feof($internic)) {
					$buffer .= fread($internic,128);
				}
				if ($tipo == 1) {
					if (strpos($buffer, "No match for") > 0) $dados .= false; else $dados .= true;
				}
				else {
					if (strpos($buffer, "No match for") > 0) {
						$dados .= "<pre>" . trim($buffer) . "</pre>";
					}
					else {
						$servidor = substr($buffer, strpos($buffer, "Whois Server:")+14, strlen($buffer));
						$servidor = substr($servidor, 0, strpos($servidor, "\n"));
						$entidade = fsockopen("$servidor", 43, $errno, $errstr, 30);
						if (!$entidade) {
							$dados .= "$errstr ($errno)";
						}
						else {
							$buffer .= "-------------------------------------------------------------------------------<BR>
 <BR>";
							fputs($entidade, "$domain\r\n");
							while (!feof($entidade)) {
								$buffer .= fread($entidade,128);
							}
							$dados .= str_replace("\n","<BR>\n",trim($buffer));

							fclose ($entidade);
						}
					}
				}
				fclose ($internic);
			}
		// Verifica os outros
		}
		else {
			$network = fsockopen($server[2], 43, $errno, $errstr, 30);
			if (!$network) {
				$dados .= "$errstr ($errno)";
			} else {
				fputs($network, "$domain\r\n");
				while (!feof($network)) {
					$buffer .= fread($network,128);
				}
				if ($tipo == 1) {
					if (strpos($buffer, "NOT FOUND") > 0) $dados .= false; else $dados .= true;
				} else {
					$dados .= str_replace("\n","<BR>\n",trim($buffer));

				}
				fclose ($network);
			}
		}
	}
	return $dados;
}
}
