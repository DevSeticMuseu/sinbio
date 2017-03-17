<?php

class Sinbio_MetodosVariaveisController extends Zend_Controller_Action {

    public function init() {
           error_reporting (E_ALL & ~E_NOTICE);
        $this->view->layout()->nmModulo = "Módulo Coleta";
        $this->view->layout()->nmController = "metodo";
        $this->view->layout()->nmPrograma = "Metodos Variaveis";
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
   					$("#fVariaveis").multiselect({
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


        $oMetodosVariaveis = new Protocolo_MetodosVariaveis();
        $oVariaveis = new Protocolo_Variaveis();
        $oMetodo = new Protocolo_Metodo();

        $nIdMetodo = $this->_request->getParam('nId');
        $vMetodo = $oMetodo->fetchAll("id = $nIdMetodo");
        $this->view->fMetodo = $vMetodo[0]["nm_metodo"];
        $this->view->fIdMetodo = $vMetodo[0]["id"];
        $where = 'coleta_metodos_id = ' . $nIdMetodo;


        $this->view->vVariavel = $oVariaveis->fetchAll();

        $vMetodosVariaveis = $oMetodosVariaveis->fetchAll($where);
        $n = 1;
        foreach ($vMetodosVariaveis as $metodosvariaveis) {
            $vMetodosVariaveisNovo[$n] = $metodosvariaveis["coleta_variaveis_id"];
            $n++;
        }

        $this->view->vMetodosVariaveis = $vMetodosVariaveisNovo;
    }

    public function cadastrarAction() {
        try {
            //RECUPERAR USUARIO LOGADO
            $auth = Zend_Auth::getInstance();
            $vUsuarioLogado = $auth->getIdentity();

            $oMetodosVariaveis = new Protocolo_MetodosVariaveis();

            $request = $this->_request;
            $nIdMetodo = $request->getParam("fIdMetodo");
            $oVariaveis = $request->getParam("fVariaveis");


            $oMetodosVariaveis->delete("coleta_metodos_id =  $nIdMetodo", "excluir-metodos-variaveis", $vUsuarioLogado["id"]);

            foreach ($oVariaveis as $variaveis) {
                $vData = array("coleta_metodos_id" => $nIdMetodo, "coleta_variaveis_id" => $variaveis);
                $oMetodosVariaveis->insert($vData, "cadastrar-metodos-variaveis", $vUsuarioLogado["id"]);
            }
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Metodos Variaveis alteradas com sucesso!");
            $this->_redirect('/metodo');
        } catch (Zend_Exception $e) {
            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Metodos Variaveis", $sString);
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("metodos-variaveis", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

}
