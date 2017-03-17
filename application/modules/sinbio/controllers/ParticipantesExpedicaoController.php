<?php

class Sinbio_ParticipantesExpedicaoController extends Zend_Controller_Action {

	public function init() {
               error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "participantes-expedicao";
		$this->view->layout()->nmPrograma = "Participantes da Expedição";
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
                
                $oParticipantesExpedicao = new Expedicao_ParticipantesExpedicao();
                $oProjetoPrograma = new ProjetoPrograma_ProjetoPrograma();
                $oExpedicao = new Expedicao_Expedicao();
                $oUsuario = new Seg_Usuario();
                
		
		$nIdExpedicao = $this->_request->getParam('nId');
		$vExpedicao = $oExpedicao->fetchAll("id = $nIdExpedicao");
		$this->view->fExpedicao = $vExpedicao[0]["id"];
		$this->view->fIdExpedicao = $vExpedicao[0]["id"];
		$where = 'coleta_expedicao_id = '.$nIdExpedicao;
	
                $vUsuario = $oUsuario->fetchAll(null, "nm_usuario ASC");
                $this->view->vUsuario = $vUsuario;
   		
             $vParticipantesExpedicao = $oParticipantesExpedicao->fetchAll($where);
               $n = 1;
                foreach($vParticipantesExpedicao as $participantesexpedicao) {
			$vParticipantesExpedicaoNovo[$n] = $participantesexpedicao["seg_usuario_id"];
			$n++;
		}
		
		$this->view->vParticipantesExpedicao = $vParticipantesExpedicaoNovo;
		
		
	}
	
	public function cadastrarAction() {
		try {
			//RECUPERAR USUARIO LOGADO
			$auth = Zend_Auth::getInstance();
			$vUsuarioLogado = $auth->getIdentity();
			
                       $oParticipantesExpedicao = new Expedicao_ParticipantesExpedicao();
                        
			$request = $this->_request;
			$nIdExpedicao = $request->getParam("fIdExpedicao");
			$vUsuario = $request->getParam("fUsuarios");

			$oParticipantesExpedicao->delete("coleta_expedicao_id = $nIdExpedicao", "excluir-participantes-expedicao", $vUsuarioLogado["id"]);
			
                        
			foreach ($vUsuario as $usuario) {
				$vData = array("coleta_expedicao_id" => $nIdExpedicao, "seg_usuario_id" => $usuario);
				$oParticipantesExpedicao->insert($vData, "cadastrar-participantes-expedicao", $vUsuarioLogado["id"]);
			}
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Permissões alteradas com sucesso!");
			$this->_redirect('/expedicao');
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
            	$oVerifica = new VerificaPermissao("participantes-expedicao", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
         	if ($oVerifica->bResultado) {
			$this->view->bPermissao = $oVerifica->bResultado;
		}
	}
}
