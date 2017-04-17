<?php

class Sinbio_ExpedicaoController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        error_reporting(E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Expedicao";
        $this->view->layout()->nmController = "expedicao";
        $this->view->layout()->nmPrograma = "Expedicao";

        if ($_SESSION["sMsg"]) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-expedicao.js"></script>
		';

        $this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
				';
        $this->view->layout()->nmOperacao = "Listar";
    }

    public function cadastrarAction() {
        $this->view->layout()->nmPrograma = "Expedicao";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '
                        <link rel="stylesheet" href="/css/sinbio/datepicker-change.css" />
                ';

        //ALIMENTANDO SELECT DE PROTOCOLO
        $oProtocolo = new Protocolo_Protocolo();
        $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();

        //ALIMENTANDO SELECT DE SITIO
        $oSitio = new Loc_Sitio();
        $this->view->vSitio = $oSitio->fetchAll()->toArray();


        //ALIMENTANDO SELECT DE MUNICÍPIO
        $oMunicipio = new Loc_Municipio();
        $this->view->vMunicipio = $oMunicipio->fetchAll()->toArray();

        //ALIMENTANDO SELECT DE UF
        $oUF = new Loc_Uf();
        $this->view->vUF = $oUF->fetchAll()->toArray();


        //ALIMENTANDO SELECT PROJETOS/PROGRAMAS
        $oProjetoPrograma = new ProjetoPrograma_ProjetoPrograma();
        $this->view->vProjetoPrograma = $oProjetoPrograma->fetchAll()->toArray();

        //ALIMENTANDO SELECT PROJEÇÃO
        $oProjecao = new Projecao_Projecao();
        $this->view->vProjecao = $oProjecao->fetchAll()->toArray();

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {
            try {
                $vData = array(
                    "coleta_protocolo_id" => $request->getParam("fIdProtocolo"),
                    "loc_sitio_id" => $request->getParam("fIdSitio"),
                    "loc_uf_id" => $request->getParam("fLocUfId"),
                    "loc_municipio" => $request->getParam("fIdMunicipio"),
                    "coleta_projeto_programa_id" => $request->getParam("fIdProjetoPrograma"),
                    "coleta_projecao_id" => $request->getParam("fIdProjecao"),
                    "data_inicio" => $request->getParam("fDataInicio"),
                    "data_fim" => $request->getParam("fDataFim"),
                    "observacao" => $request->getParam("fObservacao"),
                    "coleta_amostra" => $request->getParam("fColetaAmostra")
                );

                //ficar atento sobre isso...
                $sAtributosChave = "coleta_protocolo_id,loc_sitio_id";
                $sNmAtributosChave = "Nome do Protocolo, Nome do Sitio";
                $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

                if ($sMsg) {
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Expedicao", $sMsg);
                } else {
                    $oExpedicao = new Expedicao_Expedicao();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oExpedicao->insert($vData, "cadastrar-expedicao", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Expedição", $oExpedicao->getErroMensagem());
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/expedicao');
                    }
                }
            } catch (Zend_Db_Exception $e) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Expedição", $e);
            }
        }
    }

    public function alterarAction() {
        $this->view->layout()->nmPrograma = "Expedicao";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '

		';

        $oProtocolo = new Protocolo_Protocolo();
        $oSitio = new Loc_Sitio();
        $oProjetoPrograma = new ProjetoPrograma_ProjetoPrograma();
        $oExpedicao = new Expedicao_Expedicao();
        $oMunicipio = new Loc_Municipio();
        $oUF = new Loc_Uf();
        $oProjecao = new Projecao_Projecao();

        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        //VALIDA O ID
        if ($nId) {

            $vExpedicao = $oExpedicao->find($nId)->toArray();
            $vExpedicao = $vExpedicao[0];


            //RECUPERA O PROTOCOLO DA EXPEDIÇÃO
            $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();

            //RECUPERA O SITIO DA EXPEDIÇÃO
            $this->view->vSitio = $oSitio->fetchAll()->toArray();

            // RECUPERA O PROJETO PROGRAMA
            $this->view->vProjetoPrograma = $oProjetoPrograma->fetchAll()->toArray();

            // RECUPERA O Municipio DA EXPEDIÇÃO
            $this->view->vMunicipio = $oMunicipio->fetchAll()->toArray();

            // RECUPERA A UF DA EXPEDIÇÃO
            $this->view->vUF = $oUF->fetchAll()->toArray();

            // RECUPERA A PROJEÇÃO DA EXPEDIÇÃO
            $this->view->vProjecao = $oProjecao->fetchAll()->toArray();

            // VALIDA SE O USUARIO EXISTE
            if (count($vExpedicao)) {
                $this->view->nId = $vExpedicao["id"];
                $this->view->nIdProtocolo = $vExpedicao["coleta_protocolo_id"];
                $this->view->nIdUF = $vExpedicao["loc_uf_id"];
                $this->view->nIdMunicipio = $vExpedicao["loc_municipio"];
                $this->view->nIdSitio = $vExpedicao["loc_sitio_id"];
                $this->view->sLocalidade = $vExpedicao["coleta_amostra"];
                $this->view->nIdProjetoPrograma = $vExpedicao["coleta_projeto_programa_id"];
                $this->view->nIdProjecao = $vExpedicao["coleta_projecao_id"];
                $this->view->sDtInicio = $vExpedicao["data_inicio"];
                $this->view->sDtFim = $vExpedicao["data_fim"];
                $this->view->sObservacao = $vExpedicao["observacao"];

                //VALIDA SE FOI SUBMETIDO O FORMULARIO
                if ($sOP == "alterar") {
                    $vData = array(
                        "id" => $request->getParam("nId"),
                        "coleta_protocolo_id" => $request->getParam("fIdProtocolo"),
                        "loc_sitio_id" => $request->getParam("fIdSitio"),
                        "loc_uf_id" => $request->getParam("fLocUfId"),
                        "loc_municipio" => $request->getParam("fIdMunicipio"),
                        "coleta_projeto_programa_id" => $request->getParam("fIdProjetoPrograma"),
                        "coleta_projecao_id" => $request->getParam("fIdProjecao"),
                        "data_inicio" => $request->getParam("fDataInicio"),
                        "data_fim" => $request->getParam("fDataFim"),
                        "observacao" => $request->getParam("fObservacao"),
                        "coleta_amostra" => $request->getParam("fColetaAmostra")
                    );

                    $sWhere = "id = " . $vData["id"];
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    //VERIFICA SE O REGISTRO VAI SER ALTERADO
                    if ($oExpedicao->update($vData, $sWhere, "alterar-expedicao", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "A Expedição foi alterado com sucesso.");
                        $this->_redirect('/expedicao');
                    } else {
                        //UtilsFile::printvardie($oPrograma->getErroMensagem());
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Expedição", $oExpedicao->getErroMensagem());
                    }
                }//VALIDA SE FOI SUBMETIDO O FORMULARIO
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Expedição", "Este Expedição não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/expedicao');
            }//VALIDA SE O USUARIO EXISTE
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Expedição", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/expedicao');
        }//VALIDA O ID
    }

    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");


        $oExpedicao = new Expedicao_Expedicao();
        $oExpedicaoParticipantes = new Expedicao_ParticipantesExpedicao();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {

                //  $vDataParticipantes = $oExpedicaoParticipantes->find($nId);

                $vData = $oExpedicao->find($nId);
                $sWhere = "id =" . $nId;



                $oExpedicaoParticipantes->delete("coleta_expedicao_id = $nId", "excluir-participantes-expedicao", $vUsuarioLogado["id"]);

                $oExpedicao->delete($sWhere, "excluir-expedicao", $vUsuarioLogado["id"]);
            }

            if ($oExpedicao->getErroMensagem()) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Expedição", $oExpedicao->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Expedição removida(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Expedição", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("expedicao", $sQP, $vUsuarioLogado["id"]);
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

        if ($sSortname == "nm_protocolo")
            $sSortname = "coleta_protocolo_id";

        if ($sSortname == "nm_sitio")
            $sSortname = "loc_sitio_id";


        $sWhere = "";
        if ($sQuery != "" && $sCampo != "") {
            $sWhere = $sCampo . " LIKE '%" . $sQuery . "%' ";
        }
        $sOrder = $sSortname . " " . $sSortorder;

        $oExpedicao = new Expedicao_Expedicao();

        $vReg = $oExpedicao->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oExpedicao->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {

            $oProtocolo = new Protocolo_Protocolo();
            $vProtocolo = $oProtocolo->find($reg['coleta_protocolo_id'])->toArray();

            $oMunicipio = new Loc_Municipio();
            $vMunicipio = $oMunicipio->find($reg['loc_municipio'])->toArray();

            $oUF = new Loc_Uf();
            $vUF = $oUF->find($reg['loc_uf_id'])->toArray();

            $oSitio = new Loc_Sitio();
            $vSitio = $oSitio->find($reg['loc_sitio_id'])->toArray();

            $oProjetoPrograma = new ProjetoPrograma_ProjetoPrograma();
            $vProjetoPrograma = $oProjetoPrograma->find($reg['coleta_projeto_programa_id'])->toArray();

            $oProjecao = new Projecao_Projecao();
            $vProjecao = $oProjecao->find($reg['coleta_projecao_id'])->toArray();

            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vProtocolo[0]["nm_protocolo"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vUF[0]["nm_uf"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vMunicipio[0]["nm_municipio"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vSitio[0]["nm_sitio"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vProjetoPrograma[0]["nm_projeto_programa"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vProjecao[0]["sistema_projecao"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . UtilsDate::formataDataSemHoraToShow($reg["data_inicio"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[" . UtilsDate::formataDataSemHoraToShow($reg["data_fim"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["observacao"] . "]]></cell>";
            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
