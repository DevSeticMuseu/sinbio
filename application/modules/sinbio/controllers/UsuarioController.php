<?php

class Sinbio_UsuarioController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        //error_reporting (E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Segurança";
        $this->view->layout()->nmController = "usuario";
        $this->view->layout()->nmPrograma = "Usuário";

        if (isset($_SESSION["sMsg"])) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeJs = '
                                <script src="/js/jquery-1.12.0.min.js"></script>
                                <script src="/js/jquery.dataTables.min.js"></script>
                                <script src="/js/dataTables.buttons.min.js"></script>
                                <script src="/js/sinbio/tabelas-datatable.js"></script>
				<script src="/js/sinbio/seguranca-seg-usuario.js"></script>
		';
        $this->view->layout()->includeCss = '
                                <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
                                <link href="/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
                ';
        $this->view->layout()->nmOperacao = "Listar";
        
        $joins = array();
        $joinGrupo= array(table => array('grupo' => 'seg_grupo_usuario'), onCols => ' usuario.seg_grupo_usuario_id = grupo.id', colReturn => array('nm_grupo_usuario'));
        array_push($joins, $joinGrupo);
        
        $user = new Seg_Usuario();
        $this->view->paginator = $user->fetchAll(null, null, $joins);
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

        $this->view->layout()->includeCss = '
                 <link rel="stylesheet" href="/css/sinbio/datepicker-change.css" />
                ';

        //RECUPERA GRUPO USUARIO PARA SELECT
        $oGrupoUsuario = new Seg_GrupoUsuario();
        $this->view->vGrupoUsuario = $oGrupoUsuario->fetchAll(null, 'nm_grupo_usuario')->toArray();

        $oInstituicao = new Instituicao_Instituicao();
        $this->view->vInstituicao = $oInstituicao->fetchAll(null, 'razao_social')->toArray();

        $oTitulacao = new Seg_Titulacao();
        $this->view->vTitulacao = $oTitulacao->fetchAll(null, 'hierarquia')->toArray();

        $oBanco = new Seg_Banco();
        $this->view->vBanco = $oBanco->fetchAll()->toArray();

        $oProtocolo = new Protocolo_Protocolo();
        $this->view->vProtocolo = $oProtocolo->fetchAll(null, 'sigla')->toArray();

        $oNucleo = new Loc_Nucleo();
        $this->view->vNucleo = $oNucleo->fetchAll()->toArray(null, 'nm_nucleo');

        //INSERINDO NO BANCO
        $request = $this->_request;

        if ($request->getParam("sOP") == "cadastrar") {

            $vData = array(
                "seg_grupo_usuario_id" => $request->getParam("fIdGrupoUsuario"),
                "seg_banco_id" => $request->getParam("fIdBanco"),
                "seg_titulacao_id" => $request->getParam("fIdTitulacao"),
                "coleta_protocolo_id" => $request->getParam("fIdProtocolo"),
                "nm_usuario" => $request->getParam("fNmUsuario"),
                "email" => $request->getParam("fEmail"),
                "telefone" => $request->getParam("fNumTelefone"),
                "login" => $request->getParam("fLogin"),
                "senha" => $request->getParam("fSenha"),
                "dt_cadastro" => $request->getParam("fDatadeCadastro"),
                "dt_saida" => $request->getParam("fDtSaida"),
                "citacao" => $request->getParam("fCitacao"),
                "rg" => $request->getParam("fRg"),
                "cpf" => $request->getParam("fCpf"),
                "sisbio" => $request->getParam("fSisbio"),
                "lattes" => $request->getParam("fLattes"),
                "banco_agencia" => $request->getParam("fBancoAgencia"),
                "banco_conta" => $request->getParam("fBancoConta"),
                "bolsista" => $request->getParam("fBolsista"),
                "vinculo_empregaticio" => $request->getParam("fVinculoEmpregaticio"),
                "outro" => $request->getParam("fOutro"),
                "ultima_atualizacao" => $request->getParam("fDtUtimaAtualizacao"),
                "seg_instituicao_id" => $request->getParam("fIdInstituicao")
            );


            $sAtributosChave = "nm_usuario,login,senha,cpf";
            $sNmAtributosChave = "Nome de Usuário,Login,Senha,Cpf";
            $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

            if ($sMsg) {
                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Usuário", $sMsg);
            } else {

                try {
                    $oUsuario = new Seg_Usuario();
                    $auth = Zend_Auth::getInstance();
                    $vUsuarioLogado = $auth->getIdentity();
                    $nId = $oUsuario->insert($vData, "cadastrar-usuario", $vUsuarioLogado["id"]);

                    //Salvando núcleos
                    $aNucleos = $request->getParam("fIdNucleo");

                    $oUsuarioNucleo = new UsuarioNucleo_UsuarioNucleo();
                    $oUsuarioNucleo->insert($aNucleos, $nId, "cadastrar-usuario", $vUsuarioLogado["id"]);

                    if (!$nId) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro 2 ao Cadasrar Usuário", $oUsuario->getErroMensagem());
                    } else {
                        $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Cadastro realizado com sucesso!");
                        $this->_redirect('/usuario');
                    }
                } catch (Zend_Db_Exception $e) {
                    $sString = $e->getMessage();
                    $bErro = strstr($sString, "SQLSTATE[23000]");

                    if ($bErro) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadasrar Usuário", "Login já existente no sistema.");
                    } else {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro 3 ao Cadasrar Usuário", $sString);
                    }
                }
            }
        }
    }

    public function alterarAction() {
        $this->view->layout()->nmPrograma = "Usuário";
        $this->view->layout()->nmOperacao = "Alterar";

        $this->view->layout()->includeJs = '
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';

        $this->view->layout()->includeCss = '
                <link rel="stylesheet" href="/css/sinbio/datepicker-change.css" />
                ';

        $oUsuario = new Seg_Usuario();
        $oGrupoUsuario = new Seg_GrupoUsuario();
        $oInstituicao = new Instituicao_Instituicao();
        $oBanco = new Seg_Banco();
        $oTitulacao = new Seg_Titulacao();
        $oProtocolo = new Protocolo_Protocolo();
        $oNucleo = new Loc_Nucleo();
        $oUsuarioNucleo = new UsuarioNucleo_UsuarioNucleo();

        $request = $this->_request;
        $nId = $request->getParam("nId");
        $sOP = $request->getParam("sOP");

        //VALIDA O ID
        if ($nId) {
            $vUsuarioRetorno = $oUsuario->find($nId);
            $vUsuario = $vUsuarioRetorno->toArray();
            $vUsuario = $vUsuario[0];

            //RECUPERA GRUPO USUARIO PARA SELECT
            $this->view->vGrupoUsuario = $oGrupoUsuario->fetchAll(null, 'nm_grupo_usuario')->toArray();

            //RECUPERA INSTITUICAO PARA O SELECT
            $this->view->vInstituicao = $oInstituicao->fetchAll(null, 'razao_social')->toArray();

            //RECUPERA BANCO PARA O SELECT
            $this->view->vBanco = $oBanco->fetchAll()->toArray();

            //RECUPERA TITULACAO PARA O SELECT
            $this->view->vTitulacao = $oTitulacao->fetchAll()->toArray(null, 'hierarquia');

            //RECUPERA PROTOCOLO PARA O SELECT
            $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray(null, 'sigla');

            //RECUPERA NUCLEO PARA O SELECT
            $vNucleosRetorno = $oNucleo->fetchAll()->toArray(null, 'nm_nucleo');

            $vUsuarioRow = $vUsuarioRetorno->current();
            $vNucleosUsuario = $oUsuarioNucleo->findNucleos($vUsuarioRow); //BUSCA OS NUCLEOS NA TABELA DE JUNCAO

            $vNucleos = array();
            foreach ($vNucleosRetorno as $nucleo) {
                foreach ($vNucleosUsuario as $nucleoUsuario) {
                    if ($nucleoUsuario["id"] == $nucleo["id"]) {
                        $nucleo["selected"] = "selected";
                        $vNucleos[] = $nucleo;
                        continue 2;
                    }
                }
                $vNucleos[] = $nucleo;
            }
            $this->view->vNucleos = $vNucleos;

            //VALIDA SE O USUARIO EXISTE
            if (count($vUsuario)) {

                $this->view->nId = $vUsuario["id"];
                $this->view->sNome = $vUsuario["nm_usuario"];
                $this->view->sLogin = $vUsuario["login"];
                $this->view->sSenha = $vUsuario["senha"];
                $this->view->sEmail = $vUsuario["email"];
                $this->view->sTelefone = $vUsuario["telefone"];
                $this->view->nIdGrupo = $vUsuario["seg_grupo_usuario_id"];
                $this->view->nIdInstituicao = $vUsuario["seg_instituicao_id"];
                $this->view->nIdTitulacao = $vUsuario["seg_titulacao_id"];
                $this->view->nIdProtocolo = $vUsuario["coleta_protocolo_id"];
                $this->view->nIdNucleo = $vUsuario["loc_nucleo_id"];
                $this->view->nIdBanco = $vUsuario["seg_banco_id"];
                $this->view->sDtCadastro = $vUsuario["dt_cadastro"];
                $this->view->sDtSaida = $vUsuario["dt_saida"];
                $this->view->sCitacao = $vUsuario["citacao"];
                $this->view->sRg = $vUsuario["rg"];
                $this->view->sCpf = $vUsuario["cpf"];
                $this->view->sSisbio = $vUsuario["sisbio"];
                $this->view->sLattes = $vUsuario["lattes"];
                $this->view->sBancoAgencia = $vUsuario["banco_agencia"];
                $this->view->sBancoConta = $vUsuario["banco_conta"];
                $this->view->sBolsista = $vUsuario["bolsista"];
                $this->view->sVinculoEmpregaticio = $vUsuario["vinculo_empregaticio"];
                $this->view->sOutro = $vUsuario["outro"];
                $this->view->sUltimaAtualizacao = $vUsuario["ultima_atualizacao"];


                //VALIDA SE FOI SUBMETIDO O FORMULARIO
                if ($sOP == "alterar") {

                    //RECUPERA CAMPOS DO FORMULARIO
                    $nId = $request->getParam("nId");
                    $sSenha = $request->getParam("fSenha");
                    $sSenhaConf = $request->getParam("fSenhaConf");
                    //VERIFICA SE O USUARIO ESTA TENTADO TROCAR SENHA
                    if ($sSenha && $sSenhaConf && $sSenhaConf != $sSenha) {
                        $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", "As senhas digitadas não são iguais. Por favor tente novamente.");
                    } else {
                        if ($request->getParam("fDtSaida") == "" && $request->getParam("fDatadeCadastro") == "") {
                            $vData = array(
                                "id" => $nId,
                                "seg_grupo_usuario_id" => $request->getParam("fIdGrupoUsuario"),
                                "seg_titulacao_id" => $request->getParam("fIdTitulacao"),
                                "coleta_protocolo_id" => $request->getParam("fIdProtocolo"),
                                "seg_banco_id" => $request->getParam("fIdBanco"),
                                "nm_usuario" => $request->getParam("fNmUsuario"),
                                "email" => $request->getParam("fEmail"),
                                "telefone" => $request->getParam("fNumTelefone"),
                                "login" => $request->getParam("fLogin"),
                                "senha" => $request->getParam("fSenha"),
                                "citacao" => $request->getParam("fCitacao"),
                                "rg" => $request->getParam("fRg"),
                                "sisbio" => $request->getParam("fSisbio"),
                                "lattes" => $request->getParam("fLattes"),
                                "banco_agencia" => $request->getParam("fBancoAgencia"),
                                "banco_conta" => $request->getParam("fBancoConta"),
                                "bolsista" => $request->getParam("fBolsista"),
                                "vinculo_empregaticio" => $request->getParam("fVinculoEmpregaticio"),
                                "outro" => $request->getParam("fOutro"),
                                "ultima_atualizacao" => date("Y-m-d"),
                                "seg_instituicao_id" => $request->getParam("fIdInstituicao")
                            );
                        } else {
                            $vData = array(
                                "id" => $nId,
                                "seg_grupo_usuario_id" => $request->getParam("fIdGrupoUsuario"),
                                "seg_titulacao_id" => $request->getParam("fIdTitulacao"),
                                "coleta_protocolo_id" => $request->getParam("fIdProtocolo"),
                                "seg_banco_id" => $request->getParam("fIdBanco"),
                                "nm_usuario" => $request->getParam("fNmUsuario"),
                                "email" => $request->getParam("fEmail"),
                                "login" => $request->getParam("fLogin"),
                                "senha" => $request->getParam("fSenha"),
                                "dt_cadastro" => $request->getParam("fDatadeCadastro"),
                                "dt_saida" => $request->getParam("fDtSaida"),
                                "citacao" => $request->getParam("fCitacao"),
                                "rg" => $request->getParam("fRg"),
                                "cpf" => $request->getParam("fCpf"),
                                "sisbio" => $request->getParam("fSisbio"),
                                "lattes" => $request->getParam("fLattes"),
                                "banco_agencia" => $request->getParam("fBancoAgencia"),
                                "banco_conta" => $request->getParam("fBancoConta"),
                                "bolsista" => $request->getParam("fBolsista"),
                                "vinculo_empregaticio" => $request->getParam("fVinculoEmpregaticio"),
                                "outro" => $request->getParam("fOutro"),
                                "ultima_atualizacao" => date("Y-m-d"),
                                "seg_instituicao_id" => $request->getParam("fIdInstituicao")
                            );
                        }

                        //-----VERIFICA SE O USUARIO VAI ALTERAR A SENHA-----
                        if ($sSenha) {
                            $vData += array("senha" => $sSenha);
                        }
                        //---------------------------------------------------

                        $sAtributosChave = "nm_usuario,login,senha,cpf";
                        $sNmAtributosChave = "Nome de Usuário,Login,Senha,Cpf";
                        $sMsg = UtilsFile::verificaArrayVazio($vData, $sAtributosChave, $sNmAtributosChave);

                        if ($sMsg) {
                            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", $sMsg);
                        } else {

                            $auth = Zend_Auth::getInstance();
                            $vUsuarioLogado = $auth->getIdentity();

                            //---------------ATUALIZANDO NUCLEOS---------------
                            $sWhere = "seg_usuario_id = " . $vData["id"];
                            $aNucleos = $request->getParam("fIdNucleo");
                            $oUsuarioNucleo = new UsuarioNucleo_UsuarioNucleo();

                            $oUsuarioNucleo->delete($sWhere, "alterar-usuario", $vUsuarioLogado["id"]);
                            $oUsuarioNucleo->insert($aNucleos, $nId, "alterar-usuario", $vUsuarioLogado["id"]);
                            //-------------------------------------------------
                            //VERIFICA SE O REGISTRO VAI SER ALTERADO
                            $sWhere = "id = " . $vData["id"];
                            if ($oUsuario->update($vData, $sWhere, "alterar-usuario", $vUsuarioLogado["id"])) {
                                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "O Usuário foi alterado com sucesso.");
                                $this->_redirect('/usuario');
                            } else {
                                $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", $oUsuario->getErroMensagem());
                            }//VERIFICA SE O REGISTRO VAI SER ALTERADO
                        }
                    }//VERIFICA SE O USUARIO ESTA TENTADO TROCAR SENHA
                }//VALIDA SE FOI SUBMETIDO O FORMULARIO
            } else {
                unset($_SESSION["sMsg"]);
                $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", "Este usuário não foi encontrado no sistema, por favor tente novamente.");
                $this->_redirect('/usuario');
            }//VALIDA SE O USUARIO EXISTE
        } else {
            unset($_SESSION["sMsg"]);
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", "Ocorreu um erro inexperado, por favor tente novamente.");
            $this->_redirect('/usuario');
        }//VALIDA O ID
    }

    public function excluirAction() {
        try {
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $request = $this->_request;
            $vId = $request->getParam("fId");

            $oUsuario = new Seg_Usuario();

            $auth = Zend_Auth::getInstance();
            $vUsuarioLogado = $auth->getIdentity();

            if (count($vId)) {
                foreach ($vId as $nId) {
                    $vData = $oUsuario->find($nId)->toArray();
                    $sWhere = "id = " . $nId;
                    $oUsuario->delete($vData, $sWhere, "excluir-usuario", $vUsuarioLogado["id"]);
                }
                if ($oUsuario->getErroMensagem()) {
                    $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Deletar Usuário", $oUsuario->getErroMensagem());
                } else {
                    $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Usuário(s) removido(s) com sucesso.");
                }
            } else {
                throw new Exception("Você deve selecionar ao menos um registro.");
            }
        } catch (Exception $e) {
            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Cadastrar Operação", $e->getMessage());
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("usuario", $sQP, $vUsuarioLogado["id"]);
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

        if ($sSortname == "nm_grupo")
            $sSortname = "seg_grupo_usuario_id";

        $sWhere = "";
        if ($sQuery != "" && $sCampo != "") {
            if ($sCampo == "nm_grupo") {
                $sWhere = "nm_grupo_usuario ILIKE '%" . $sQuery . "%' ";
                $oGrupoUsuario = new Seg_GrupoUsuario();
                $regGrupoUsuario = $oGrupoUsuario->fetchRow($sWhere)->toArray();
                $sWhere = " seg_grupo_usuario_id = " . $regGrupoUsuario['id'];
            } else {
                $sWhere = $sCampo . " ILIKE '%" . $sQuery . "%' ";
            }
        }

        $sOrder = $sSortname . " " . $sSortorder;

        $oUsuario = new Seg_Usuario();
        $vReg = $oUsuario->fetchAll($sWhere, $sOrder, $nPagina, $nRegistroPagina)->toArray();

        $nTotal = $oUsuario->totalRegistro();

        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<rows>";
        $xml .= "<page>" . $nPagina . "</page>";
        $xml .= "<total>" . $nTotal . "</total>";
        foreach ($vReg as $reg) {
            $oGrupoUsuario = new Seg_GrupoUsuario();
            $regGrupoUsuario = $oGrupoUsuario->find($reg["seg_grupo_usuario_id"])->toArray();

            $xml .= "<row id='" . $reg["id"] . "'>";
            $xml .= "<cell><![CDATA[" . $reg["id"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["nm_usuario"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $reg["login"] . "]]></cell>";
            $xml .= "<cell><![CDATA[" . $regGrupoUsuario[0]["nm_grupo_usuario"] . "]]></cell>";
            $xml .= "</row>";
        }

        $xml .= "</rows>";

        echo $xml;
    }

    public function verificaLoginAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->_request;

        if ($request->getParam("fLogin")) {
            $oUsuario = new Seg_Usuario();
            $where = "login = '" . $request->getParam("fLogin") . "'";
            $vResult = $oUsuario->fetchAll($where);
            //UtilsFile::printvar($where);
            if (count($vResult)) {
                $bResult = false;
                echo json_encode($bResult);
            } else {
                $bResult = true;
                echo json_encode($bResult);
            }
        }
    }

}
