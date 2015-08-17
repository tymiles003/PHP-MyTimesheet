<?php
namespace MyTimesheet;

abstract class MyTimesheetViewHelper
{
	protected $view = null;
	
	public function __construct()
	{
		$this->view = new MyTimesheetView();
		$classes = explode('\\', get_class($this));
		$this->view->setTemplate('Helpers\\' . $classes[count($classes)-1] . '\\index');
	}
	
	public function setView($view)
	{
		$this->view = $view;
		return $this;
	}
	
	public function getView()
	{
		return $this->view;
	}
}