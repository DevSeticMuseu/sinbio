<?php

class Sinbio_AmostraDetalhadaController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        error_reporting(E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Amostra";
        $this->view->layout()->nmController = "amostra-detalhada";
        $this->view->layout()->nmPrograma = "Amostra Detalhada";

        if (isset($_SESSION["sMsg"])) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        try {
            $this->view->layout()->nmOperacao = "Detalhes da amostra";

            $oAmostra = new Amostra_Amostra();
            $nIdAmostra = $this->_request->getParam('nId');
            $sWhere = array("amostra.id = $nIdAmostra");

            $joins = array();
            $joinExpedicoes = array(table => array('expedicao' => 'coleta_expedicao'), onCols => ' amostra.coleta_expedicao_id = expedicao.id', colReturn => array('id', 'data_inicio', 'data_fim'));
            array_push($joins, $joinExpedicoes);
            
            $joinUfs = array(table => array('uf' => 'loc_uf'), onCols => ' expedicao.loc_uf_id = uf.id', colReturn => array('nm_uf'));
            array_push($joins, $joinUfs);
            
            $joinMunicipios = array(table => array('municipio' => 'loc_municipio'), onCols => ' expedicao.loc_municipio_id = municipio.id', colReturn => array('nm_municipio'));
            array_push($joins, $joinMunicipios);
            
            $joinLocalidades = array(table => array('localidade' => 'loc_localidade'), onCols => ' expedicao.loc_localidade_id = localidade.id', colReturn => array('nm_localidade'));
            array_push($joins, $joinLocalidades);
            
            $joinSitios = array(table => array('sitio' => 'loc_sitio'), onCols => ' expedicao.loc_sitio_id = sitio.id', colReturn => array('nm_sitio'));
            array_push($joins, $joinSitios);
            
            $joinProtocolos = array(table => array('protocolo' => 'coleta_protocolo'), onCols => ' expedicao.coleta_protocolo_id = protocolo.id', colReturn => array('nm_protocolo'));
            array_push($joins, $joinProtocolos);
            
            $joinMetodos = array(table => array('metodo' => 'coleta_metodos'), onCols => ' amostra.coleta_metodos_id = metodo.id', colReturn => array('nm_metodo'));
            array_push($joins, $joinMetodos);
            
            $joinConservacao = array(table => array('conservacao' => 'coleta_conservacao'), onCols => ' amostra.coleta_conservacao_id = conservacao.id', colReturn => array('conservacao_material'));
            array_push($joins, $joinConservacao);

            $vAmostra = $oAmostra->fetchAll($sWhere, null, $joins)->toArray();
            
            $vAmostra[0]['data_inicio'] = date("d/m/Y", strtotime($vAmostra[0]['data_inicio']));
            $vAmostra[0]['data_fim'] = date("d/m/Y", strtotime($vAmostra[0]['data_fim']));
            $vAmostra[0]['data_coleta'] = date("d/m/Y", strtotime($vAmostra[0]['data_coleta']));
            
            $this->view->Amostra = $vAmostra;
            
            $sWhere = array("coleta_amostra_id = $nIdAmostra");
            
            $oAmostraVariaveis = new Amostra_AmostraVariaveis();
            $joins = array();
            $joinAmostras = array(table => array('variavel' => 'coleta_variaveis'), onCols => ' av.coleta_variaveis_id = variavel.id', colReturn => array('nm_variavel'));
            array_push($joins, $joinAmostras);
            $this->view->Variaveis = $oAmostraVariaveis->fetchAll($sWhere, null, $joins);
            
            $oDestinacao = new Amostra_AmostraDestinacao();
            $joins = array();
            $joinAmostras = array(table => array('destinacao' => 'coleta_destinacao'), onCols => ' ad.coleta_destinacao_id = destinacao.id', colReturn => array('nm_destinacao'));
            array_push($joins, $joinAmostras);
            $this->view->Destinacao = $oDestinacao->fetchAll($sWhere, null, $joins);
            
        } catch (Zend_Db_Exception $e) {
            $this->setErroMensagem($e->getMessage());
            return false;
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("amostra", $sQP, $vUsuarioLogado["id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

}

