<?php
class Sinbio_ErrorController extends Zend_Controller_Action
{
	
	public function init()
	{
		/* Initialize action controller here */
              // error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->includeJs = '';
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmPrograma = "Erro";
		$this->view->layout()->nmOperacao = "Erro de Permissão";
	}
	
	public function permissaoNegadaAction()
	{
		$this->view->tipoMsg = "status error";
		$this->view->icoMsg = "/img/icons/icon_error.png";
		$this->view->altMsg = "Erro no login";
		$this->view->tituloMsg = "Erro. ";
		$this->view->msg = "Você não possui permissão para acessar esta área.";
	}
}