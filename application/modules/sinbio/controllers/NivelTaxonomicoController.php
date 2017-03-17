<?php

class Sinbio_NivelTaxonomicoController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		  error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Taxonomico";
		$this->view->layout()->nmController = "nivel-taxonomico";
		$this->view->layout()->nmPrograma = "Nivel Taxonomico";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/taxonomia-nivel-taxonomico.js"></script>
		';
	
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
		$this->view->layout()->nmOperacao = "Listar";
	}
	
	public function cadastrarAction() {
		$this->view->layout()->nmPrograma = "Nivel Taxonomico";
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
					"nivel_taxonomico"		=> $request->getParam("fNivelTaxonomico"),
					"nivel_superior"	=> $request->getParam("fNivelSuperior"),
                                        "nm_nivel_taxonomico"	=> $request->getParam("fNome"),
			);
			
			
			$sAtributosChave = "nome";
			$sNmAtributosChave = "Nome do Nivel";
			$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
			if ($sMsg) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nivel Taxonomico", $sMsg);
			}
			else {
				try {
                                        $oNivelTaxonomico = new Amostra_NivelTaxonomico();
					
                                        $auth = Zend_Auth::getInstance();
					
                                        $vUsuarioLogado = $auth->getIdentity();
					
                                        $nId = $oNivelTaxonomico->insert($vData,"cadastrar-nivel-taxonomico",$vUsuarioLogado["id"]);

					if (!$nId) {
						$sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nivel Taxonomico", $oNivelTaxonomico->getErroMensagem());
						$bErro = strstr($sString,"1062");
						if ($bErro){
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrtrar Nivel Taxonomico","Nivel Taxonomico já existente no sistema.");
						}
						else {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nivel Taxonomico", $sString);
						}
						
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/nivel-taxonomico');						
					}
				}
				catch (Zend_Db_Exception $e) {
					$sString = $e->getMessage();
					$bErro = strstr($sString,"1062");					
					if ($bErro){
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nivel Taxonomico","Nivel Taxonomico já existente no sistema.");
					}
					else {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nivel Taxonomico", $sString);
					}
				}
			}
		}
	}
	
	public function alterarAction() {
		$this->view->layout()->nmPrograma = "Nivel Taxonomico";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
                $oNivelTaxonomico = new Amostra_NivelTaxonomico();
		
                $request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vNivelTaxonomico = $oNivelTaxonomico->find($nId)->toArray();
			$vNivelTaxonomico = $vNivelTaxonomico[0];
			
			//VALIDA SE O USUARIO EXISTE
			if (count($vNivelTaxonomico)) {
				$this->view->nId		= $vNivelTaxonomico["id"];
				$this->view->sNivelTaxonomico	= $vNivelTaxonomico["nivel_taxonomico"];
				$this->view->sNivelSuperior	= $vNivelTaxonomico["nivel_superior"];
                                $this->view->sNome	= $vNivelTaxonomico["nm_nivel_taxonomico"];
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId			= $request->getParam("nId");
					$sNivelTaxonomico	= $request->getParam("fNivelTaxonomico");
					$sNivelSuperior		= $request->getParam("fNivelSuperior");
                                        $sNome                  = $request->getParam("fNome");
                                        
					$vData = array(
							"id"			=> $request->getParam("nId"),
							"nivel_taxonomico"	=> $request->getParam("fNivelTaxonomico"),
							"nivel_superior"	=> $request->getParam("fNivelSuperior"),
                                                        "nm_nivel_taxonomico"                  => $request->getParam("fNome"),
					);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oNivelTaxonomico->update($vData, $sWhere, "alterar-nivel-taxonomico", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Nivel Taxonomico foi alterado com sucesso.");
						$this->_redirect('/nivel-taxonomico');
					}
					else {
						//UtilsFile::printvardie($oModulo->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Nivel Taxonomico", $oNivelTaxonomico->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Nivel Taxonomico", "Este Nivel Taxonomico não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/nivel-taxonomico');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Nivel Taxonomico", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/nivel-taxonomico');
		}//VALIDA O ID
	}
	
	public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
		$vId = $request->getParam("fId");
		
                $oNivelTaxonomico = new Amostra_NivelTaxonomico();
		
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oNivelTaxonomico->find($nId)->toArray();
				$sWhere = "id =".$nId;
				$oNivelTaxonomico->delete($vData, $sWhere, "excluir-nivel-taxonomico", $vUsuarioLogado["id"]);
			}
			
			if ($oNivelTaxonomico->getErroMensagem()) {
				$sString = $e->getMessage();
				$bErro = strstr($sString,"1062");
				if ($bErro){
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nivel Taxonomico","Nivel Taxonomico já existente no sistema.");
				}
				else {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Nivel Taxonomico", $sString);
				}
				
				UtilsFile::recuperaMensagens(2, "Erro ao Excluir Nivel Taxonomico", $oNivelTaxonomico->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Nivel(is) Taxonomico(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Nivel Taxonomico", "Você deve selecionar ao menos um registro.");
		}
	}
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("nivel-taxonomico", $sQP, $vUsuarioLogado["id"]);
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
		
                $oNivelTaxonomico = new Amostra_NivelTaxonomico();
		$vReg = $oNivelTaxonomico->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oNivelTaxonomico->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){		
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nivel_taxonomico"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nivel_superior"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["nm_nivel_taxonomico"]."]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
}
