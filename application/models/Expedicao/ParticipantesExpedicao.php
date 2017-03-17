<?php
class Expedicao_ParticipantesExpedicao extends Zend_Db_Table_Abstract {

	protected $_name			= 'coleta_participantes_expedicao';
	protected $_primary		= 'id';
	protected $_erroMensagem	= null;
        
//             
//      private $db = null;
//    
//        public function __construct() {
//            $this->db = Zend_Db_Table::getDefaultAdapter();
//        }	
//        
        
        public function fetchAll($sWhere=null,$sOrder=null,$nPagina=null,$nResultadoPagina=null) {
		$sSql = $this->select();
	
		if ($sWhere)
			$sSql->where($sWhere);
	
		if ($sOrder)
			$sSql->order($sOrder);
	
		if ($nPagina && $nResultadoPagina) {
			$nInicio = (($nPagina-1) * $nResultadoPagina);
			$sSql->limit($nResultadoPagina, $nInicio);
		}
	
		return parent::fetchAll($sSql);
	}

	public function insert($vData, $sOperacao, $nIdUsuario) {
		try {
				
			$vLog = array(
					"operacao"		=> $sOperacao,
					"seg_usuario_id"	=> $nIdUsuario,
					"objeto"		=> serialize($vData),
					"dt_log"			=> date("Y-m-d")
			);
				
			$oLog = new Seg_Log();
				
			$oLog->insert($vLog);

			//UtilsFile::printVarDie($vData, $sOperacao, $nIdUsuario);
			return parent::insert($vData);
		}
		catch (Zend_Db_Exception $e) {
			$this->setErroMensagem($e->getMessage());
			return false;
		}
	}
	
	public function delete($sWhere, $sOperacao, $nIdUsuario) {
		try {
			$vLog = array(
					"operacao"		=> $sOperacao,
					"seg_usuario_id"	=> $nIdUsuario,
					"objeto"		=> "Excluiu Participantes da Expedicao",
					"dt_log"			=> date("Y-m-d")
			);
				
			$oLog = new Seg_Log();
			$oLog->insert($vLog);
	
			//UtilsFile::printVarDie($sWhere, $sOperacao, $nIdUsuario);
			return parent::delete($sWhere);
		}
		catch (Zend_Db_Exception $e) {
			$this->setErroMensagem($e->getMessage());
			return false;
		}
	}

	public function setErroMensagem($erroMensagem) {
		$this->_erroMensagem = $erroMensagem;
	}
	
	public function getErroMensagem() {
		return $this->_erroMensagem;
	}
        
        
        public function setParticipantes($sWhere)
        {
             $sql = "
               select * 
		   
	           from coleta_participantes_expedicao  partexp

                   join seg_usuario usuario on partexp.seg_usuario_id = usuario.id

                 ";
                 if($sWhere != null)
                 {
                     $sql.="where " .$sWhere;
                 }  
                
                //  print_r($sql);
         
                return $result = $this->db->fetchAll($sql);
        }
        
        
        
        
}