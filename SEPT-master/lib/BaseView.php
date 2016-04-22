<?php

  class BaseView
  {
  	  public function renderView($fileName)
  	  {
  	  	 require 'views/'.$fileName.'.php';
  	  }
  } 	