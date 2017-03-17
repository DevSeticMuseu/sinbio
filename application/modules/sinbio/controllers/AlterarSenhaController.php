<?php

class Sinbio_AlterarSenhaController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
		error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "alterar-senha";
		$this->view->layout()->nmPrograma = "Alterar Senha";
		
		if ($_SESSION["sMsg"]) {
			$this->view->layout()->msg = $_SESSION["sMsg"];
			unset($_SESSION["sMsg"]);
		}
	}
        
        
        public function indexAction()
        {
            	$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
                
                $auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
                $usuario = $vUsuarioLogado['id'];
                
		$this->_redirect('/alterar-senha/alterar/nId/'.$usuario);
        }


        

	public function alterarAction() {
		$this->view->layout()->nmPrograma = "Usuário";
		$this->view->layout()->nmOperacao = "Alterar Senha";
		
		$this->view->layout()->includeJs =	'
			<script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
			<script src="/js/sinbio/validacao.js" type="text/javascript"></script>
		';
		
		$this->view->layout()->includeCss = '';
		
		$oUsuario = new Seg_Usuario();
		$oGrupoUsuario = new Seg_GrupoUsuario();
		
		$request = $this->_request;
		$nId = $request->getParam("nId");
		$sOP = $request->getParam("sOP");
		
		//VALIDA O ID
		if ($nId) {
			$vUsuario = $oUsuario->find($nId)->toArray();
			$vUsuario = $vUsuario[0];
			
			//RECUPERA GRUPO USUARIO PARA SELECT
			$this->view->vGrupoUsuario = $oGrupoUsuario->fetchAll()->toArray();
			
			//VALIDA SE O USUARIO EXISTE
			if (count($vUsuario)) {
				$this->view->nId		= $vUsuario["id"];
				$this->view->sNome		= $vUsuario["nm_usuario"];
				$this->view->sLogin		= $vUsuario["login"];
				$this->view->sEmail		= $vUsuario["email"];
				$this->view->nIdGrupo	= $vUsuario["seg_grupo_usuario_id"];
				
				
				//VALIDA SE FOI SUBMETIDO O FORMULARIO
				if ($sOP =="alterar") {
					
					//RECUPERA CAMPOS DO FORMULARIO
					$nId				= $request->getParam("nId");
					$nIdGrupoUsuario	= $request->getParam("fIdGrupoUsuario");
					$sNomeUsuario		= $request->getParam("fNmUsuario");
					$sEmail				= $request->getParam("fEmail");
					$sLogin				= $request->getParam("fLoginAlterar");
					$sSenha				= $request->getParam("fSenha");
					$sSenhaConf			= $request->getParam("fSenhaConf");					
					
					//VERIFICA SE O USUARIO ESTA TENTADO TROCAR SENHA
					if ($sSenha && $sSenhaConf && $sSenhaConf != $sSenha) {
						$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", "As senhas digitadas não são iguais. Por favor tente novamente.");
					}
					else {
						$vData = array(
								"id"				=> $nId,
								"seg_grupo_usuario_id"	=> $nIdGrupoUsuario,
								"nm_usuario"		=> $sNomeUsuario,
								"login"				=> $sLogin,
								"email"				=> $sEmail,
						);
						
						//VERIFICA SE O USUARIO VAI ALTERAR A SENHA
						if ($sSenha) {
							$vData += array("senha" => $sSenha);
						}//VERIFICA SE O USUARIO VAI ALTERAR A SENHA
						
						$sWhere = "id = ".$vData["id"];
						$auth = Zend_Auth::getInstance();
						$vUsuarioLogado = $auth->getIdentity();
						
						//VERIFICA SE O REGISTRO VAI SER ALTERADO
						if ($oUsuario->update($vData, $sWhere, "alterar-senha-usuario", $vUsuarioLogado["id"])) {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(1, "Sucesso", "O Usuário foi alterado com sucesso.");
							//$this->_redirect('/sinbio');
						}
						else {
							$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", $oUsuario->getErroMensagem());
						}//VERIFICA SE O REGISTRO VAI SER ALTERADO
					}//VERIFICA SE O USUARIO ESTA TENTADO TROCAR SENHA
				}//VALIDA SE FOI SUBMETIDO O FORMULARIO
			}
			else {
				unset($_SESSION["sMsg"]);
				$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", "Este usuário não foi encontrado no sistema, por favor tente novamente.");
				$this->_redirect('/alterar-senha');
			}//VALIDA SE O USUARIO EXISTE
		}
		else {
			unset($_SESSION["sMsg"]);
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Usuário", "Ocorreu um erro inexperado, por favor tente novamente.");
			$this->_redirect('/alterar-senha');
		}//VALIDA O ID
	}


        

	public function verificaPermissaoAction() {
		$this->view->layout()->disableLayout();
		
		$sQP = $this->_request->getParam("sOP");
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
		$oVerifica = new VerificaPermissao("alterar-senha", $sQP, $vUsuarioLogado["id"]);
		if ($oVerifica->bResultado) {
			$this->view->bPermissao = $oVerifica->bResultado;
		}
	}
	
	
}
