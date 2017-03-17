<?php

class Sinbio_ProtocolosMetodosController extends Zend_Controller_Action {

    public function init() {
           error_reporting (E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Coleta";
        $this->view->layout()->nmController = "protocolo";
        $this->view->layout()->nmPrograma = "Metodos";
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
   					$("#fMetodos").multiselect({
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

        
        $ProtocolosMetodos = new Protocolo_ProtocolosMetodos();
        $oMetodo = new Protocolo_Metodo();
        $oProtocolos = new Protocolo_Protocolo();

        $nIdProtocolo = $this->_request->getParam('nId');
        $vProtocolo = $oProtocolos->fetchAll("id = $nIdProtocolo");
        $this->view->fProtocolo = $vProtocolo[0]["nm_protocolo"];
        $this->view->fIdProtocolo = $vProtocolo[0]["id"];
        $where = 'coleta_protocolo_id = '.$nIdProtocolo;

        $this->view->vMetodo = $oMetodo->fetchAll();

        $vProtocolosMetodos = $ProtocolosMetodos->fetchAll($where);
        $n = 1;
        foreach ($vProtocolosMetodos as $protocolosmetodos) {
            $vProtocolosMetodosNovo[$n] = $protocolosmetodos["coleta_metodos_id"];
            $n++;
        }

        $this->view->vProtocolosMetodos = $vProtocolosMetodosNovo;
    }

    public function cadastrarAction() {
        try {
            //RECUPERAR USUARIO LOGADO
            $auth = Zend_Auth::getInstance();
            $vUsuarioLogado = $auth->getIdentity();

            $oProtocolosMetodos = new Protocolo_ProtocolosMetodos();

            $request = $this->_request;
            $nIdProtocolo = $request->getParam("fIdProtocolo");
            $oMetodo = $request->getParam("fMetodos");

            $oProtocolosMetodos->delete("coleta_protocolo_id =  $nIdProtocolo", "excluir-protocolos-metodos", $vUsuarioLogado["id"]);

            foreach ($oMetodo as $metodo) {
                $vData = array("coleta_protocolo_id" => $nIdProtocolo, "coleta_metodos_id" => $metodo);
                $oProtocolosMetodos->insert($vData, "cadastrar-protocolos-metodos", $vUsuarioLogado["id"]);
            }
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Protocolos Metodos alteradas com sucesso!");
            $this->_redirect('/protocolo');
        } catch (Zend_Exception $e) {
            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Protocolos Metodos", $sString);
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("protocolos-metodos", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

}
