<?php

class Loc_Sitio extends Zend_Db_Table_Abstract {

    protected $_name = 'loc_sitio';
    protected $_primary = 'id';
    protected $_erroMensagem = null;

    public function fetchAll($sWhere = null, $sOrder = null, $sJoin = null, $nPagina = null, $nResultadoPagina = null) {
        $sSql = $this->select();

        if ($sJoin) {
            $sSql->setIntegrityCheck(FALSE);
            $sSql->from(array('sitio' => 'loc_sitio'));
            foreach ($sJoin as $joinStatement) {
                $sSql->joinLeft($joinStatement["table"], $joinStatement["onCols"], $joinStatement["colReturn"]);
            }
        }
        
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

            return parent::update($vData, $sWhere);

        } catch (Zend_Db_Exception $e) {
            UtilsFile::printvardie($e);
            $this->setErroMensagem($e->getMessage());
            return false;
        }
    }

    public function delete($vData, $sWhere, $sOperacao, $nIdUsuario) {
        try {
            $vLog = array(
                "operacao" => $sOperacao,
                "seg_usuario_id" => $nIdUsuario,
                "objeto" => serialize($vData),
                "dt_log" => date("Y-m-d")
            );
            $oLog = new Seg_Log();
            $oLog->insert($vLog);

            return parent::delete($sWhere);
        } catch (Zend_Db_Exception $e) {
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