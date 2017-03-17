<?php

class Sinbio_ExpedicaoLiderController extends Zend_Controller_Action {

	public function init() {
               error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "expedicao";
		$this->view->layout()->nmController = "expedicao-lider";
		$this->view->layout()->nmPrograma = "Lider da Expedição";
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
   					$("#fUsuariosParticipantes").multiselect({
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
                
                $oExpedicaoLider = new Expedicao_ExpedicaoLider();
                $oParticipantesExpedicao = new Expedicao_ParticipantesExpedicao();
                $oExpedicao = new Expedicao_Expedicao();
                
                
		
		$nIdExpedicao = $this->_request->getParam('nId');
		$vExpedicao = $oExpedicao->fetchAll("id = $nIdExpedicao");
		$this->view->fExpedicao = $vExpedicao[0]["id"];
		$this->view->fIdExpedicao = $vExpedicao[0]["id"];
		$where = 'coleta_expedicao_id = '.$nIdExpedicao;
	
                $vParticipantesExpedicao = $oParticipantesExpedicao->fetchAll($where);
                $this->view->vUsuarioParticipantes = $vParticipantesExpedicao;
   		
               $vExpedicaoLider = $oExpedicaoLider->fetchAll($where);
               $n = 1;
                foreach($vExpedicaoLider as $expedicao_lider) {
			$vExpedicaoLiderNovo[$n] = $expedicao_lider["seg_usuario_id"];
			$n++;
		}
		
		$this->view->vExpedicaoLider = $vExpedicaoLiderNovo;
		
		
	}
	
	public function cadastrarAction() {
		try {
			//RECUPERAR USUARIO LOGADO
			$auth = Zend_Auth::getInstance();
			$vUsuarioLogado = $auth->getIdentity();
			
                        $oExpedicaoLider = new Expedicao_ExpedicaoLider(); 
                        
			$request = $this->_request;
			$nIdExpedicao = $request->getParam("fIdExpedicao");
                        
			$vUsuariosParticipantes = $request->getParam("fUsuariosParticipantes");

			$oExpedicaoLider->delete("coleta_expedicao_id = $nIdExpedicao", "excluir-expedicao-lider", $vUsuarioLogado["id"]);
			
                        
			foreach ($vUsuariosParticipantes as $usuarioparticipantes) {
                            
                                $vData = array("coleta_expedicao_id" => $nIdExpedicao, "seg_usuario_id" => $usuarioparticipantes);
				$oExpedicaoLider->insert($vData, "cadastrar-expedicao-lider", $vUsuarioLogado["id"]);
			}
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Permissões alteradas com sucesso!");
			$this->_redirect('/expedicao');
		}
		catch (Zend_Exception $e) {
			$this->view->layout()->msg = UtilsFile::recuperaMensagens(2, "Erro ao Alterar Permissões", $e);
		}
	}
	

}
