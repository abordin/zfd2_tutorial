<?php

class IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			return $this->_helper->redirector->gotoSimple('index', 'dashboard');
		}
	}
}