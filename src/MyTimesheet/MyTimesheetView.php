<?php
namespace MyTimesheet;

class MyTimesheetView
{
	protected $data = array();
	protected $templateName = null;
	protected $templateRendered = false;
	protected $layoutName = null;
	protected $layoutRendered = false;
	protected $helpers = array();
	protected $placeholder = array();
	
	public function __construct()
	{
		;
	}
	
	public function render($templateName = null)
	{
		if(null!=$templateName ) {
			$templateName = $templateName . '.phtml';
		} elseif($this->hasLayout() && !$this->layoutIsRendered()) { // Layout first
			$this->setLayoutRendered(true);
			$templateName = $this->getLayout();
		} elseif($this->hasTemplate() && !$this->templateIsRendered()) { // Template second
			$this->setTemplateRendered(true);
			$templateName = $this->getTemplate();
		} else {
			return null;
		}
		
		include(ROOT_PATH . DIRECTORY_SEPARATOR . 'src'. DIRECTORY_SEPARATOR . 'MyTimesheet'. DIRECTORY_SEPARATOR . 'Views'. DIRECTORY_SEPARATOR . $templateName);
		
		return $this;
	}
	
	public function setPlaceholder($key, $templateName, $html=null)
	{
		$this->placeholder[$key] = array('templatename'=>$templateName, 'html'=>$html);
		return $this;
	}
	
	public function getPlaceholder($key)
	{
		if(!empty($this->placeholder[$key])){
			if(!empty($this->placeholder[$key]['html'])){
				return $this->placeholder[$key]['html'];
			} else {
				$this->render($this->placeholder[$key]['templatename']);
			}
		}
		return '';
	}
	
	public function partialRender($templateName)
	{
		ob_start();
		include(__DIR__ . '/Views/' . $templateName . '.phtml');
		return ob_get_clean();
	}
	
	public function setLayout($layoutName)
	{
		$this->layoutName = $layoutName;
		return $this;
	}
	
	public function setLayoutRendered($rendered = true)
	{
		$this->layoutRendered = $rendered;
		return $this;
	}
	
	public function getLayout()
	{
		return 'Layouts' . '\\' . $this->layoutName . '.phtml';
	}
	
	public function hasLayout()
	{
		return null!=$this->layoutName;
	}
	
	public function layoutIsRendered()
	{
		return $this->layoutRendered;
	}
	
	public function setTemplate($templateName)
	{
		$this->templateName = $templateName;
		return $this;
	}
	
	public function setTemplateRendered($rendered = false)
	{
		$this->templateRendered = $rendered;
		return $this;
	}
	
	public function getTemplate($extension=true)
	{
		return $this->templateName . ($extension?'.phtml':'');
	}
	
	public function hasTemplate()
	{
		return null!=$this->templateName;
	}
	
	public function templateIsRendered()
	{
		return $this->templateRendered;
	}
	
	public function setParameter($key,$value)
	{
		$this->data[$key] = $value;
		return $this;
	}
	
	public function getParameter($key)
	{
		return $this->data[$key];
	}
	
	public function getHelper($helpername)
	{
		if(empty($this->helpers[$helpername])){
			$classname = 'MyTimesheet\Views\Helpers\\' . $helpername . 'Helper';
			$this->helpers[$helpername] = new $classname();
		}
		return $this->helpers[$helpername];
	}
	
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
		return $this;
	}
	
	public function __get($key)
	{
		if(isset($this->data[$key])){
			return $this->data[$key];
		} else {
			return null;
		}
	}
}

