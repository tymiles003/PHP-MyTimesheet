<?php
namespace MyTimesheet;

use MyTimesheet\MyTimesheetView;

abstract class MyTimesheetController
{
	/**
	 * @var MyTimesheet\MyTimesheetView
	 */
	public $view = null;
	
	public function preAction()
	{
		;
	}
	
	public function postAction()
	{
		;
	}
	
	public function redirectTo($uri, $code = 3)
	{
		header('Location: '. $uri, true, 300 + (int) $code);
		exit();
	}
}