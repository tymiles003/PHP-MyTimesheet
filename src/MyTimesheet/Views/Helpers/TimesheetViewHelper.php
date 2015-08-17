<?php 
namespace MyTimesheet\Views\Helpers;

use MyTimesheet\Model\WebStorageIndex;
use MyTimesheet\MyTimesheetViewHelper;
use MyTimesheet\Model\Timesheet;

class TimesheetViewHelper extends MyTimesheetViewHelper
{
	/**
	 * @var \MyTimesheet\Model\WebStorageIndex
	 */
	protected $webStorageIndex = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->webStorageIndex = WebStorageIndex::getInstance();
	}
	
	public function render($fileId)
	{
		$this->view->setParameter('timesheet', new Timesheet($fileId));
		$this->view->render();
		return $this;
	}
}
