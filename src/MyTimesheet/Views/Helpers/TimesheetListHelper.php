<?php 
namespace MyTimesheet\Views\Helpers;

use MyTimesheet\Model\WebStorageIndex;
use MyTimesheet\MyTimesheetViewHelper;

class TimesheetListHelper extends MyTimesheetViewHelper
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
	
	public function render()
	{
		$files = $this->webStorageIndex->getIndexByFilename();
		$this->view->setParameter('files', $files);
		$this->view->render();
		return $this;
	}
}
