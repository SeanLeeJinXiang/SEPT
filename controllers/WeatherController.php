<?php

class WeatherController extends BaseController
{
	    public function getCities()
      {
          $state = null;

          if(isset($_POST['state']))
          {
            $state = $_POST['state'];
          }

          $json_data = file_get_contents('stations.json');

          $decoded = json_decode($json_data);

          $targeted_state = null;

          for($i=0;$i<count($decoded);$i++)
          {
              if($decoded[$i]->state==$state)
              {
                  $targeted_state = $decoded[$i];
                  break;
              }
          }
 
          echo json_encode($targeted_state);
      }


      public function getFavourites()
      {

              $my_favourite = array();
              $temp = Session::get("my_favourite");

              if(!empty($temp))
              {
                  $my_favourite = Session::get("my_favourite");
              }

              if(count($my_favourite)>0)
              {
                  $json = json_encode($my_favourite);

                  echo $json;
              }


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

