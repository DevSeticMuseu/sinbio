<?php

class Sinbio_PessoasController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		   error_reporting (E_ALL & ~E_NOTICE & ~E_STRICT);
		$this->view->layout()->nmModulo = "Modulo de Pessoas";
		$this->view->layout()->nmController = "pessoas";
		$this->view->layout()->nmPrograma = "pessoas";
		
		
	}

        
        public function indexAction() {
            
//            $this->view->layout()->includeCss = '
//				<link href="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
//				';
        
        $this->view->layout()->includeCss = '
				<link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
		';
         
                  $this->view->layout()->includeJs = '
				<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
                                <script src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
			
		';
            
             //ALIMENTANDO SELECT DE TITULA��O
            $oTitulacao = new Seg_Titulacao();
            $this->view->vTitulacao = $oTitulacao->fetchAll()->toArray();
            
            //ALIMENTANDO SELECT DE Grupo
            $oGrupo = new Seg_GrupoUsuario();
            $this->view->vGrupo = $oGrupo->fetchAll()->toArray();
            
            //ALIMENTANDO SELECT DE Protocolo
            $oProtocolo = new Protocolo_Protocolo();
            $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();
             //ALIMENTANDO SELECT DE Instituicao
            $oInstituicao = new Instituicao_Instituicao();
            $this->view->vInstituicao = $oInstituicao->fetchAll()->toArray();
            
            //ALIMENTANDO SELECT DE Instituicao
            $oProjetos = new ProjetoPrograma_ProjetoPrograma();
            $this->view->vProjetos = $oProjetos->fetchAll()->toArray();
            
            //ALIMENTANDO SELECT DE Municipio
            $oMunicipio = new Loc_Municipio();
            $this->view->vMunicipio = $oMunicipio->fetchAll()->toArray();

             $this->view->layout()->nmOperacao = "Listar Pessoas";
             $request = $this->_request;
              $pessoa = new Pessoas_Pessoas();
            
                          
            $data = $pessoa->getSearchPessoas( $request->getParam("fUsuario"),$request->getParam("fIdTitulacao"),$request->getParam("fIdGrupo"),$request->getParam("fIdProtocolo"),$request->getParam("fIdInstituicao"),$request->getParam("fIdProjetos"));
              
                $this->view->paginator = $data;
            
                
               
                
            //  $this->view->dadosPes = $data;
              
        }
        
//        public function searchAction()
//        {
//            //ALIMENTANDO SELECT DE TITULA��O
//            $oTitulacao = new Seg_Titulacao();
//            $this->view->vTitulacao = $oTitulacao->fetchAll()->toArray();
//            
//            //ALIMENTANDO SELECT DE Grupo
//            $oGrupo = new Seg_GrupoUsuario();
//            $this->view->vGrupo = $oGrupo->fetchAll()->toArray();
//            
//          //ALIMENTANDO SELECT DE Protocolo
//            $oProtocolo = new Protocolo_Protocolo();
//            $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();
//            
//            //ALIMENTANDO SELECT DE Instituicao
//            $oInstituicao = new Instituicao_Instituicao();
//            $this->view->vInstituicao = $oInstituicao->fetchAll()->toArray();
//            
//            //ALIMENTANDO SELECT DE Instituicao
//            $oProjetos = new ProjetoPrograma_ProjetoPrograma();
//            $this->view->vProjetos = $oProjetos->fetchAll()->toArray();
//            
//            //ALIMENTANDO SELECT DE Municipio
//            $oMunicipio = new Loc_Municipio();
//            $this->view->vMunicipio = $oMunicipio->fetchAll()->toArray();
//            
//             $this->view->layout()->nmOperacao = "Listar Pessoas";
//             $request = $this->_request;
//             
//              $pessoa = new Pessoas_Pessoas();
//                
//              $data = $pessoa->getSearchPessoas( $request->getParam("fUsuario"),$request->getParam("fIdTitulacao"),$request->getParam("fIdGrupo"),$request->getParam("fIdProtocolo"),$request->getParam("fIdInstituicao"),$request->getParam("fIdProjetos"));
//                
//              
//               $paginator = Zend_Paginator::factory($data);
//                $paginator->setCurrentPageNumber($this->_getParam('page'));
//                $paginator->setItemCountPerPage(15);
//                $this->view->paginator = $paginator;
//                
//                
//                
//              
//              $this->render('index');
//              
//           
//        }
        
        
     
   
}
