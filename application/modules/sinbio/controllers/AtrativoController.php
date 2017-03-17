<?php

class Sinbio_AtrativoController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        error_reporting (E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Coleta";
        $this->view->layout()->nmController = "atrativo";
        $this->view->layout()->nmPrograma = "Atrativo";

        if ($_SESSION["sMsg"]) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-atrativo.js"></script>
		';

        $this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
				';
        $this->view->layout()->nmOperacao = "Listar";
    }

    public function cadastrarAction() {
        $this->view->layout()->nmPrograma = "atrativo";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';
        
        //
        $Atrativos = new Protocolo_Atrativos();
        $this->view->vAtrativos = $Atrativos->fetchAll()->toArray();
        

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {
            try {
                $vData = array(
                    "nm_atrativos" => $request->getParam("fNmAtrativo")
                );

                $sAtributosChave = "nm_atrativos";
                $sNmAtributosChave = "Nome do Atrativo";
                $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

                if ($sMsg) {
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Atrativo", $sMsg);
                } else {
                    $oAtrativo = new Protocolo_Atrativos();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oAtrativo->insert($vData, "cadastrar-atrativo", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Atrativo", $oAtrativo->getErroMensagem());
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/atrativo');
                    }
                }
            } catch (Zend_Db_Exception $e) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Atrativo", $e);
            }
        }
    }

    public function alterarAction() {
		$this->view->layout()->nmPrograma = "Atrativo";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '
		
		';
                $Atrativo = new Protocolo_Atrativos();
                		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
                        $vAtrativo = $Atrativo->find($nId)->toArray();
                        $vAtrativo = $vAtrativo[0];
                    
                           //RECUPERA O Atrativos
                           $this->view->vAtrativos = $Atrativo->fetchAll()->toArray();
                           
                         
                         //VALIDA SE O USUARIO EXISTE
			if (count($vAtrativo)) {
				$this->view->nId		= $vAtrativo["id"];
                $this->view->sNmAtrativo	= $vAtrativo["nm_atrativos"];
                
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					
					$vData = array(
									"id"			=> $request->getParam("nId"),
									"nm_atrativos" => $request->getParam("fNmAtrativo")
									);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($Atrativo->update($vData, $sWhere, "alterar-atrativo", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "A Atrativo foi alterado com sucesso.");
						$this->_redirect('/atrativo');
					}
					else {
						//UtilsFile::printvardie($oPrograma->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Atrativo", $Atrativo->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Atrativo", "Esta Atrativo não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/atrativo');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Atrativo", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/atrativo');
		}//VALIDA O ID
	}
    
    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");

        $Atrativo = new Protocolo_Atrativos();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $Atrativo->find($nId);
                $sWhere = "id =" . $nId;
                $Atrativo->delete($vData, $sWhere, "excluir-atrativo", $vUsuarioLogado["id"]);
            }

            if ($Atrativo->getErroMensagem()) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Atrativo", $Atrativo->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Atrativo removida(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Atrativo", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("atrativo", $sQP, $vUsuarioLogado["id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

    public function geraXmlAction() {

        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;

        $nPagina = ($request->getParam('page')) ? $request->getParam('page') : 1;
        $nRegistroPagina = ($request->getParam('rp')) ? $request->getParam('rp') : 15;
        $sSortname = ($request->getParam('sortname')) ? $request->getParam('sortname') : "id";
        $sSortorder = ($request->getParam('sortorder')) ? $request->getParam('sortorder') : "asc";
        $sQuery = ($request->getParam('query')) ? $request->getParam('query') : "";
        $sCampo = ($request->getParam('qtype')) ? $request->getParam('qtype') : "";

        if ($sSortname == "nm_atrativos")
            $sSortname = "id";


        $sWhere = "";
        if ($sQuery != "" && $sCampo != "") {
            $sWhere = $sCampo . " LIKE '%" . $sQuery . "%' ";
        }
        $sOrder = $sSortname . " " . $sSortorder;

        $oAtrativo = new Protocolo_Atrativos();
        
        $vReg = $oAtrativo->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oAtrativo->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {            
            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["nm_atrativos"] . "]]></cell>";
            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
