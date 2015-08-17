<?php
namespace MyTimesheet;

use MyTimesheet\MyTimesheetView;

class MyTimesheetDispatcher
{
	protected $url = array();
	protected $controller = null;
	protected $currentRequest = array('module'=> 'MyTimesheet', 'controller'=>null, 'action'=>null);
	
	public function preDispatch()
	{
		$GLOBALS['myTimesheet']['dispatcher']['current'] = $this;
		
		if(false !== ($getParameters=strpos($_SERVER['REQUEST_URI'], '?')) ){
			$this->url = explode('/', substr($_SERVER['REQUEST_URI'], 0, $getParameters) );
		} else {
			$this->url = explode('/', $_SERVER['REQUEST_URI']);
		}
		
		// Delete hostname :
		array_shift($this->url);
	}
	
	public function dispatch()
	{
		$this->currentRequest['controller'] = ucfirst(strtolower(!empty($this->url[0])?$this->url[0]:'index'));
		$controllerclass = 'MyTimesheet\Controller\\' . $this->currentRequest['controller']  . 'Controller';
		$this->controller = new $controllerclass();
		$this->controller->view = new MyTimesheetView();
		$this->controller->view->setTemplate( ucfirst(strtolower(!empty($this->url[0])?$this->url[0]:'index')) . '/' . (!empty($this->url[1])?$this->url[1]:'index') );
		$this->controller->view->setLayout('default');
		
		$this->currentRequest['action'] = ucfirst(strtolower((!empty($this->url[1])?$this->url[1]:'index')));
		$this->controller->preAction();
		$this->controller->{$this->currentRequest['action']  . 'Action'}();
		$this->controller->postAction();
		
	}
	
	public function postDispatch()
	{
		$this->controller->view->render();
	}
	
	public function getCurrentRequest()
	{
		return $this->currentRequest;
	}
	
	public function getCurrentController()
	{
		return $this->currentRequest['controller'];
	}
	
	public function getCurrentAction()
	{
		return $this->currentRequest['action'];
	}
}