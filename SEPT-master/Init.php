<?php 

/*  
*   Basic MVC pattern initialization
*	 This class gets url and 
*   find a controller and method from it
*
*/
 
   class Init extends Helper{

   	protected $DEFAULT_CONTROLLER = "HomeController";
   	protected $DEFAULT_METHOD = "render";
   	protected $controller;
   	protected $method;
   	protected $url = array();
   	protected $params = array();

   	   public function __construct()
   	   {
   	   	  Session::init();
 
   	   	  $this->url = $this->getURL();

   	   	  $this->controller = $this->checkController($this->url[0]) == true ? $this->url[0] : $this->DEFAULT_CONTROLLER;

       		  require_once("controllers/".$this->controller.".php");

       		  $controller = new $this->controller();	

       		  if(isset($this->url[1]))
       		  {
       		  	 $this->method = $this->checkMethod($controller,$this->url[1]) == true ? $this->url[1] : $this->DEFAULT_METHOD;		
       		  }
       		else
       		  {
       		  	 $this->method = $this->DEFAULT_METHOD;
       		  }  

   	   	  $this->params = $this->params!=null ? array_slice($this->url,2) :array();
 
   	        call_user_func(array($controller,$this->method),$this->params);
 
   	   	  unset($this->url); // URL must be unset

   	   }



   }
