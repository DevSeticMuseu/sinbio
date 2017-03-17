<?php

class Sinbio_TaxonomiaController extends Zend_Controller_Action
{

	public function init() {
		/* Initialize action controller here */
		 // error_reporting (E_ALL & ~E_NOTICE);
		$this->view->layout()->nmModulo = "Módulo Taxonomia";
		$this->view->layout()->nmController = "taxonomia";
		$this->view->layout()->nmPrograma = "listar";
		
		
	}

	public function indexAction() {
            $this->view->layout()->includeCss = '
				<link href="/plugin/dist/themes/default/style.css" rel="stylesheet" type="text/css"/>
                                <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

		';

            $this->view->layout()->includeJs = '
                    <script src="/plugin/dist/libs/jquery.js"></script>
                    <script src="/plugin/dist/jstree.min.js"></script>
                    <script type="text/javascript">
                        $(function () { $("#jstree_test").jstree({
                                "types" : {
                                    "default" : {
                                        "icon" : "fa fa-dot-circle-o"
                                    },
                                    "#" : {
                                        "icon" : "fa fa-circle-thin",
                                        "valid_children" : ["root"]
                                    },
                                    "root" : {
                                        "icon" : "fa fa-bullseye"
                                    }
                                },
                                "plugins" : [ "types" ]
                            }); 
                        });
                        
                    </script>
            ';

            $nivelTaxonomico = new Taxonomia_NivelTaxonomico();
            $taxonomia = new Taxonomia_Taxonomia();
            
            $rs = $nivelTaxonomico->fetchAll(null, "id ASC");
            $this->view->nivelTaxonomico = $rs;
            
            $rs = $taxonomia->fetchAll();
            $this->view->taxon = $rs;
	}

	

    
   
}
