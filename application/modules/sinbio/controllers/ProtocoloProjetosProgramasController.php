<?php

class Sinbio_ProtocoloProjetosProgramasController extends Zend_Controller_Action {

    public function init() {
           error_reporting (E_ALL & ~E_NOTICE & ~E_STRICT);
        $this->view->layout()->nmModulo = "Módulo Projetos Programas";
        $this->view->layout()->nmController = "protocolo";
        $this->view->layout()->nmPrograma = "projetos-programas";
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
   					$("#fProjetosProgramas").multiselect({
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

        
        $oProtocoloProjetosProgramas = new Protocolo_ProtocoloProjetosProgramas();
        $oProjetosProgramas = new ProjetoPrograma_ProjetoPrograma();
        $oProtocolo = new Protocolo_Protocolo();
        
        
        

        $nIdProtocolo = $this->_request->getParam('nId');
        $vProtocolo = $oProtocolo->fetchAll("id = $nIdProtocolo");
        $this->view->fProtocolo = $vProtocolo[0]["nm_protocolo"];
        $this->view->fIdProtocolo = $vProtocolo[0]["id"];
        $where = 'coleta_protocolo_id = '.$nIdProtocolo;

        $this->view->vProjetosProgramas = $oProjetosProgramas->fetchAll();

        $vProtocoloProjetosProgramas = $oProtocoloProjetosProgramas->fetchAll($where);
        $n = 1;
        foreach ($vProtocoloProjetosProgramas as $protocoloProjetosProgramas) {
            $vProtocoloProjetosProgramasNovo[$n] = $protocoloProjetosProgramas["coleta_projeto_programa_id"];
            $n++;
        }

        $this->view->vProtocolosProgramas = $vProtocoloProjetosProgramasNovo;
    }

    public function cadastrarAction() {
        try {
            //RECUPERAR USUARIO LOGADO
            $auth = Zend_Auth::getInstance();
            $vUsuarioLogado = $auth->getIdentity();

            //$oProtocolosMetodos = new Protocolo_ProtocolosMetodos();
            $oProtocoloProjetosProgramas = new Protocolo_ProtocoloProjetosProgramas();

            $request = $this->_request;
            $nIdProtocolo = $request->getParam("fIdProtocolo");
            $oProjetoPrograma = $request->getParam("fProjetosProgramas");

            $oProtocoloProjetosProgramas->delete("coleta_protocolo_id =  $nIdProtocolo", "excluir-projeto-programa-protocolo", $vUsuarioLogado["id"]);

            foreach ($oProjetoPrograma as $projetoprograma) {
                $vData = array("coleta_protocolo_id" => $nIdProtocolo, "coleta_projeto_programa_id" => $projetoprograma);
                $oProtocoloProjetosProgramas->insert($vData, "cadastrar-projetos-programas-protocolo", $vUsuarioLogado["id"]);
            }
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Protocolos Projetos/Programas alteradas com sucesso!");
            $this->_redirect('/protocolo');
        } catch (Zend_Exception $e) {
            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Protocolos ", $sString);
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("protocolo-projetos-programas", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

}
