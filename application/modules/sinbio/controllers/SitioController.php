<?php

class Sinbio_SitioController extends Zend_Controller_Action {

    public function init() {
        $this->view->layout()->nmModulo = "Módulo Sitio";
        $this->view->layout()->nmController = "sitio";
        $this->view->layout()->nmPrograma = "Sitio";

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
                                <script src="/js/sinbio/localidade-loc-sitio.js"></script>
		';

        $this->view->layout()->includeCss = '
                        <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
                        <link href="/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
		';


        $this->view->layout()->nmOperacao = "Listar";

        $uf = new Loc_Sitio();
        $this->view->paginator = $uf->fetchAll();
    }

    public function cadastrarAction() {
        $this->view->layout()->nmPrograma = "sitio";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';

        $oLocalidade = new Loc_Localidade();
        $this->view->vLocalidades = $oLocalidade->fetchAll()->toArray();

        $oProjecao = new Projecao_Projecao();
        $this->view->vProjecoes = $oProjecao->fetchAll()->toArray();

        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {
            $vData = array(
                "nm_sitio" => $request->getParam("fNmSitio"),
                "loc_localidade_id" => $request->getParam("fIdLocalidade"),
                "latitude" => $request->getParam("fLatitude"),
                "longitude" => $request->getParam("fLongitude"),
                "direcao_latitude" => $request->getParam("direcaoLatitude"),
                "direcao_longitude" => $request->getParam("direcaoLongitude"),
                "coleta_projecao_id" => $request->getParam("fIdProjecao")
            );


            $sAtributosChave = "nm_sitio, loc_localidade_id, latitude, longitude, direcao_latitude, direcao_longitude, coleta_projecao_id";
            $sNmAtributosChave = "Nome Sítio, Localidade, Latitude, Longitude, Direção Latitude, Direção Longitude, Projeção";
            $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

            if ($sMsg) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Sítio.", $sMsg);
            } else {
                try {
                    $oSitio = new Loc_Sitio();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oSitio->insert($vData, "cadastrar-sitio", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Sítio.", $oSitio->getErroMensagem());
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/sitio');
                    }
                } catch (Zend_Db_Exception $e) {
                    $sString = $e->getMessage();
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Sítio", $sString);
                }
            }
        }
    }

    public function alterarAction() {
        $this->view->layout()->nmPrograma = "sitio";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
                        <script src="/js/sinbio/localidade-loc-sitio.js"></script>
		';

        $this->view->layout()->includeCss = '';

        $oSitio = new Loc_Sitio();

        $oLocalidade = new Loc_Localidade();
        $this->view->vLocalidades = $oLocalidade->fetchAll()->toArray();

        $oProjecao = new Projecao_Projecao();
        $this->view->vProjecoes = $oProjecao->fetchAll()->toArray();

        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        if ($nId) {
            $vSitio = $oSitio->find($nId)->toArray();
            $vSitio = $vSitio[0];

            if (count($vSitio)) {
                $this->view->nId = $vSitio["id"];
                $this->view->sNmSitio = $vSitio["nm_sitio"];
                $this->view->nIdLocalidade = $vSitio["loc_localidade_id"];
                $this->view->nLatitude = $vSitio["latitude"];
                $this->view->nLongitude = $vSitio["longitude"];
                $this->view->sDirecaoLatitude = $vSitio["direcao_latitude"];
                $this->view->sDirecaoLongitude = $vSitio["direcao_longitude"];
                $this->view->nIdProjecao = $vSitio["coleta_projecao_id"];

                if ($sOP == "alterar") {
                    $nId = $request->getParam("nId");

                    $vData = array(
                        "id" => $request->getParam("nId"),
                        "nm_sitio" => $request->getParam("fNmSitio"),
                        "loc_localidade_id" => $request->getParam("fIdLocalidade"),
                        "latitude" => $request->getParam("fLatitude"),
                        "longitude" => $request->getParam("fLongitude"),
                        "direcao_latitude" => $request->getParam("direcaoLatitude"),
                        "direcao_longitude" => $request->getParam("direcaoLongitude"),
                        "coleta_projecao_id" => $request->getParam("fIdProjecao")
                    );

                    $sWhere = "id = " . $vData["id"];
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    if ($oSitio->update($vData, $sWhere, "alterar-sitio", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Sítio foi alterado com sucesso.");
                        $this->_redirect('/sitio');
                    } else {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Sítio", $oSitio->getErroMensagem());
                    }
                }
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Sítio", "Este Sítio não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/sitio');
            }
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Sítio", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/sitio');
        }
    }
    
    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");


        $oSitio = new Loc_Sitio();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $oSitio->find($nId);
                $sWhere = "id =" . $nId;
                $oSitio->delete($vData, $sWhere, "excluir-amostra", $vUsuarioLogado["id"]);
            }

            if ($oSitio->getErroMensagem()) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Sítio", $oSitio->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Sítio(s) removido(s) com sucesso.");
            }
        }
    }
}