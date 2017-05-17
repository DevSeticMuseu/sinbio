<?php

class Sinbio_UfController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        //error_reporting (E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Localidade";
        $this->view->layout()->nmController = "uf";
        $this->view->layout()->nmPrograma = "UF";

        if (isset($_SESSION["sMsg"])) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
                                <script src="/js/jquery.dataTables.min.js"></script>
                                <script src="/js/dataTables.buttons.min.js"></script>
                                <script src="/js/sinbio/tabelas-datatable.js"></script>
                                <script src="/js/sinbio/localidade-loc-uf.js"></script>
		';

        $this->view->layout()->includeCss = '
                        <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
                        <link href="/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
		';


        $this->view->layout()->nmOperacao = "Listar";

        $uf = new Loc_Uf();
        $this->view->paginator = $uf->fetchAll();
    }

    public function cadastrarAction() {
        $this->view->layout()->nmPrograma = "UF";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';


        $oPais = new Loc_Pais();
        $this->view->vPais = $oPais->fetchAll()->toArray();

        $oRegiao = new Loc_Regiao();
        $this->view->vRegiao = $oRegiao->fetchAll()->toArray();

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {
            $vData = array(
                "loc_pais_id" => $request->getParam("fIdPais"),
                "loc_regiao_id" => $request->getParam("fIdRegiao"),
                "sigla" => $request->getParam("fSigla"),
                "nm_uf" => $request->getParam("fNmUf"),
            );


            $sAtributosChave = "sigla,nm_uf";
            $sNmAtributosChave = "";
            $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

            if ($sMsg) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar UF", $sMsg);
            } else {
                try {
                    $oUf = new Loc_Uf();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oUf->insert($vData, "cadastrar-uf", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar UF", $oUf->getErroMensagem());
                        $bErro = strstr($sString, "1062");
                        if ($bErro) {
                            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar UF", "UF já existente no sistema.");
                        } else {
                            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar UF", $sString);
                        }
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/uf');
                    }
                } catch (Zend_Db_Exception $e) {
                    $sString = $e->getMessage();
                    $bErro = strstr($sString, "1062");
                    if ($bErro) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar UF", "UF já existente no sistema.");
                    } else {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar UF", $sString);
                    }
                }
            }
        }
    }

    public function alterarAction() {
        $this->view->layout()->nmPrograma = "UF";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';

        $oUf = new Loc_Uf();

        $oPais = new Loc_Pais();
        $this->view->vPais = $oPais->fetchAll()->toArray();

        $oRegiao = new Loc_Regiao();
        $this->view->vRegiao = $oRegiao->fetchAll()->toArray();

        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        //VALIDA O ID
        if ($nId) {
            $vUf = $oUf->find($nId)->toArray();
            $vUf = $vUf[0];

            //VALIDA SE O USUARIO EXISTE
            if (count($vUf)) {
                $this->view->nId = $vUf["id"];
                $this->view->nIdPais = $vUf["loc_pais_id"];
                $this->view->nIdRegiao = $vUf["loc_regiao_id"];
                $this->view->sNmSigla = $vUf["sigla"];
                $this->view->sNmUf = $vUf["nm_uf"];

                //VALIDA SE FOI SUBMETIDO O FORMULARIO
                if ($sOP == "alterar") {

                    //RECUPERA CAMPOS DO FORMULARIO
                    $nId = $request->getParam("nId");
                    $sNmSigla = $request->getParam("fSigla");
                    $sNmUf = $request->getParam("fNmUf");

                    $vData = array(
                        "id" => $request->getParam("nId"),
                        "loc_pais_id" => $request->getParam("fIdPais"),
                        "loc_regiao_id" => $request->getParam("fIdRegiao"),
                        "sigla" => $request->getParam("fSigla"),
                        "nm_uf" => $request->getParam("fNmUf"),
                    );

                    $sWhere = "id = " . $vData["id"];
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    //VERIFICA SE O REGISTRO VAI SER ALTERADO
                    if ($oUf->update($vData, $sWhere, "alterar-uf", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O UF foi alterado com sucesso.");
                        $this->_redirect('/uf');
                    } else {
                        //UtilsFile::printvardie($oModulo->getErroMensagem());
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar UF", $oUf->getErroMensagem());
                    }
                }//VALIDA SE FOI SUBMETIDO O FORMULARIO
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar UF", "Este Módulo não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/uf');
            }//VALIDA SE O USUARIO EXISTE
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar UF", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/uf');
        }//VALIDA O ID
    }

    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;

        $vId = $request->getParam("fId");

        $oUf = new Loc_Uf();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $oUf->find($nId)->toArray();
                $sWhere = "id = $nId";
                $oUf->delete($vData, $sWhere, "excluir-uf", $vUsuarioLogado["id"]);
            }

            if ($oUf->getErroMensagem()) {
                $sString = $oUf->getErroMensagem();
                $bErro = strstr($sString, "23503");
                if ($bErro) {
                    $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao deletar UF", "Existem municípios utilizando este estado. ");
                } else{
                    $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao deletar UF", $sString);
                }
                
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "UF(s) removido(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar UF", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("uf", $sQP, $vUsuarioLogado["id"]);
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

        $sWhere = "";
        if ($sQuery != "" && $sCampo != "") {
            $sWhere = $sCampo . " LIKE '%" . $sQuery . "%' ";
        }
        $sOrder = $sSortname . " " . $sSortorder;

        $oUf = new Loc_Uf();
        $vReg = $oUf->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oUf->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {
            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["sigla"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["nm_uf"] . "]]></cell>";
            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
