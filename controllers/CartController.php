<?php

  class CartController extends BaseController
  {
	  
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

  }