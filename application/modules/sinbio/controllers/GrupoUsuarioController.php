<?php

class Sinbio_GrupoUsuarioController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		    error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "grupo-usuario";
		$this->view->layout()->nmPrograma = "Grupo Usuário";
		
		if ($_SESSION["sMsg"]) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/seguranca-seg-grupo-usuario.js"></script>
		';
	
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
		$this->view->layout()->nmOperacao = "Listar";
	}

	public function cadastrarAction() {
		$this->view->layout()->nmPrograma = "Grupo Usuário";
		$this->view->layout()->nmOperacao = "Cadastrar";
	
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
	
		$this->view->layout()->includeCss = '';
	
		//INSERINDO NO BANCO
		$request = $this->_request;
	
		if ($request->getParam("sOP") == "cadastrar") {
			try {
				$vData = array(
						"nm_grupo_usuario" => $request->getParam("fNmGrupo")
				);
	
				$sAtributosChave = "nm_grupo_usuario";
				$sNmAtributosChave = "Nome do Grupo Usuário";
				$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
	
				if ($sMsg) {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Grupo de Usuário", $sMsg);
				}
				else {
					$oGrupoUsuario = new Seg_GrupoUsuario();
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oGrupoUsuario->insert($vData,"cadastrar-grupo-usuario",$vUsuarioLogado["id"]);
	
					if (!$nId) {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Grupo de Usuário", $oGrupoUsuario->getErroMensagem());
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/grupo-usuario');
					}
				}
			}
			catch (Zend_Db_Exception $e) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Grupo de Usuário", $oGrupoUsuario->getErroMensagem());
			}
		}
	}
	
	public function alterarAction() {
		$this->view->layout()->nmPrograma = "Grupo Usuário";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
		try {
			$oGrupoUsuario = new Seg_GrupoUsuario();
			
			$request = $this->_request;
			$nId = $request->getParam("nId");
			$sOP = $request->getParam("sOP");

			//VALIDA O ID
			if ($nId) {
				$vGrupoUsuario = $oGrupoUsuario->find($nId)->toArray();
				$vGrupoUsuario = $vGrupoUsuario[0];
				
				//VALIDA SE O GRUPO USUARIO EXISTE
				if (count($vGrupoUsuario)) {
					$this->view->nId				= $vGrupoUsuario["id"];
					$this->view->sNmGrupoUsuario	= $vGrupoUsuario["nm_grupo_usuario"];
					
					//VALIDA SE FOI SUBMETIDO O FORMULARIO
					if ($sOP =="alterar") {
						
						//RECUPERA CAMPOS DO FORMULARIO
						$nId				= $request->getParam("nId");
						$sNmGrupoUsuario	= $request->getParam("fNmGrupo");
						
						$vData = array(
								"id"				=> $request->getParam("nId"),
								"nm_grupo_usuario"	=> $request->getParam("fNmGrupo")
						);
						
						$sWhere = "id = ".$vData["id"];
						$auth = Zend_Auth::getInstance();
						$vUsuarioLogado = $auth->getIdentity();
						
						//VERIFICA SE O REGISTRO VAI SER ALTERADO
						if ($oGrupoUsuario->update($vData, $sWhere, "alterar-grupo-usuario", $vUsuarioLogado["id"])) {
							$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Grupo de Usuário foi alterado com sucesso.");
							$this->_redirect('/grupo-usuario');
						}
						else {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Grupo de Usuário", $oGrupoUsuario->getErroMensagem());
						}//VERIFICA SE O REGISTRO VAI SER ALTERADO
					}
				}
				else {
					$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Grupo de Usuário", "Este Grupo de Usuário não foi encontrado no sistema, por favor tente novamente.");
					$this->_redirect('/grupo-usuario');
				}//VALIDA SE O GRUPO USUARIO EXISTE
			}
			else {
				throw new Exception("Ocorreu um erro inexperado, por favor tente novamente.");
			}//VALIDA O ID
		}
		catch (Zend_Db_Exception $e) {
			$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Grupo de Usuário", $oGrupoUsuario->getErroMensagem());
		}
	}
	
	public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	
		try {
			$request = $this->_request;
			$vId = $request->getParam("fId");
		
			$oGrupoUsuario = new Seg_GrupoUsuario();
		
			$auth = Zend_Auth::getInstance();
			$vUsuarioLogado = $auth->getIdentity();
			
			//UtilsFile::printvardie($vId);
		
			if (count($vId)) {
				foreach ($vId as $nId) {
					$vData = $oGrupoUsuario->find($nId)->toArray();
					$sWhere = "id =".$nId;
					if (!$oGrupoUsuario->delete($vData, $sWhere, "excluir-grupo-usuario", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Grupo de Usuário", $oGrupoUsuario->getErroMensagem());
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Grupo(s) de Usuário(s) excluído(s) com sucesso!");
					}
					unset($sWhere);
				}
			}		
		}
		catch (Zend_Db_Exception $e) {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Grupo de Usuário", $oGrupoUsuario->getErroMensagem());
		}
	}
	
	public function verificaPermissaoAction() {
		$this->view->layout()->disableLayout();
		
		$sQP = $this->_request->getParam("sOP");
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("grupo-usuario", $sQP, $vUsuarioLogado["id"]);
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
	
		$oGrupoUsuario = new Seg_GrupoUsuario();
		$vReg = $oGrupoUsuario->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
	
		$nTotal = $oGrupoUsuario->totalRegistro();
	
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_grupo_usuario"]."]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
}
