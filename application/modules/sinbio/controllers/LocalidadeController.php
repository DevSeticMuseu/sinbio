<?php

class Sinbio_LocalidadeController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        // error_reporting (E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Localidade";
        $this->view->layout()->nmController = "localidade";
        $this->view->layout()->nmPrograma = "Localidade";

        if (isset($_SESSION["sMsg"])) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/localidade-loc-localidade.js"></script>
		';
        $this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
        $this->view->layout()->nmOperacao = "Listar";
    }

    public function cadastrarAction() {
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
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

        //RECUPERA MUNICIPIO
        $oMunicipio = new Loc_Municipio();
        $this->view->vMunicipio = $oMunicipio->fetchAll()->toArray();

        //RECUPERA NUCLEO
        $oNucleo = new Loc_Nucleo();
        $this->view->vNucleo = $oNucleo->fetchAll()->toArray();

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {

            //UtilsFile::realizaUpload($arquivo, $destino);

            $vData = array(
                "nm_localidade" => $request->getParam("fNmLocalidade"),
                "loc_municipio_id" => $request->getParam("fIdMunicipio"),
                "loc_nucleo_id" => $request->getParam("fIdNucleo"),
                "latitude" => $request->getParam("flatitude"),
                "longitude" => $request->getParam("flongitude")
            );

            $sAtributosChave = "nm_localidade";
            $sNmAtributosChave = "Nome";
            $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

            if ($sMsg) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Município", $sMsg);
            } else {
                try {
                    $oLocalidade = new Loc_Localidade();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oLocalidade->insert($vData, "cadastrar-localidade", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Localidade", $oLocalidade->getErroMensagem());
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/localidade');
                    }
                } catch (Zend_Db_Exception $e) {
                    $sString = $e->getMessage();
                    $bErro = strstr($sString, "SQLSTATE[23000]");

                    if ($bErro) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Localidade", "Login já existente no sistema.");
                    } else {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Localidade", $sString);
                    }
                }
            }
        }
    }

    public function alterarAction() {
        $this->view->layout()->nmPrograma = "Localidades";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';


        $oLocalidade = new Loc_Localidade();
        $oMunicipio = new Loc_Municipio();
        $oNucleo = new Loc_Nucleo();

        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        //VALIDA O ID
        if ($nId) {
            $vLocalidade = $oLocalidade->find($nId)->toArray();
            $vLocalidade = $vLocalidade[0];

            //RECUPERA LOCALIDADE	
            $this->view->vMunicipio = $oMunicipio->fetchAll()->toArray();

            //RECUPERA NUCLEO
            $this->view->vNucleo = $oNucleo->fetchAll()->toArray();

            if (count($vLocalidade)) {
                $this->view->nId = $vLocalidade["id"];
                $this->view->sNome = $vLocalidade["nm_localidade"];
                $this->view->fIdMunicipio = $vLocalidade["loc_municipio_id"];
                $this->view->fIdNucleo = $vLocalidade["loc_nucleo_id"];
                $this->view->flatitude = $vLocalidade["latitude"];
                $this->view->flongitude = $vLocalidade["longitude"];

                //VALIDA SE FOI SUBMETIDO O FORMULARIO
                if ($sOP == "alterar") {

                    //RECUPERA CAMPOS DO FORMULARIO
                    $nId = $request->getParam("nId");
                    $sNomeLocalidade = $request->getParam("fNmLocalidade");
                    $nIdMunicipio = $request->getParam("nIdMunicipio");
                    $nIdNucleo = $request->getParam("nIdNucleo");
                    $sLatitude = $request->getParam("flatitude");
                    $sLongitude = $request->getParam("flongitude");



                    $vData = array(
                        "id" => $nId,
                        "nm_localidade" => $sNomeLocalidade,
                        "loc_municipio_id" => $nIdMunicipio,
                        "loc_nucleo_id" => $nIdNucleo,
                        "latitude" => $sLatitude,
                        "longitude" => $sLongitude
                    );
                    $sWhere = "id = " . $vData["id"];
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    //VERIFICA SE O REGISTRO VAI SER ALTERADO
                    if ($oLocalidade->update($vData, $sWhere, "alterar-localidade", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "A localidade foi alterado com sucesso.");
                        $this->_redirect('/localidade');
                    } else {
                        //UtilsFile::printvardie($oPrograma->getErroMensagem());
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Municipio", $oLocalidade->getErroMensagem());
                    }
                }//VALIDA SE FOI SUBMETIDO O FORMULARIO
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Localidade", "Este Programa não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/localidade');
            }//VALIDA SE O USUARIO EXISTE
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Localidade", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/municipio');
        }//VALIDA O ID
    }

    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");


        $oLocalidade = new Loc_Localidade();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $oLocalidade->find($nId);
                $sWhere = "id =" . $nId;
                $oLocalidade->delete($vData, $sWhere, "excluir-localidade", $vUsuarioLogado["id"]);
            }

            if ($oLocalidade->getErroMensagem()) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Localidade", $oLocalidade->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Localidade(s) removida(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Localidade", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("localidade", $sQP, $vUsuarioLogado["id"]);
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

        if ($sSortname == "nm_municipio")
            $sSortname = "loc_municipio_id";

        if ($sSortname == "nm_nucleo") {
            $sSortname = "loc_nucleo_id";
        }

        $sWhere = "";
        if ($sQuery != "" && $sCampo != "") {
            $sWhere = $sCampo . " LIKE '%" . $sQuery . "%' ";
        }

        $sOrder = $sSortname . " " . $sSortorder;

        $oLocalidade = new Loc_Localidade();
        $vReg = $oLocalidade->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oLocalidade->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {

            $Nucleo = new Loc_Nucleo();
            $regNucleo = $Nucleo->find($reg['loc_nucleo_id'])->toArray();

            $Municipio = new Loc_Municipio();
            $regMunicipio = $Municipio->find($reg['loc_municipio_id'])->toArray();


            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["nm_localidade"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $regNucleo[0]["nm_nucleo"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $regMunicipio[0]["nm_municipio"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["latitude"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["longitude"] . "]]></cell>";



            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
