<?php

class Sinbio_TaxonController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		   error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Taxonomia";
		$this->view->layout()->nmController = "taxon";
		$this->view->layout()->nmPrograma = "Taxon";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}
	
	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/taxonomia-taxon.js"></script>
		';
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
		$this->view->layout()->nmOperacao = "Listar";
	}
        
              public function cadastrarAction() {
		$this->view->layout()->nmOperacao = "Cadastrar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
			<script type="text/javascript">
				$(document).ready(function(){
					$("select").select2();
				});
			</script>
                       
		';
		
		$this->view->layout()->includeCss = '';
		
	  
                //RECUPERA USUARIO SELECT
//                $oUsuario = new Seg_Usuario();
//                $this->view->vUsuario = $oUsuario->fetchAll()->toArray();
                
                //RECUPERA USUARIO SELECT
                $oNivel = new Amostra_NivelTaxonomico();
                $this->view->vNivel = $oNivel->fetchAll()->toArray();
		
		//INSERINDO NO BANCO
		$request = $this->_request;
		
		if ($request->getParam("sOP") == "cadastrar") {
			
			//UtilsFile::realizaUpload($arquivo, $destino);
			
			$vData = array(
					"autor_ano"	=> $request->getParam("fAutorAno"),
					"taxonomia_nivel_taxonomico_id"		=> $request->getParam("fIdNivelTaxonomico"),
					"taxon"				=> $request->getParam("fTaxon"),
					"taxon_superior"				=> $request->getParam("fTaxonSuperior"),
                                        "dt_determinacao"				=> $request->getParam("fData"),
                                        "referencia"				=> $request->getParam("fReferencia"),
                                        "observacao"				=> $request->getParam("fObservacao"),
                            );
			
			$sAtributosChave = "";
			$sNmAtributosChave = "";
			$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
			if ($sMsg) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Taxon", $sMsg);
			}
			else {
				try {
					$oTaxon = new Amostra_Taxon();
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oTaxon->insert($vData,"cadastrar-taxon",$vUsuarioLogado["id"]);
					
					if (!$nId) {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Taxon", $oTaxon->getErroMensagem());
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/taxon');
					}
				}
				catch (Zend_Db_Exception $e) {
					$sString = $e->getMessage();
					$bErro = strstr($sString,"SQLSTATE[23000]");
					
					if ($bErro){
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Taxon","Taxon já existente no sistema.");
					}
					else {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Taxon", $sString);
					}
				}
			}
		}
	}
        
        
        
        	
	public function alterarAction() {
		$this->view->layout()->nmPrograma = "Taxon";
		$this->view->layout()->nmOperacao = "Taxon";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
                
                $oNivel = new Amostra_NivelTaxonomico();
//                $oUsuario = new Seg_Usuario();
                
                $oTaxon = new Amostra_Taxon();
                
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vTaxon = $oTaxon->find($nId)->toArray();
			$vTaxon = $vTaxon[0];
			
			//RECUPERA NIVEL SELECT
			$this->view->vNivel = $oNivel->fetchAll()->toArray();
			
                        //RECUPERA DETERMINADOR SELECT
//                        $this->view->vUsuario = $oUsuario->fetchAll()->toArray();
                        
			//VALIDA SE O USUARIO EXISTE
			if (count($vTaxon)) {
				$this->view->nId		= $vTaxon["id"];
				$this->view->sTaxon		= $vTaxon["taxon"];
                                $this->view->sTaxonSuperior	= $vTaxon["taxon_superior"];
                                $this->view->sData		= $vTaxon["dt_determinacao"];
                                $this->view->sReferencia	= $vTaxon["referencia"];
                                $this->view->sObservacao	= $vTaxon["observacao"];
				$this->view->sAutorAno          = $vTaxon["autor_ano"];
                                $this->view->nIdNivel		= $vTaxon["taxonomia_nivel_taxonomico"];
				
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
										
					
					
		$vData = array(
                                       "id" => $nId,
                                        "autor_ano"                      	=> $request->getParam("fAutorAno"),
					"taxonomia_nivel_taxonomico_id"		=> $request->getParam("fIdNivelTaxonomico"),
					"taxon"                 		=> $request->getParam("fTaxon"),
					"taxon_superior"				=> $request->getParam("fTaxonSuperior"),
                                        "dt_determinacao"				=> $request->getParam("fData"),
                                        "referencia"				=> $request->getParam("fReferencia"),
                                        "observacao"				=> $request->getParam("fObservacao"),
                       
                    );
                    $sWhere = "id = " . $vData["id"];
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    //VERIFICA SE O REGISTRO VAI SER ALTERADO
                    if ($oTaxon->update($vData, $sWhere, "alterar-taxon", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Taxon foi alterado com sucesso.");
                        $this->_redirect('/taxon');
					}
					else {
						//UtilsFile::printvardie($oPrograma->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Municipio", $oTaxon->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Taxon", "Este Taxon não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/taxon');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Taxon", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/taxon');
		}//VALIDA O ID
	}
        
        
        public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
		$vId = $request->getParam("fId");
		
                $oTaxon = new Amostra_Taxon();
                $oUsuario = new Seg_Usuario();
                $oNivel = new Amostra_NivelTaxonomico();
                
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oTaxon->find($nId);
				$sWhere = "id =".$nId;
				$oTaxon->delete($vData, $sWhere, "excluir-taxon", $vUsuarioLogado["id"]);
			}
			
			if ($oTaxon->getErroMensagem()) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Taxon", $oTaxon->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Taxon(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Taxon", "Você deve selecionar ao menos um registro.");
		}
	}
	
	
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("taxon", $sQP, $vUsuarioLogado["id"]);
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

//                if ($sSortname == "nm_usuario") {
//                    $sSortname = "seg_usuario_id";
//                }

                if ($sSortname == "nivel_taxonomico") {
                    $sSortname = "taxonomia_nivel_taxonomico";
                }
                
		$sWhere = "";
		if ($sQuery != "" && $sCampo != "") {
			$sWhere = $sCampo." LIKE '%".$sQuery."%' ";
		}

		$sOrder = $sSortname." ".$sSortorder;
		
		$oTaxon = new Amostra_Taxon();
		$vReg = $oTaxon->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oTaxon->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){
                    
//                        $oUsuario = new Seg_Usuario();
//                        $regUsuario = $oUsuario->find($reg["seg_usuario_id"])->toArray();
                        
                      
                        
                        $oNivelTaxonomico = new Amostra_NivelTaxonomico();
                        $regNivel = $oNivelTaxonomico->find($reg["taxonomia_nivel_taxonomico_id"])->toArray();
                        
                        
                        
//                        print_r($regNivel);
//                        print_r($regUsuario);
//                        exit();
//                        
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";                     
                        $xml .= "<cell><![CDATA[".$regNivel[0]["nivel_taxonomico"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["taxon_superior"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["taxon"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["autor_ano"]."]]></cell>";
			$xml .= "<cell><![CDATA[".UtilsDate::formataDataSemHoraToShow($reg["dt_determinacao"]) . "]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["referencia"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["observacao"]."]]></cell>";
                        
			
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;

               
	}
        
  
        
	

}
