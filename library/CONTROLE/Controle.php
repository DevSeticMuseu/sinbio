<?php
class CONTROLE_Controle extends Zend_Controller_Plugin_Abstract {

	private $bd;
	
	public function __construct($db) {
		$this->bd = $db;
	}

	public function verificaPermissao($sController,$sAction,$nIdGrupoUsuario) {
		
		//UtilsFile::printvardie($this->bd);
		
		$sSql = $this->bd->query("
					SELECT
							sp.*
					
					FROM	seg_permissao sp
					
					JOIN	seg_operacao so
							ON (so.id = sp.seg_operacao_id)
					
					JOIN	seg_programa spr
							ON (spr.id = so.seg_programa_id)
							
					JOIN	seg_modulo sm
							ON (sm.id = spr.seg_modulo_id)
							
					JOIN seg_grupo_usuario sgu
							ON (sgu.id = sp.seg_grupo_usuario_id)
							
					WHERE	sgu.id = ".$nIdGrupoUsuario."
					
					AND		spr.nm_programa = '".$sController."'
					
					AND		so.nm_operacao = '".$sAction."'
				");

		$vReg = $sSql->fetchAll();
		$sSql->closeCursor();
		
		//UtilsFile::printvardie($vReg,$sSql);
		
		if (count($vReg))
			return true;
		else
			return false;
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$sModule = $request->getModuleName();
		$sController = $request->getControllerName();
		$sAction = $request->getActionName();
	
		//UtilsFile::printvardie($sController,$sAction);
		
		if ($sModule == "sinbio" && $sController != "login") {
			$auth = Zend_Auth::getInstance();
			
			if ($auth->hasIdentity()) {
				
				$vUsuarioLogado = $auth->getIdentity();
				
				if (!$this->verificaPermissao($sController, $sAction, $vUsuarioLogado["seg_grupo_usuario_id"])) {
					$request->setControllerName('error')
							->setActionName('permissao-negada');
				}
			}
			else if ($sController != "login") {
				$request->setControllerName('login')
						->setActionName('index');
			}
		}
	}
}