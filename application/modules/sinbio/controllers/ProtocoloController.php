<?php

class Sinbio_ProtocoloController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		   error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Coleta";
		$this->view->layout()->nmController = "protocolo";
		$this->view->layout()->nmPrograma = "Protocolo";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {

        $this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-protocolo.js"></script>
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
		
                
                //RECUPERA GRUPO USUARIO PARA SELECT
		$oUsuario = new Seg_Usuario();
		$this->view->vUsuario = $oUsuario->fetchAll(null, "nm_usuario ASC")->toArray();
                
                //RECUPERA NUCLEO
                $oNucleo = new Loc_Nucleo();
                $this->view->vNucleo = $oNucleo->fetchAll()->toArray();
                
		//INSERINDO NO BANCO
		$request = $this->_request;
		
		if ($request->getParam("sOP") == "cadastrar") {
			$vData = array(
					"sigla"		=> $request->getParam("fSigla"),
					"nm_protocolo"	=> $request->getParam("fProtocolo"),
                                        "descricao"	=> $request->getParam("fDescricao"),
                                        "seg_usuario_id"	=> $request->getParam("fIdUsuario")
			);
			
			
			$sAtributosChave = "sigla,nm_protocolo";
			$sNmAtributosChave = "";
			$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
			if ($sMsg) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Protocolo", $sMsg);
			}
			else {
				try {
                                    
                                        $oProtocolo = new Protocolo_Protocolo();
                                        
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oProtocolo->insert($vData,"cadastrar-protocolo",$vUsuarioLogado["id"]);

					if (!$nId) {
						$sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Protocolo", $oProtocolo->getErroMensagem());
						$bErro = strstr($sString,"1062");
						if ($bErro){
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Protocolo","Protocolo já existente no sistema.");
						}
						else {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Protocolo", $sString);
						}
						
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/sinbio/protocolo');						
					}
				}
				catch (Zend_Db_Exception $e) {
					$sString = $e->getMessage();
					$bErro = strstr($sString,"1062");					
					if ($bErro){
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Protocolo","Protocolo já existente no sistema.");
					}
					else {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Protocolo", $sString);
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
		
                
                $oProtocolo = new Protocolo_Protocolo();
                $oUsuario = new Seg_Usuario();
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vProtocolo = $oProtocolo->find($nId)->toArray();
			$vProtocolo = $vProtocolo[0];
                        
                        //RECUPERA  USUARIO PARA SELECT
			$this->view->vUsuario = $oUsuario->fetchAll(null, "nm_usuario ASC")->toArray();
			                        
			//VALIDA SE O USUARIO EXISTE
			if (count($vProtocolo)) {
				$this->view->nId		= $vProtocolo["id"];
				$this->view->sNmSigla	= $vProtocolo["sigla"];
				$this->view->sNmProtocolo	= $vProtocolo["nm_protocolo"];	
                                $this->view->sDescricao	= $vProtocolo["descricao"];	
                                $this->view->nIdUsuario	= $vProtocolo["seg_usuario_id"];
                                $this->view->nIdNucleo	= $vProtocolo["loc_nucleo_id"];
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId			= $request->getParam("nId");
					$sNmSigla		= $request->getParam("fSigla");
					$sNmProtocolo		= $request->getParam("fProtocolo");
                                        $sDescricao		= $request->getParam("fDescricao");
                                        $nIdUsuario	= $request->getParam("fIdUsuario");
				
					$vData = array(
							"id"			=> $nId,
							"sigla"		=> $sNmSigla,
							"nm_protocolo"	=> $sNmProtocolo,
                                                        "descricao"	=>  $sDescricao,
                                                        "seg_usuario_id" => $nIdUsuario
                                                      
					);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oProtocolo->update($vData, $sWhere, "alterar-protocolo", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Protocolo(s) foi alterado com sucesso.");
						$this->_redirect('/protocolo');
					}
					else {
						//UtilsFile::printvardie($oModulo->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Protocolo)", $oProtocolo->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Protocolo(s)", "Este Protocolo não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/protocolo');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Protocolo(s)", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/protocolo');
		}//VALIDA O ID
	}
        
        
        public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
                
		$vId = $request->getParam("fId");
		
                
                $oProtocolo = new Protocolo_Protocolo();
                
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oProtocolo->find($nId)->toArray();
				$sWhere = "id =".$nId;
				$oProtocolo->delete($vData, $sWhere, "excluir-protocolo", $vUsuarioLogado["id"]);
			}
			
			if ($oProtocolo->getErroMensagem()) {
				$sString = $e->getMessage();
				$bErro = strstr($sString,"1062");
				if ($bErro){
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Protocolo","Protocolo já existente no sistema.");
				}
				else {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Protocolo", $sString);
				}
				
				UtilsFile::recuperaMensagens(2, "Erro ao Excluir Protocolo", $oProtocolo->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Protocolo(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Protocolo(s)", "Você deve selecionar ao menos um registro.");
		}
	}
  
     
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("protocolo", $sQP, $vUsuarioLogado["id"]);
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
		
                
                
                $oProtocolo = new Protocolo_Protocolo();
                
		$vReg = $oProtocolo->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oProtocolo->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){
                        $oNucleo = new Loc_Nucleo();
                        $vNucleo = $oNucleo->find($reg["loc_nucleo_id"])->toArray();
                        
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["sigla"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_protocolo"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["descricao"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$vNucleo[0]["nm_nucleo"]."]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
        
        
        
        
}
