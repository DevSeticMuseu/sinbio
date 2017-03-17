<?php

class Sinbio_ImportacaoController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		   error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Importação";
		$this->view->layout()->nmController = "importacao";
		$this->view->layout()->nmPrograma = "Importação";


		
	}

	public function indexAction() {
	
                $oProtocolo = new Protocolo_Protocolo();
                $oMetodo = new Protocolo_Metodo();
                $oExpedicao = new Expedicao_Expedicao();
                $oDestinacao = new Destinacao_Destinacao();
                $oConservacao = new Conservacao_Conservacao();
                $oProjecao = new Projecao_Projecao();
                $oAtrativo = new Atrativo_Atrativo();
                $oUsuario = new Seg_Usuario();
                
                $this->view->vUsuario = $oUsuario->fetchAll(null,"nm_usuario")->toArray();
                $this->view->vProtocolo = $oProtocolo->fetchAll(null,"sigla")->toArray();
                $this->view->vMetodo = $oMetodo->fetchAll(null,"nm_metodo")->toArray();
                $this->view->vExpedicao = $oExpedicao->fetchAll(null,"data_inicio")->toArray();
                $this->view->vDestinacao = $oDestinacao->fetchAll()->toArray();
                $this->view->vConservacao = $oConservacao->fetchAll()->toArray();
                $this->view->vAtrativo = $oAtrativo->fetchAll()->toArray();
                $this->view->vProjecao = $oProjecao->fetchAll()->toArray();
                $this->view->layout()->nmOperacao = "Listar";

                $this->view->layout()->includeCss = '
                                        <link href="/plugin/jquery-ui/css/ui-lightness/jquery-ui-1.10.2.custom.min.css" rel="stylesheet" type="text/css"/>
                        ';
        
                 $this->view->layout()->includeJs =	'
                                <script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
                                <script src="/js/sinbio/validacao.js" type="text/javascript"></script>
                                <script src="/plugin/jquery-ui/js/jquery-ui-1.10.2.custom.min.js"></script>
                                <script src="/js/geral/jquery.multiselect.min.js"></script>
                                <script type="text/javascript">
                                $(function(){
                                        $("#fIdColetores").multiselect({
                                                noneSelectedText: \'Por favor selecione\',
                                                checkAllText: \'Marcar Todos\',
                                                uncheckAllText: \'Desmarcar Todos\',
                                                selectedText: \'# Selecionado(s)\',
                                                minWidth: 270,
                                                height: 200
                                        });
                                        $("#fIdDeterminadores").multiselect({
                                                noneSelectedText: \'Por favor selecione\',
                                                checkAllText: \'Marcar Todos\',
                                                uncheckAllText: \'Desmarcar Todos\',
                                                selectedText: \'# Selecionado(s)\',
                                                minWidth: 270,
                                                height: 200
                                        });
                                });
                                </script>
                        ';
	
	}
     
	public function visualizarAction() {
		
		$this->view->layout()->nmPrograma = "Importacao";
                $this->view->layout()->nmOperacao = "Cadastrar";

                $this->view->layout()->includeJs = '
                                <script src="/js/geral/jquery.validate.js" type="text/javascript"></script>
                                <script src="/js/sinbio/validacao.js" type="text/javascript"></script>
                                <script src="/plugin/flexigrid/js/flexigrid.pack.js"></script>
                                <script src="/js/sinbio/coleta-importacao.js"></script>
                        ';

                $this->view->layout()->includeCss = '
                                <link href="/plugin/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css"/>
                                ';

                        $request = $this->_request;

                        if ($request->getParam("sOP") == "cadastrar") {
                                try{
                                        /* variable */
                                        $fIdProtocolo       = $request->getParam("fIdProtocolo");
                                        $fIdMetodo          = $request->getParam("fIdMetodo");
                                        $fIdAtrativo        = $request->getParam("fIdAtrativo");
                                        $fIdExpedicao       = $request->getParam("fIdExpedicao");
                                        $fIdProjecao        = $request->getParam("fIdProjecao");
                                        $fIdColetores       = $request->getParam("fIdColetores");
                                        $fIdDeterminadores  = $request->getParam("fIdDeterminadores");
                                        $this->view->fDataDeterminacao  = $request->getParam("fDataDeterminacao");
                                        $this->view->fReferencia        = $request->getParam("fReferencia");
                                        $this->view->fObservacao        = $request->getParam("fObservacao");
                                        $fIdConservacao     = $request->getParam("fIdConservacao");
                                        $fIdDestinacao      = $request->getParam("fIdDestinacao");

                                        /* Models */
                                        $oProtocolo = new Protocolo_Protocolo();
                                        $oMetodo = new Protocolo_Metodo();
                                        $oExpedicao = new Expedicao_Expedicao();
                                        $oDestinacao = new Destinacao_Destinacao();
                                        $oConservacao = new Conservacao_Conservacao();
                                        $oProjecao = new Projecao_Projecao();
                                        $oAtrativo = new Atrativo_Atrativo();
                                        $oUsuario = new Seg_Usuario();

                                        $vProtocolo = $oProtocolo->find($fIdProtocolo);
                                        $vMetodo = $oMetodo->find($fIdMetodo);
                                        $vExpedicao = $oExpedicao->find($fIdExpedicao);
                                        $vDestinacao = $oDestinacao->find($fIdDestinacao);
                                        $vConservacao = $oConservacao->find($fIdConservacao);
                                        $vProjecao = $oProjecao->find($fIdProjecao);
                                        $vAtrativo = $oAtrativo->find($fIdAtrativo);
                                        $vColetores = $oUsuario->find($fIdColetores);
                                        $vDeterminadores = $oUsuario->find($fIdDeterminadores);

                                        $nmProtocolo = $vProtocolo[0]['nm_protocolo'];
                                        $nmMetodo = $vMetodo[0]['nm_metodo'];
                                        $dtExpedicao = $vExpedicao[0]['data_inicio']." a ".$vExpedicao[0]['data_fim'];
                                        $nmDestinacao = $vDestinacao[0]['nm_destinacao'];
                                        $nmConservacao = $vConservacao[0]['conservacao_material'];
                                        $nmProjecao = $vProjecao[0]['sistema_projecao'];
                                        $nmAtrativo = $vAtrativo[0]['nm_atrativos'];


                                        $this->view->vColetores = $vColetores;
                                        $this->view->vDeterminadores = $vDeterminadores;
                                        $this->view->nmProtocolo = $nmProtocolo;
                                        $this->view->nmMetodo = $nmMetodo;
                                        $this->view->dtExpedicao = $dtExpedicao;
                                        $this->view->nmDestinacao = $nmDestinacao;
                                        $this->view->nmConservacao = $nmConservacao;
                                        $this->view->nmProjecao = $nmProjecao;
                                        $this->view->nmAtrativo = $nmAtrativo;

                                        /* path to worksheets */
                                        $path = '/var/www/sinbio/planilhas/';
                                        $adapter = new Zend_File_Transfer_Adapter_Http();
                                        $adapter->setDestination($path);

                                        /* Send file and confirm if was send*/
                                        if(!$adapter->receive()){
                                                $messages = $adapter->getMessages();
                                                echo implode("\n", $messages);
                                                exit();
                                        }

                                        /* Start get file */
                                        $objPhpExcel = PHPExcel_IOFactory::load($adapter->getFileName());
                                        $objWorksheet = $objPhpExcel->getActiveSheet();
                                        $highestRow = $objWorksheet->getHighestRow();
                                        $highestColumn = $objWorksheet->getHighestColumn();
                                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                                        $arrayWorksheet = array();
                                        $name = array('id_amostra','data_coleta','hora_coleta','local','latitude','direcao_latitude','longitude','direcao_longitude','trilha','distancia_trilha','parcela','distancia_parcela','metodo_coleta','altura_armadilha','densidade','abertura_dossel','temperatura_ar','umidade_ar','tipo_substrato','ordem','familia','subfamilia','genero','subgenero','grupo_especie','especie','autor','morfoespecie','individuos','femeas','machos','total');
                                        for($row=1; $row<=$highestRow; ++$row){
                                                if($row==1)
                                                        continue;
                                                for($col=0; $col<=$highestColumnIndex; ++$col){
                                                        $arrayWorksheet[$row][$name[$col]] = $objWorksheet->getCellByColumnAndRow($col, $row)->getFormattedValue();
                                                }
                                        }
                                        $this->view->worksheet = $arrayWorksheet;
                                }
                                catch(Zend_Exception $e){
                                        $error = $e->getMessage();
                                        echo "Error: $error";
                                        exit();
                                }
                        }
	}
	
	public function verificaPermissaoAction() {
        $sQP = $this->_request->getParam("sOP");
        $this->view->layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $vUsuarioLogado = $auth->getIdentity();
        $oVerifica = new VerificaPermissao("importacao", $sQP, $vUsuarioLogado["id"]);
        if ($oVerifica->bResultado) {
            $this->view->bPermissao = $oVerifica->bResultado;
        }
    }
	
}
