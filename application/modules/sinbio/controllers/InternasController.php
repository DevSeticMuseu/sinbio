<?php

class Sinbio_InternasController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		 //  error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "login";
		$this->view->layout()->nmPrograma = "Login";
		
		if (isset($_SESSION["sMsg"])) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}

	public function indexAction() {
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_redirect('/internas/pagina-inicial');
	}
	
	
    public function paginaInicialAction() {
    	$this->view->layout()->nmOperacao = "Boas Vindas";
    }
    
    public function geraXmlAction() {
    	
    }
}
