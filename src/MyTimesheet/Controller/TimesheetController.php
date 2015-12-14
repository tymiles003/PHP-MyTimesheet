<?php
namespace MyTimesheet\Controller;

use MyTimesheet\MyTimesheetController;
use MyTimesheet\Model\WebStorageIndex;
use MyTimesheet\Model\Timesheet;
use MyTimesheet\Model\Task;

class TimesheetController extends MyTimesheetController
{
	protected $webStorageIndex = null;
	
	public function preAction()
	{
		$this->webStorageIndex = WebStorageIndex::getInstance();
	}
	
	public function indexAction()
	{
		$id = mysql_escape_string($_GET['id']);
		if(isset($_POST['ispost']) && '1'==$_POST['ispost']){
			if(!isset($_POST['editdate'])){
				$this->edit();
			} elseif($_POST['editdate'] == '1') {
				$this->editDate();
			}
		}
		
		if($this->webStorageIndex->indexHasId($id)){
			$this->view->setParameter('file', $this->webStorageIndex->getIndexById($id));
			$this->view->setParameter('timesheet', new Timesheet($id));
			$this->view->setParameter('header', array(
					'title'=>array(1=>'Timesheet  -  ' . $this->view->timesheet->getName()),
					'description'=>$this->view->timesheet->getName() . ' - Time spent : ' . $this->view->timesheet->getTasksTime() . ' days ',
					'project-date'=>null!==$this->view->timesheet->getStartDate()?$this->view->timesheet->getStartDate():$this->view->timesheet->getWeekDate(1)
			));
		} else {
			$this->view->setTemplate('404');
		}
	}
	
	public function deletetaskAction()
	{
		if(isset($_GET['id']) && isset($_GET['week']) && isset($_GET['task'])){
			$id = mysql_escape_string($_GET['id']);
			$week = mysql_escape_string((int) $_GET['week']);
			$taskindex = mysql_escape_string((int) $_GET['task']);
			
			$timesheet = new Timesheet($id);
			if($timesheet->deleteTask($week, $taskindex)){
				$timesheet->save();
			}
			$this->redirectTo('/timesheet?id=' . $id);
		} else {
			$this->view->setTemplate('404_timesheet');
		}
	}
	
	public function downtaskAction()
	{
		if(isset($_GET['id']) && isset($_GET['week']) && isset($_GET['task'])){
			$id = mysql_escape_string($_GET['id']);
			$week = mysql_escape_string((int) $_GET['week']);
			$taskindex = (int) mysql_escape_string((int) $_GET['task']);
				
			$timesheet = new Timesheet($id);
			if($timesheet->updateTaskIndex($week, $taskindex, ++$taskindex)){
				$timesheet->save();
			}
			$this->redirectTo('/timesheet?id=' . $id);
		} else {
			$this->view->setTemplate('404_timesheet');
		}
	}
	
	public function uptaskAction()
	{
		if(isset($_GET['id']) && isset($_GET['week']) && isset($_GET['task'])){
			$id = mysql_escape_string($_GET['id']);
			$week = mysql_escape_string((int) $_GET['week']);
			$taskindex = (int) mysql_escape_string((int) $_GET['task']);
				
			$timesheet = new Timesheet($id);
			if($timesheet->updateTaskIndex($week, $taskindex, --$taskindex)){
				$timesheet->save();
			}
			$this->redirectTo('/timesheet?id=' . $id);
		} else {
			$this->view->setTemplate('404_timesheet');
		}
	}
	
	public function editDate()
	{
		if(!isset($_POST['date']) || empty($_POST['date'])){
			return false;
		}
		
		$id = mysql_escape_string($_GET['id']);
		$weekindex = null;
		if(isset($_GET['week'])){
			$weekindex = mysql_escape_string((int) $_GET['week']);
		}
		
		if(null !== $weekindex && $this->webStorageIndex->indexHasId($id)){
			$timesheet = new Timesheet($id);
			$timesheet->setWeekDate($weekindex, date_create_from_format('Y-m-d', $_POST['date'])->getTimestamp());
			$timesheet->save();
		} else {
			$this->view->setTemplate('404_timesheet');
		}
	}
	
	public function edit()
	{
		$id = mysql_escape_string($_POST['id']);
		$taskindex = $weekindex = null;
		if(isset($_GET['task'])){
			$taskindex = mysql_escape_string($_GET['task']);
			if(is_numeric($taskindex) && isset($_GET['week'])){
				$weekindex = mysql_escape_string((int) $_GET['week']);
			}
		}
		
		$task = null;
		if($this->webStorageIndex->indexHasId($id)){
			$timesheet = new Timesheet($id);
			
			// Edit mode :
			if(null !== $weekindex && null !== $taskindex && $timesheet->hasTask($weekindex, $taskindex)){
				
				$task = $timesheet->getTask($weekindex, $taskindex);
				$task->setTime($_POST['time']);
				$task->setName($_POST['name']);
				
				if((int) $_POST['week'] != (int) $weekindex){
					$task->setWeek((int) $_POST['week']);
					$task = clone $task;
					$timesheet->deleteTask($weekindex, $taskindex);
					$timesheet->addTask($task);
				}
			
			// Create mode :
			} else {
				$task = new Task();
				$task->setTime($_POST['time']);
				$task->setName($_POST['name']);
				$task->setWeek((int) $_POST['week']);
				$timesheet->addTask($task);
			}
			
			// Save timesheet :
			$timesheet->save();
			
			//$this->view->setParameter('file', $file);
			//$this->view->setParameter('timesheet', $timesheet);
		} else {
			$this->view->setTemplate('404_timesheet');
		}
	}
	
	public function exportAction()
	{
		$id = mysql_escape_string($_GET['id']);
		$week = !empty($_GET['week'])?(int) $_GET['week']:1;
		
		if($this->webStorageIndex->indexHasId($id)){
			
			$type = !empty($_GET['type'])?$_GET['type']:'html';
			$this->view->setParameter('timesheet', new Timesheet($id));
			
			switch($type){
				default:
				case 'html':
					$this->view->header = array('title' => array(1=>$this->view->timesheet->getName()), 'description'=>'Timesheet pour la semaine (' . $week . ')',
											    'week-date' => $this->view->timesheet->getWeekDate($week));
					$this->view->setParameter('weekindex', $week);
					break;
				case 'csv':
					
					header('Content-Encoding: UTF-8');
					header('Content-type: text/csv; charset=UTF-8');
					header("Content-type: text/csv");
					header("Content-Disposition: attachment; filename=export_" . $id . "_week" . $week . ".csv");
					header("Pragma: no-cache");
					header("Expires: 0");
					
					echo "\xEF\xBB\xBF"; // UTF-8 BOM
					echo $this->view->timesheet->getName() . ' Timesheet pour la semaine (' . $week . ')' . "\n";
					
					$timesheet = new Timesheet($id);
					foreach($timesheet->getTasksWeek($week) as $task){
						echo  $task->getTime() . ';' . $task->getName() . "\n";
					}
					exit();
				break;
			}
		} else {
			$this->view->setTemplate('404_timesheet');
		}
	}
}