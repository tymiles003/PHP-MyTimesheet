<?php 
namespace MyTimesheet\Views\Helpers;

use MyTimesheet\Model\WebStorageIndex;
use MyTimesheet\MyTimesheetViewHelper;

class ActionsHelper extends MyTimesheetViewHelper
{
	protected $actions = array();
	protected $actionsParameters = array();
	
	public function addAction($key)
	{
		$this->actions[] = $key;
		return $this;
	}
	
	public function setActionParameters($key, $parameters)
	{
		$this->actionsParameters[$key] = $parameters;
		return $this;
	}
	
	public function getActionParameters($key)
	{
		return $this->actionsParameters[$key];
	}
	
	public function getActions()
	{
		return $this->actions;
	}
	
	public function render()
	{
		$this->view->actions = $this->actions;
		$this->view->parameters = $this->actionsParameters;
		return $this->view->partialRender($this->view->getTemplate(false));
	}
}

