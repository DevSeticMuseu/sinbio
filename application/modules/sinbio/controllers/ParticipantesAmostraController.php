<?php

class Sinbio_ParticipantesAmostraController extends Zend_Controller_Action {

	public function init() {
               error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "participantes-amostra";
		$this->view->layout()->nmPrograma = "Participantes da Amostra";
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
   					$("#fUsuarios").multiselect({
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
                
                $oParticipantesAmostra = new Amostra_ParticipantesAmostra();
                $oAmostra = new Amostra_Amostra();
                $oUsuario = new Seg_Usuario();
                
		
		$nIdAmostra = $this->_request->getParam('nId');
		$vAmostra = $oAmostra->fetchAll("id = $nIdAmostra");
		$this->view->fAmostra = $vAmostra[0]["id"];
		$this->view->fIdAmostra = $vAmostra[0]["id"];
		$where = 'coleta_amostra_id = '.$nIdAmostra;
	
                $vUsuario = $oUsuario->fetchAll(null, "nm_usuario ASC");
                $this->view->vUsuario = $vUsuario;
   		
             $vParticipantesAmostra = $oParticipantesAmostra->fetchAll($where);
               $n = 1;
                foreach($vParticipantesAmostra as $participantesamostra) {
			$vParticipantesAmostraNovo[$n] = $participantesamostra["seg_usuario_id"];
			$n++;
		}
		
		$this->view->vParticipantesAmostra = $vParticipantesAmostraNovo;
		
		
	}
	
	public function cadastrarAction() {
		try {
			//RECUPERAR USUARIO LOGADO
			$auth = Zend_Auth::getInstance();
			$vUsuarioLogado = $auth->getIdentity();
			
                       $oParticipantesAmostra = new Amostra_ParticipantesAmostra();
                        
			$request = $this->_request;
			$nIdAmostra = $request->getParam("fIdAmostra");
			$vUsuario = $request->getParam("fUsuarios");

			$oParticipantesAmostra->delete("coleta_amostra_id = $nIdAmostra", "excluir-participantes-amostra", $vUsuarioLogado["id"]);
			
                        
			foreach ($vUsuario as $usuario) {
				$vData = array("coleta_amostra_id" => $nIdAmostra, "seg_usuario_id" => $usuario);
				$oParticipantesAmostra->insert($vData, "cadastrar-participantes-amostra", $vUsuarioLogado["id"]);
			}
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Permissões alteradas com sucesso!");
			$this->_redirect('/amostra');
		}
		catch (Zend_Exception $e) {
			$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Permissões", $sString);
		}
	}
	
	public function verificaPermissaoAction() {
		$sQP = $this->_request->getParam("sOP");
		$this->view->layout()->disableLayout();
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
            	$oVerifica = new VerificaPermissao("participantes-amostra", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
         	if ($oVerifica->bResultado) {
			$this->view->bPermissao = $oVerifica->bResultado;
		}
	}
}
