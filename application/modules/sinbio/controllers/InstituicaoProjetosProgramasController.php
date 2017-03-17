<?php

class Sinbio_InstituicaoProjetosProgramasController extends Zend_Controller_Action {

    public function init() {
           error_reporting (E_ALL & ~E_NOTICE & ~E_STRICT);
        $this->view->layout()->nmModulo = "Módulo Projetos Programas";
        $this->view->layout()->nmController = "instituicao";
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

        
        $oInstituicaoProjetosProgramas = new Instituicao_InstituicaoProjetosProgramas();
        $oProjetosProgramas = new ProjetoPrograma_ProjetoPrograma();
        $oInstituicao = new Instituicao_Instituicao();
        
        
        

        $nIdInstituicao = $this->_request->getParam('nId');
        $vInstituicao = $oInstituicao->fetchAll("id = $nIdInstituicao");
        $this->view->fInstituicao = $vInstituicao[0]["razao_social"];
        $this->view->fIdInstituicao = $vInstituicao[0]["id"];
        $where = 'seg_instituicao_id = '.$nIdInstituicao;

        $this->view->vProjetosProgramas = $oProjetosProgramas->fetchAll();

        $vInstituicaoProjetosProgramas = $oInstituicaoProjetosProgramas->fetchAll($where);
        $n = 1;
        foreach ($vInstituicaoProjetosProgramas as $instituicaoProjetosProgramas) {
            $vInstituicaoProjetosProgramasNovo[$n] = $instituicaoProjetosProgramas["coleta_projeto_programa_id"];
            $n++;
        }

        $this->view->vProtocolosProgramas = $vInstituicaoProjetosProgramasNovo;
    }

    public function cadastrarAction() {
        try {
            //RECUPERAR USUARIO LOGADO
            $auth = Zend_Auth::getInstance();
            $vUsuarioLogado = $auth->getIdentity();

            //$oProtocolosMetodos = new Protocolo_ProtocolosMetodos();
            $oInstituicaoProjetosProgramas = new Instituicao_InstituicaoProjetosProgramas();

            $request = $this->_request;
            $nIdInstituicao = $request->getParam("fIdInstituicao");
            $oProjetoPrograma = $request->getParam("fProjetosProgramas");

            $oInstituicaoProjetosProgramas->delete("seg_instituicao_id =  $nIdInstituicao", "excluir-projeto-programa-instituicao", $vUsuarioLogado["id"]);

            foreach ($oProjetoPrograma as $projetoprograma) {
                $vData = array("seg_instituicao_id" => $nIdInstituicao, "coleta_projeto_programa_id" => $projetoprograma);
                $oInstituicaoProjetosProgramas->insert($vData, "cadastrar-projetos-programas-instituicao", $vUsuarioLogado["id"]);
            }
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Protocolos Metodos alteradas com sucesso!");
            $this->_redirect('/instituicao');
        } catch (Zend_Exception $e) {
            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Protocolos Metodos", $sString);
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("instituicao-projetos-programas", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

}
