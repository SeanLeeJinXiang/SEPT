<?php

  class Helper{

    /*  @param: controller name (file)
    *   it returns true or false depends on file existance
    *
    */

		public function checkController($controller)
		{

			if(file_exists('controllers/'.$controller.".php"))
			{

				  return true;	
			}
		 else
		   {

		      return false;
		   }	
		}

    /*  @param: controller name (file) , method name
    *   it returns true or false depends on method existance in the controller
    *
    */    

    public function checkMethod($controller,$methodName)
    {
        if(method_exists($controller, $methodName))
        {
           return true;
        }
       else
        {
           return false;
        } 

    }

   	   public function getURL()
   	   {
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {

             return $url = explode("/",substr($_SERVER['REQUEST_URI'],1)); 
          }

   	   	  if(isset($_GET['url'])) // the reason that we use super global $_GET[] is because the param url
   	   	  {						  // is coming through our htaccess rewrite rule			
   	   	  	  
   	   	  	  return $url = explode("/",$_GET['url']); 
   	   	  	  // explode is like a slice method in Java
   	   	  	  // it returns an array of sliced elements
   	   	  }
   	   }

  }