<?php

class Sinbio_ColecaoController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        error_reporting(E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Colecao";
        $this->view->layout()->nmController = "colecao";
        $this->view->layout()->nmPrograma = "Coleções";

        if (isset($_SESSION["sMsg"])) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {

        $this->view->layout()->includeJs = '
				<script src="/js/sinbio/colecao-colecao.js"></script>
                                <script src="/js/dataTables.buttons.min.js"></script>
                                <script src="/js/sinbio/tabelas-datatable.js"></script>
		';

        $this->view->layout()->includeCss = '
                                <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
                                <link href="/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
		';
        $this->view->layout()->nmOperacao = "Listar";

        $joins = array();
        $joinUsuario = array(table => array('usuario' => 'seg_usuario'), onCols => ' colecao.id_curador = usuario.id',
                            colReturn => array('nm_usuario', 'email', 'telefone'), joinType => 'joinLeft');
        array_push($joins, $joinUsuario);

        $oColecao = new Colecao_Colecao();
        $vColecao = $oColecao->fetchAll(null, array('id DESC'), $joins);

        $this->view->paginator = $vColecao;
    }

    public function cadastrarAction() {


        $this->view->layout()->nmController = "colecao";
        $this->view->layout()->nmPrograma = "Coleção";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
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
                "nm_colecoes" => $request->getParam("fColecoes"),
                "sigla" => $request->getParam("fSigla"),
                "id_curador" => $request->getParam("fIdCurador")
            );


            $sAtributosChave = "";
            $sNmAtributosChave = "Razão  ";
            $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

            if ($sMsg) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Coleção", $sMsg);
            } else {
                try {
                    $oColecao = new Colecao_Colecao();

                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oColecao->insert($vData, "cadastrar-colecao", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Coleção", $oColecao->getErroMensagem());
                        $bErro = strstr($sString, "1062");
                        if ($bErro) {
                            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Coleção", "Coleção já existente no sistema.");
                        } else {
                            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Coleção", $sString);
                        }
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/colecao');
                    }
                } catch (Zend_Db_Exception $e) {
                    $sString = $e->getMessage();
                    $bErro = strstr($sString, "1062");
                    if ($bErro) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Coleção", "Módulo já existente no sistema.");
                    } else {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Coleção", $sString);
                    }
                }
            }
        }
    }

    public function alterarAction() {


        $this->view->layout()->nmPrograma = "Coleções";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';

        $oColecao = new Colecao_Colecao();
        $oCurador = new Seg_Usuario();
        $this->view->vCurador = $oCurador->fetchAll()->toArray();


        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        //VALIDA O ID
        if ($nId) {
            $vColecao = $oColecao->find($nId)->toArray();
            $vColecao = $vColecao[0];

            //VALIDA SE O USUARIO EXISTE
            if (count($vColecao)) {
                $this->view->nId = $vColecao["id"];
                $this->view->nColecao = $vColecao["nm_colecoes"];
                $this->view->nSigla = $vColecao["sigla"];
                $this->view->nIdCurador = $vColecao["id_curador"];

                //VALIDA SE FOI SUBMETIDO O FORMULARIO
                if ($sOP == "alterar") {

                    //RECUPERA CAMPOS DO FORMULARIO
                    $nId = $request->getParam("nId");

                    $vData = array(
                        "id" => $request->getParam("nId"),
                        "nm_colecoes" => $request->getParam("fColecoes"),
                        "sigla" => $request->getParam("fSigla"),
                        "id_curador" => $request->getParam("fIdCurador"),
                    );

                    $sWhere = "id = " . $vData["id"];
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    //VERIFICA SE O REGISTRO VAI SER ALTERADO
                    if ($oColecao->update($vData, $sWhere, "alterar-colecao", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "A Coleção foi alterado com sucesso.");
                        $this->_redirect('/colecao');
                    } else {
                        //UtilsFile::printvardie($oModulo->getErroMensagem());
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Coleção", $oColecao->getErroMensagem());
                    }
                }//VALIDA SE FOI SUBMETIDO O FORMULARIO
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Coleção", "Este Coleção não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/colecao');
            }//VALIDA SE O USUARIO EXISTE
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Coleção", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/colecao');
        }//VALIDA O ID
    }

    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");

        $oColecao = new Colecao_Colecao();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $oColecao->find($nId)->toArray();
                $sWhere = "id =" . $nId;
                $oColecao->delete($vData, $sWhere, "excluir-colecao", $vUsuarioLogado["id"]);
            }

            if ($oColecao->getErroMensagem()) {
                $sString = $e->getMessage();
                $bErro = strstr($sString, "1062");
                if ($bErro) {
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Módulo", "Coleção já existente no sistema.");
                } else {
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Coleção", $sString);
                }

                UtilsFile::recuperaMensagens(2, "Erro ao Excluir Coleção", $oColecao->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Coleção removida(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Coleção", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("colecao", $sQP, $vUsuarioLogado["id"]);
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

        $oColecao = new Colecao_Colecao();
        $vReg = $oColecao->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oColecao->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {


            $oCurador = new Seg_Usuario();
            $vCurador = $oCurador->find($reg['id_curador'])->toArray();

            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["nm_colecoes"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["sigla"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vCurador[0]["nm_usuario"] . "]]></cell>";
            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
