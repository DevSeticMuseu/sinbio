<?php
class Seg_Permissao extends Zend_Db_Table_Abstract {

	protected $_name			= 'seg_permissao';
	protected $_primary		= array('seg_grupo_usuario_id', 'seg_operacao_id');
	protected $_erroMensagem	= null;

	public function insert($vData, $sOperacao, $nIdUsuario) {
		try {
				
			$vLog = array(
					"operacao"		=> $sOperacao,
					"seg_usuario_id"	=> $nIdUsuario,
					"objeto"		=> serialize($vData),
					"dt_log"			=> date("Y-m-d")
			);
				
			$oLog = new Seg_Log();
				
			$oLog->insert($vLog);

			//UtilsFile::printVarDie($vData, $sOperacao, $nIdUsuario);
			return parent::insert($vData);
		}
		catch (Zend_Db_Exception $e) {
			$this->setErroMensagem($e->getMessage());
			return false;
		}
	}
	
	public function delete($sWhere, $sOperacao, $nIdUsuario) {
		try {
			$vLog = array(
					"operacao"		=> $sOperacao,
					"seg_usuario_id"	=> $nIdUsuario,
					"objeto"		=> "Alterou Permissão",
					"dt_log"			=> date("Y-m-d")
			);
				
			$oLog = new Seg_Log();
			$oLog->insert($vLog);
	
			//UtilsFile::printVarDie($sWhere, $sOperacao, $nIdUsuario);
			return parent::delete($sWhere);
		}
		catch (Zend_Db_Exception $e) {
			$this->setErroMensagem($e->getMessage());
			return false;
		}
	}

	public function setErroMensagem($erroMensagem) {
		$this->_erroMensagem = $erroMensagem;
	}
	
	public function getErroMensagem() {
		return $this->_erroMensagem;
	}
}