<?php 
namespace MyTimesheet\Model;

use MyTimesheet\Model\WebStorageIndex;
use MyTimesheet\Model\Task;
use MyTimesheet\MyTimesheetException;
use Symfony\Component\Yaml\Yaml;

class Timesheet
{
	protected $id = null;
	protected $info = null;
	protected $tasksByWeeks = array();
	protected $webStorageIndex = null;
	
	public function __construct($id) 
	{
		$this->webStorageIndex = WebStorageIndex::getInstance();
		if($this->webStorageIndex->indexHasId($id)){
			$this->info = $this->webStorageIndex->getIndexById($id);
			$this->id = $id;
			$this->createTasksTimesheetFromData(Yaml::parse(file_get_contents($this->info['absolutePath'])));
		} else {
			throw new MyTimesheetException('Timesheet with id "' . $id . '" does not exists in index.');
		}
	}
	
	public function getName()
	{
		return $this->info['name'];
	}
	
	public function hasTask($weekIndex, $taskIndex)
	{
		return !empty($this->tasksByWeeks[$weekIndex][$taskIndex]);
	}
	
	public function getTasks()
	{
		return $this->tasksByWeeks;
	}
	
	public function getTasksWeek($weekIndex)
	{
		return $this->tasksByWeeks[$weekIndex];
	}
	
	/**
	 * @param int $weekIndex
	 * @param int $taskIndex
	 * @return \MyTimesheet\Model\Task
	 */
	public function getTask($weekIndex, $taskIndex)
	{
		return $this->tasksByWeeks[$weekIndex][$taskIndex];
	}
	
	public function getTasksTimeByWeek($week)
	{
		$result = 0;
		foreach($this->getTasksWeek((int) $week) as $task){
			$result += $task->getTime();
		}
		return $result;
	}
	
	public function getTasksTime()
	{
		$tasks = $this->getTasks();
		array_walk($tasks, function(&$tasks){ 
				array_walk($tasks, function(&$task){$task = $task->getTime(); }); 
					$tasks = array_sum($tasks); });
		
		return array_sum($tasks);
	}
	
	public function addTask(Task $task)
	{
		if(isset($this->tasksByWeeks[$task->getWeek()])){
			$task->setIndex(count($this->tasksByWeeks[$task->getWeek()]));
		} else{
			$task->setIndex(0);
		}
		
		$this->tasksByWeeks[$task->getWeek()][] = $task;
	}
	
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setInfo($infoArray)
	{
		$this->info = $infoArray;
		return $this;
	}
	
	public function getInfo()
	{
		return $this->info;
	}
	
	public function createTasksWeekFromData($weekIndex, $data)
	{
		$this->tasksByWeeks[$weekIndex] = Task::createTasksFromData($data, $weekIndex);
		ksort($this->tasksByWeeks);
		return $this;
	}
	
	public function createTasksTimesheetFromData($dataFile)
	{
		foreach($dataFile[$this->getId()] as $weekName=>$dataWeek) {
			$this->createTasksWeekFromData((int) explode('-',$weekName)[1], $dataWeek);
		}
		return $this;
	}
	
	public function save()
	{
		$result = array($this->getId()=>array());
		$data = &$result[$this->getId()];
		foreach($this->tasksByWeeks as $weekIndex=>$tasks){
			$data['week-' . $weekIndex] = array();
			$week = &$data['week-' . $weekIndex];
			foreach($tasks as $task){
				$week[(int) $task->getIndex()] = array(
					'name' => $task->getName(),
					'time' => $task->getTime()
				);
			}
		}
		
		return file_put_contents($this->info['absolutePath'], Yaml::dump($result));
	}
	
	public function delete()
	{
		$this->webStorageIndex->deleteIndexById($this->getId());
		$this->webStorageIndex->save();
		unlink($this->info['absolutePath']);
		return true;
	}
	
	public function deleteTask($weekIndex, $taskIndex)
	{
		if($this->hasTask($weekIndex, $taskIndex)){
			unset($this->tasksByWeeks[$weekIndex][$taskIndex]);
			if(empty($this->tasksByWeeks[$weekIndex])){
				unset($this->tasksByWeeks[$weekIndex]);
			}
			return true;
		} else {
			return false;
		}
	}
	
	public function updateTaskIndex($weekIndex, $taskIndex, $newTaskIndex)
	{
		if($this->hasTask($weekIndex, $taskIndex) && $this->hasTask($weekIndex, $newTaskIndex)){

			$task0 = $this->getTask($weekIndex, $taskIndex);
			$task0->setIndex($newTaskIndex);
			
			$task1 = $this->getTask($weekIndex, $newTaskIndex);
			$task1->setIndex($taskIndex);
			
			$this->tasksByWeeks[$weekIndex][$taskIndex] = $task1;
			$this->tasksByWeeks[$weekIndex][$newTaskIndex] = $task0;
			
			return true;
			
		} else {
			return false;
		}
	}
}

