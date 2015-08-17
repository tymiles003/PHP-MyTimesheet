<?php
namespace MyTimesheet\Model;

class Task
{
	protected $index = null;
	protected $week = null;
	protected $time = null;
	protected $name = null;
	
	public function __construct()
	{
		;
	}
	
	public function setWeek($week)
	{
		$this->week = (int) $week;
		return $this;
	}
	
	public function getWeek()
	{
		return $this->week;
	}
	
	public function setIndex($index)
	{
		$this->index = $index;
		return $this;
	}
	
	public function getIndex()
	{
		return $this->index;
	}
	
	public function setTime($time)
	{
		$this->time = $time;
		return $this;
	}
	
	public function getTime()
	{
		return strval(floatval($this->time));
	}
	
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public static function createTasksFromData($data, $week=null)
	{
		$result = array();
		foreach($data as $index=>$taskArray)
		{
			$task = new Task();
			$task->setTime($taskArray['time']);
			$task->setName($taskArray['name']);
			$task->setIndex($index);
			$task->setWeek($week);
			$result[$index] = $task;
		}
		return $result;
	}
}