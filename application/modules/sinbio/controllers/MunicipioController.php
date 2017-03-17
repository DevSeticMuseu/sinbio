<?php

class Sinbio_MunicipioController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		 //error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Localidade";
		$this->view->layout()->nmController = "municipio";
		$this->view->layout()->nmPrograma = "Municípios";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}
	
	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/localidade-loc-municipio.js"></script>
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
		
	  
                //RECUPERA UF DO MUNICIPIO PARA O SELECT
                $oUf = new Loc_Uf();
                $this->view->vUf = $oUf->fetchAll()->toArray();
		
		//INSERINDO NO BANCO
		$request = $this->_request;
		
		if ($request->getParam("sOP") == "cadastrar") {
			
			//UtilsFile::realizaUpload($arquivo, $destino);
			
			$vData = array(
					"loc_uf_id"	=> $request->getParam("fIdUf"),
					"nm_municipio"		=> $request->getParam("fNmMunicipio"),
					"capital"				=> $request->getParam("fcapital"),
					"ddd"				=> $request->getParam("fddd")
			);
			
			$sAtributosChave = "nm_municipio";
			$sNmAtributosChave = "Nome";
			$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
			if ($sMsg) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Município", $sMsg);
			}
			else {
				try {
					$oMunicipio = new Loc_Municipio();
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oMunicipio->insert($vData,"cadastrar-municipio",$vUsuarioLogado["id"]);
					
					if (!$nId) {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Município", $oMunicipio->getErroMensagem());
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/municipio');
					}
				}
				catch (Zend_Db_Exception $e) {
					$sString = $e->getMessage();
					$bErro = strstr($sString,"SQLSTATE[23000]");
					
					if ($bErro){
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Município","Login já existente no sistema.");
					}
					else {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Município", $sString);
					}
				}
			}
		}
	}
        
        
        
        	
	public function alterarAction() {
		$this->view->layout()->nmPrograma = "Município";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
                
                $oMunicipio = new Loc_Municipio();
                $oUf = new Loc_Uf();
                
//		$oUsuario = new Seg_Usuario();
//		$oGrupoUsuario = new Seg_GrupoUsuario();
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vMunicipio = $oMunicipio->find($nId)->toArray();
			$vMunicipio = $vMunicipio[0];
			
			//RECUPERA GRUPO USUARIO PARA SELECT
			$this->view->vUf = $oUf->fetchAll()->toArray();
			
			//VALIDA SE O USUARIO EXISTE
			if (count($vMunicipio)) {
				$this->view->nId		= $vMunicipio["id"];
				$this->view->sNome		= $vMunicipio["nm_municipio"];
                                $this->view->sCapital		= $vMunicipio["capital"];
                                $this->view->sDDD		= $vMunicipio["ddd"];
				$this->view->nIdUf	= $vMunicipio["loc_uf_id"];
				
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId				= $request->getParam("nId");
					$nIdUf                          = $request->getParam("fIdUf");
                                        $sNomeMunicipio               = $request->getParam("fNmMunicipio");
                                        $sCapital               = $request->getParam("fCapital");
                                        $sDDD              = $request->getParam("fddd");
										
					
					
		$vData = array(
                        "id" => $nId,
                        "loc_uf_id" => $nIdUf,
                        "nm_municipio" => $sNomeMunicipio,
                        "capital" => $sCapital,
                        "ddd" => $sDDD,
                    );
                    $sWhere = "id = " . $vData["id"];
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    //VERIFICA SE O REGISTRO VAI SER ALTERADO
                    if ($oMunicipio->update($vData, $sWhere, "alterar-municipio", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Municipio foi alterado com sucesso.");
                        $this->_redirect('/sinbio/municipio');
					}
					else {
						//UtilsFile::printvardie($oPrograma->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Municipio", $oMunicipio->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Municipio", "Este Programa não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/sinbio/municipio');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Municipio", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/sinbio/municipio');
		}//VALIDA O ID
	}
        
        
        public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
		$vId = $request->getParam("fId");
		
                
                $oMunicipio = new Loc_Municipio();
		
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oMunicipio->find($nId);
				$sWhere = "id =".$nId;
				$oMunicipio->delete($vData, $sWhere, "excluir-municipio", $vUsuarioLogado["id"]);
			}
			
			if ($oMunicipio->getErroMensagem()) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Municipio", $oMunicipio->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Municipio(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Municipio", "Você deve selecionar ao menos um registro.");
		}
	}
	
	
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("municipio", $sQP, $vUsuarioLogado["id"]);
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
		
		if ($sSortname == "nm_uf")
			$sSortname = "loc_uf_id";
		
		$sWhere = "";
		if ($sQuery != "" && $sCampo != "") {
			$sWhere = $sCampo." LIKE '%".$sQuery."%' ";
		}

		$sOrder = $sSortname." ".$sSortorder;
		
		$oMuncipio = new Loc_Municipio();
		$vReg = $oMuncipio->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oMuncipio->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){
                    
                        $oUf = new Loc_Uf();
                        $regUf = $oUf->find($reg["loc_uf_id"])->toArray();
                        
		
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_municipio"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$regUf[0]["nm_uf"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["ddd"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["capital"]."]]></cell>";
                        
			
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;

               
	}
        
  
        
	

}
