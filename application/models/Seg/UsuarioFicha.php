<?php

class Seg_UsuarioFicha extends Zend_Db_Table_Abstract {

    protected $_name = 'seg_usuario';
    protected $_primary = 'id';
    protected $_erroMensagem = null;
    private $db = null;

    public function __construct() {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }

    public function getFichaUsuario($Usuario = 0) {
        $sql = "
            select 

            use.id,
            use.nm_usuario,
            use.email,
            use.dt_cadastro,
            use.dt_saida,
            use.citacao,
            use.cpf,
            use.rg,
            use.sisbio,
            use.lattes,
            titu.nm_titulacao,
            proto.nm_protocolo,
            inst.sigla,
            inst.razao_social 

            from seg_usuario use

            join seg_titulacao titu on titu.id = use.seg_titulacao_id
            
            join coleta_protocolo proto on proto.id = use.coleta_protocolo_id

            join seg_instituicao inst on inst.id = use.seg_instituicao_id";
        
        if ($Usuario != 0) {
            $sql.=" where use.id  = " . $Usuario;
        }

        return $result = $this->db->fetchAll($sql);
    }
}