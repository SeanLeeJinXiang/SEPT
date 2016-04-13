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

      public function addToFavourite()
      {
 
        $user_id = Session::get("user_id");
        $city = $_POST["city"];
        $url = $_POST["url"];  

          if(isset($_POST["city"]) && isset($_POST["url"]) && isset($user_id))
          {
              $my_favourite = array();
              $temp = Session::get("my_favourite");
              $new_favourite = array("city"=>$city,"url"=>$url);

              if(!empty($temp))
              {
                  $my_favourite = Session::get("my_favourite");
              }
        
              $my_favourite_exist = false; 
             for($i=0;$i<count($my_favourite);$i++)
             {
                 if($my_favourite[$i]["city"]==$_POST["city"])
                 {
                    $my_favourite_exist = true;
                    break;
                 }
             } 

             if(!$my_favourite_exist)
             {

                $stmt = $this->db->prepare("INSERT INTO favourites (user_id,city,url) 
                VALUES (:user_id,:city,:url)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':city', $city);
                $stmt->bindParam(':url', $url);
                $stmt->execute();        

                 array_push($my_favourite,$new_favourite);   
                 Session::set("my_favourite",$my_favourite);
                  
                 echo true; 
             }
           else
             {
                 echo false;
             }  

             return;
          }

          echo -1;
 

      }

      public function removeFavorite()
      {
 
          $user_id = Session::get("user_id");
          $city = $_POST["city"];

          $sql = "DELETE FROM favourites WHERE user_id =  :user_id AND city = :city";
          $stmt = $this->db->prepare($sql);
          $stmt->bindParam(':user_id', $user_id);   
          $stmt->bindParam(':city', $city); 
          $stmt->execute();


          $my_favourite = Session::get("my_favourite");
 
          for($i=0;$i<count($my_favourite);$i++)
          {
                 if($my_favourite[$i]["city"]==$city)
                 {
                    unset($my_favourite[$i]);
                    break;
                 }
          }

          if(count($my_favourite)>0)
          {
            $my_favourite = array_values($my_favourite);
          }
 
          Session::set("my_favourite",$my_favourite);

          echo "true";

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

 
      public function register_account()
      {
  
        $user_id = $_POST['value'];

        $stmt = $this->db->prepare("INSERT INTO users (user_id) 
        VALUES (:user_id)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        echo "true";

      }

 
      public function login()
      {
         $user_id = $_POST['value'];
         $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = :user_id"); 
         $stmt->bindParam(':user_id', $user_id);
         $stmt->execute();
         $total = $stmt->rowCount();


          if($total>0)
          {
              Session::set("user_id",$user_id);

             $stmt2 = $this->db->prepare("SELECT * FROM favourites WHERE user_id = :user_id"); 
             $stmt2->bindParam(':user_id', $user_id);
             $stmt2->execute();
             $result = $stmt2->fetchAll();

              $my_favourite = array();
              $i = 0;
             foreach($result as $row){

                $new_favourite = array("city"=>$row['city'],"url"=>$row['url']);
                array_push($my_favourite,$new_favourite); 

              }

              Session::set("my_favourite",$my_favourite);


              echo "true";
          }
         else
          {
              echo "false";
          } 

      }      
 
      public function loginChecked()
      {
          echo Session::get("user_id");
      }

 
      public function logout()
      {
         Session::destroy();
      }
      
      /*  author Victor
       *    This function is for creating accounts. data will be saved into      *    database
       */
      public function register_account()
      {
  
        $user_id = $_POST['value'];

        $stmt = $this->db->prepare("INSERT INTO users (user_id) 
        VALUES (:user_id)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        echo "true";

      }
      /*  author Victor
       *    This function is for loggin in , check id exists in database, if 
       *    there is, get the favrourite data and
       *    put them in session 
       */

      public function login()
      {
         $user_id = $_POST['value'];
         $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = :user_id"); 
         $stmt->bindParam(':user_id', $user_id);
         $stmt->execute();
         $total = $stmt->rowCount();


          if($total>0)
          {
              Session::set("user_id",$user_id);

             $stmt2 = $this->db->prepare("SELECT * FROM favourites WHERE user_id = :user_id"); 
             $stmt2->bindParam(':user_id', $user_id);
             $stmt2->execute();
             $result = $stmt2->fetchAll();

              $my_favourite = array();
              $i = 0;
             foreach($result as $row){

                $new_favourite = array("city"=>$row['city'],"url"=>$row['url']);
                array_push($my_favourite,$new_favourite); 

              }

              Session::set("my_favourite",$my_favourite);


              echo "true";
          }
         else
          {
              echo "false";
          } 

      }      
      /*  author Victor
       *    check if logged in
       */
 
      public function loginChecked()
      {
          echo Session::get("user_id");
      }
      /*  author Victor
       *    destory session 
       */

 
      public function logout()
      {
         Session::destroy();
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