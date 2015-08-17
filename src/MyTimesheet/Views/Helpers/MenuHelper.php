<?php 
namespace MyTimesheet\Views\Helpers;

use MyTimesheet\Model\WebStorageIndex;
use MyTimesheet\MyTimesheetViewHelper;

class MenuHelper extends MyTimesheetViewHelper
{
	public function __construct()
	{
		parent::__construct();
		$dispatcher = $GLOBALS['myTimesheet']['dispatcher']['current'];
		$menuId = $dispatcher->getCurrentController() . '_' . $dispatcher->getCurrentAction();
		$this->view->setParameter('active_item', $menuId);
	}
	
	public function render()
	{
		$this->view->render();
		return $this;
	}
}