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
				<script src="/js/sinbio/coleta-amostra.js"></script>
                                <script src="/js/dataTables.buttons.min.js"></script>
                                <script src="/js/sinbio/tabelas-datatable.js"></script>
		';

        $this->view->layout()->includeCss = '
                        
		';


        $this->view->layout()->nmOperacao = "Listar";

        $joins = array();
        $joinExpedicao= array(table => array('expedicao' => 'coleta_expedicao'), onCols => ' amostra.coleta_expedicao_id = expedicao.id', colReturn => array('data_inicio', 'coleta_protocolo_id', 'loc_localidade_id'));
        array_push($joins, $joinExpedicao);
        $joinProtocolo = array(table => array('protocolo' => 'coleta_protocolo'), onCols => ' expedicao.coleta_protocolo_id = protocolo.id', colReturn => array('nm_protocolo'));
        array_push($joins, $joinProtocolo);
        $joinMetodo = array(table => array('metodo' => 'coleta_metodos'), onCols => ' amostra.coleta_metodos_id = metodo.id', colReturn => array('nm_metodo'));
        array_push($joins, $joinMetodo);
        $joinLocalidade = array(table => array('localidade' => 'loc_localidade'), onCols => ' expedicao.loc_localidade_id = localidade.id', colReturn => array('nm_localidade'));
        array_push($joins, $joinLocalidade);
        
        $oAmostra = new Amostra_Amostra();
        $vAmostra = $oAmostra->fetchAll(null, array('id DESC'), $joins);
        
        foreach ($vAmostra as $amostra) {
            $amostra['data_inicio'] = date("d/m/Y", strtotime($amostra['data_inicio']));
        }
        
        $this->view->paginator = $vAmostra;
    }

    public function cadastrarAction() {
        $op = $this->_request->getParam('op');
        if ($op == 'filtrarCep' || $op == 'filtrarExp') {
            $this->filtrar();
        } else if ($op == 'filtrarVariaveis') {
            $this->filtrarVaiaveis();
        } else {
            $this->view->layout()->nmPrograma = "Amostra";
            $this->view->layout()->nmOperacao = "Cadastrar";

            $this->view->layout()->includeJs = '
                            <script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
                            <script src="/js/sinbio/validacao.js" type="text/javascript"></script>
                            <script src="/js/sinbio/tabelas-datatable.js"></script>
                            <script src="/js/sinbio/amostra-scripts.js" type="text/javascript"></script>
                    ';

            $this->view->layout()->includeCss = '';

            $oUf = new Loc_Uf();
            $this->view->vUf = $oUf->fetchAll(null, array('nm_uf'))->toArray();

            $oProtocolo = new Protocolo_Protocolo();
            $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();

            $oProjecao = new Amostra_Projecao();
            $this->view->vProjecao = $oProjecao->fetchAll()->toArray();

            $oMetodo = new Protocolo_Metodo();
            $this->view->vMetodo = $oMetodo->fetchAll()->toArray();

            $oCategoriaVariaveis = new Amostra_CategoriaVariaveis();
            $this->view->vVariaveisCategoria = $oCategoriaVariaveis->fetchAll()->toArray();

            $oConservacao = new Amostra_Conservacao();
            $this->view->vConservacao = $oConservacao->fetchAll()->toArray();

            $oDestinacao = new Amostra_Destinacao();
            $this->view->vDestinacao = $oDestinacao->fetchAll()->toArray();

            $request = $this->_request;
            if ($request->getParam("sOP") == "cadastrar") {
                try {
                    $vData = array(
                        "coleta_expedicao_id" => $request->getParam("fIdExpedicao"),
                        "latitude" => $request->getParam("fIdLatitude"),
                        "longitude" => $request->getParam("fIdLongitude"),
                        "direcao_latitude" => $request->getParam("direcaoLatitude"),
                        "direcao_longitude" => $request->getParam("direcaoLongitude"),
                        "codigo_amostra_numero_coletor" => $request->getParam("fIdAmostraColeta"),
                        "coleta_metodos_id" => $request->getParam("fIdMetodos")
                    );
                    $sitio = $request->getParam("fIdSitio");
                    if (strlen(trim($sitio)) > 0)
                        $vData["loc_sitio_id"] = $sitio;

                    $coletaProjecao = $request->getParam("fIdProjecao");
                    if (strlen(trim($coletaProjecao)) > 0)
                        $vData["coleta_projecao_id"] = $coletaProjecao;

                    $dataColeta = $request->getParam("fIdDataColeta");
                    if (strlen(trim($dataColeta)) > 0)
                        $vData["data_coleta"] = $dataColeta;

                    $horaColeta = $request->getParam("fIdHoraColeta");
                    if (strlen(trim($horaColeta)) > 0)
                        $vData["hora_coleta"] = $horaColeta;

                    $variaveis = $request->getParam("fIdVariaveis");
                    if (strlen(trim($variaveis)) > 0)
                        $vData["coleta_variaveis_id"] = $variaveis;

                    $conservacao = $request->getParam("fIdConservacao");
                    if (strlen(trim($conservacao)) > 0)
                        $vData["coleta_conservacao_id"] = $conservacao;


                    $aDestinacoes = $request->getParam("fIdDestinacao");
                    $sVariaveis = $request->getParam("hVariaveis");
                    $aVariaveis = explode(',', $sVariaveis);

                    $sAtributosChave = "codigo_amostra_numero_coletor, coleta_metodos_id";
                    $sNmAtributosChave = "Código Amostra/Número Coletor , Método Coleta";
                    $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

                    if ($sMsg) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Amostra", $sMsg);
                    } else if ($aDestinacoes == null) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Amostra", "Selecione uma destinação.");
                    } else if ($vData['coleta_expedicao_id'] == "null") {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Amostra", "Selecione uma expedição.");
                    } else {
                        $oAmostra = new Amostra_Amostra();
                        $auth = Zend_Auth::getInstance();
                        $vUsuarioLogado = $auth->getIdentity();
                        $nIdUsuario = $vUsuarioLogado["id"];
                        $nId = $oAmostra->insert($vData, "cadastrar-amostra", $nIdUsuario);

                        $oAmostraDestinacao = new Amostra_AmostraDestinacao();
                        $oAmostraDestinacao->insert($aDestinacoes, $nId, "cadastrar-amostra", $nIdUsuario);

                        $oAmostraVariaveis = new Amostra_AmostraVariaveis();
                        $oAmostraVariaveis->insert($aVariaveis, $nId, "cadastrar-amostra", $nIdUsuario);

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
    }

    public function filtrarDadosExp() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $idExpedicao = $this->_request->getParam('idExpedicao');
        $oExpedicao = new Expedicao_Expedicao();
        $whereExp = "id = $idExpedicao";
        $aExpedicao = $oExpedicao->fetchAll($whereExp)->toArray();

        $idSitio = $aExpedicao[0]['loc_sitio_id'];
        $whereSitio = "sitio.id = $idSitio";

        $joins = array();
        $joinProjecao = array(table => array('projecao' => 'coleta_projecao'), onCols => ' sitio.coleta_projecao_id = projecao.id', colReturn => array('id', 'sistema_projecao'));
        array_push($joins, $joinProjecao);

        $oSitio = new Loc_Sitio();
        $json = $oSitio->fetchAll($whereSitio, null, $joins)->toArray();

        $this->_helper->json->sendJson($json);
    }

    public function filtrar() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $filtro = $this->getRequest()->getParam('filtro');
        if ($filtro == 'municipio')
            $aMunicipios = $this->filtrarMunicipios();
        if ($filtro == 'localidade')
            $aLocalidades = $this->filtrarLocalidades();
        if ($filtro == 'sitio')
            $aSitios = $this->filtrarSitios();

        $aExpedicoes = $this->filtrarExpedicoes();

        $json = array(expedicoes => $aExpedicoes,
            municipios => $aMunicipios,
            localidades => $aLocalidades,
            sitios => $aSitios);

        $this->_helper->json->sendJson($json);
    }

    public function filtrarMunicipios() {
        $idUf = $this->getRequest()->getParam('idUf');
        $where = "loc_uf_id = $idUf";
        $order = array('nm_municipio ASC');
        $oMunicipio = new Loc_Municipio();
        return $oMunicipio->fetchAll($where, $order)->toArray();
    }

    public function filtrarLocalidades() {
        $idMunicipio = $this->getRequest()->getParam('idMunicipio');
        $where = "loc_municipio_id = $idMunicipio";
        $order = array('nm_localidade ASC');
        $oLocalidade = new Loc_Localidade();
        return $oLocalidade->fetchAll($where, $order)->toArray();
    }

    public function filtrarSitios() {
        $idLocalidade = $this->getRequest()->getParam('idLocalidade');
        $where = "loc_localidade_id = $idLocalidade";
        $order = array('nm_sitio ASC');
        $oSitio = new Loc_Sitio();
        return $oSitio->fetchAll($where, $order)->toArray();
    }

    public function filtrarExpedicoes() {
        $joins = array();
        $joinLocalidades = array(table => array('localidade' => 'loc_localidade'), onCols => ' expedicao.loc_localidade_id = localidade.id', colReturn => array('nm_localidade'));
        array_push($joins, $joinLocalidades);

        $where = array();

        $data = $this->getRequest()->getParam('data');
        if (strlen(trim($data)) > 0)
            array_push($where, "expedicao.data_inicio = '$data'");

        $protocolo = $this->getRequest()->getParam('idProtocolo');
        if ($protocolo != "null")
            array_push($where, "expedicao.coleta_protocolo_id = '$protocolo'");

        $idUf = $this->getRequest()->getParam('idUf');
        if ($idUf != "null")
            array_push($where, "expedicao.loc_uf_id = $idUf");

        $idMunicipio = $this->getRequest()->getParam('idMunicipio');
        if ($idMunicipio != "null")
            array_push($where, "expedicao.loc_municipio_id = $idMunicipio");

        $idLocalidade = $this->getRequest()->getParam('idLocalidade');
        if ($idLocalidade != "null")
            array_push($where, "expedicao.loc_localidade_id = $idLocalidade");

        $order = array('data_inicio ASC');
        $oExpedicao = new Expedicao_Expedicao();
        return $oExpedicao->fetchAll($where, $order, $joins)->toArray();
    }

    public function filtrarVaiaveis() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $idCategoriaVariavel = $this->_request->getParam('idCategoriaVariavel');
        $where = "coleta_categoria_variaveis_id = $idCategoriaVariavel";
        $oVariaveis = new Amostra_Variaveis();
        $aVariaveis = $oVariaveis->fetchAll($where)->toArray();

        $this->_helper->json->sendJson($aVariaveis);
    }

    public function alterarAction() {
        $this->view->layout()->nmPrograma = "Amostra";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
                        <script src="/js/sinbio/tabelas-datatable.js"></script>
                        <script src="/js/sinbio/amostra-scripts.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '

		';

        $oAmostra = new Amostra_Amostra();
        $oProtocolo = new Protocolo_Protocolo();
        $oUf = new Loc_Uf();
        $oExpedicao = new Expedicao_Expedicao();
        $oSitio = new Loc_Sitio();
        $oProjecao = new Amostra_Projecao();
        $oMetodo = new Protocolo_Metodo();
        $oCategoriaVariaveis = new Amostra_CategoriaVariaveis();
        $oVariaveis = new Amostra_Variaveis();
        $oAmostraVariavel = new Amostra_AmostraVariaveis();
        $oConservacao = new Amostra_Conservacao();
        $oDestinacao = new Amostra_Destinacao();
        $oAmostraDestinacao = new Amostra_AmostraDestinacao();


        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        //VALIDA O ID
        if ($nId) {
            $vAmostraRetorno = $oAmostra->fetchAll(array("id = $nId"), array('id DESC'));
            $vAmostra = $vAmostraRetorno->toArray();
            $vAmostra = $vAmostra[0];
            $vAmostraRow = $vAmostraRetorno->current();

            $this->view->vUf = $oUf->fetchAll(null, array('nm_uf'))->toArray();

            $joins = array();
            $joinLocalidades = array(table => array('localidade' => 'loc_localidade'), onCols => ' expedicao.loc_localidade_id = localidade.id', colReturn => array('nm_localidade'));
            array_push($joins, $joinLocalidades);
            $this->view->vExpedicao = $oExpedicao->fetchAll(null, array('data_inicio ASC'), $joins)->toArray();
            
            $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();

            $this->view->vSitio = $oSitio->fetchAll(null, array('nm_sitio ASC'))->toArray();

            $this->view->vProjecao = $oProjecao->fetchAll()->toArray();

            $this->view->vMetodo = $oMetodo->fetchAll()->toArray();

            $this->view->vVariaveisCategoria = $oCategoriaVariaveis->fetchAll()->toArray();

            $vVariaveisRetorno = $oVariaveis->fetchAll()->toArray();
            $vAmostraVariaveis = $oAmostraVariavel->findVariaveis($vAmostraRow);

            $vVariaveis = array();
            foreach ($vVariaveisRetorno as $variavel) {
                foreach ($vAmostraVariaveis as $amostraVariavel) {
                    if ($amostraVariavel["id"] == $variavel["id"]) {
                        $variavel["selected"] = "selected";
                        $vVariaveis[] = $variavel;
                        continue 2;
                    }
                }
                $vVariaveis[] = $variavel;
            }
            $this->view->vVariaveis = $vVariaveis;

            $this->view->vConservacao = $oConservacao->fetchAll()->toArray();

            $vDestinacoesRetorno = $oDestinacao->fetchAll()->toArray();
            $vAmostraDestinacoes = $oAmostraDestinacao->findDestinacoes($vAmostraRow);

            $vDestinacoes = array();
            foreach ($vDestinacoesRetorno as $destinacao) {
                foreach ($vAmostraDestinacoes as $amostraDestinacao) {
                    if ($amostraDestinacao["id"] == $destinacao["id"]) {
                        $destinacao["selected"] = "selected";
                        $vDestinacoes[] = $destinacao;
                        continue 2;
                    }
                }
                $vDestinacoes[] = $destinacao;
            }
            $this->view->vDestinacoes = $vDestinacoes;

            if (count($vAmostra)) {
                $this->view->nId = $vAmostra["id"];
                $this->view->nIdExpedicao = $vAmostra["coleta_expedicao_id"];
                $this->view->nIdSitio = $vAmostra["loc_sitio_id"];
                $this->view->nLatitude = $vAmostra["latitude"];
                $this->view->nLongitude = $vAmostra["longitude"];
                $this->view->sDirecaoLatitude = $vAmostra["direcao_latitude"];
                $this->view->sDirecaoLongitude = $vAmostra["direcao_longitude"];
                $this->view->nIdProjecao = $vAmostra["coleta_projecao_id"];
                $this->view->sCodigoAmostraNumeroColetor = $vAmostra["codigo_amostra_numero_coletor"];
                $this->view->sDtColeta = isset($vAmostra["data_coleta"]) ? $vAmostra["data_coleta"] : '';
                $this->view->sHoraColeta = $vAmostra["hora_coleta"];
                $this->view->nIdMetodo = $vAmostra["coleta_metodos_id"];
                $this->view->nIdVariavel = $vAmostra["coleta_variaveis_id"];
                $this->view->nIdConservacao = $vAmostra["coleta_conservacao_id"];
                $this->view->nIdDestinacao = $vAmostra["coleta_destinacao_id"];

                //VALIDA SE FOI SUBMETIDO O FORMULARIO
                if ($sOP == "alterar") {

                    $vData = array(
                        "id" => $request->getParam("nId"),
                        "latitude" => $request->getParam("fIdLatitude"),
                        "longitude" => $request->getParam("fIdLongitude"),
                        "direcao_latitude" => $request->getParam("direcaoLatitude"),
                        "direcao_longitude" => $request->getParam("direcaoLongitude"),
                        "codigo_amostra_numero_coletor" => $request->getParam("fIdAmostraColeta"),
                        "coleta_metodos_id" => $request->getParam("fIdMetodos")
                    );

                    $expedicao = $request->getParam("fIdExpedicao");
                    $vData["coleta_expedicao_id"] = $expedicao != "null" ? $expedicao : $vAmostra["coleta_expedicao_id"];

                    $sitio = $request->getParam("fIdSitio");
                    if (strlen(trim($sitio)) > 0)
                        $vData["loc_sitio_id"] = $sitio;

                    $coletaProjecao = $request->getParam("fIdProjecao");
                    if (strlen(trim($coletaProjecao)) > 0)
                        $vData["coleta_projecao_id"] = $coletaProjecao;

                    $dataColeta = $request->getParam("fIdDataColeta");
                    if (strlen(trim($dataColeta)) > 0)
                        $vData["data_coleta"] = $dataColeta;

                    $horaColeta = $request->getParam("fIdHoraColeta");
                    if (strlen(trim($horaColeta)) > 0)
                        $vData["hora_coleta"] = $horaColeta;

                    $conservacao = $request->getParam("fIdConservacao");
                    if (strlen(trim($conservacao)) > 0)
                        $vData["coleta_conservacao_id"] = $conservacao;

                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    $aDestinacoes = $request->getParam("fIdDestinacao");

                    if ($aDestinacoes != null) {
                        $sWhere = "coleta_amostra_id = " . $vData["id"];
                        $oAmostraDestinacao = new Amostra_AmostraDestinacao();
                        $oAmostraDestinacao->delete($sWhere, "alterar-usuario", $vUsuarioLogado["id"]);
                        $oAmostraDestinacao->insert($aDestinacoes, $nId, "alterar-usuario", $vUsuarioLogado["id"]);

                        $sWhere = "id = " . $vData["id"];
                        if ($oAmostra->update($vData, $sWhere, "alterar-amostra", $vUsuarioLogado["id"])) {
                            $sWhere = "coleta_amostra_id = " . $vData["id"];
                            $sVariaveis = $request->getParam("hVariaveis");
                            $aVariaveis = explode(',', $sVariaveis);
                            $oAmostraVariaveis = new Amostra_AmostraVariaveis();

                            $oAmostraVariaveis->delete($sWhere, "alterar-usuario", $vUsuarioLogado["id"]);
                            $oAmostraVariaveis->insert($aVariaveis, $nId, "alterar-usuario", $vUsuarioLogado["id"]);

                            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Amostra foi alterado com sucesso.");
                            $this->_redirect('/sinbio/amostra');
                        } else {
                            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Amostra", $oAmostra->getErroMensagem());
                        }
                    } else {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Amostra", "Selecione uma destinação.");
                    }
                }
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Amostra", "Este Amostra não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/amostra');
            }
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Amostra", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/amostra');
        }
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
