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
 
      public function getEachStationJSON()
      {
          /* due to xmlrequestorigin,
          *  we need to use curl to get the json data 
          */

          /*
          *  @param: url for each json file
          *  return: station json file
          */

          $url = $_POST['url'];
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_URL,$url);
          $result = curl_exec($ch);
          curl_close($ch);

          echo $result;
      }
 
  }