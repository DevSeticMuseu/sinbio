<?php

class Sinbio_SitioProjetosProgramasController extends Zend_Controller_Action {

    public function init() {
           error_reporting (E_ALL & ~E_NOTICE & ~E_STRICT);
        $this->view->layout()->nmModulo = "Módulo Projetos Programas";
        $this->view->layout()->nmController = "sitio";
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

        
        $oSitioProjetosProgramas = new Loc_SitioProjetosProgramas();
        $oProjetosProgramas = new ProjetoPrograma_ProjetoPrograma();
        $oSitio = new Loc_Sitio();
        
        
        

        $nIdSitio = $this->_request->getParam('nId');
        $vSitio = $oSitio->fetchAll("id = $nIdSitio");
        $this->view->fSitio = $vSitio[0]["nm_sitio"];
        $this->view->fIdSitio = $vSitio[0]["id"];
        $where = 'loc_sitio_id = '.$nIdSitio;

        $this->view->vProjetosProgramas = $oProjetosProgramas->fetchAll();

        $vSitioProjetosProgramas = $oSitioProjetosProgramas->fetchAll($where);
        $n = 1;
        foreach ($vSitioProjetosProgramas as $sitioProjetosProgramas) {
            $vSitioProjetosProgramasNovo[$n] = $sitioProjetosProgramas["coleta_projeto_programa_id"];
            $n++;
        }

        $this->view->vSitioProgramas = $vSitioProjetosProgramasNovo;
    }

    public function cadastrarAction() {
        try {
            //RECUPERAR USUARIO LOGADO
            $auth = Zend_Auth::getInstance();
            $vUsuarioLogado = $auth->getIdentity();

            //$oProtocolosMetodos = new Protocolo_ProtocolosMetodos();
            $oSitioProjetosProgramas = new Loc_SitioProjetosProgramas();

            $request = $this->_request;
            $nIdSitio = $request->getParam("fIdSitio");
            $oProjetoPrograma = $request->getParam("fProjetosProgramas");

            $oSitioProjetosProgramas->delete("loc_sitio_id =  $nIdSitio", "excluir-projeto-programa-sitio", $vUsuarioLogado["id"]);

            foreach ($oProjetoPrograma as $projetoprograma) {
                $vData = array("loc_sitio_id" => $nIdSitio, "coleta_projeto_programa_id" => $projetoprograma);
                $oSitioProjetosProgramas->insert($vData, "cadastrar-projetos-programas-sitio", $vUsuarioLogado["id"]);
            }
            $_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Sitio Projetos/Programas alteradas com sucesso!");
            $this->_redirect('/sitio');
        } catch (Zend_Exception $e) {
            $this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Sitio Projetos/Programas", $sString);
        }
    }

    public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("sitio-projetos-programas", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }

}
