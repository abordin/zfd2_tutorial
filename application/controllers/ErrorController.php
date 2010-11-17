<?php

use Forms\Login;

class ErrorController extends Zend_Controller_Action
{
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');
		
		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->message = $this->view->translate('Page not found');
				break;
			default:
				{
					switch (get_class($errors->exception)) {
						case 'My\Exception\PermissionDeniedException':
							if (!\Zend_Auth::getInstance()->hasIdentity()) {
								$this->_request->setParam('f', true);
								
								$this->view->form = new \Forms\Login();
								$this->view->form->submit();
							}
							
							$this->getResponse()->setHttpResponseCode(403);
							$this->view->message = $this->view->translate('Permission denied');
							break;
						default:
							$this->getResponse()->setHttpResponseCode(500);
							$this->view->message = $this->view->translate('Application error');
					}
					break;
				}
		}
		
		// Log exception, if logger available
		if ($log = $this->getLog()) {
			$log->crit($this->view->message, $errors->exception);
		}
		
		// conditionally display exceptions
		if ($this->getInvokeArg('displayExceptions') == true) {
			$this->view->exception = $errors->exception;
		}
		
		$this->view->request = $errors->request;
	}
	
	public function permissionAction()
	{
		$auth = Zend_Auth::getInstance();
		
		if (!$auth->hasIdentity()) {
			$this->_request->setParam('f', true);
			
			$userService = new \Services\User();
			$form = new Login();
			$form->submit();
		}
		
		$this->view->request = $this->_request;
	}
	
	public function getLog()
	{
		$bootstrap = $this->getInvokeArg('bootstrap');
		if (!$bootstrap || !$bootstrap->hasPluginResource('Log')) {
			return false;
		}
		$log = $bootstrap->getResource('Log');
		return $log;
	}
}