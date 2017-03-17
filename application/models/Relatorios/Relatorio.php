<?php
class Relatorios_Relatorio extends Zend_Db_Table_Abstract {


        
      private $db = null;
    
    public function __construct() {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }	

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
	

	public function totalRegistro() {
		$vReg = parent::fetchAll();
	
		return count($vReg);
	}
	
	public function setErroMensagem($erroMensagem) {
		$this->_erroMensagem = $erroMensagem;
	}
	
	public function getErroMensagem() {
		return $this->_erroMensagem;
	}
        
       
        
        public function getRelatorio($protocolo = 0 , $metodo = 0 , $expedicao = 0 )
        {
               $sql = "
              select 

		   am.id_amostra_coleta,
		   am.data_coleta,
		   am.hora_coleta,
		   sit.nm_sitio,
		   am.latitude,
		   am.direcao_latitude,
		   am.longitude,
		   am.direcao_longitude,
		   proj.sistema_projecao,
		   cons.conservacao_material,
		   dest.nm_destinacao,
		   atra.nm_atrativos,
		   prot.nm_protocolo,
                   met.id,
		   met.nm_metodo,
		   met.sigla as sigla_metodo,
                   exp.id ,
                   prot.id,
		   prot.sigla as sigla_protocolo,
                   projpro.id,
		   projpro.nm_projeto_programa
		   
		 

	           from coleta_amostra am

	           join coleta_projecao proj on am.coleta_projecao_id = proj.id

	           join coleta_conservacao cons on am.coleta_conservacao_id = cons.id

	           join coleta_destinacao dest on am.coleta_destinacao_id = dest.id

		   left join coleta_atrativos atra on am.coleta_atrativos_id = atra.id

		   join coleta_protocolo prot on am.coleta_protocolo_id = prot.id

		   join coleta_metodos met on am.coleta_metodos_id = met.id
				
	           join coleta_expedicao exp on am.coleta_expedicao_id = exp.id

	           join coleta_projeto_programa projpro on exp.coleta_projeto_programa_id = projpro.id

	           join loc_sitio sit on exp.loc_sitio_id = sit.id
	           

                 ";
                 if($protocolo != 0)
                 {
                     $sql.="where prot.id = " .$protocolo;
                 }  
                 if($metodo != 0)
                 {
                     $sql.=" and  met.id = " .$metodo;
                 }
                 
                 if($expedicao != 0)
                 {
                     $sql.=" and  exp.id = " .$expedicao;
                 }
                 
                 $sql.="
                 order by prot.id asc
                    ";
         
             //print_r($sql);
         
         return $result = $this->db->fetchAll($sql);
         
        }
        
        
        
        
        public function getRelatorioSisbio($protocolo = 0 , $metodo = 0 , $expedicao = 0 )
        {
               $sql = "
             select 
distinct
		   am.id_amostra_coleta,
		   users.nm_usuario,
		   am.data_coleta,
		   am.hora_coleta,
		   sit.nm_sitio,
		   am.latitude,
		   am.direcao_latitude,
		   am.longitude,
		   am.direcao_longitude,
		   proj.sistema_projecao,
		   cons.conservacao_material,
		   sub.qt_machos as machos,
		   sub.qt_femeas as femea,
		   
		   dest.nm_destinacao,
		   atra.nm_atrativos,
		   prot.nm_protocolo,
                   met.id,
		   met.sigla as sigla_metodo,
                   exp.id ,
                   prot.id,
		   prot.sigla as sigla_protocolo,
                   projpro.id,
		   projpro.nm_projeto_programa,
		   taxon.referencia as referencia_taxon,
		   taxon.dt_determinacao as data_determinacao,
		   taxon.observacao as taxon_observacao
		   
		 

	           from coleta_amostra am

	           join coleta_projecao proj on am.coleta_projecao_id = proj.id

	           join coleta_conservacao cons on am.coleta_conservacao_id = cons.id

	           join coleta_destinacao dest on am.coleta_destinacao_id = dest.id

		   left join coleta_atrativos atra on am.coleta_atrativos_id = atra.id

		   join coleta_protocolo prot on am.coleta_protocolo_id = prot.id

		   join coleta_metodos met on am.coleta_metodos_id = met.id
				
	           join coleta_expedicao exp on am.coleta_expedicao_id = exp.id

	           join coleta_projeto_programa projpro on exp.coleta_projeto_programa_id = projpro.id

	           join loc_sitio sit on exp.loc_sitio_id = sit.id

	           join coleta_subamostra  sub on am.id = sub.coleta_amostra_id

	           join taxonomia_taxon taxon on sub.coleta_amostra_id = taxon.id

	           join coleta_participantes_amostra part on part.coleta_amostra_id = am.id

	           join seg_usuario users on part.seg_usuario_id = users.id
	           
	           

                 ";
                 if($protocolo != 0)
                 {
                     $sql.="where prot.id = " .$protocolo;
                 }  
                 if($metodo != 0)
                 {
                     $sql.=" and  met.id = " .$metodo;
                 }
                 
                 if($expedicao != 0)
                 {
                     $sql.=" and  exp.id = " .$expedicao;
                 }
                 
                 $sql.="
                 order by prot.id asc
                    ";
         
             //print_r($sql);
         
         return $result = $this->db->fetchAll($sql);
         
        }
        
        
            public function getQtdProtocoloExpedicao($protocolo = 0)
        {
               $sql = "
select 

distinct

exp.id,
prot.sigla,
prot.id as id_protocolo 

from coleta_expedicao exp

join coleta_protocolo prot on prot.id = exp.coleta_protocolo_id ";

 if($protocolo != 0)
                 {
                     $sql.="where prot.id = " .$protocolo;
                 }  

 $sql.="order by  id_protocolo  asc                 
";
         
             //print_r($sql);
         
         return $result = $this->db->fetchAll($sql);
         
        }
        
        
          public function getQtdAmostraProtocolo($protocolo = 0)
        {
               $sql1 = "
select distinct

amo.id as amostra,
prot.id,
prot.sigla

from coleta_protocolo prot

join coleta_amostra amo on amo.coleta_protocolo_id = prot.id ";
 if($protocolo != 0)
                 {
                     $sql1.=" where prot.id = " .$protocolo;
                 }  
$sql1.=" order by prot.id asc";
         
             //print_r($sql);
         
         return $result = $this->db->fetchAll($sql1);
         
        }
        
        
        public function getQtdAtrativos()
        {
            
            $sql = "select * from coleta_atrativos";
            return $result = $this->db->fetchAll($sql);
        }
        
        
            public function getQtdMetodosAmostra()
        {
            
            $sql = "select 
met.id as id_metodo,
met.sigla,
met.nm_metodo,
am.id as id_amostra
from coleta_metodos met

join coleta_amostra am on met.id = am.coleta_metodos_id

order by  met.id asc";
            return $result = $this->db->fetchAll($sql);
        }
        
        
}