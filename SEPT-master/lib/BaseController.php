<?php

 class BaseController extends BaseView
 {
 	  protected $view;
      protected $db;
 
      public function __construct()
      {
          $this->view = new BaseView();
              try {
          $this->db = new PDO("mysql:host=localhost;dbname=weather",'root', '');
          $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
          }
      catch(PDOException $e)
          {
          echo "Connection failed: " . $e->getMessage();
          }
      }


 }
