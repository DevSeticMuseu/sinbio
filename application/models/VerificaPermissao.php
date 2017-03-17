<?php
class VerificaPermissao
{
	public $bResultado;
	
	public function __construct($sController,$sAction,$nGrupoUsuario)
	{
		$bd = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$sql = $bd->query("
					SELECT
							sp.*
					
					FROM	seg_permissao sp
					
					JOIN	seg_operacao so
							ON (so.id = sp.seg_operacao_id)
					        
					JOIN	seg_programa spr
							ON (spr.id = so.seg_programa_id)
					
					JOIN	seg_grupo_usuario sgu
							ON (sgu.id = sp.seg_grupo_usuario_id)
					
					WHERE	sgu.id = ".$nGrupoUsuario."
					
					AND		spr.nm_programa = '.$sController.'
					
					AND		so.nm_operacao = '.$sAction.'
				");
		$reg = $sql->fetchAll();
		
                UtilsFile::printvar($reg,$sql);
		//UtilsFile::printvardie($sql->queryString());
		
                if (count($reg))
			$this->bResultado = true;
		else
			$this->bResultado = false;
	}	
}