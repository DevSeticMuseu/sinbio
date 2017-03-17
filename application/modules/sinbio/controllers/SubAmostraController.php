<?php

class Sinbio_SubAmostraController extends Zend_Controller_Action {

    public function init() {


      error_reporting (E_ALL & ~E_NOTICE);
        
        $this->view->layout()->nmModulo = "Módulo Amostra";
        $this->view->layout()->nmController = "sub-amostra";
        $this->view->layout()->nmPrograma = "Sub-Amostra";

        if ($_SESSION["sMsg"]) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-sub-amostra.js"></script>
		';

        $this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
				';
        $this->view->layout()->nmOperacao = "Listar";
    }

    public function cadastrarAction() {
        $this->view->layout()->nmPrograma = "Sub-Amostra";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';

        //ALIMENTANDO SELECT TAXON
        $oTaxon = new Amostra_Taxon();
        $this->view->vTaxon = $oTaxon->fetchAll()->toArray();
        
        //ALIMENTADO SELECT AMOSTRA
        $oAmostra = new Amostra_Amostra();
        $this->view->vAmostra = $oAmostra->fetchAll()->toArray();
        

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {
            try {
                $vData = array(
                    "taxonomia_taxon_id" => $request->getParam("fIdTaxon"),
                    "coleta_amostra_id" => $request->getParam("fIdAmostra"),
                    "quantidade" => $request->getParam("fQuantidade"),
                    "status_2" => $request->getParam("fStatus_2"),
                    "qt_machos" => $request->getParam("fQtMachos"),
                    "qt_femeas" => $request->getParam("fQtFemeas"),
                  );

                $sAtributosChave = "";
                $sNmAtributosChave = "";
                $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

                if ($sMsg) {
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Sub-Amostra", $sMsg);
                } else {
                    $oSubAmostra = new Amostra_SubAmostra();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oSubAmostra->insert($vData, "cadastrar-sub-amostra", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Sub-Amostra", $oSubAmostra->getErroMensagem());
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/sub-amostra');
                    }
                }
            } catch (Zend_Db_Exception $e) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Sub-Amostra", $e);
            }
        }
    }

    public function alterarAction() {
		$this->view->layout()->nmPrograma = "Sub-Amostra";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '
		
		';
        
                $oTaxon = new Amostra_Taxon();
                $oSubAmostra = new Amostra_SubAmostra();
                $oAmostra = new Amostra_Amostra();
                    
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
                        $vSubAmostra = $oSubAmostra->find($nId)->toArray();
                        $vSubAmostra = $vSubAmostra[0];
                    
			
                           //RECUPERA O Amostra
                           $this->view->vAmostra = $oAmostra->fetchAll()->toArray();
   
                           //RECUPERA A Taxon
                          $this->view->vTaxon = $oTaxon->fetchAll()->toArray();
                   
                           
                         //VALIDA SE O USUARIO EXISTE
			if (count($vSubAmostra)) {
				$this->view->nId		= $vSubAmostra["id"];
                                $this->view->nIdTaxon	= $vSubAmostra["taxonomia_taxon_id"];
                                $this->view->nIdAmostra	= $vSubAmostra["coleta_amostra_id"];
                                $this->view->sQuantidade = $vSubAmostra["quantidade"];
                                $this->view->sStatus	= $vSubAmostra["status_2"];
                                $this->view->sQtMachos	= $vSubAmostra["qt_machos"];
                                $this->view->sQtFemeas	= $vSubAmostra["qt_femeas"];
                                
                                
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					
					$vData = array(
                                                        "id" => $request->getParam("nId"),
                                                        "taxonomia_taxon_id" => $request->getParam("fIdTaxon"),
                                                        "coleta_amostra_id" => $request->getParam("fIdAmostra"),
                                                        "quantidade" => $request->getParam("fQuantidade"),
                                                        "status_2" => $request->getParam("fStatus_2"),
                                                        "qt_machos" => $request->getParam("fQtMachos"),
                                                        "qt_femeas" => $request->getParam("fQtFemeas"),
                                                        );
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oSubAmostra->update($vData, $sWhere, "alterar-sub-amostra", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Sub-Amostra foi alterado com sucesso.");
						$this->_redirect('/sub-amostra');
					}
					else {
						//UtilsFile::printvardie($oPrograma->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Sub-Amostra", $oSubAmostra->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Amostra", "Este Amostra não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/sub-amostra');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Sub-Amostra", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/sub-amostra');
		}//VALIDA O ID
	}
    
    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");
        
        $oSubAmostra = new Amostra_SubAmostra();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $oSubAmostra->find($nId);
                $sWhere = "id =" . $nId;
                $oSubAmostra->delete($vData, $sWhere, "excluir-sub-amostra", $vUsuarioLogado["id"]);
            }

            if ($oSubAmostra->getErroMensagem()) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Sub-Amostra", $oSubAmostra->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Sub-Amostra removida(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Sub-Amostra", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("sub-amostra", $sQP, $vUsuarioLogado["id"]);
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

        if ($sSortname == "taxon")
            $sSortname = "taxonomia_taxon_id";

        if ($sSortname == "id_amostra_coleta")
            $sSortname = "coleta_amostra_id";
  

        $sWhere = "";
        if ($sQuery != "" && $sCampo != "") {
            $sWhere = $sCampo . " LIKE '%" . $sQuery . "%' ";
        }
        $sOrder = $sSortname . " " . $sSortorder;

        $oSubAmostra = new Amostra_SubAmostra();

        $vReg = $oSubAmostra->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oSubAmostra->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {

            $oTaxon  = new Amostra_Taxon();
            $vTaxon = $oTaxon->find($reg['taxonomia_taxon_id'])->toArray();
            
            $oAmostra = new Amostra_Amostra();
            $vAmostra = $oAmostra->find($reg['coleta_amostra_id'])->toArray();


            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vTaxon[0]["taxon"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vAmostra[0]["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["quantidade"] . "]]></cell>";
            if($reg["status_2"] == 1)
            {
                $reg["status_2"] = "Ativo";
            }else
            {
             $reg["status_2"] = "Inativo";   
            }
            $xml .= "<cell><![CDATA[" . $reg["status_2"] . "]]></cell>";
            
            $xml .= "<cell><![CDATA[" . $reg["qt_machos"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["qt_femeas"] . "]]></cell>";
            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
