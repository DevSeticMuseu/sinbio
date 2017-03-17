<?php
class Login {
	private $sMsg;
	 
	public function logar($login=NULL,$senha=NULL){
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		//Inicia o adaptador Zend_Auth para banco de dados
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		$authAdapter->setTableName('seg_usuario')
					->setIdentityColumn('login')
					->setCredentialColumn('senha');
		//->setCredentialTreatment('SHA1(?)');
		//Define os dados para processar o login
		$authAdapter->setIdentity($login)
					->setCredential($senha);
                //join
                // 
                // $select = $authAdapter->getDbSelect();
                // $select->join( array('gu' => 'seg_grupo_usuario'), 'gu.id_usuario = seg_usuario.id_usuario' );
                
		//Efetua o login
		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($authAdapter);
		
		//UtilsFile::printvardie($authAdapter->getResultRowObject(),$result,$result->isValid());	
	
		//Verifica se o login foi efetuado com sucesso
		if ($result->isValid()==1) {
			//Armazena os dados do usuário em sessão, apenas desconsiderando
			//a senha do usuário
			$info = $authAdapter->getResultRowObject();
			
			//UtilsFile::printvardie($info,$info->seg_grupo_usuario_id);
			
			$vUsuarioLogado = array(
						"id"				=> $info->id,
						//"id_grupo_usuario"	=> $info->id_grupo_usuario,
                                                "seg_grupo_usuario_id"	=> $info->seg_grupo_usuario_id,
						"nm_usuario"		=> $info->nm_usuario,
						"login"				=> $info->login,
						"senha"				=> $info->senha,
					);
			
			$storage = $auth->getStorage();
			$storage->write($vUsuarioLogado);
			//Redireciona para o Controller protegido
			return true;
		}
		else {
			$this->getMessages($result);
			
			return false;
		}
	}
	
	public function getMessages(Zend_Auth_Result $result){
	
		switch ($result->getCode()){
			case $result::FAILURE_IDENTITY_NOT_FOUND:
				$this->sMsg = 'Login não encontrado';
				break;
			case $result::FAILURE_IDENTITY_AMBIGUOUS:
				$this->sMsg = 'Login em duplicidade';
				break;
			case $result::FAILURE_CREDENTIAL_INVALID:
				$this->sMsg = 'Senha não corresponde';
				break;
			default:
				$this->sMsg = 'Login e/ou senha não encontrados';
		}
	}
	
	public function getMsg() {
		return $this->sMsg;
	}
}