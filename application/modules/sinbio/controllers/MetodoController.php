<?php

class Sinbio_MetodoController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		  // error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Coleta";
		$this->view->layout()->nmController = "metodo";
		$this->view->layout()->nmPrograma = "Métodos de coleta";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-metodo.js"></script>
		';
	
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
		$this->view->layout()->nmOperacao = "Listar";
	}
        
        
            public function cadastrarAction() {
		$this->view->layout()->nmPrograma = "Métodos de coleta";
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
					"sigla"		=> $request->getParam("fSigla"),
					"nm_metodo"	=> $request->getParam("fMetodo"),
                                        "descricao"	=> $request->getParam("fDescricao"),
			);
			
			
			$sAtributosChave = "sigla,nm_metodo";
			$sNmAtributosChave = "";
			$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
			if ($sMsg) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo", $sMsg);
			}
			else {
				try {
                                    
                                        $oMetodo = new Protocolo_Metodo();
                                        
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oMetodo->insert($vData,"cadastrar-metodo",$vUsuarioLogado["id"]);

					if (!$nId) {
						$sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo", $oMetodo->getErroMensagem());
						$bErro = strstr($sString,"1062");
						if ($bErro){
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo","Projeto/Programa já existente no sistema.");
						}
						else {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo", $sString);
						}
						
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/metodo');						
					}
				}
				catch (Zend_Db_Exception $e) {
					$sString = $e->getMessage();
					$bErro = strstr($sString,"1062");					
					if ($bErro){
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo","Metodo já existente no sistema.");
					}
					else {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo", $sString);
					}
				}
			}
		}
	}
        
        
        public function alterarAction() {
		$this->view->layout()->nmPrograma = "Métodos de coleta";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
                $oMetodo = new Protocolo_Metodo();
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vMetodo = $oMetodo->find($nId)->toArray();
			$vMetodo = $vMetodo[0];
			
			//VALIDA SE O USUARIO EXISTE
			if (count($vMetodo)) {
				$this->view->nId		= $vMetodo["id"];
				$this->view->sNmSigla	= $vMetodo["sigla"];
				$this->view->sNmMetodo	= $vMetodo["nm_metodo"];	
                                $this->view->sDescricao	= $vMetodo["descricao"];	
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId			= $request->getParam("nId");
					$sNmSigla		= $request->getParam("fSigla");
					$sNmMetodo		= $request->getParam("fMetodo");
                                        $sDescricao		= $request->getParam("fDescricao");
				
					$vData = array(
							"id"			=> $nId,
							"sigla"		=> $sNmSigla,
							"nm_metodo"	=> $sNmMetodo,
                                                        "descricao"	=>  $sDescricao,
					);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oMetodo->update($vData, $sWhere, "alterar-metodo", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Metodo(s) foi alterado com sucesso.");
						$this->_redirect('/metodo');
					}
					else {
						//UtilsFile::printvardie($oModulo->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Metodos)", $oMetodo->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Metodo(s)", "Este Módulo não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/metodo');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Metodo(s)", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/metodo');
		}//VALIDA O ID
	}
        
        
        public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
                
		$vId = $request->getParam("fId");
		
                $oMetodo = new Protocolo_Metodo();
                
                
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oMetodo->find($nId)->toArray();
				$sWhere = "id =".$nId;
				$oMetodo->delete($vData, $sWhere, "excluir-metodo", $vUsuarioLogado["id"]);
			}
			
			if ($oMetodo->getErroMensagem()) {
				$sString = $e->getMessage();
				$bErro = strstr($sString,"1062");
				if ($bErro){
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo","Metodo já existente no sistema.");
				}
				else {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo", $sString);
				}
				
				UtilsFile::recuperaMensagens(2, "Erro ao Excluir Metodo", $oMetodo->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Metodo(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Metodo(s)", "Você deve selecionar ao menos um registro.");
		}
	}
  
         
    
	
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("metodo", $sQP, $vUsuarioLogado["id"]);
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
		
                
                $oMetodo = new Protocolo_Metodo();
                
		$vReg = $oMetodo->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oMetodo->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){		
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["sigla"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_metodo"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["descricao"]."]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
        
        
    
	

}
