<?php
class CONTROLE_Menu extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$auth = Zend_Auth::getInstance();
			
		if ($auth->hasIdentity()) {
			$layout = Zend_Layout::getMvcInstance();
			$view = $layout->getView();
			
			$oMenu = new Menu($request->getControllerName());
			$view->vMenu = $oMenu->vMenu;
		}
	}
}