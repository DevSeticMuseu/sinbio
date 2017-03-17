<?php

class Sinbio_NucleoController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		 //  error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Localidade";
		$this->view->layout()->nmController = "nucleo";
		$this->view->layout()->nmPrograma = "Nucleo";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/localidade-loc-nucleo.js"></script>
		';
	
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
		$this->view->layout()->nmOperacao = "Listar";
	}
        
        
        
            public function cadastrarAction() {
		$this->view->layout()->nmController = "nucleo";
		$this->view->layout()->nmPrograma = "Nucleo";
		$this->view->layout()->nmOperacao = "Cadastrar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
		//INSERINDO NO BANCO
		$request = $this->_request;
		
		if ($request->getParam("sOP") == "cadastrar") {
			$vData = array(
					"nm_nucleo"		=> $request->getParam("fNucleo"),
                                        "descricao"		=> $request->getParam("sDescricao"),
                                        "representante"		=> $request->getParam("sRepresentante"),
                                        "org_vinculado"		=> $request->getParam("sOrg_Vinculado"),
			);
			
			
			$sAtributosChave = "nm_nucleo";
			$sNmAtributosChave = "Nome Nucleo";
			$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
			if ($sMsg) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nucleo", $sMsg);
			}
			else {
				try {
					$oNucleo = new Loc_Nucleo();
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oNucleo->insert($vData,"cadastrar-nucleo",$vUsuarioLogado["id"]);

					if (!$nId) {
						$sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nucleo", $oNucleo->getErroMensagem());
						$bErro = strstr($sString,"1062");
						if ($bErro){
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nucleo","Nucleo já existente no sistema.");
						}
						else {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nucleo", $sString);
						}
						
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/nucleo');						
					}
				}
				catch (Zend_Db_Exception $e) {
					$sString = $e->getMessage();
					$bErro = strstr($sString,"1062");					
					if ($bErro){
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar UF","UF já existente no sistema.");
					}
					else {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar UF", $sString);
					}
				}
			}
		}
	}
        
          public function alterarAction() {
		$this->view->layout()->nmController = "nucleo";
		$this->view->layout()->nmPrograma = "Nucleo";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
		$oNucleo = new Loc_Nucleo();
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vNucleo = $oNucleo->find($nId)->toArray();
			$vNucleo = $vNucleo[0];
			
			//VALIDA SE O USUARIO EXISTE
			if (count($vNucleo)) {
				$this->view->nId		= $vNucleo["id"];
				$this->view->sNmNucleo	= $vNucleo["nm_nucleo"];
                                $this->view->sDescricao	= $vNucleo["descricao"];
                                $this->view->sRepresentante = $vNucleo["representante"];
                                $this->view->sOrgVinculado	= $vNucleo["org_vinculado"];
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId			= $request->getParam("nId");
					$sNmNucleo		= $request->getParam("fNucleo");
                                        $sDescricao		= $request->getParam("sDescricao");
                                        $sRepresentante		= $request->getParam("sRepresentante");
                                        $sOrgVinculado		= $request->getParam("sOrg_Vinculado");
				
					$vData = array(
							"id"			=> $nId,
							"nm_nucleo"		=> $sNmNucleo,
                                                        "descricao"		=> $sDescricao,
                                                        "representante"		=> $sRepresentante,
                                                        "org_vinculado"		=> $sOrgVinculado,
					);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oNucleo->update($vData, $sWhere, "alterar-nucleo", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Nucleo foi alterado com sucesso.");
						$this->_redirect('/nucleo');
					}
					else {
						//UtilsFile::printvardie($oModulo->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Nucleo", $oNucleo->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Nucleo", "Este Módulo não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/nucleo');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Nucleo", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/nucleo');
		}//VALIDA O ID
	}
        
        
      
	public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
                
		$vId = $request->getParam("fId");
		
		$oNucleo = new Loc_Nucleo();
		
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oNucleo->find($nId)->toArray();
				$sWhere = "id =".$nId;
				$oNucleo->delete($vData, $sWhere, "excluir-nucleo", $vUsuarioLogado["id"]);
			}
			
			if ($oNucleo->getErroMensagem()) {
				$sString = $e->getMessage();
				$bErro = strstr($sString,"1062");
				if ($bErro){
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nucleo","UF já existente no sistema.");
				}
				else {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nucleo", $sString);
				}
				
				UtilsFile::recuperaMensagens(2, "Erro ao Excluir Nucleo", $oNucleo->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Nucleo(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Nucleo", "Você deve selecionar ao menos um registro.");
		}
	}
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("nucleo", $sQP, $vUsuarioLogado["id"]);
		if ($oVerifica->bResultado) {
			$this->view->bPermissao = $oVerifica->bResultado;
		}
	}
        
        public function geraXmlAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	
		$request = $this->_request;
		
		$nPagina =			($request->getParam('page'))?$request->getParam('page'):1;
		$nRegistroPagina =	($request->getParam('rp'))?$request->getParam('rp'):15;
		$sSortname =		($request->getParam('sortname'))?$request->getParam('sortname'):"id";
		$sSortorder =		($request->getParam('sortorder'))?$request->getParam('sortorder'):"asc";
		$sQuery =			($request->getParam('query'))?$request->getParam('query'):"";
		$sCampo =			($request->getParam('qtype'))?$request->getParam('qtype'):"";
		
		$sWhere = "";
		if ($sQuery != "" && $sCampo != "") {
			$sWhere = $sCampo." LIKE '%".$sQuery."%' ";
		}
		$sOrder = $sSortname." ".$sSortorder;
		
		$oNucleo = new Loc_Nucleo();
		$vReg = $oNucleo->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oNucleo->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){		
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_nucleo"]."]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
	

}
