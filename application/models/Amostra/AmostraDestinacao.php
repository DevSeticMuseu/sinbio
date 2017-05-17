<?php

class Amostra_AmostraDestinacao extends Zend_Db_Table_Abstract {

    protected $_name = 'amostra_destinacao';
    protected $_primary = 'id';
    protected $_erroMensagem = null;
    protected $_referenceMap = array(
        'Amostra' => array(
            'columns' => array('coleta_amostra_id'),
            'refTableClass' => 'Amostra_Amostra',
            'refColumns' => array('id')
        ),
        'Destinacao' => array(
            'columns' => array('coleta_destinacao_id'),
            'refTableClass' => 'Amostra_Destinacao',
            'refColumns' => array('id')
        )
    );

    public function fetchAll($sWhere = null, $sOrder = null, $sJoin = null, $nPagina = null, $nResultadoPagina = null) {
        $sSql = $this->select();

        if ($sJoin) {
            $sSql->setIntegrityCheck(FALSE);
            $sSql->from(array('ad' => 'amostra_destinacao'));
            foreach ($sJoin as $joinStatement) {
                $sSql->joinInner($joinStatement["table"], $joinStatement["onCols"], $joinStatement["colReturn"]);
            }
        }

        if ($sWhere) {
            foreach ($sWhere as $where) {
                $sSql->where($where);
            }
        }

        if ($sOrder)
            $sSql->order($sOrder);

        if ($nPagina && $nResultadoPagina) {
            $nInicio = (($nPagina - 1) * $nResultadoPagina);
            $sSql->limit($nResultadoPagina, $nInicio);
        }

        return parent::fetchAll($sSql);
    }

    public function insert($aDestinacoes, $nId, $sOperacao, $nIdUsuario) {
        try {
            $oLog = new Seg_Log();

            foreach ($aDestinacoes as $destinacao) {
                $aux = array("coleta_destinacao_id" => $destinacao,
                    "coleta_amostra_id" => $nId);

                parent::insert($aux);

                $vLog = array(
                    "operacao" => $sOperacao,
                    "seg_usuario_id" => $nIdUsuario,
                    "objeto" => serialize($aux),
                    "dt_log" => date("Y-m-d")
                );

                $oLog->insert($vLog);
            }
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

            return parent::update($vData, $sWhere);

        } catch (Zend_Db_Exception $e) {
//            UtilsFile::printvardie($e);
            $this->setErroMensagem($e->getMessage());
            return false;
        }
    }

    public function delete($sWhere, $sOperacao, $nIdUsuario) {
        try {
            $vLog = array(
                "operacao" => $sOperacao,
                "seg_usuario_id" => $nIdUsuario,
                "dt_log" => date("Y-m-d")
            );
            $oLog = new Seg_Log();
            $oLog->insert($vLog);

            return parent::delete($sWhere);
        } catch (Zend_Db_Exception $e) {
            UtilsFile::printVarDieAjax($e);
            $this->setErroMensagem($e->getMessage());
            return false;
        }
    }

    public function findDestinacoes($vAmostraRow) {
        try {
            return $vAmostraRow->findManyToManyRowset('Amostra_Destinacao', 'Amostra_AmostraDestinacao');
        } catch (Zend_Db_Exception $e) {
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

}
