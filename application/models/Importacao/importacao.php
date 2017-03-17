<?php
class Importacao_Importacao extends Zend_Db_Table_Abstract {


	
	public function fetchAll($sWhere=null,$sOrder=null,$nPagina=null,$nResultadoPagina=null) {
		$sSql = $this->select();
	
		if ($sWhere)
			$sSql->where($sWhere);
	
		if ($sOrder)
			$sSql->order($sOrder);
	
		if ($nPagina && $nResultadoPagina) {
			$nInicio = (($nPagina-1) * $nResultadoPagina);
			$sSql->limit($nResultadoPagina, $nInicio);
		}
	
		return parent::fetchAll($sSql);
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
}