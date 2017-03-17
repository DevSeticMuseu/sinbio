<?php

class Expedicao_Expedicao extends Zend_Db_Table_Abstract {

    protected $_name = 'coleta_expedicao';
    protected $_primary = 'id';
    protected $_erroMensagem = null;

    public function fetchAll($sWhere = null, $sOrder = null, $nPagina = null, $nResultadoPagina = null) {
        $sSql = $this->select();

        if ($sWhere)
            $sSql->where($sWhere);

        if ($sOrder)
            $sSql->order($sOrder);

        if ($nPagina && $nResultadoPagina) {
            $nInicio = (($nPagina - 1) * $nResultadoPagina);
            $sSql->limit($nResultadoPagina, $nInicio);
        }

        return parent::fetchAll($sSql);
    }

    public function insert($vData, $sOperacao, $nIdUsuario) {
        try {

            $vLog = array(
                "operacao" => $sOperacao,
                "seg_usuario_id" => $nIdUsuario,
                "objeto" => serialize($vData),
                "dt_log" => date("Y-m-d")
            );

            $oLog = new Seg_Log();

            $oLog->insert($vLog);

            return parent::insert($vData);
        } catch (Zend_Db_Exception $e) {
            //UtilsFile::printVarDieAjax($e);
            $this->setErroMensagem($e->getMessage());
            return false;
        }
    }

    public function update($vData, $sWhere, $sOperacao, $nIdUsuario) {
        try {
            $vLog = array(
                "operacao" => $sOperacao,
                "seg_usuario_id" => $nIdUsuario,
                "objeto" => serialize($vData),
                "dt_log" => date("Y-m-d")
            );
            $oLog = new Seg_Log();
            $oLog->insert($vLog);

            //UtilsFile::printvardie($vData, $sWhere);
            return parent::update($vData, $sWhere);

            //$teste = parent::update($vData,$sWhere);
            //UtilsFile::printvardie(parent::update($vData,$sWhere));
        } catch (Zend_Db_Exception $e) {
            UtilsFile::printvardie($e);
            $this->setErroMensagem($e->getMessage());
            return false;
        }
    }

    public function delete($sWhere, $sOperacao, $nIdUsuario) {
        try {
            $vLog = array(
                "operacao" => $sOperacao,
                "seg_usuario_id" => $nIdUsuario,
                "objeto" => "excluiu expedicao",
                "dt_log" => date("Y-m-d")
            );
            $oLog = new Seg_Log();
            $oLog->insert($vLog);

            //UtilsFile::printvardie($vData, $sWhere);
            return parent::delete($sWhere);
        } catch (Zend_Db_Exception $e) {
            //UtilsFile::printVarDieAjax($e);
            $this->setErroMensagem($e->getMessage());
            return false;
        }
    }
    
    public function findBySitio() {
        $sSql = $this->select()
                ->setIntegrityCheck(FALSE)
                ->from(array('col' => 'coleta_expedicao'))
                ->join(array('loc_s' => 'loc_sitio'), ' col.loc_sitio_id = loc_s.id', array('nm_sitio'));

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

    public function getAllCustom() {
        $sSql = "
				select
						col.id,
						col.coleta_protocolo_id,
						col.coleta_projeto_programa_id,
						col.loc_sitio_id,
						col.data_inicio,
						col.data_fim,
						col.observacao,
						loc_s.nm_sitio as bug,
						col.loc_municipio,
						col.loc_uf_id


						from coleta_expedicao col

						join loc_sitio loc_s  on loc_s.id = col.loc_sitio_id;

					";


        return parent::fetchAll($sSql);
    }

}
