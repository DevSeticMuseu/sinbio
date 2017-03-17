<?php

class Sinbio_ProgramaController extends Zend_Controller_Action
{

	public function init()
	{
           error_reporting (E_ALL & ~E_NOTICE);
		/* Initialize action controller here */
		
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "programa";
		$this->view->layout()->nmPrograma = "Programa";
		
		if ($_SESSION["sMsg"]) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/seguranca-seg-programa.js"></script>
		';
	
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
				';
		$this->view->layout()->nmOperacao = "Listar";
	}
	
	public function cadastrarAction() {
		$this->view->layout()->nmPrograma = "Programa";
		$this->view->layout()->nmOperacao = "Cadastrar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
		//ALIMENTANDO SELECT DE MODULOS
		$oModulo = new Seg_Modulo();
		$this->view->vModulo = $oModulo->fetchAll()->toArray();
		
		//INSERINDO NO BANCO
		$request = $this->_request;
		
		if ($request->getParam("sOP") == "cadastrar") {
			try {
				$vData = array(
						"nm_programa"	=> $request->getParam("fNmPrograma"),
						"nm_display"	=> $request->getParam("fNmDisplay"),
						"seg_modulo_id"		=> $request->getParam("fIdModulo")
				);
			
				$sAtributosChave = "nm_programa,nm_display,seg_modulo_id";
				$sNmAtributosChave = "Nome do programa (controller), Nome para exibir, Módulo";
				$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
				if ($sMsg) {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Programa", $sMsg);
				}
				else {
					$oPrograma = new Seg_Programa();
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oPrograma->insert($vData,"cadastrar-programa",$vUsuarioLogado["id"]);

					if (!$nId) {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Programa", $oPrograma->getErroMensagem());
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/programa');						
					}
				}
			}
			catch (Zend_Db_Exception $e) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Programa", $sString);
			}
		}
	}
	
	public function alterarAction() {
		$this->view->layout()->nmPrograma = "Programa";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '
		
		';
		
		$oPrograma = new Seg_Programa();
                $oModulo = new Seg_Modulo();
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vPrograma = $oPrograma->find($nId)->toArray();
			$vPrograma = $vPrograma[0];
			
                         //RECUPERA O MODULO Do PROGRAMA
                           $this->view->vModulo = $oModulo->fetchAll()->toArray();
                        
			//VALIDA SE O USUARIO EXISTE
			if (count($vPrograma)) {
				$this->view->nId		= $vPrograma["id"];
				$this->view->sNmPrograma	= $vPrograma["nm_programa"];
				$this->view->sNmDisplay	= $vPrograma["nm_display"];
                                $this->view->nIdModulo	= $vPrograma["seg_modulo_id"];
                                
                                
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId			= $request->getParam("nId");
                                        $nIdModulo	        = $request->getParam("nIdModulo");
					$sNmPrograma		= $request->getParam("fNmPrograma");
					$sNmDisplay		= $request->getParam("fNmDisplay");
				
					$vData = array(
							"id"			=> $request->getParam("nId"),
                                                        "seg_modulo_id"             => $request->getParam("nIdModulo"),
							"nm_programa"		=> $request->getParam("fNmPrograma"),
							"nm_display"	=> $request->getParam("fNmDisplay"),
					);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oPrograma->update($vData, $sWhere, "alterar-programa", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Programa foi alterado com sucesso.");
						$this->_redirect('/programa');
					}
					else {
						//UtilsFile::printvardie($oPrograma->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Programa", $oPrograma->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Programa", "Este Programa não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/programa');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Programa", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/programa');
		}//VALIDA O ID
	}
	
	public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
		$vId = $request->getParam("fId");
		
		$oPrograma = new Seg_Programa();
		
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oPrograma->find($nId);
				$sWhere = "id =".$nId;
				$oPrograma->delete($vData, $sWhere, "excluir-programa", $vUsuarioLogado["id"]);
			}
			
			if ($oPrograma->getErroMensagem()) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Programa", $oPrograma->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Programa(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Programa", "Você deve selecionar ao menos um registro.");
		}
	}
	
	public function verificaPermissaoAction() {
			$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("programa", $sQP, $vUsuarioLogado["id"]);
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
		
		if ($sSortname == "nm_modulo")
			$sSortname = "seg_modulo_id";
		
		$sWhere = "";
		if ($sQuery != "" && $sCampo != "") {
			$sWhere = $sCampo." LIKE '%".$sQuery."%' ";
		}
		$sOrder = $sSortname." ".$sSortorder;
		
		$oPrograma = new Seg_Programa();
		$vReg = $oPrograma->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oPrograma->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){
			$oModulo = new Seg_Modulo();
			$vModulo = $oModulo->find($reg["seg_modulo_id"])->toArray();
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_display"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_programa"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$vModulo[0]["nm_modulo"]."]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
}
