<?php

class Sinbio_ProjetoProgramaController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		   error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo ProjetoPrograma";
		$this->view->layout()->nmController = "projeto-programa";
		$this->view->layout()->nmPrograma = "Projeto/Programa";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-projeto-programa.js"></script>
		';
	
		$this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
		$this->view->layout()->nmOperacao = "Listar";
	}
        
        
          public function cadastrarAction() {
		$this->view->layout()->nmPrograma = "Projetos/Programas";
		$this->view->layout()->nmOperacao = "Cadastrar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
                
                    //RECUPERA GRUPO USUARIO PARA SELECT
		$oCurador = new Seg_Usuario();
		$this->view->vCurador = $oCurador->fetchAll()->toArray();
		
		//INSERINDO NO BANCO
		$request = $this->_request;
		
		if ($request->getParam("sOP") == "cadastrar") {
			$vData = array(
					"sigla"		=> $request->getParam("fSigla"),
					"nm_projeto_programa"	=> $request->getParam("fProjetoPrograma"),
                                        "descricao"	=> $request->getParam("fDescricao"),
                                        "data_inicio" => $request->getParam("fDataInicio"),
                                        "data_fim" => $request->getParam("fDataFim"), 
                            "id_cordenador" => $request->getParam("fIdCurador"),
                            
			);
			
			
			$sAtributosChave = "sigla,nm_projeto_programa";
			$sNmAtributosChave = "";
			$sMsg = UtilsFile::verificaArrayVazio($vData,$sAtributosChave,$sNmAtributosChave);
			
			if ($sMsg) {
				$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Projeto/Programa", $sMsg);
			}
			else {
				try {
                                        $oProjetoPrograma = new ProjetoPrograma_ProjetoPrograma();
                                        
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
					$nId = $oProjetoPrograma->insert($vData,"cadastrar-projeto-programa",$vUsuarioLogado["id"]);

					if (!$nId) {
						$sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Projeto/Programa", $oProjetoPrograma->getErroMensagem());
						$bErro = strstr($sString,"1062");
						if ($bErro){
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Projeto/Programa","Projeto/Programa já existente no sistema.");
						}
						else {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Projeto/Programa", $sString);
						}
						
					}
					else {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
						$this->_redirect('/sinbio/projeto-programa');						
					}
				}
				catch (Zend_Db_Exception $e) {
					$sString = $e->getMessage();
					$bErro = strstr($sString,"1062");					
					if ($bErro){
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Projeto/Programa","Projeto/Programa já existente no sistema.");
					}
					else {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Projeto/Programa", $sString);
					}
				}
			}
		}
	}
        
           public function alterarAction() {
		$this->view->layout()->nmPrograma = "Projetos/Programas";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
                $oProjetoPrograma = new ProjetoPrograma_ProjetoPrograma();
                   $oCurador = new Seg_Usuario();
		$this->view->vCurador = $oCurador->fetchAll()->toArray();
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vProjetoPrograma = $oProjetoPrograma->find($nId)->toArray();
			$vProjetoPrograma = $vProjetoPrograma[0];
			
			//VALIDA SE O USUARIO EXISTE
			if (count($vProjetoPrograma)) {
				$this->view->nId		= $vProjetoPrograma["id"];
				$this->view->sNmSigla	= $vProjetoPrograma["sigla"];
				$this->view->sNmProjetoPrograma	= $vProjetoPrograma["nm_projeto_programa"];	
                                $this->view->sDescricao	= $vProjetoPrograma["descricao"];
                                 $this->view->sDtInicio	= $vProjetoPrograma["data_inicio"];
                                $this->view->sDtFim	= $vProjetoPrograma["data_fim"];
                                $this->view->nIdCurador       = $vProjetoPrograma["id_cordenador"];
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId			= $request->getParam("nId");
					$sNmSigla		= $request->getParam("fSigla");
					$sProjetoPrograma		= $request->getParam("fProjetoPrograma");
                                        $sDescricao		= $request->getParam("fDescricao");
				
					$vData = array(
							"id"			=> $nId,
							"sigla"		=> $sNmSigla,
							"nm_projeto_programa"	=> $sProjetoPrograma,
                                                        "descricao"	=>  $sDescricao,
                                            "data_inicio" => $request->getParam("fDataInicio"),
                                                        "data_fim" => $request->getParam("fDataFim"),
                                            "id_cordenador" => $request->getParam("fIdCurador"),
					);
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($oProjetoPrograma->update($vData, $sWhere, "alterar-projeto-programa", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Projeto(s)/Programa(s) foi alterado com sucesso.");
						$this->_redirect('/projeto-programa');
					}
					else {
						//UtilsFile::printvardie($oModulo->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Projeto(s)/Programa(s)", $oProjetoPrograma->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Projeto(s)/Programa(s)", "Este Módulo não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/projeto-programa');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Projeto(s)/Programa(s)", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/projeto-programa');
		}//VALIDA O ID
	}
        
        public function excluirAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->_request;
                
		$vId = $request->getParam("fId");
		
                $oProjetoPrograma = new ProjetoPrograma_ProjetoPrograma();
                
		
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		
		if (count($vId)) {
			foreach ($vId as $nId) {
				$vData = $oProjetoPrograma->find($nId)->toArray();
				$sWhere = "id =".$nId;
				$oProjetoPrograma->delete($vData, $sWhere, "excluir-programa-projeto", $vUsuarioLogado["id"]);
			}
			
			if ($oProjetoPrograma->getErroMensagem()) {
				$sString = $e->getMessage();
				$bErro = strstr($sString,"1062");
				if ($bErro){
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Projeto/Programa","UF já existente no sistema.");
				}
				else {
					$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Projeto/Programa", $sString);
				}
				
				UtilsFile::recuperaMensagens(2, "Erro ao Excluir Projeto/Programa", $oProjetoPrograma->getErroMensagem());
			}
			else {
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Projeto(s)/Programa(s) removido(s) com sucesso.");
			}
		}
		else {
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Projeto(s)/Programa(s)", "Você deve selecionar ao menos um registro.");
		}
	}
  
	
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("projeto-programa", $sQP, $vUsuarioLogado["id"]);
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
		
                
                $oProgramaProjeto = new ProjetoPrograma_ProjetoPrograma();
                
		$vReg = $oProgramaProjeto->fetchAll($sWhere,$sOrder,$nPagina,$nRegistroPagina)->toArray();
		
		$nTotal = $oProgramaProjeto->totalRegistro();
		
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= "<rows>";
		$xml .= "<page>".$nPagina."</page>";
		$xml .= "<total>".$nTotal."</total>";
		foreach($vReg as $reg){	
                    $oCurador = new Seg_Usuario();
                       $vCurador = $oCurador->find($reg['id_cordenador'])->toArray();
			$xml .= "<row id='".$reg["id"]."'>";
			$xml .= "<cell><![CDATA[".$reg["id"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["sigla"]."]]></cell>";
			$xml .= "<cell><![CDATA[".$reg["nm_projeto_programa"]."]]></cell>";
                        $xml .= "<cell><![CDATA[".$reg["descricao"]."]]></cell>";
                        $xml .= "<cell><![CDATA[" . $vCurador[0]["nm_usuario"] . "]]></cell>";
			$xml .= "</row>";
		}
	
		$xml .= "</rows>";
	
		echo $xml;
	}
        
    
	

}
