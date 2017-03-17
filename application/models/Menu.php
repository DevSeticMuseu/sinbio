<?php
class Menu
{
	var	$vMenu;
	var	$sMenuSelecionado;
	//variavel necessaria para mostrar o total de amostras cadastradas no sistsema
	var $vAmostra;
	var $total;
	
	public function __construct($sMenuSelecionado=null) {
		
		if ($sMenuSelecionado) {
			$this->sMenuSelecionado = $sMenuSelecionado;
		}
		
		$bd = Zend_Db_Table::getDefaultAdapter();
		
		//RECUPERA TOTAL DE AMOSTRAS CADASTRADAS NO SISTEMA
		$amostraSql = $bd->query("
						SELECT *
						FROM	coleta_amostra
					");
		$rs = $amostraSql->fetchAll();
		$this->total = count($rs);
		
		
		
		$auth = Zend_Auth::getInstance();
		$vUsuarioLogado = $auth->getIdentity();
                
            
                $nIdGrupoUsuario = $vUsuarioLogado["seg_grupo_usuario_id"];
		
		//RECUPERA CONTROLLER
		$sSql = $bd->query("
						SELECT DISTINCT
								sm.*
								
						FROM	seg_modulo sm
						
						JOIN seg_programa spr
								ON (spr.seg_modulo_id = sm.id)
						
						JOIN seg_operacao so
								ON (so.seg_programa_id = spr.id)
						
						JOIN seg_permissao spe
								ON (spe.seg_operacao_id = so.id)
						
						JOIN seg_grupo_usuario sgu
								ON (sgu.id = spe.seg_grupo_usuario_id)
						
						WHERE
								sgu.id = ".$nIdGrupoUsuario."
                                                    AND		spr.menu_lateral = 1 
                                                    ORDER by sm.nm_display ASC
                                                       
					");
		
                
		$vRegModulo = $sSql->fetchAll();
		
                
                $sSql->closeCursor();
		
		foreach ($vRegModulo as $regModulo) {
			//RECUPERA PROGRAMA (CONTROLLER)
			$sSql = $bd->query("
						SELECT DISTINCT
								spr.*
								
						FROM	seg_programa spr
						
						JOIN seg_operacao so
								ON (so.seg_programa_id = spr.id)
						
						JOIN seg_permissao spe
								ON (spe.seg_operacao_id = so.id)
						
						JOIN seg_grupo_usuario sgu
								ON (sgu.id = spe.seg_grupo_usuario_id)
						
						WHERE	sgu.id = ".$nIdGrupoUsuario."
						
						AND		spr.seg_modulo_id = ".$regModulo["id"]."	
					
						AND		spr.menu_lateral = 1
                                                ORDER BY spr.id ASC
					");
			$vRegPrograma = $sSql->fetchAll();
			$sSql->closeCursor();
			
			//UtilsFile::printvar($vRegAction);
			$bAtivo = false;
			foreach ($vRegPrograma as $regPrograma) {
				//RECUPERA OPERACAO (ACTION)
				/*
				$sSql = $bd->query("
						SELECT DISTINCT
								so.*
								
						FROM	seg_operacao so
						
						JOIN seg_programa spr
								ON (spr.id = so.id_programa)
								
						JOIN seg_permissao spe
								ON (spe.id_operacao = so.id)
						
						JOIN seg_grupo_usuario sgu
								ON (sgu.id = spe.id_grupo_usuario)
						
						WHERE	sgu.id = ".$nIdGrupoUsuario."
						
						AND		spr.id = ".$regPrograma["id"]."	
					");
				$vRegOperacao = $sSql->fetchAll();
				$sSql->closeCursor();
					
				//UtilsFile::printvar($vRegAction);
					
				foreach ($vRegOperacao as $regOperacao) {
						$vOperacoes[] = array("operacao" => $regOperacao["nm_display"], "action" => $regOperacao["nm_operacao"]);
				}
				$vProgramas[] = array("programa" => $regPrograma["nm_display"], "controller" => $regPrograma["nm_programa"],"operacoes" => $vOperacoes);
				unset($vOperacoes);
				*/
				
				if ($regPrograma["nm_programa"] == $this->sMenuSelecionado )
					$bAtivo = true;
				
				$vProgramas[] = array("programa" => $regPrograma["nm_display"], "controller" => $regPrograma["nm_programa"]);
			}
			$vModulo[] = array("ico" => $regModulo["ico"],"modulo" => $regModulo["nm_display"], "ativo" => $bAtivo, "programas" => $vProgramas);
			unset($vProgramas);
		}
		
		//UtilsFile::printvardie($vModulo,$this->sMenuSelecionado);
		$this->vMenu = $vModulo;
		$this->vAmostra = $this->total;
	}
}