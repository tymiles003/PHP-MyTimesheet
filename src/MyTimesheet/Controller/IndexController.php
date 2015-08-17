<?php
namespace MyTimesheet\Controller;

use MyTimesheet\MyTimesheetController;
use MyTimesheet\Model\WebStorageIndex;
use MyTimesheet\Model\Timesheet;

class IndexController extends MyTimesheetController
{
	/**
	 * @var WebStorageIndex
	 */
	protected $webStorageIndex = null;
	
	public function preAction()
	{
		$this->webStorageIndex = WebStorageIndex::getInstance();
	}
	
	public function indexAction()
	{
		if(!empty($_POST['ispost']) && 'true'==$_POST['ispost']){
			$this->edit();
		}
		
		if(!empty($_GET['id']) && $this->webStorageIndex->indexHasId($_GET['id'])){
			$this->view->setParameter('timesheet', $this->webStorageIndex->getIndexById($_GET['indexId'])[$_GET['id']]);
		}
		
		$this->view->setParameter('header', array(
			'title'=>array(1=>'Timesheets'),
			'description'=>'List of timesheets'
		));
	}
	
	public function edit()
	{
		if(!empty($_POST['filename']) && !empty($_POST['projectname'])){
			$filename = mysql_escape_string($_POST['filename']);
			$projectname = mysql_escape_string($_POST['projectname']);
			
			$transliterator = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();');
			$id = str_replace(' ', '_', $transliterator->transliterate($projectname));
			$filename = str_replace(' ', '_', $transliterator->transliterate($projectname));
			
			if(!empty($_GET['id']) && $this->webStorageIndex->indexHasId($_GET['id'])){
				$this->webStorageIndex->setIndexProjectname($_GET['id'], $projectname);
				$this->webStorageIndex->save();
				
			} elseif(!$this->webStorageIndex->addIndex($id, $projectname, $filename)){
				$this->view->setParameter('timesheet', array(
					'projectname' => $projectname,
					'filename' => $filename,
					'error' => (strlen($id)>3 || strlen($filename)>3 || strlen($projectname)>3 ?'Min length is not right':'This project or filename already exists')
				));
				
			} else {
				$this->webStorageIndex->save();
			}
		}
	}
	
	public function deleteAction()
	{
		$id = empty($_GET['id'])?'':mysql_escape_string($_GET['id']);
		
		if($this->webStorageIndex->indexHasId($id)){
			$this->view->setParameter('file', $this->webStorageIndex->getIndexById($id));
			$timesheet = new Timesheet($id);
			
			if(isset($_POST['ispost']) && '1'==$_POST['ispost'] &&
					isset($_POST['confirm_delete']) && '1'==$_POST['confirm_delete'] ){
				$timesheet->delete();
				$this->redirectTo('/');
			} else {
				$this->view->setParameter('timesheet', $timesheet);
			}
		} else {
			$this->view->setTemplate('404_timesheet');
		}
	}
}