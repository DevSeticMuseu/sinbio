<?php
	private function __construct(){

		foreach ($aErros as $erro) {
		if (strpos($sMsg, $erro) !== false) {
		$sMsg = str_replace("\n", "<br />", $sMsg);
		$sMsg .= "<br /><br /><i>Por favor informar este erro a DITEC.</i>";
		}
		}
				$sTipo = "alert-success";
				$sAlt = "Sucesso";
				break;
				$sTipo = "";
				$sAlt = "Atenção";
				break;
				$sAlt = "Informação";

		/*
		 * 1 - SUCESSO
		* 2 - ERRO
		*/
		switch ($nTipo) {
			case 1:
				$sTipo = "notify-success";
				break;
			case 2:
				$sTipo = "notify-error";
				break;
		}

		$sLayout = '
										';

		return $sLayout;
	}
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
	}
	public static function delete_directory($path){
	public static function formatNome($frase){
	public static function getIP(){
	function desabilitaTeclaF5(){ #ADD EM TODOS OS ARUIVOS (res...php)
		$arquivo = explode("/",$_SERVER['PHP_SELF']); #pega o caminho do arquivo
	function upload($arquivo,$caminho,$tmp_name){
	function GeradorSenha($tipo="L N L N L N") {
		
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
		
	}

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
	}
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
	}
		$novoNmArquivo = $nmArquivo.'.'.$originalFilename['extension'];
		$oUpload->setDestination($destino);
		$files = $oUpload->getFileInfo();
		foreach ($files as $file => $info) {
			if ($oUpload->isValid($file)) {
				$oUpload->receive($file);
		}

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
	}