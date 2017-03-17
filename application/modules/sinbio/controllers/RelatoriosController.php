<?php

class Sinbio_RelatoriosController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		   error_reporting (E_ALL & ~E_NOTICE & ~E_STRICT);
		$this->view->layout()->nmModulo = "Módulo Relatorios";
		$this->view->layout()->nmController = "relatorios";
		$this->view->layout()->nmPrograma = "relatorios";
		
		
	}

	public function indexAction() {
        $this->view->layout()->nmOperacao = "Gerar Relatorio";
	     
        //ALIMENTANDO SELECT DE PROTOCOLO
//        $oProtocolo = new Protocolo_Protocolo();
//        $this->view->vProtocolo = $oProtocolo->fetchAll()->toArray();

        //ALIMENTANDO SELECT EXPEDIÇÃO
        $bd = Zend_Db_Table::getDefaultAdapter();
        $sSql = $bd->query("
                                SELECT DISTINCT
                                                clt_e.*, loc_sitio.nm_sitio

                                FROM	coleta_amostra clt_a, coleta_expedicao clt_e, loc_sitio

                                WHERE	clt_a.coleta_expedicao_id = clt_e.id
                                
                                AND  clt_e.loc_sitio_id = loc_sitio.id
                                
                                ORDER BY clt_e.data_inicio ASC
                        ");
        $this->view->vExpedicao = $sSql->fetchAll();
        //$oExpedicao = new Expedicao_Expedicao();
        //$this->view->vExpedicao = $oExpedicao->fetchAll()->toArray();
        
               //ALIMENTANDO SELECT EXPEDIÇÃO
        $bd1 = Zend_Db_Table::getDefaultAdapter();
        $sSql1 = $bd1->query("
                                Select *  from coleta_protocolo order by id asc
                        ");
        $this->view->vProtocolo = $sSql1->fetchAll();
        
        //ALIMENTANDO SELECT METODOS
        $oMetodo = new Protocolo_Metodo();
        $this->view->vMetodo = $oMetodo->fetchAll()->toArray();     
        
              
        }
        
        
        public function relatorioExcelAmostraAction() {
     
            $this->view->layout()->nmOperacao = "Gerar Relatorio";
             $this->_helper->layout->disableLayout();
             $this->view->dadosRel = $_SESSION['rel'];
              
        }
        
           public function relatorioExcelProtocoloExpedicaoAction() {
     
            $this->view->layout()->nmOperacao = "Gerar Relatorio";
             $this->_helper->layout->disableLayout();
             $this->view->dadosRel = $_SESSION['rel2'];
              
        }
        
           public function relatorioExcelAmostraProtocoloAction() {
     
            $this->view->layout()->nmOperacao = "Gerar Relatorio";
             $this->_helper->layout->disableLayout();
             $this->view->dadosRel = $_SESSION['rel3'];
              
        }
        
        
            public function relatorioExcelAtrativosAction() {
     
            $this->view->layout()->nmOperacao = "Gerar Relatorio";
             $this->_helper->layout->disableLayout();
             $this->view->dadosRel = $_SESSION['rel4'];
              
        }
        
        
              public function relatorioExcelSisbioAction() {
     
            $this->view->layout()->nmOperacao = "Gerar Relatorio";
             $this->_helper->layout->disableLayout();
             $this->view->dadosRel = $_SESSION['relsis'];
              
        }
        
    
        
        
        public function relatorioAction()
        {
            $this->view->layout()->nmOperacao = "Gerar Relatorio";
             $request = $this->_request;
             
             
//           $teste = $request->getParam("fIdProtocolo");
//           $teste1 = $request->getParam("fIdMetodos");
//           print_r($teste);
//           print_r($teste1);
//          
//           exit();
             $relatorio = new Relatorios_Relatorio();
             
             $rel =  $relatorio->getRelatorio($request->getParam("fIdProtocolo"),$request->getParam("fIdMetodos"),$request->getParam("fIdExpedicao"));
                
             $this->view->dadosRel = $rel;
             
             $_SESSION['rel'] = $this->view->dadosRel = $rel;
        }
        
        public function relatorioSisbioAction()
        {
            $this->view->layout()->nmOperacao = "Gerar Relatorio Sisbio";
             $request = $this->_request;
             
             
//           $teste = $request->getParam("fIdProtocolo");
//           $teste1 = $request->getParam("fIdMetodos");
//           print_r($teste);
//           print_r($teste1);
//          
//           exit();
             $relatorio = new Relatorios_Relatorio();
             
             $rel =  $relatorio->getRelatorioSisbio($request->getParam("fIdProtocolo"),$request->getParam("fIdMetodos"),$request->getParam("fIdExpedicao"));
                
             $this->view->dadosRel = $rel;
             
             $_SESSION['relsis'] = $this->view->dadosRel = $rel;
        }
        
        
          public function relatorioProtocoloExpedicaoAction()
        {
            $this->view->layout()->nmOperacao = "Gerar Relatorio Quantidade de Protocolos Expedicoes";
             $request = $this->_request;
             
             
//           $teste = $request->getParam("fIdProtocolo");
//           $teste1 = $request->getParam("fIdMetodos");
//           print_r($teste);
//           print_r($teste1);
//          
//           exit();
             $relatorio = new Relatorios_Relatorio();
             
             $rel =  $relatorio->getQtdProtocoloExpedicao($request->getParam("fIdProtocolo"));
                
             $this->view->dadosRel = $rel;
             
             $_SESSION['rel2'] = $this->view->dadosRel = $rel;
        }
        
        
        public function relatorioAmostraProtocoloAction()
        {
            $this->view->layout()->nmOperacao = "Gerar Relatorio Quantidade de Amostra por Protocolo";
             $request = $this->_request;
             
             
//           $teste = $request->getParam("fIdProtocolo");
//           $teste1 = $request->getParam("fIdMetodos");
//           print_r($teste);
//           print_r($teste1);
//          
//           exit();
             $relatorio = new Relatorios_Relatorio();
             
             $rel =  $relatorio->getQtdAmostraProtocolo($request->getParam("fIdProtocolo"));
                
             $this->view->dadosRel = $rel;
             
             $_SESSION['rel3'] = $this->view->dadosRel = $rel;
        }
        
        
        
         public function relatorioAtrativosAction()
        {
            $this->view->layout()->nmOperacao = "Gerar Relatorio Quantidade de Atrativos";
             $request = $this->_request;
             
             
//           $teste = $request->getParam("fIdProtocolo");
//           $teste1 = $request->getParam("fIdMetodos");
//           print_r($teste);
//           print_r($teste1);
//          
//           exit();
             $relatorio = new Relatorios_Relatorio();
             
             $rel =  $relatorio->getQtdAtrativos();
                
             $this->view->dadosRel = $rel;
             
             $_SESSION['rel4'] = $this->view->dadosRel = $rel;
        }
        
        
         public function relatorioAmostraMetodosAction()
        {
            $this->view->layout()->nmOperacao = "Gerar Relatorio Quantidade de Amostra Metodos";
             $request = $this->_request;
             
             
//           $teste = $request->getParam("fIdProtocolo");
//           $teste1 = $request->getParam("fIdMetodos");
//           print_r($teste);
//           print_r($teste1);
//          
//           exit();
             $relatorio = new Relatorios_Relatorio();
             
             $rel =  $relatorio->getQtdMetodosAmostra();
                
             $this->view->dadosRel = $rel;
             
             $_SESSION['rel5'] = $this->view->dadosRel = $rel;
        }
   
}
