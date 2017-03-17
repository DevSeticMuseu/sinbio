<?php

class Sinbio_VariaveisController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        error_reporting (E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Coleta";
        $this->view->layout()->nmController = "variaveis";
        $this->view->layout()->nmPrograma = "Variaveis";

        if ($_SESSION["sMsg"]) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-variaveis.js"></script>
		';

        $this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
				';
        $this->view->layout()->nmOperacao = "Listar";
    }

    public function cadastrarAction() {
        $this->view->layout()->nmPrograma = "variaveis";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';

        //ALIMENTANDO SELECT DE UNIDADE
        $oUnidadeMedida = new Protocolo_UnidadeMedida();
        $this->view->vUnidade = $oUnidadeMedida->fetchAll()->toArray();
        
        //
        

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {
            try {
                $vData = array(
                    "coleta_unidade_medida_id" => $request->getParam("fIdUnidadeMedida"),
                    "nm_variavel" => $request->getParam("fNmVariavel"),
                    "descricao" => $request->getParam("fDescricao"),
                    "ordem" => $request->getParam("fOrdem"),
                    "nivel_amostra" => $request->getParam("fNivelAmostra"),
                    "obrigatorio" => $request->getParam("fObrigatorio")
                );

                $sAtributosChave = "coleta_unidade_medida_id";
                $sNmAtributosChave = "Nome da Unidade de Medida";
                $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

                if ($sMsg) {
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Variavel", $sMsg);
                } else {
                    $oVariavel = new Protocolo_Variaveis();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oVariavel->insert($vData, "cadastrar-variavel", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Variavel", $oVariavel->getErroMensagem());
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/variaveis');
                    }
                }
            } catch (Zend_Db_Exception $e) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Variavel", $e);
            }
        }
    }

    public function alterarAction() {
		$this->view->layout()->nmPrograma = "Variaveis";
		$this->view->layout()->nmOperacao = "Alterar";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '
		
		';
		
                $UnidadeMedida = new Protocolo_UnidadeMedida();
                $Variavel = new Protocolo_Variaveis();
                
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
                        $vVariavel = $Variavel->find($nId)->toArray();
                        $vVariavel = $vVariavel[0];
                    
			
                           //RECUPERA O UNIDADE
                           $this->view->vUnidade = $UnidadeMedida->fetchAll()->toArray();
                           
                           
                         
                           
                         //VALIDA SE O USUARIO EXISTE
			if (count($vVariavel)) {
				$this->view->nId		= $vVariavel["id"];
                                $this->view->nIdUnidadeMedida = $vVariavel["coleta_unidade_medida_id"];
                                $this->view->sNmVariavel	= $vVariavel["nm_variavel"];
                                $this->view->sDescricao	= $vVariavel["descricao"];
                                $this->view->sOrdem	= $vVariavel["ordem"];
                                $this->view->sNivelAmostra	= $vVariavel["nivel_amostra"];
                                $this->view->sObrigtorio	= $vVariavel["obrigatorio"];
                                
                                
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					
					$vData = array(
                                                        "id"			=> $request->getParam("nId"),
                                                        "coleta_unidade_medida_id" => $request->getParam("fIdUnidade"),
                                                        "nm_variavel" => $request->getParam("fNmVariavel"),
                                                        "descricao" => $request->getParam("fDescricao"),
                                                        "ordem" => $request->getParam("fOrdem"),
                                                        "nivel_amostra" => $request->getParam("fNivelAmostra"),
                                                        "obrigatorio" => $request->getParam("fObrigatorio")
                                                        );
						
					$sWhere = "id = ".$vData["id"];
					$auth = Zend_Auth::getInstance();
					$vUsuarioLogado = $auth->getIdentity();
						
					//VERIFICA SE O REGISTRO VAI SER ALTERADO
					if ($Variavel->update($vData, $sWhere, "alterar-variavel", $vUsuarioLogado["id"])) {
						$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "A Variavel foi alterado com sucesso.");
						$this->_redirect('/variaveis');
					}
					else {
						//UtilsFile::printvardie($oPrograma->getErroMensagem());
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Variavel", $Variavel->getErroMensagem());
					}
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Variavel", "Esta Variavel não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/variaveis');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Variavel", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/variaveis');
		}//VALIDA O ID
	}
    
    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");

        $Variavel = new Protocolo_Variaveis();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $Variavel->find($nId);
                $sWhere = "id =" . $nId;
                $Variavel->delete($vData, $sWhere, "excluir-variavel", $vUsuarioLogado["id"]);
            }

            if ($Variavel->getErroMensagem()) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Variavel", $Variavel->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Variavel removida(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Variavel", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("variaveis", $sQP, $vUsuarioLogado["id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

    public function geraXmlAction() {

        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;

        $nPagina = ($request->getParam('page')) ? $request->getParam('page') : 1;
        $nRegistroPagina = ($request->getParam('rp')) ? $request->getParam('rp') : 15;
        $sSortname = ($request->getParam('sortname')) ? $request->getParam('sortname') : "id";
        $sSortorder = ($request->getParam('sortorder')) ? $request->getParam('sortorder') : "asc";
        $sQuery = ($request->getParam('query')) ? $request->getParam('query') : "";
        $sCampo = ($request->getParam('qtype')) ? $request->getParam('qtype') : "";

        if ($sSortname == "nm_unidade")
            $sSortname = "coleta_unidade_medida_id";


        $sWhere = "";
        if ($sQuery != "" && $sCampo != "") {
            $sWhere = $sCampo . " LIKE '%" . $sQuery . "%' ";
        }
        $sOrder = $sSortname . " " . $sSortorder;

        $oVariavel = new Protocolo_Variaveis();
        
        $vReg = $oVariavel->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oVariavel->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {

            $oUnidadeMedida = new Protocolo_UnidadeMedida();
            $vUnidadeMedida = $oUnidadeMedida->find($reg['coleta_unidade_medida_id'])->toArray();
            

            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vUnidadeMedida[0]["nm_unidade"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["nm_variavel"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["descricao"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["ordem"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["nivel_amostra"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["obrigatorio"] . "]]></cell>";
            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
