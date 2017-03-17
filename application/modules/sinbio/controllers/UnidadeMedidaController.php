<?php

class Sinbio_UnidadeMedidaController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		 // error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Protocolo";
		$this->view->layout()->nmController = "unidademedida";
		$this->view->layout()->nmPrograma = "Unidade de Medida";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-unidade-medida.js"></script>
		';
	
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
		$this->view->layout()->nmOperacao = "Listar";
	}
        
        
            public function cadastrarAction() {
		$this->view->layout()->nmPrograma = "Unidade de Medida";
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
					"nm_unidade"	=> $request->getParam("fUnidade"),
					"sigla"		=> $request->getParam("fSigla"),
			);
			
			
			$sAtributosChave = "nm_unidade";
			$sNmAtributosChave = "Unidade de Medida";
			$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
			if ($sMsg) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Metodo", $sMsg);
			}
			else {
				try {
                                    
                                        $oUnidadeMedida = new Protocolo_UnidadeMedida();
                                        
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oUnidadeMedida->insert($vData,"cadastrar-unidade-medida",$vUsuarioLogado["id"]);

					if (!$nId) {
						$sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Unidade", $oUnidadeMedida->getErroMensagem());
						$bErro = strstr($sString,"1062");
						if ($bErro){
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Unidade","Unidade já existente no sistema.");
						}
						else {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Unidade", $sString);
						}
						
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/unidade-medida');						
					}
				}
				catch (Zend_Db_Exception $e) {
					$sString = $e->getMessage();
					$bErro = strstr($sString,"1062");					
					if ($bErro){
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Unidade","Unidade já existente no sistema.");
					}
					else {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Unidade", $sString);
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
		
                $oUnidadeMedida = new Protocolo_UnidadeMedida();
                
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vUnidadeMedida = $oUnidadeMedida->find($nId)->toArray();
			$vUnidadeMedida = $vUnidadeMedida[0];
			
			//VALIDA SE O USUARIO EXISTE
			if (count($vUnidadeMedida)) {
				$this->view->nId		= $vUnidadeMedida["id"];
				$this->view->sNmUnidade	= $vUnidadeMedida["nm_unidade"];	
                                $this->view->sSigla	= $vUnidadeMedida["sigla"];	
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId			= $request->getParam("nId");
					$sNmUnidade		= $request->getParam("fUnidade");
                                        $sNmSigla		= $request->getParam("fSigla");
				
					$vData = array(
							"id"			=> $nId,
							"nm_unidade"		=> $sNmUnidade,
							"sigla"         	=> $sNmSigla,
					);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oUnidadeMedida->update($vData, $sWhere, "alterar-unidade-medida", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "A Unidade foi alterado com sucesso.");
						$this->_redirect('/unidade-medida');
					}
					else {
						//UtilsFile::printvardie($oModulo->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Unidade)", $oUnidadeMedida->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Unidade", "Este Unidade não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/unidade-medida');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Unidade", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/unidade-medida');
		}//VALIDA O ID
	}
        
        
        public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
                
		$vId = $request->getParam("fId");
		
                $oUnidadeMedida = new Protocolo_UnidadeMedida();
                
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oUnidadeMedida->find($nId)->toArray();
				$sWhere = "id =".$nId;
				$oUnidadeMedida->delete($vData, $sWhere, "excluir-unidade-medida", $vUsuarioLogado["id"]);
			}
			
			if ($oUnidadeMedida->getErroMensagem()) {
				$sString = $e->getMessage();
				$bErro = strstr($sString,"1062");
				if ($bErro){
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Unidade","Metodo já existente no sistema.");
				}
				else {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Unidade", $sString);
				}
				
				UtilsFile::recuperaMensagens(2, "Erro ao Excluir Metodo", $oUnidadeMedida->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Unidade(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Unidade(s)", "Você deve selecionar ao menos um registro.");
		}
	}
  
         
    
	
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("unidade-medida", $sQP, $vUsuarioLogado["id"]);
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
		
                
                $oUnidadeMedida = new Protocolo_UnidadeMedida();
                
		$vReg = $oUnidadeMedida->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oUnidadeMedida->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){		
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_unidade"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["sigla"]."]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
        
        
    
	

}
