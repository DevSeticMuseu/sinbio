<?php

class Sinbio_PermissaoController extends Zend_Controller_Action {

	public function init() {
             error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Segurança";
		$this->view->layout()->nmController = "grupousuario";
		$this->view->layout()->nmPrograma = "Permissão";
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
   					$("#fOperacao").multiselect({
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
		
		$oPermissao = new Seg_Permissao();
		$oPrograma = new Seg_Programa();
		$oOperacao = new Seg_Operacao();
		$oGrupoUsuario = new Seg_GrupoUsuario();
		
		$nIdGrupoUsuario = $this->_request->getParam('nId');
		$vGrupoUsuario = $oGrupoUsuario->fetchAll("id = $nIdGrupoUsuario");
		$this->view->fGrupoUsuario = $vGrupoUsuario[0]["nm_grupo_usuario"];
		$this->view->fIdGrupoUsuario = $vGrupoUsuario[0]["id"];
		$where = 'seg_grupo_usuario_id = '.$nIdGrupoUsuario;
		
		$vPrograma = $oPrograma->fetchAll();
		$this->view->vPrograma = $vPrograma;
		
		$vPermissao = $oPermissao->fetchAll($where);
		$n = 1;
		foreach($vPermissao as $permissao) {
			$vPermissaoNovo[$n] = $permissao["seg_operacao_id"];
			$n++;
		}
		
		$this->view->vPermissao = $vPermissaoNovo;
		
		//UtilsFile::printvar($vPermissaoNovo,$vPermissao);
		
		foreach ($vPrograma as $programa) {
			$where = "seg_programa_id = ".$programa["id"];
			$vOperacao = $oOperacao->fetchAll($where);
			
			$vNovoArray[] = array("nome" => $programa["nm_display"],"operacao" => $vOperacao); 
			//UtilsFile::printvardie($this->view->vOperacao);
		}
		$this->view->vNovoArray = $vNovoArray;
		//UtilsFile::printvar($vNovoArray,$vPermissaoNovo);
	}
	
	public function cadastrarAction() {
		try {
			//RECUPERAR USUARIO LOGADO
			$auth = Zend_Auth::getInstance();
			$vUsuarioLogado = $auth->getIdentity();
			
			$oPermissao = new Seg_Permissao();
			
			$request = $this->_request;
			$nIdGrupoUsuario = $request->getParam("fIdGrupoUsuario");
			$vOperacao = $request->getParam("fOperacao");

			$oPermissao->delete("seg_grupo_usuario_id = $nIdGrupoUsuario", "excluir-permissao", $vUsuarioLogado["id"]);
			
			foreach ($vOperacao as $operacao) {
				$vData = array("seg_grupo_usuario_id" => $nIdGrupoUsuario, "seg_operacao_id" => $operacao);
				$oPermissao->insert($vData, "cadastrar-permissao", $vUsuarioLogado["id"]);
			}
			$_SESSION["sMsg"] = UtilsFile::recuperaMensagens(1, "Sucesso", "Permissões alteradas com sucesso!");
			$this->_redirect('/sinbio/grupo-usuario');
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
            	$oVerifica = new VerificaPermissao("permissao", $sQP, $vUsuarioLogado["seg_grupo_usuario_id"]);
         	if ($oVerifica->bResultado) {
			$this->view->bPermissao = $oVerifica->bResultado;
		}
	}
}