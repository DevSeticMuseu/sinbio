<?php

class Sinbio_OperacaoController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		error_reporting (E_ALL & ~E_NOTICE);
                $this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "operacao";
		$this->view->layout()->nmPrograma = "Operação";
//            
//		$this->view->layout()->nmModulo = "Módulo Segurança";
//		$this->view->layout()->nmController = "operacao";
//		$this->view->layout()->nmOperacao = "Operação";
		
		if ($_SESSION["sMsg"]) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/seguranca-seg-operacao.js"></script>
		';
	
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
				';
		$this->view->layout()->nmOperacao = "Listar";
	}
	
	public function cadastrarAction() {
		$this->view->layout()->nmOperacao = "Operação";
		$this->view->layout()->nmOperacao = "Cadastrar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
		//ALIMENTANDO SELECT DE PROGRAMAS
		$oPrograma = new Seg_Programa();
		$this->view->vPrograma = $oPrograma->fetchAll()->toArray();
		
		//INSERINDO NO BANCO
		$request = $this->_request;
		
		if ($request->getParam("sOP") == "cadastrar") {
			try {
				$vData = array(
						"nm_operacao"	=> $request->getParam("fNmOperacao"),
						"nm_display"	=> $request->getParam("fNmDisplay"),
						"seg_programa_id"	=> $request->getParam("fIdPrograma")
				);
				
				$sAtributosChave = "nm_operacao,nm_display,seg_programa_id";
				$sNmAtributosChave = "Nome da operação (action), Nome para exibir, Operacao";
				$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
				
				if ($sMsg) {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Operação", $sMsg);
				}
				else {
					$oOperacao = new Seg_Operacao();
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oOperacao->insert($vData,"cadastrar-operacao",$vUsuarioLogado["id"]);
	
					if (!$nId) {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Operação", $oOperacao->getErroMensagem());
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/operacao');						
					}
				}
			}
			catch (Zend_Db_Exception $e) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Operação", $sString);
			}
		}
	}
	
	public function alterarAction() {
		$this->view->layout()->nmOperacao = "Operação";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
		$oOperacao = new Seg_Operacao();
                $oPrograma = new Seg_Programa();
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vOperacao = $oOperacao->find($nId)->toArray();
			$vOperacao = $vOperacao[0];
			
                        //RECUPERA O PROGRAMA DA OPERACAO
                           $this->view->vPrograma = $oPrograma->fetchAll()->toArray();
                        
			//VALIDA SE O USUARIO EXISTE
			if (count($vOperacao)) {
				$this->view->nId		= $vOperacao["id"];
				$this->view->sNmOperacao	= $vOperacao["nm_operacao"];
				$this->view->sNmDisplay	= $vOperacao["nm_display"];
                                $this->view->nIdPrograma	= $vOperacao["seg_programa_id"];
                                
                              
                                 
                                 
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId			= $request->getParam("nId");
                                        $nIdPrograma	        = $request->getParam("nIdPrograma");
					$sNmOperacao		= $request->getParam("fNmOperacao");
					$sNmDisplay		= $request->getParam("fNmDisplay");
				
					$vData = array(
							"id"			=> $request->getParam("nId"),
                                                        "seg_programa_id"             => $request->getParam("nIdPrograma"),
							"nm_operacao"		=> $request->getParam("fNmOperacao"),
							"nm_display"	=> $request->getParam("fNmDisplay"),
					);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oOperacao->update($vData, $sWhere, "alterar-operacao", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Operacao foi alterado com sucesso.");
						$this->_redirect('/operacao');
					}
					else {
						//UtilsFile::printvardie($oOperacao->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Operacao", $oOperacao->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Operacao", "Este Operacao não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/operacao');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Operacao", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/operacao');
		}//VALIDA O ID
	}
	
	public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
		$vId = $request->getParam("fId");
		
		$oOperacao = new Seg_Operacao();
		
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oOperacao->find($nId);
				$sWhere = "id =".$nId;
				$oOperacao->delete($vData, $sWhere, "excluir-operacao", $vUsuarioLogado["id"]);
			}
			
			if ($oOperacao->getErroMensagem()) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Operacao", $oOperacao->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Operacao(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Operacao", "Você deve selecionar ao menos um registro.");
		}
	}
	
	public function verificaPermissaoAction() {
		$sOP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$oVerifica = new VerificaPermissao("programa", $sOP, "1");
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
		
		if ($sSortname == "nm_programa")
			$sSortname = "seg_programa_id";
		
		$sWhere = "";
		if ($sQuery != "" && $sCampo != "") {
			$sWhere = $sCampo." LIKE '%".$sQuery."%' ";
		}
		$sOrder = $sSortname." ".$sSortorder;
		
		$oOperacao = new Seg_Operacao();
		$vReg = $oOperacao->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oOperacao->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){
			$oPrograma = new Seg_Programa();
			$vPrograma = $oPrograma->find($reg["seg_programa_id"])->toArray();
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_display"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_operacao"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$vPrograma[0]["nm_programa"]."]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
}
