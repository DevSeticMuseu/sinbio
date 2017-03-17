<?php

class Sinbio_LoginController extends Zend_Controller_Action {
	public function init() {
	$this->view->layout()->disableLayout();
       
        ini_set( "display_errors", true );
        error_reporting( E_ALL ^ E_NOTICE );
	}

	public function indexAction()
	{
               
		$sMensagens = new Zend_Session_Namespace("sMensagens");
		
		if ($sMensagens->msg) {
			$this->view->tipoMsg = $sMensagens->tipoMsg;
			$this->view->icoMsg = $sMensagens->icoMsg;
			$this->view->altMsg = $sMensagens->altMsg;
			$this->view->tituloMsg = $sMensagens->tituloMsg;
			//$this->view->msg = $sMensagens->msg;
                        
                        $this->view->msg = UtilsFile::recuperaMensagens(2,"Error ao entrar no Sistema!","$sMensagens->msg");
		}
		
		unset($sMensagens->msg);
                
          
               
	}
	
	public function cadastreAction(){
	}

	public function processaAction() {
		$request = $this->_request;
		if ($request->getPost('sOP')=="Logar" && $request->getPost("fUsuario") != "" && $request->getPost("fSenha") != "") {
			$data = $request->getPost();
			$oLogin = new Login();

			//UtilsFile::printvardie($oLogin);

			if ($oLogin->logar($data["fUsuario"], $data["fSenha"])) {
				$this->_redirect('/internas/pagina-inicial');
			}
			else {
				$sMensagens = new Zend_Session_Namespace("sMensagens");
				$sMensagens->tipoMsg = "status error";
				$sMensagens->icoMsg = "/img/icons/icon_error.png";
				$sMensagens->altMsg = "Erro no login";
				$sMensagens->tituloMsg = "Erro. ";
				$sMensagens->msg = $oLogin->getMsg();
				
				$this->_redirect('/login');
			}
		}
		else {
			$sMensagens = new Zend_Session_Namespace("sMensagens");
			$sMensagens->tipoMsg = "status error";
			$sMensagens->icoMsg = "/img/icons/icon_error.png";
			$sMensagens->altMsg = "Erro no login";
			$sMensagens->tituloMsg = "Erro. ";
			$sMensagens->msg = "Campo Usuário e Senha são obrigatórios";
			
			$this->_redirect('/login');
		}
	}
	
	public function logoffAction()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		
		$this->_redirect('/login');
	}
}