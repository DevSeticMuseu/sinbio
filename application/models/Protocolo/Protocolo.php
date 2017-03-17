<?php
class Protocolo_Protocolo extends Zend_Db_Table_Abstract {

	protected $_name			= 'coleta_protocolo';
	protected $_primary			= 'id';
	protected $_erroMensagem	= null;




	public function fetchAll($sWhere=null,$sOrder=null,$nPagina=null,$nResultadoPagina=null) {
		$sSql = $this->select()
		->setIntegrityCheck(FALSE)
		->order('sigla ASC');
  //  $sOrder = "ORDER BY sigla";
		if ($sWhere)
			$sSql->where($sWhere);

	//	if ($sOrder)
	//		$sSql->order('sigla ASC');

		if ($nPagina && $nResultadoPagina) {
			$nInicio = (($nPagina-1) * $nResultadoPagina);
			$sSql->limit($nResultadoPagina, $nInicio);
		}

		return parent::fetchAll($sSql);
	}

	public function insert($vData,$sOperacao,$nIdUsuario) {
		try {

			$vLog = array(
				"operacao"		=> $sOperacao,
				"seg_usuario_id"	=> $nIdUsuario,
				"objeto"		=> serialize($vData),
				"dt_log"			=> date("Y-m-d")
			);

			$oLog = new Seg_Log();

			$oLog->insert($vLog);

			return parent::insert($vData);
		}
		catch (Zend_Db_Exception $e) {
			//UtilsFile::printVarDieAjax($e);
			$this->setErroMensagem($e->getMessage());
			return false;
		}
	}

	public function update($vData, $sWhere,$sOperacao, $nIdUsuario) {
		try {
			$vLog = array(
					"operacao"		=> $sOperacao,
					"seg_usuario_id"	=> $nIdUsuario,
					"objeto"		=> serialize($vData),
					"dt_log"			=> date("Y-m-d")
			);
			$oLog = new Seg_Log();
			$oLog->insert($vLog);

			//UtilsFile::printvardie($vData, $sWhere);
			return parent::update($vData,$sWhere);

			//$teste = parent::update($vData,$sWhere);
			//UtilsFile::printvardie(parent::update($vData,$sWhere));
		}
		catch (Zend_Db_Exception $e) {
			UtilsFile::printvardie($e);
			$this->setErroMensagem($e->getMessage());
			return false;
		}
	}

	public function delete($vData, $sWhere,$sOperacao, $nIdUsuario) {
		try {
			$vLog = array(
					"operacao"		=> $sOperacao,
					"seg_usuario_id"	=> $nIdUsuario,
					"objeto"		=> serialize($vData),
					"dt_log"			=> date("Y-m-d")
			);
			$oLog = new Seg_Log();
			$oLog->insert($vLog);

			//UtilsFile::printvardie($vData, $sWhere);
			return parent::delete($sWhere);
		}
		catch (Zend_Db_Exception $e) {
			//UtilsFile::printVarDieAjax($e);
			$this->setErroMensagem($e->getMessage());
			return false;
		}
	}

	public function totalRegistro() {
		$vReg = parent::fetchAll();

		return count($vReg);
	}

	public function setErroMensagem($erroMensagem) {
		$this->_erroMensagem = $erroMensagem;
	}

	public function getErroMensagem() {
		return $this->_erroMensagem;
	}

/*
	public function getOrder() {
		$this->db = Zend_Db_Table::getDefaultAdapter();
				$sql = "
				select  *

					from coleta_protocolo

					ORDER BY sigla;
	";
				return $result = $this->db->fetchAll($sql);

	}
*/




}
