<?phpclass UtilsFile {
	private function __construct(){	}	public static function recuperaMensagens($nTipo, $sTitulo, $sMsg) {		/*		 $aErros = array("SQLSTATE","column");

		foreach ($aErros as $erro) {
		if (strpos($sMsg, $erro) !== false) {
		$sMsg = str_replace("\n", "<br />", $sMsg);
		$sMsg .= "<br /><br /><i>Por favor informar este erro a DITEC.</i>";
		}
		}		*/		/*		 * 1 - SUCESSO		* 2 - ERRO		* 3 - WARNING		* D - PADRÃO/INFO		*/		switch ($nTipo) {			case 1:
				$sTipo = "alert-success";
				$sAlt = "Sucesso";
				break;			case 2:				$sTipo = "alert-error";				$sAlt = "Error";				break;			case 3:
				$sTipo = "";
				$sAlt = "Atenção";
				break;			default:				$sTipo = "alert-info";
				$sAlt = "Informação";				break;		}		$sLayout = '<div id="msg" class="container-fluid"><div class="row-fluid"><div class="span12"><div class="alert '.$sTipo.' alert-block"><a class="close" data-dismiss="alert" href="#">×</a><h4 class="alert-heading">'.$sTitulo.'</h4>'.$sMsg.'</div></div></div></div>';		return $sLayout;	}	public static function recuperaMensagensSite($nTipo, $sTitulo, $sMsg) {

		/*
		 * 1 - SUCESSO
		* 2 - ERRO		* 3 - WARNING		* Default - INFORMATIVO
		*/
		switch ($nTipo) {
			case 1:
				$sTipo = "notify-success";
				break;
			case 2:
				$sTipo = "notify-error";
				break;			case 3:				$sTipo = "notify-warning";				break;			default:				$sTipo = "";				break;
		}

		$sLayout = '				<div class="notify '.$sTipo.'">						<a href="javascript:;" class="close">&times;</a>						<h4'.$sTitulo.'</h4>								<p>'.$sMsg.'</p>										</div>
										';

		return $sLayout;
	}	/**
	 *  Mesma funcao do printdie mas não imprime com formatacao html
	 * facilitando a exibicao no firebug
	 * @param <type> $args
	 * @since 25/05/2009
	 * @author Philipe Barra
	 */

	public static function printVarDieAjax($args) {
		$args = func_get_args();
		$dbt = debug_backtrace();
		$linha   = $dbt[0]['line'];
		$arquivo = $dbt[0]['file'];
		echo "=================================================================================\n";
		echo "Arquivo:".$arquivo."\nLinha:$linha\nDebug On : printvardieajax ( )\n";
		echo "=================================================================================\n";

		foreach($args as $idx=> $arg) {
			echo "-----  ARG[$idx]  -----\n";
			print_r($arg);
			echo "\n \n";
		}
		die();
	}	public static function verificaArrayVazio($vCampos,$sChave,$sNmChave){		$vChave  = explode(",",$sChave);		$vNmChave  = explode(",",$sNmChave);		$nCont = 0;		foreach($vCampos as $sAtributo => $sValor){			if (in_array($sAtributo,$vChave)){				if (UtilsFile::verificaVazio($sValor)){					$sMsg .= "$vNmChave[$nCont],\n";				}				$nCont++;			}		}		$sMsg = substr($sMsg,0,strlen($sMsg) - 2);		if ($sMsg){			$sMsgFinal = "O(s) seguinte(s) campo(s) precisa(m) ser preenchido(s):\n".$sMsg;		}		return $sMsgFinal;	}	public static function verificaVazio($sCampo){		if ($sCampo == ""){			return true;		}		return false;	}
	public static function delete_directory($path){		$dir = new RecursiveDirectoryIterator($path);		//Remove all files		foreach(new RecursiveIteratorIterator($dir) as $file){			unlink($file);		}		//Remove all subdirectories		foreach($dir as $subDir){			//If a subdirectory can't be removed, it's because it has subdirectories, so recursiveRemoveDirectory is called again passing the subdirectory as path			if(!@rmdir($subDir)){				self::delete_directory($subDir);			}		}		//Remove main directory		@rmdir($path);		return true;	}
	public static function formatNome($frase){		$palavras = str_word_count($frase, 1);		$count_palavras = str_word_count($frase);		for($i=0; $i < $count_palavras; $i++){			$palavra = (strlen($palavras[$i]) > 2) ? (ucwords(strtolower($palavras[$i]))) : (strtolower($palavras[$i]));			$nova_frase = ($i < $count_palavras) ? $palavra." " : $palavra;			print $nova_frase;		}	}
	public static function getIP(){		return gethostbyname($_SERVER['HTTP_X_FORWARDED_FOR']);	}
	function desabilitaTeclaF5(){ #ADD EM TODOS OS ARUIVOS (res...php)		$html =  print('<html>');		$html .= print('<head>');		$html .= print('<script type="text/javascript" src="../js/shortcuts.js"></script>');		$html .= print('<script type="text/javascript">');		$html .= print('function arquivo(arq){'); #recebe o nome_do_arquivo.php (res...)		$html .= print('var file = arq;');		$html .= print('var res = file.substring(0, 3);');#separa as 3 primeiras letras (res)		$html .= print('if(res == "res"){'); #faz a codi��o		$html .= print('shortcut.add("F5",function(){'); #ativa a tecla F5 apenas para enviar o alert abaixo		$html .= print('alert("VOC\xCA REALIZOU UMA OPERA\xC7\xC3O ILEGAL.");');		$html .= print('return true;');		$html .= print('});');		$html .= print('}');		$html .= print('}');		$html .= print('</script>');		$html .= print('');		$html .= print('</head>');		$html .= print('');		$html .= print('<body>');		$html .= print('</body>');		$html .= print('</html>');
		$arquivo = explode("/",$_SERVER['PHP_SELF']); #pega o caminho do arquivo		$arq = $arquivo[4]; #pega o nome_do_arquivo.php, neste caso o arquivo(resFrm...php)		print"<script>arquivo('".$arq."');</script>"; #chama a fun��o js	}	function RemoveAcentos ($sString="", $mesma=1) {		if($sString != "") {			$com_acento = "à á â ã ä è é ê ë ì í î ï ò ó ô õ ö ù ú û ü À �? Â Ã Ä È É Ê Ë Ì �? Î Ò Ó Ô Õ Ö Ù Ú Û Ü ç Ç ñ Ñ";			$sem_acento = "a a a a a e e e e i i i i o o o o o u u u u A A A A A E E E E I I I O O O O O U U U U c C n N";			$c = explode(' ',$com_acento);			$s = explode(' ',$sem_acento);			$i=0;			foreach ($c as $letra) {				if (ereg($letra, $sString)) {					$pattern[] = $letra;					$replacement[] = $s[$i];				}				$i=$i+1;			}			if (isset($pattern)) {				$i=0;				foreach ($pattern as $letra) {					$sString = eregi_replace($letra, $replacement[$i], $sString);					$i=$i+1;				}				return $sString;			}			if ($mesma != 0) {				return $sString;			}		}		return "";	}
	function upload($arquivo,$caminho,$tmp_name){		if(!(empty($arquivo))){			$arquivo1 = $arquivo;			$arquivo_minusculo = strtolower($arquivo);			$caracteres = array("ç","~","^","]","[","{","}",";",":","´",",",">","<","-","/","|","@","$","%","ã","â","á","à","é","è","ó","ò","+","=","*","&","(",")","!","#","?","`","ã"," ","©");			$arquivo_tratado = str_replace($caracteres,"",$arquivo_minusculo);			$destino = $caminho."/".$arquivo_tratado;			if(move_uploaded_file($tmp_name,$destino)){				return true;			}else{				return false;			}		}	}
	function GeradorSenha($tipo="L N L N L N") {		/* o explode retira os espa�os presentes entre as letras (L) e n�meros (N) */		$tipo = explode(" ", $tipo);		/* Cria��o de um padr�o de letras e n�meros (no meu caso, usei letras mai�sculas		 * mas voc� pode intercalar maiusculas e minusculas, ou adaptar ao seu modo.)		*/		$padrao_letras = "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|X|W|Y|Z";		$padrao_numeros = "0|1|2|3|4|5|6|7|8|9";		/* criando os arrays, que armazenar�o letras e n�meros		 * o explode retire os separadores | para utilizar as letras e n�meros		*/		$array_letras = explode("|", $padrao_letras);		$array_numeros = explode("|", $padrao_numeros);		/* cria a senha baseado nas informa��es da fun��o (L para letras e N para n�meros) */		$senha = "";		for ($i=0; $i<sizeOf($tipo); $i++) {			if ($tipo[$i] == "L") {				$senha.= $array_letras[array_rand($array_letras,1)];			} else {				if ($tipo[$i] == "N") {					$senha.= $array_numeros[array_rand($array_numeros,1)];				}			}		}		// informa qual foi a senha gerada para o usu�rio naquele momento		//echo "A senha gerada &#233;: " . $senha;		return $senha;	}	public static function apagaFiles($arquivo, $diretorio) {
				if(is_dir($diretorio)) {
			if($handle = opendir($diretorio)) {
				while(($file = readdir($handle)) !== false) {
					if($file != '.' && $file != '..') {
						if( $file != $arquivo) {
							unlink($diretorio.$file);
						}
					}
				}
			}
		}
		else {
			die("Erro ao abrir dir: $dir");
		}
				return 0;
	}	public static function parse_utf8_url($url) {		static $keys = array('scheme'=>0,'user'=>0,'pass'=>0,'host'=>0,'port'=>0,'path'=>0,'query'=>0,'fragment'=>0);		if (is_string($url) && preg_match(				'~^((?P<scheme>[^:/?#]+):(//))?((\\3|//)?(?:(?P<user>[^:]+):(?P<pass>[^@]+)@)?(?P<host>[^/?:#]*))(:(?P<port>\\d+))?' .				'(?P<path>[^?#]*)(\\?(?P<query>[^#]*))?(#(?P<fragment>.*))?~u', $url, $matches))		{			foreach ($matches as $key => $value)				if (!isset($keys[$key]) || empty($value))				unset($matches[$key]);			return $matches;		}		return false;	}
	public static function printvar($args) {
		$args = func_get_args();
		$dbt = debug_backtrace();
		$linha   = $dbt[0]['line'];
		$arquivo = $dbt[0]['file'];
		echo "<fieldset style='border:1px solid; border-color:#F00;background-color:#FFF000;legend'><b>Arquivo:</b>".$arquivo."<b><br>Linha:</b><legend><b>Debug On : printvar ( )</b></legend> $linha</fieldset>";
		$args = func_get_args();
		foreach($args as $idx=> $arg) {
			echo "<fieldset style='background-color:#CBA; border:1px solid; border-color:#00F;'><legend><b>ARG[$idx]</b></legend>";
			echo "<pre style='background-color:#CBA; width:100%; heigth:100%;'>";
			print_r($arg);
			echo "</pre>";
			echo "</fieldset><br>";
		}
	}	public static function printvardie($args) {
		$args = func_get_args();
		$dbt = debug_backtrace();
		$linha   = $dbt[0]['line'];
		$arquivo = $dbt[0]['file'];
		echo "<fieldset style='border:1px solid; border-color:#F00;background-color:#FFF000;legend'><b>Arquivo:</b>".$arquivo."<b><br>Linha:</b><legend><b>Debug On : printvardie ( )</b></legend> $linha</fieldset>";

		foreach($args as $idx=> $arg) {
			echo "<fieldset style='background-color:#CBA; border:1px solid; border-color:#00F;'><legend><b>ARG[$idx]</b></legend>";
			echo "<pre style='background-color:#CBA; width:100%; heigth:100%;'>";
			print_r($arg);
			echo "</pre>";
			echo "</fieldset><br>";
		}
		die();
	}		public static function realizaUpload($nmArquivo,$destino,$formato=NULL,$tamanho=2000000) {		$oUpload = new Zend_File_Transfer_Adapter_Http();				//VALIDANDO FORMATOS		switch ($formato) {			case "img":				$oUpload->addValidator('MimeType', false, array('image/gif','image/jpeg','image/png','image,jpg'));			break;		}				//RENOMEAR		$originalFilename = pathinfo($oUpload->getFileName());
		$novoNmArquivo = $nmArquivo.'.'.$originalFilename['extension'];		$oUpload->addFilter('Rename', $novoNmArquivo);				//VALIDANDO TAMANHOS		$oUpload->addValidator('FilesSize', false, $tamanho);				//VALIDANDO DIRETORIO
		$oUpload->setDestination($destino);		
		$files = $oUpload->getFileInfo();
		foreach ($files as $file => $info) {
			if ($oUpload->isValid($file)) {
				$oUpload->receive($file);				return $novoNmArquivo;			}			else {				return $oUpload->getMessages();			}
		}	}	

	/**
	 * Função para verificar a segurança de senhas
	 *
	 * @author    Paulo Freitas <paulofreitas dot web at gmail dot com>
	 * @copyright Copyright &copy; 2006, Paulo Freitas
	 * @license   http://creativecommons.org/licenses/by-nc-sa/2.0/br Commons Creative
	 * @version   20060311
	 * @param     string $password senha que deseja verificar
	 * @param     integer $min_len comprimento mínimo da senha
	 * @param     integer $max_len comprimento máximo da senha
	 * @return    bool se a senha é ou não forte
	 */
	
	function checkPassword($password, $min_len = 6, $max_len = 14) {
		if (strlen($password) >= $min_len && strlen($password) <= $max_len) {
			$rules = array('[0-9]',                    // Digits
					'[A-Z]',                    // Uppercase characters
					'[a-z]',                    // Lowercase characters
					'[ -/:-@[-`{-~]',           // Non-alphanumeric characters
					'[^0-9A-Za-z -/:-@[-`{-~]', // Extended characters
			);
	
			$matches = 0;
	
			foreach ($rules as $rule) {
				if (ereg($rule, $password)) ++$matches;
			}
	
			return ($matches >= 3) ? true
			: false;
		} else {
			return false;
		}
	}}