<?php

class Sinbio_InstituicaoController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        error_reporting(E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Instituição";
        $this->view->layout()->nmController = "instituicao";
        $this->view->layout()->nmPrograma = "Instituição";

        if (isset($_SESSION["sMsg"])) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
				<script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
				<script src="/js/sinbio/instituicao-instituicao.js"></script>
		';

        $this->view->layout()->includeCss = '
				<link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
		';
        $this->view->layout()->nmOperacao = "Listar";
    }

    public function cadastrarAction() {
        $this->view->layout()->nmController = "instituicao";
        $this->view->layout()->nmPrograma = "Instituição";
        $this->view->layout()->nmOperacao = "Cadastrar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '
                        <link rel="stylesheet" href="/css/sinbio/datepicker-change.css" />
                ';

        $oUf = new Loc_Uf();
        $this->view->vUf = $oUf->fetchAll()->toArray();
        
        $oMunicipio = new Loc_Municipio();
        $this->view->vMunicipio = $oMunicipio->fetchAll()->toArray();

        //RECUPERA NÚCLEO PARA SELECT
        $oNucleo = new Loc_Nucleo();
        $this->view->vNucleo = $oNucleo->fetchAll()->toArray();

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {
            $vData = array(
                "razao_social" => $request->getParam("fRazaoSocial"),
                "sigla" => $request->getParam("fSigla"),
                "url" => $request->getParam("fUrl"),
                "cnpj" => $request->getParam("fCnpj"),
                "nm_diretor" => $request->getParam("fNmDiretor"),
                "cargo_diretor" => $request->getParam("fCargoDiretor"),
                "email" => $request->getParam("fEmail"),
                "cpf_diretor" => $request->getParam("fCpfDiretor"),
                "loc_nucleo_id" => $request->getParam("fIdNucleo"),
                "loc_uf_id" => $request->getParam("fIdUf"),
                "loc_municipio_id" => $request->getParam("fIdMunicipio"),
                "logradouro" => $request->getParam("fLogradouro"),
                "bairro" => $request->getParam("fBairro"),
                "cep" => $request->getParam("fCep"),
                "telefone" => $request->getParam("fTelefone"),
                "descricao" => $request->getParam("fDescricao"),
                "complemento" => $request->getParam("fComplemento"),
                "numero" => $request->getParam("fNumero"),
                "portaria_designacao" => $request->getParam("fPDesignacao"),
                "convenio" => $request->getParam("fConvenio"),
                "ini_convenio" => $request->getParam("fIniConvenio"),
            );
            $dataFimConvenio = $request->getParam("fFimConvenio");
            if (!empty($dataFimConvenio))
                $vData["fim_convenio"] = $request->getParam("fFimConvenio");


            $sAtributosChave = "razao_social";
            $sNmAtributosChave = "Razão Social ";
            $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

            if ($sMsg) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Instituição", $sMsg);
            } else {
                try {
                    $oInstituicao = new Instituicao_Instituicao();

                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oInstituicao->insert($vData, "cadastrar-instituicao", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $sString = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Instituicao", $oInstituicao->getErroMensagem());
                        $bErro = strstr($sString, "1062");
                        if ($bErro) {
                            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Instituicao", "Instituição já existente no sistema.");
                        } else {
                            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Instituição", $sString);
                        }
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/instituicao');
                    }
                } catch (Zend_Db_Exception $e) {
                    $sString = $e->getMessage();
                    $bErro = strstr($sString, "1062");
                    if ($bErro) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Instituição", "Módulo já existente no sistema.");
                    } else {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Instituição", $sString);
                    }
                }
            }
        }
    }

    public function alterarAction() {
        $this->view->layout()->nmPrograma = "Instituição";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '';

        $oInstituicao = new Instituicao_Instituicao();

        $oUf = new Loc_Uf();
        $this->view->vUf = $oUf->fetchAll()->toArray();
        
        $oMunicipio = new Loc_Municipio();
        $this->view->vMunicipio = $oMunicipio->fetchAll()->toArray();

        $oNucleo = new Loc_Nucleo();
        $this->view->vNucleo = $oNucleo->fetchAll()->toArray();

        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        //VALIDA O ID
        if ($nId) {
            $vInstituicao = $oInstituicao->find($nId)->toArray();
            $vInstituicao = $vInstituicao[0];

            //VALIDA SE O USUARIO EXISTE
            if (count($vInstituicao)) {
                $this->view->nId = $vInstituicao["id"];
                $this->view->nIdUf = $vInstituicao["loc_uf_id"];
                $this->view->nIdMunicipio = $vInstituicao["loc_municipio_id"];
                $this->view->nIdNucleo = $vInstituicao["loc_nucleo_id"];
                $this->view->sRazaoSocial = $vInstituicao["razao_social"];
                $this->view->sSigla = $vInstituicao["sigla"];
                $this->view->sUrl = $vInstituicao["url"];
                $this->view->sCnpj = $vInstituicao["cnpj"];
                $this->view->sNmDiretor = $vInstituicao["nm_diretor"];
                $this->view->sCargo_Diretor = $vInstituicao["cargo_diretor"];
                $this->view->sEmail = $vInstituicao["email"];
                $this->view->sCpfDiretor = $vInstituicao["cpf_diretor"];
                $this->view->sLogradouro = $vInstituicao["logradouro"];
                $this->view->sBairro = $vInstituicao["bairro"];
                $this->view->sCep = $vInstituicao["cep"];
                $this->view->sTelefone = $vInstituicao["telefone"];
                $this->view->sDescricao = $vInstituicao["descricao"];
                $this->view->sComplemento = $vInstituicao["complemento"];
                $this->view->sNumero = $vInstituicao["numero"];
                $this->view->sPDesignacao = $vInstituicao["portaria_designacao"];
                $this->view->sConvenio = $vInstituicao["convenio"];
                $this->view->sIniConvenio = $vInstituicao["ini_convenio"];
                $this->view->sFimConvenio = $vInstituicao["fim_convenio"];


                //VALIDA SE FOI SUBMETIDO O FORMULARIO
                if ($sOP == "alterar") {

                    //RECUPERA CAMPOS DO FORMULARIO
                    $nId = $request->getParam("nId");

                    $vData = array(
                        "id" => $request->getParam("nId"),
                        "razao_social" => $request->getParam("fRazaoSocial"),
                        "sigla" => $request->getParam("fSigla"),
                        "url" => $request->getParam("fUrl"),
                        "cnpj" => $request->getParam("fCnpj"),
                        "nm_diretor" => $request->getParam("fNmDiretor"),
                        "cargo_diretor" => $request->getParam("fCargoDiretor"),
                        "email" => $request->getParam("fEmail"),
                        "cpf_diretor" => $request->getParam("fCpfDiretor"),
                        "loc_uf_id" => $request->getParam("fIdUf"),
                        "loc_municipio_id" => $request->getParam("fIdMunicipio"),
                        "loc_nucleo_id" => $request->getParam("fIdNucleo"),
                        "logradouro" => $request->getParam("fLogradouro"),
                        "bairro" => $request->getParam("fBairro"),
                        "cep" => $request->getParam("fCep"),
                        "telefone" => $request->getParam("fTelefone"),
                        "descricao" => $request->getParam("fDescricao"),
                        "complemento" => $request->getParam("fComplemento"),
                        "numero" => $request->getParam("fNumero"),
                        "portaria_designacao" => $request->getParam("fPDesignacao"),
                        "convenio" => $request->getParam("fConvenio"),
                        "ini_convenio" => $request->getParam("fIniConvenio"),
                    );
                    $dataFimConvenio = $request->getParam("fFimConvenio");
                    if (!empty($dataFimConvenio))
                        $vData["fim_convenio"] = $request->getParam("fFimConvenio");

                    $sWhere = "id = " . $vData["id"];
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();

                    //VERIFICA SE O REGISTRO VAI SER ALTERADO
                    if ($oInstituicao->update($vData, $sWhere, "alterar-instituicao", $vUsuarioLogado["id"])) {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "A Instituição foi alterado com sucesso.");
                        $this->_redirect('/instituicao');
                    } else {
                        //UtilsFile::printvardie($oModulo->getErroMensagem());
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Instituição", $oInstituicao->getErroMensagem());
                    }
                }//VALIDA SE FOI SUBMETIDO O FORMULARIO
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Instituição", "Este Instituição não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/instituicao');
            }//VALIDA SE O USUARIO EXISTE
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Instituição", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/instituição');
        }//VALIDA O ID
    }

    public function excluirAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;
        $vId = $request->getParam("fId");

        $oInstituicao = new Instituicao_Instituicao();

        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();

        if (count($vId)) {
            foreach ($vId as $nId) {
                $vData = $oInstituicao->find($nId)->toArray();
                $sWhere = "id =" . $nId;
                $oInstituicao->delete($vData, $sWhere, "excluir-instituicao", $vUsuarioLogado["id"]);
            }

            if ($oInstituicao->getErroMensagem()) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Excluir Instituicao", $oInstituicao->getErroMensagem());
            } else {
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Instituicao(ões) removida(s) com sucesso.");
            }
        } else {
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Instituição", "Você deve selecionar ao menos um registro.");
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("instituicao", $sQP, $vUsuarioLogado["id"]);
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

        $oInstituicao = new Instituicao_Instituicao();
        $vReg = $oInstituicao->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oInstituicao->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {
            $oMunicipio = new Loc_Municipio();
            $vMunicipio = $oMunicipio->find($reg['loc_municipio_id'])->toArray();


            $data_atual = strtotime(date('Y-m-d'));
            $data_fim = strtotime($reg["fim_convenio"]);

            // Verifica quantos dias faltam para finalizar o convenio
            $intervalo = ($data_fim - $data_atual) / 86400;

            $color1 = 'color';
            $color2 = 'color2';



            if ($data_atual > $data_fim) {
                $xml .= "<row id='" . $color1 . "'>";
                $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $reg["razao_social"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $vMunicipio[0]["nm_municipio"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $reg["url"] . "]]></cell>";
                $xml .= "</row>";
            } elseif ($intervalo <= 365) {
                $xml .= "<row id='" . $color2 . "'>";
                $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $reg["razao_social"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $vMunicipio[0]["nm_municipio"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $reg["url"] . "]]></cell>";
                $xml .= "</row>";
            } else {
                $xml .= "<row id='" . $reg["id"] . "'>";
                $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $reg["razao_social"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $vMunicipio[0]["nm_municipio"] . "]]></cell>";
                $xml .= "<cell><![CDATA[" . $reg["url"] . "]]></cell>";
                $xml .= "</row>";
            }
        }

        $xml .= "</rows>";

        echo $xml;
    }

}
