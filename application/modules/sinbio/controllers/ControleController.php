<?php

class Sinbio_ControleController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
         //  error_reporting (E_ALL & ~E_NOTICE);
    	$this->view->layout()->nmModulo = "Módulo Segurança";
    	$this->view->layout()->nmController = "controle";
    	$this->view->layout()->nmPrograma = "Controle";
    }

    public function indexAction() {
    //	$this->view->layout()->nmOperacao = "Qualquer coisa";
    }
}