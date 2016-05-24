<?php
 
  class HomeController extends BaseController
  {
  	  /*
	    *  Land page rendering, As a single page app, we have only one php file for client side
  	  */
      
  		public function render()
  		{
		  	 $this->view->renderView('home');
  		}
 
  }