<?php
class Pessoas_Pessoas extends Zend_Db_Table_Abstract {



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

       public function getPessoas() {
             $sql = "
             select

               usr.seg_grupo_usuario_id,
               usr.seg_instituicao_id,
               usr.seg_titulacao_id,
               usr.nm_usuario,
               usr.email,
               usr.bolsista,
               usr.vinculo_empregaticio,
               titu.nm_titulacao,
               inst.razao_social,
               grupo.nm_grupo_usuario,
               mun.id,
               mun.nm_municipio,
               usernucl.loc_nucleo_id,
               nucl.nm_nucleo,
               prot.id,
               prot.nm_protocolo,
               projprog.nm_projeto_programa


               from seg_usuario usr

               join seg_grupo_usuario grupo on usr.seg_grupo_usuario_id = grupo.id

               join usuario_nucleo usernucl on usr.id = usernucl.seg_usuario_id

               join loc_nucleo nucl on usernucl.loc_nucleo_id = nucl.id

               join seg_titulacao titu on usr.seg_titulacao_id = titu.id

               join seg_instituicao inst on usr.seg_instituicao_id = inst.id

               join loc_municipio mun on inst.loc_municipio_id = mun.id

               left join coleta_protocolo prot on prot.seg_usuario_id = usr.id

               left join coleta_protocolo_coleta_projeto_programa projetos on projetos.coleta_protocolo_id = prot.id

               left join coleta_projeto_programa projprog on projprog.id = projetos.coleta_projeto_programa_id


               LIMIT 10

       ";
             return $result = $this->db->fetchAll($sql);

       }

//        public function getSearchPessoas($nome_usuario = 0 ,$titulacao = 0 ,$grupo = 0 , $protocolo = 0 ,
//                $instituicao = 0 , $municipio = 0 )
       public function getSearchPessoas( $nome_usuario = "" , $titulacao = 0 ,$grupo = 0 , $protocolo = 0 ,$nucleo = 0 ,$instituicao = 0 ,$projeto = 0  )
        {
               $sql = "
                  select

                      DISTINCT
                      usr.id as id_usuario,
                      usr.seg_grupo_usuario_id,
                      usr.seg_instituicao_id,
                      usr.seg_titulacao_id,
                      usr.nm_usuario,
                      usr.email,
                      usr.bolsista,
                      usr.vinculo_empregaticio,
                      titu.nm_titulacao,
                      inst.id,
                      inst.razao_social,
                      grupo.id,
                      grupo.nm_grupo_usuario,
                      mun.id,
                      mun.nm_municipio,
                      usernucl.loc_nucleo_id,
                      nucl.nm_nucleo,
                      prot.id,
                      prot.nm_protocolo,
                      projprog.id,
                      projprog.nm_projeto_programa



                      from seg_usuario usr

                      join seg_grupo_usuario grupo on usr.seg_grupo_usuario_id = grupo.id

                      join seg_titulacao titu on usr.seg_titulacao_id = titu.id

                      join seg_instituicao inst on usr.seg_instituicao_id = inst.id

                      join loc_municipio mun on inst.loc_municipio_id = mun.id

                      left join usuario_nucleo usernucl on usr.id = usernucl.seg_usuario_id

                      left join loc_nucleo nucl on usernucl.loc_nucleo_id = nucl.id

                      left join coleta_protocolo prot on prot.seg_usuario_id = usr.id

                      left join coleta_protocolo_coleta_projeto_programa projetos on projetos.coleta_protocolo_id = prot.id

                      left join coleta_projeto_programa projprog on projprog.id = projetos.coleta_projeto_programa_id

                     where usr.nm_usuario LIKE '%".$nome_usuario."%'




                 ";


                 if($titulacao != 0)
                 {
                     $sql.=" and titu.id = " .$titulacao;
                 }

                  if($grupo != 0)
                 {
                     $sql.=" and grupo.id = " .$grupo;
                 }

                 if($protocolo != 0)
                 {
                     $sql.=" and prot.id = " .$protocolo;
                 }

                 //Comentario:***Paulo Rosa**** Atenção!! não tenho nenhum usuario nessa tabela
                 if($nucleo != 0)
                 {
                     $sql.=" and nucl.id = " .$nucleo;
                 }

                  if($instituicao != 0)
                 {
                     $sql.=" and inst.id = " .$instituicao;
                 }

                 if($projeto != 0)
                 {
                     $sql.=" and projprog.id = " .$projeto;
                 }





                 $sql.=" order by  usr.nm_usuario ASC,  grupo.id ASC
                    ";

            // print_r($sql);

         return $result = $this->db->fetchAll($sql);

        }






}
