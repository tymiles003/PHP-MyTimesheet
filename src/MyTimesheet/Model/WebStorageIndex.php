<?php
namespace MyTimesheet\Model;

class WebStorageIndex
{
	protected $indexByFilename = array();
	protected $indexById = array();
	const INDEX_FILEPATH = ROOT_PATH . '/web/storage/index_storage.raw';
	
	private static $instance = null;
	
	private function __construct()
	{
		$indexNames = file_get_contents(self::INDEX_FILEPATH);
		$indexNames = explode("\n", $indexNames);
		foreach($indexNames as &$indexName){
			$indexName = explode('|',$indexName);
			$index = array(
				'absolutePath' => ROOT_PATH . 'web/storage/' . $indexName[0],
				'filename' => $indexName[0],
				'id' => $indexName[1],
				'name' => $indexName[2],
			);
			$index['file_exists'] = file_exists($index['absolutePath']);
			
			$this->indexByFilename[$index['filename']] = $index;
			$this->indexById[$index['id']] = $index;
		}
	}
	
	/**
	 * @return \MyTimesheet\Model\WebStorageIndex
	 */
	public static function getInstance()
	{
		if(null==self::$instance){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function getIndexByFilename($filename=null)
	{
		if(!empty($filename)){
			return $this->indexByFilename[$filename];
		} else {
			return $this->indexByFilename;
		}
	}
	
	public function getIndexById($id=null)
	{
		if(null !== $id){
			return $this->indexById[$id];
		} else {
			return $this->indexById;
		}
	}
	
	public function getNameByFilename($timesheetFilename)
	{
		return $this->indexByFilename[$timesheetFilename]['name'];
	}
	
	public function getIdByFilename($timesheetFilename)
	{
		return $this->indexByFilename[$timesheetFilename]['id'];
	}
	
	public function indexHasId($id)
	{
		return isset($this->indexById[$id]);
	}
	
	public function indexHasFilename($timesheetFilename)
	{
		return isset($this->indexByFilename[$timesheetFilename]);
	}
	
	public function addIndex($id, $projectname, $filename)
	{
		if( (strlen($id)>3 || strlen($filename)>3 || strlen($projectname)>3) &&
		    !$this->indexHasId($id) && !$this->indexHasFilename($filename) ) {
			$this->indexByFilename[$filename] = array(
				'absolutePath' => ROOT_PATH . 'web/storage/' . $filename . '.yaml',
				'filename' => $filename . '.yaml',
				'id' => $id,
				'name' => $projectname,
			);
			
			$this->indexById[$id] = $this->indexByFilename[$filename];
			return true;
		} else {
			return false;
		}
	}
	
	public function setIndexProjectname($id, $projectname)
	{
		$this->indexById[$id]['name'] = $projectname;
		$this->indexByFilename[$this->indexById[$id]['filename']]['name'] = $projectname;
		return $this;
	}
	
	public function deleteIndexById($id)
	{
		if($this->indexHasId($id)){
			foreach($this->indexByFilename as $filename=>$item){
				if($item['id'] == $id){
					unset($this->indexByFilename[$filename]);
					unset($this->indexById[$id]);
					break;
				}
			}
			return true;
		} else {
			return false;
		}
	}
	
	public function save()
	{
		$result = '';
		foreach($this->indexByFilename as $item){
			$result .= (''!=$result?"\n":'') . $item['filename'] . '|' . $item['id'] . '|' . $item['name'];
		}
		
		return file_put_contents(self::INDEX_FILEPATH, $result)?true:false;
	}
}