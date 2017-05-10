<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	/**
	 * Init Autoloader
	 */
	
	protected function _initDoctype() {
		$this->bootstrap ('view');
		$view = $this->getResource ('view');
		$view->doctype ('XHTML1_STRICT');
	}
	
	protected function _initAutoLoader() {
		$autoloader = Zend_Loader_Autoloader::getInstance ();
		$autoloader->registerNamespace ('SON');
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->setFallbackAutoloader(true);
	}
	
	protected function _initPlugins() {
		$this->bootstrap('db');
                $db = $this->getResource('db');
		
		$bootstrap = $this->getApplication ();
		if ($bootstrap instanceof Zend_Application) {
			$bootstrap = $this;
		}
		$bootstrap->bootstrap ('FrontController');
		$front = $bootstrap->getResource ('FrontController');
		
		$pluginLayout = new SON_Layout();
		$front->registerPlugin ($pluginLayout);
		
		$pluginControle = new CONTROLE_Controle($db);
		$front->registerPlugin ($pluginControle);
		
		$pluginMenu = new CONTROLE_Menu();
		$front->registerPlugin ($pluginMenu);
	}
}