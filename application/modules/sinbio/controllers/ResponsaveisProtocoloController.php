<?php

class Sinbio_ResponsaveisProtocoloController extends Zend_Controller_Action {

    public function init() {
          error_reporting (E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Coleta";
        $this->view->layout()->nmController = "usuario";
        $this->view->layout()->nmPrograma = "Responsaveis Protocolo";
        $this->view->layout()->nmOperacao = "Alterar";

        if ($_SESSION["sMsg"]) {
            $this->view->layout()->msg = $_SESSION["sMsg"];
            unset($_SESSION["sMsg"]);
        }
    }

    public function indexAction() {
        $this->view->layout()->includeCss = '
				<link href="/plugin/jquery-ui/css/ui-lightness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet" type="text/css"/>
		';

        $this->view->layout()->includeJs = '
				<script src="/plugin/jquery-ui/js/jquery-ui-1.10.2.custom.min.js"></script>
				<script src="/js/geral/jquery.multiselect.min.js"></script>
				<script type="text/javascript">
				$(function(){
   					$("#fUsuarios").multiselect({
						noneSelectedText: \'Por favor selecione\',
						checkAllText: \'Marcar Todos\',
						uncheckAllText: \'Desmarcar Todos\',
						selectedText: \'# Selecionado(s)\',
						minWidth: 270,
						height: 200
					}); 
				});
				</script>
		';

        
        $oResponsaveisProtocolo = new Protocolo_ResponsaveisProtocolo();
        $oUsuario = new Seg_Usuario();
        $oProtocolo = new Protocolo_Protocolo();
     

        $nIdProtocolo = $this->_request->getParam('nId');
        $vProtocolo = $oProtocolo->fetchAll("id = $nIdProtocolo");
        $this->view->fProtocolo = $vProtocolo[0]["nm_protocolo"];
        $this->view->fIdProtocolo = $vProtocolo[0]["id"];
        $where = 'coleta_protocolo_id = '.$nIdProtocolo;

        $this->view->vUsuario = $oUsuario->fetchAll(null, "nm_usuario ASC");

        $vResponsaveisProtocolo = $oResponsaveisProtocolo->fetchAll($where);
        $n = 1;
        foreach ($vResponsaveisProtocolo as $responsaveisprotocolo) {
            $vResponsaveisProtocoloNovo[$n] = $responsaveisprotocolo["seg_usuario_id"];
            $n++;
        }

        $this->view->vResponsaveisProtocolo = $vResponsaveisProtocoloNovo;
    }

    public function cadastrarAction() {
        try {
            //RECUPERAR USUARIO LOGADO
            $auth = Zend_Auth::getInstance();
            $vUsuarioLogado = $auth->getIdentity();

            //$oProtocolosMetodos = new Protocolo_ProtocolosMetodos();
            $oResponsaveisProtocolos = new Protocolo_ResponsaveisProtocolo();

            $request = $this->_request;
            
            $nIdProtocolo = $request->getParam("fIdProtocolo");
            $oUsuario = $request->getParam("fUsuarios");
            
            
            

            $oResponsaveisProtocolos->delete("coleta_protocolo_id =  $nIdProtocolo", "excluir-usuario-reposavel-protocolo", $vUsuarioLogado["id"]);

            foreach ($oUsuario as $usuario) {
                $vData = array("coleta_protocolo_id" => $nIdProtocolo, "seg_usuario_id" => $usuario);
                $oResponsaveisProtocolos->insert($vData, "cadastrar-usuario-responsavel-protocolo", $vUsuarioLogado["id"]);
            }
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Usuario Responsavel pelo Protocolo alteradas com sucesso!");
            $this->_redirect('/sinbio/protocolo');
        } catch (Zend_Exception $e) {
            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuario Responsavel pelo Protocolo ", $sString);
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("responsaveis-protocolo", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

}