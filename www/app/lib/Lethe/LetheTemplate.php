<?php

/**
 * Template object
 */
class LetheTemplate implements \ArrayAccess{
  
  private $templateFile;
  private $fetchOutput_var = array();
  private $fetchOutput_replace = array();
  
  public $templateVariables = array();
  
  /**
   * @param string Path to the template file to render. Optional, use setTplFile($templateFile) or provide file path at render time.
   */ 
  public function __construct($templateFile = null,$vars = array()){
    $this->setTplFile($templateFile);
    $this->assignVars($vars);    
  }
  
  /**
   * throw Exception if anyone tries to set undefined property (as in Nette\Object, but no need to implement whole class)
   */
  public function __set($var,$value){
    throw new \Exception("Cannot set undefined property $var. For assigning template variables use ArrayAccess or method assign((string \$var,mixed \$value).");
  }
  
  /**
   * Set template variable
   * @param string Variable name
   * @param mixed Variable value
   * @return TemplateObject   
   */      
  public function assign($var,$value = null){
    if(is_array($var)){return $this->assignVars($var);}
    $this->templateVariables[$var] = $value;
    return $this;
  }
  
  public function assignVars($vars){
    foreach($vars as $key => $value) $this->templateVariables[$key] = $value;
    return $this;
  }
  
  public function push($var,$value){
    $args = func_get_args();
    $var = array_shift($args);
    
    if(!isset($this->templateVariables[$var])){$this->templateVariables[$var] = array();}
    
    while($args) $this->templateVariables[$var][] = array_shift($args);
    
    return $this;    
  }
  
  /**
   * Set template file location.
   * @param String template file location. Null value is ignored.
   * @return TemplateObject     
   */     
  public function setTplFile($templateFile = null){
    if($templateFile){$this->templateFile = $templateFile;}   
    return $this;
  }
  
  /**
   * After calling this method, any output of the running script will be
   * assigned to the specified template variable.
   * 
   * wrap_output_end is automatically called for the last wrap.
   * 
   * @param string Variable name
   * @param string Should the target variable be replaced or appended to
   * @param string Should the last fetchOutput continue?          
   *      
   * @return TemplateObject
   */
  public function fetchOutput($var,$replace = false,$nest = false){
    
    $level = $this->fetchOutput_end($nest);
    
    if(!$level) ob_start();
    
    array_push($this->fetchOutput_var,(string) $var);
    array_push($this->fetchOutput_replace,(bool) $replace);

    return $this;
  }
  
  public function fetchOutput_level(){
    return count($this->fetchOutput_var);
  }
  
  public function fetchOutput_terminate(){
    while($result = $this->fetchOutput_end(false,true));
    return $result;
  }
  
  /* save buffer contents to a variable from stack. optionally preserve variable in stack */
  public function fetchOutput_end($preserve = false,$kill = false){
    
    if(!$this->fetchOutput_level()) return false;
    
    $var = $preserve ? end($this->fetchOutput_var) : array_pop($this->fetchOutput_var);
    $replace = $preserve ? end($this->fetchOutput_replace) : array_pop($this->fetchOutput_replace);
    
    if(isset($this->templateVariables[$var]) && !$replace) $this->templateVariables[$var] .= ob_get_clean();
    else $this->templateVariables[$var] = ob_get_clean();
    
    $level = $this->fetchOutput_level();
    if($level && !$kill) ob_start();
    
    return $level;
  }
  
  /* Version compatibility */
  public function append(){return call_user_func_array(array($this,"push"),func_get_args());}
  public function wrap_output($var,$nest = false){return $this->fetchOutput($var,false,$nest);}
  public function wrap_output_end(){return $this->fetchOutputEnd();}

  /**
   * Render template
   * @param string Template file. Optional if already provided.
   * @return mixed Return the result of include statement
   */   
  private function render($templateFile = null,$display = true){
    
    $this->fetchOutput_terminate();
    
    $this->setTplFile($templateFile);
    
    if(!$this->templateFile){throw new \Exception("No template path provided.");}
    
    $templateVariables = $this->templateVariables;
    
    while($templateVariables){
      ${key($templateVariables)} = array_shift($templateVariables);
    }
    
    /* display template */
    if($display) include $this->templateFile;
    else{
      /* else fetch template */
      ob_start();
      include $this->templateFile;
      return ob_get_clean();
    }

  }
  
  /* Shorthand functions for rendering */
  public function fetch($templateFile = null){return $this->render($templateFile,false);}
  public function display($templateFile = null){return $this->render($templateFile,true);}
  
  public function __toString(){
    try{
      $value = $this->fetch();
      return (string) $value;
    }catch(\Exception $e){
      return (string) $e;
    }
    
  } 
  
  /* ArrayAccess implmentation */
  public function &offsetGet($var){return $this->templateVariables[$var];}
  public function offsetSet($var,$value){return $this->templateVariables[$var] = $value;}
  public function offsetUnset($var){unset($this->templateVariables[$var]);}
  public function offsetExists($var){return isset($this->templateVariables[$var]);}
}

?>