<?php

class Sinbio_AmostraController extends Zend_Controller_Action {

    public function init() {


        error_reporting(E_ALL & ~E_NOTICE);

        $this->view->layout()->nmModulo = "Módulo Amostra";
        $this->view->layout()->nmController = "amostra";
        $this->view->layout()->nmPrograma = "Amostra";

        if ($_SESSION["sMsg"]) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/coleta-amostra.js"></script>
		';

        $this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
				';
        $this->view->layout()->nmOperacao = "Listar";
    }

    public function cadastrarAction() {
        $this->view->layout()->nmPrograma = "Amostra";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';

        //ALIMENTANDO SELECT DE PROTOCOLO
        $oProtocolo = new Protocolo_Protocolo();
        $sOrder = "sigla";
        $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();

        //ALIMENTANDO SELECT EXPEDIÇÃO
        $oExpedicao = new Expedicao_Expedicao();
        $this->view->vExpedicao = $oExpedicao->findBySitio()->toArray();

        //ALIMENTANDO SELECT METODOS
        $oMetodo = new Protocolo_Metodo();
        $this->view->vMetodo = $oMetodo->fetchAll()->toArray();

        //ALIMENTANDO SELECT ATRATIVOS
        $oAtrativos = new Protocolo_Atrativos();
        $this->view->vAtrativos = $oAtrativos->fetchAll()->toArray();

        //ALIMENTANDO SELECT PROJEÇÃO
        $oProjecao = new Amostra_Projecao();
        $this->view->vProjecao = $oProjecao->fetchAll()->toArray();

        //ALIMENTANDO SELECT CONSERVAÇÃO
        $oConservacao = new Amostra_Conservacao();
        $this->view->vConservacao = $oConservacao->fetchAll()->toArray();

        //ALIMENTANDO SELECT DESTINAÇÃO
        $oDestinacao = new Amostra_Destinacao();
        $this->view->vDestinacao = $oDestinacao->fetchAll()->toArray();

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {
            try {
                $vData = array(
                    "coleta_protocolo_id" => $request->getParam("fIdProtocolo"),
                    "coleta_metodos_id" => $request->getParam("fIdMetodos"),
                    "coleta_expedicao_id" => $request->getParam("fIdExpedicao"),
                    "coleta_atrativos_id" => $request->getParam("fIdAtrativos"),
                    "coleta_projecao_id" => $request->getParam("fIdProjecao"),
                    "coleta_conservacao_id" => $request->getParam("fIdConservacao"),
                    "id_amostra_coleta" => $request->getParam("fIdAmostraColeta"),
                    "id_amostra_projeto" => $request->getParam("fidAmostraProjeto"),
                    "data_coleta" => $request->getParam("fDataColeta"),
                    "hora_coleta" => $request->getParam("fHoraColeta"),
                    "latitude" => $request->getParam("fLatitude"),
                    "longitude" => $request->getParam("fLongitude"),
                    "direcao_latitude" => $request->getParam("fDirecaoLatitude"),
                    "direcao_longitude" => $request->getParam("fDirecaoLongitude")
                );

                $aDestinacoes = $request->getParam("fIdDestinacao");

                $sAtributosChave = "coleta_protocolo_id,coleta_metodos_id,coleta_metodos_id";
                $sNmAtributosChave = "Nome do Protocolo, Nome do Metodo , Nome da Expedicao";
                $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

                if ($sMsg) {
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Amostra", $sMsg);
                } else {
                    $oAmostra = new Amostra_Amostra();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nIdUsuario = $vUsuarioLogado["id"];
                    $nId = $oAmostra->insert($vData, "cadastrar-amostra", $nIdUsuario);

                    $oAmostraDestinacao = new Amostra_AmostraDestinacao();
                    $oAmostraDestinacao->insert($aDestinacoes, $nId, "cadastrar-amostra", $nIdUsuario);

                    if (!$nId) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Amostra", $oAmostra->getErroMensagem());
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/amostra');
                    }
                }
            } catch (Zend_Db_Exception $e) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Amostra", $e);
            }
        }
    }

    public function alterarAction() {
        $this->view->layout()->nmPrograma = "Amostra";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '

		';
        
        $oProtocolo = new Protocolo_Protocolo();
        $oExpedicao = new Expedicao_Expedicao();
        $oMetodo = new Protocolo_Metodo();
        $oAmostra = new Amostra_Amostra();
        $oAtrativos = new Protocolo_Atrativos();
        $oProjecao = new Amostra_Projecao();
        $oConservacao = new Amostra_Conservacao();
        $oDestinacao = new Amostra_Destinacao();
        $oAmostraDestinacao = new Amostra_AmostraDestinacao;


        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        //VALIDA O ID
        if ($nId) {
            $vAmostraRetorno = $oAmostra->find($nId);
            $vAmostra = $vAmostraRetorno->toArray();
            $vAmostra = $vAmostra[0];

            $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();

            $this->view->vExpedicao = $oExpedicao->fetchAll()->toArray();

            $this->view->vMetodo = $oMetodo->fetchAll()->toArray();

            $this->view->vAtrativos = $oAtrativos->fetchAll()->toArray();

            $this->view->vProjecao = $oProjecao->fetchAll()->toArray();

            $this->view->vConservacao = $oConservacao->fetchAll()->toArray();

            $vDestinacoesRetorno = $oDestinacao->fetchAll()->toArray();
            
            $vAmostraRow = $vAmostraRetorno->current();
            $vAmostraDestinacoes = $oAmostraDestinacao->findDestinacoes($vAmostraRow);
            
            $vDestinacoes = array();
            foreach ($vDestinacoesRetorno as $destinacao) {
                foreach ($vAmostraDestinacoes as $amostraDestinacao) {
                    if($amostraDestinacao["id"] == $destinacao["id"]) {
                        $destinacao["selected"] = "selected";
                        $vDestinacoes[] = $destinacao;
                        continue 2;
                    } 
                }
                $vDestinacoes[] = $destinacao;
            }
            $this->view->vDestinacoes = $vDestinacoes;


            //VALIDA SE O USUARIO EXISTE
            if (count($vAmostra)) {
                $this->view->nId = $vAmostra["id"];
                $this->view->nIdProtocolo = $vAmostra["coleta_protocolo_id"];
                $this->view->nIdMetodo = $vAmostra["coleta_metodos_id"];

                $this->view->nIdAtrativos = $vAmostra["coleta_atrativos_id"];
                $this->view->nIdProjecao = $vAmostra["coleta_projecao_id"];
                $this->view->nIdConservacao = $vAmostra["coleta_conservacao_id"];
                $this->view->nIdDestinacao = $vAmostra["coleta_destinacao_id"];

                $this->view->nIdExpedicao = $vAmostra["coleta_expedicao_id"];

                $this->view->sIdAmostraColeta = $vAmostra["id_amostra_coleta"];
                $this->view->sIdAmostraProjeto = $vAmostra["id_amostra_projeto"];

                $this->view->sDtColeta = $vAmostra["data_coleta"];
                $this->view->sHoraColeta = $vAmostra["hora_coleta"];
                $this->view->sLatitude = $vAmostra["latitude"];
                $this->view->sLongitude = $vAmostra["longitude"];
                $this->view->sDirecaoLatitude = $vAmostra["direcao_latitude"];
                $this->view->sDirecaoLongitude = $vAmostra["direcao_longitude"];



                //VALIDA SE FOI SUBMETIDO O FORMULARIO
                if ($sOP == "alterar") {


                    $vData = array(
                        "id" => $request->getParam("nId"),
                        "coleta_protocolo_id" => $request->getParam("fIdProtocolo"),
                        "coleta_metodos_id" => $request->getParam("fIdMetodos"),
                        "coleta_expedicao_id" => $request->getParam("fIdExpedicao"),
                        "coleta_atrativos_id" => $request->getParam("fIdAtrativos"),
                        "coleta_projecao_id" => $request->getParam("fIdProjecao"),
                        "coleta_conservacao_id" => $request->getParam("fIdConservacao"),
                        "id_amostra_coleta" => $request->getParam("fIdAmostraColeta"),
                        "id_amostra_projeto" => $request->getParam("fidAmostraProjeto"),
                        "data_coleta" => $request->getParam("fDataColeta"),
                        "hora_coleta" => $request->getParam("fHoraColeta"),
                        "latitude" => $request->getParam("fLatitude"),
                        "longitude" => $request->getParam("fLongitude"),
                        "direcao_latitude" => $request->getParam("fDirecaoLatitude"),
                        "direcao_longitude" => $request->getParam("fDirecaoLongitude")
                    );

                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    //---------------ATUALIZANDO DESTINAÇÕES---------------
                    $sWhere = "coleta_amostra_id = " . $vData["id"];
                    $aDestinacoes = $request->getParam("fIdDestinacao");
                    $oAmostraDestinacao = new Amostra_AmostraDestinacao();

                    $oAmostraDestinacao->delete($sWhere, "alterar-usuario", $vUsuarioLogado["id"]);
                    $oAmostraDestinacao->insert($aDestinacoes, $nId, "alterar-usuario", $vUsuarioLogado["id"]);
                    //-----------------------------------------------------
                    
                    //VERIFICA SE O REGISTRO VAI SER ALTERADO
                    $sWhere = "id = " . $vData["id"];
                    if ($oAmostra->update($vData, $sWhere, "alterar-amostra", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Amostra foi alterado com sucesso.");
                        $this->_redirect('/sinbio/amostra');
                    } else {
                        //UtilsFile::printvardie($oPrograma->getErroMensagem());
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Amostra", $oAmostra->getErroMensagem());
                    }
                }//VALIDA SE FOI SUBMETIDO O FORMULARIO
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Amostra", "Este Amostra não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/amostra');
            }//VALIDA SE O USUARIO EXISTE
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Amostra", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/amostra');
        }//VALIDA O ID
    }

    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");


        $oAmostra = new Amostra_Amostra();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $oAmostra->find($nId);
                $sWhere = "id =" . $nId;
                $oAmostra->delete($vData, $sWhere, "excluir-amostra", $vUsuarioLogado["id"]);
            }

            if ($oAmostra->getErroMensagem()) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Amostra", $oAmostra->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Amostra removida(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Amostra", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("amostra", $sQP, $vUsuarioLogado["id"]);
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

        if ($sSortname == "nm_metodo")
            $sSortname = "coleta_metodos_id";


        $sWhere = "";
        if ($sQuery != "" && $sCampo != "") {
            $sWhere = $sCampo . " LIKE '%" . $sQuery . "%' ";
        }
        $sOrder = $sSortname . " " . $sSortorder;

        $oAmostra = new Amostra_Amostra();

        $vReg = $oAmostra->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oAmostra->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {

            $oProtocolo = new Protocolo_Protocolo();
            $vProtocolo = $oProtocolo->find($reg['coleta_protocolo_id'])->toArray();

            $oMetodo = new Protocolo_Metodo();
            $vMetodo = $oMetodo->find($reg['coleta_metodos_id'])->toArray();


            $oAtrativos = new Protocolo_Atrativos();
            $vAtrativos = $oAtrativos->find($reg['coleta_atrativos_id'])->toArray();

            $oProjecao = new Amostra_Projecao();
            $vProjecao = $oProjecao->find($reg['coleta_projecao_id'])->toArray();

            $oConservacao = new Amostra_Conservacao();
            $vConservacao = $oConservacao->find($reg['coleta_conservacao_id'])->toArray();

            $oDestinacao = new Amostra_Destinacao();
            $vDestinacao = $oDestinacao->find($reg['coleta_destinacao_id'])->toArray();

            $oUsuario = new Seg_Usuario();

            $oParticipantesAmostra = new Amostra_ParticipantesAmostra();
            $vParticipantesAmostra = $oParticipantesAmostra->fetchAll("coleta_amostra_id = " . $reg['id'])->toArray();

            $citacao = array();
            foreach ($vParticipantesAmostra as $vParticipante) {
                $vUsuario = $oUsuario->find($vParticipante['seg_usuario_id'])->toArray();
                $citacao[] = $vUsuario[0]['citacao'];
            }

            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["id_amostra_coleta"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . implode(", ", $citacao) . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vProtocolo[0]["nm_protocolo"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vMetodo[0]["nm_metodo"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["coleta_expedicao_id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vAtrativos[0]["nm_atrativos"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vConservacao[0]["conservacao_material"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vDestinacao[0]["nm_destinacao"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . UtilsDate::formataDataSemHoraToShow($reg["data_coleta"]) . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["hora_coleta"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["latitude"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["direcao_latitude"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["longitude"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["direcao_longitude"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $vProjecao[0]["sistema_projecao"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["id_amostra"] . "]]></cell>";
            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
