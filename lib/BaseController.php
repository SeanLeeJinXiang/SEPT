<?php

 class BaseController extends BaseView
 {
 	protected $view;

 	public function __construct()
 	{
 		$this->view = new BaseView();
 	}
 }
