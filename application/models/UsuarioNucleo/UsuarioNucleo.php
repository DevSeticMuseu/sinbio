<?php

class UsuarioNucleo_UsuarioNucleo extends Zend_Db_Table_Abstract {

    protected $_name = 'usuario_nucleo';
    protected $_primary = 'id';
    protected $_erroMensagem = null;
    protected $_referenceMap = array(
        'Usuario' => array(
            'columns' => array('seg_usuario_id'),
            'refTableClass' => 'Seg_Usuario',
            'refColumns' => array('id')
        ),
        'Nucleo' => array(
            'columns' => array('loc_nucleo_id'),
            'refTableClass' => 'Loc_Nucleo',
            'refColumns' => array('id')
        )
    );

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

    public function insert($aNucleos, $nId, $sOperacao, $nIdUsuario) {
        try {
            $oLog = new Seg_Log();

            foreach ($aNucleos as $nucleo) {
                $aux = array("loc_nucleo_id" => $nucleo,
                    "seg_usuario_id" => $nId);

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

    public function findNucleos($vUsuarioRow) {
        try {
            return $vUsuarioRow->findManyToManyRowset('Loc_Nucleo', 'UsuarioNucleo_UsuarioNucleo');
        } catch (Zend_Db_Exception $e) {
            $this->setErroMensagem($e->getMessage());
            return false;
        }
    }

    public function findNucleosPorId($nId) {
        $this->db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "SELECT n.nm_nucleo
                    FROM usuario_nucleo un 
                    JOIN loc_nucleo n on un.loc_nucleo_id = n.id                    
                    WHERE un.seg_usuario_id = " . $nId;

        return $result = $this->db->fetchAll($sql);
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
