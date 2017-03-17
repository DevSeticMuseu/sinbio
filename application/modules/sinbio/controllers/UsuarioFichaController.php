<?php

class Sinbio_UsuarioFichaController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "usuario-ficha";
		$this->view->layout()->nmPrograma = "Usuário Ficha";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}
	
	public function indexAction() {
        try {
            $this->view->layout()->nmOperacao = "Ficha do Usuário";

            $oUsuario = new Seg_UsuarioFicha();
            $nIdUsuario = $this->_request->getParam('nId');
            $vUsuario = $oUsuario->getFichaUsuario($nIdUsuario);
            $this->view->Usuario = $vUsuario;

            $oUsuarioNucleo = new UsuarioNucleo_UsuarioNucleo();
            $nucleosUsuario = $oUsuarioNucleo->findNucleosPorId($nIdUsuario);
            $this->view->nucleos = $nucleosUsuario;
        } catch (Zend_Db_Exception $e) {
            UtilsFile::printvardie($e);
			$this->setErroMensagem($e->getMessage());
			return false;
        }
    }
	
	
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("usuario-ficha", $sQP, $vUsuarioLogado["id"]);
		if ($oVerifica->bResultado) {
			$this->view->bPermissao = $oVerifica->bResultado;
		}
	}
	
	
}
