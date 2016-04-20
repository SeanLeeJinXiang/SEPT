<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Australia Weather</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,900"/>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link href="/public/css/style.css" rel="stylesheet" type="text/css" /> 
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" type="text/css" />
<script src="https://code.jquery.com/jquery-2.2.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="https://fb.me/react-0.14.2.js"></script>
<script src="https://fb.me/react-dom-0.14.2.js"></script>
<script src="https://npmcdn.com/react-router/umd/ReactRouter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.34/browser.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js" type="text/javascript"></script>
<script src="/public/js/lib/lib.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" type="text/javascript"></script>

<!--

  @author: Jihun Nam
  Date:27th March
  Use babel to compile jsx to javascript in only development mode

-->
 
<script type="text/babel">

  var StateArray = {

    "WA":"Western Australia",
    "SA":"South Australia",
    "NT":"Northern Territory",
    "QLD":"Queensland",
    "NSW":"New South Wales",
    "VIC":"Victoria",
    "TM":"Tasmania",
    "ACT":"Canberra",
    "Antarctica":"Antarctica"

  };

  var RenderCity = React.createClass({

      getInitialState()
      {
          return {

            city:"",
            state:"",
            date:"",
            cloudy:"",
            humidity:"",
            temp:"",
            wind:"",
            time:"",
            url:"",
            min_temp:0,
            max_temp:0,
            dummyDATAURL:[
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94939.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.95937.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.95925.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.95935.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94750.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94915.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.95909.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94925.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94938.json"
            ]
          }
      },

      showLoading()
      {
          this.refs["loadingBar"].show();
      },  

      addToFavourite(e)
      {
          e.preventDefault();

          var self = this;
          $.ajax({
            
            url:"/CartController/addToFavourite",
            type:"POST",
            data:{
              city:self.state.city,
              url:self.state.url
            },
            success:function(data)
            {
                if(data==true)
                {
                   toastr.success(self.state.city + " has been added to your favourites","Updated successfully");
                   self.props.CallFavouriteComponent({

                    city:self.state.city,
                    url:self.state.url
                   });
                  
                }
               else if(data==false)
                {
                   toastr.error(self.state.city + " is already in your favourites");
                } 
               else
               {
                   toastr.error("Log in required");
               } 
            }
          });
      },      

      getCityData(url)
      {	
          var self = this;
          this.setState({
            url:url
          })
         
          $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {

              /*
              *  @param self is referencing current react component
              *  Using chart.js , use received data from BOM site make
              *  an interval if the data objects are more than 10
              *  last digit is for how many data objects to be shown
              *  Make a graph and render it
              */

              module().getSimpleGragh(data,self,self.refs["loadingBar"],7,"myChart"); 
              self.refs["loadingBar"].hide();                 
            }   
          });
      }, 

      refresh(e)
      {
          e.preventDefault();          
          var self = this; 
          var url = self.state.url;                
          $.ajax({
            
            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{             
              url:url
            },
            dataType:"json",
            success:function(data)
            {
              self.refs['loadingBar'].show();
              module().getSimpleGragh(data,self,self.refs["loadingBar"],7,"myChart"); 
              self.refs["loadingBar"].hide();               
            }
          });
      },

      renderCityByUrl(url,currentCity)
    {

        var self = this;

            $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {
              module().getSimpleGragh(data,self,null,50,"myChart",currentCity);

            }

          });

    },

      showPrevCalendar(e)
    {

        var self = this;
        var todayDate = new Date().getDate();

        if($("#hiddenField").css("display")=="none")
        {
            $("#hiddenField").css("display","block");
            $( "#hiddenField" ).datepicker({
                changeMonth: false,
                changeYear: false,
                  dateFormat: 'dd/mm/yy',
                  duration: 'fast',
                  stepMonths: 0,
                  showOn: "button",
                  buttonText: "day",
                  minDate : "-9",
                  maxDate:"0",
                  onSelect:function(date)
                  {
                      self.refreshChart(date,todayDate);
                  }
            });
         }
       else
         {
            $("#hiddenField").css("display","none");
            $('#hiddenField').datepicker('setDate', null);
         }  


    },

    showNextCalendar(e)
    {
        var self = this;
        var todayDate = new Date().getDate();

        if($("#hiddenField2").css("display")=="none")
        {
            $("#hiddenField2").css("display","block");
            $( "#hiddenField2" ).datepicker({
                changeMonth: false,
                changeYear: false,
                  dateFormat: 'dd/mm/yy',
                  duration: 'fast',
                  stepMonths: 0,
                  showOn: "button",
                  buttonText: "day",
                  minDate : "0",
                  maxDate:"9",
                  onSelect:function(date)
                  {
                      self.refreshChart(date,todayDate);
                  }
            });
         }
       else
         {
            $("#hiddenField2").css("display","none");
            $('#hiddenField2').datepicker('setDate', null);
         }  
    },

    refreshChart(date,todayDate)
    {
         var parsedDate = parseInt(date.substring(0,date.indexOf("/")));
         var currentCity = {

             city:this.state.city,
             state:this.state.state,
             date:this.state.date

          };

        if(todayDate==parsedDate)
        {
            this.renderCityByUrl(this.state.url);
        }
       else
        {  
            var ran = parsedDate % 9;
            this.renderCityByUrl(this.state.dummyDATAURL[ran],currentCity);
        } 
    },

    
   

	      render()
      
      {
        return (                
          <div className='animated fadeIn'>
              <RenderLoading ref='loadingBar'/>           
               <div className="cityInfoWrapper">
                 <p className="city">                 		
                		 <button onClick={this.addToFavourite} className='add_to_favourite btn btn-default btn-sm'>Add to Favourite</button>
        				 &nbsp;
				        <button onClick={this.refresh} className='add_to_favourite btn btn-default btn-sm'>Refresh</button>				        
				        <br/>
				        {this.state.city}				  
                 </p>
                    <div id="hiddenField"></div>
                  <div id="hiddenField2"></div>

                <button type="button" onClick={this.showPrevCalendar} className="btn btn-default favouriteLeftButton" aria-label="Left Align">
                  <span className="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
               </button>
               <button type="button" onClick={this.showNextCalendar} className="btn btn-default favouriteRightButton" aria-label="Left Align">
                  <span className="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
               </button>
               

                 <p className="date">{this.state.date} <span className="time">{this.state.time}</span></p> 
                 <p className="cloudy">{ this.state.cloudy=="-"?"": this.state.cloudy }</p> 
                 <p className="humidity">{this.state.humidity==null?"":"Humidity " + this.state.humidity +"%"}</p> 
                 <p className="temp">{this.state.temp==null?"":"Temp " + this.state.temp +" C"}</p> 
                 <p className="wind">{this.state.wind==0?"":"Wind " + this.state.wind}</p> 
              </div>
              <canvas id="myChart" width="570" height="330"></canvas>
          </div> 
          );
      },
  });

  var MainWrapper = React.createClass({

    getInitialState()
      {
          return {
	            buttonClicked:"",
	            is_logged_in:false
          }
      },


    componentWillMount()
      {
          var self = this;

          $.ajax({

            url:"/LoginController/loginChecked",
            success:function(data)
            {
               if(data!="")
               {
                  self.setState({
                    is_logged_in:true
                  })
               }
            }

          })
      },
 
    componentDidMount()
      {
        // close window for city detail when modal is closed

        $("#stateModal").on('hidden.bs.modal', function () {
    
           $("#city_view_detail").hide();

        });

          var stateLinks = document.getElementsByClassName("stateLink");
          var self = this;

          for(var i=0;i<stateLinks.length;i++)
          {
              (function(i){

              stateLinks[i].addEventListener("click",function(e){

                e.preventDefault();

                var href = e.target.href.substring(e.target.href.lastIndexOf("/")+1);

                self.getStateInfo(href);

              })

              })(i);
          }            
         
      },
     

    getStateInfo(state)
    { 
       if(state!=undefined)
       {
        state = StateArray[state];
        var self = this;

          $.ajax({

            url:"/WeatherController/getCities",
            type:"POST",
            data:{state:state},
            dataType:"json",
            success:function(data)
            {
              /*  Simple pagination for rendering cities more than 10 
              *    
              */
 
                var tableObj = {};
                var pageSeparateNum = 10;
                var pageNum = data.stations.length/pageSeparateNum;
                var pageNumUp = Math.ceil(pageNum);
                var tableArray = [];
 
              /*   First loop increament by pageSeparateNum variable
              *    0-10-20-30 ~~
              *    Second loop for building jquery objects with tr elements and buttons 
              *    each button's id has its url address
              *    tableObj store trs
              */

                for(var i=0;i<data.stations.length;i+=pageSeparateNum)
                {
                    var tr_array = [];
 
                    for(var j=0;j<pageSeparateNum;j++)
                    {
                        var tr = $("<tr><td class='col-md-9'>"+data.stations[i+j].city+"</td><td class='col-md-3'><button id="+data.stations[i+j].url+" class='each_city btn btn-info btn-sm'>View Detail</button></td></tr>");
						
                        tr_array.push(tr);

                        if(data.stations.length-1==(i+j))
                        {
                           break;
                        }

                    }

                    if(i==0)
                    {
                      tableObj[i] = tr_array;  
                    }
                   else
                    {
                      tableObj[i/pageSeparateNum] = tr_array;  
                    } 
 
                }

                var pageNation = $("<ul class='pagination'></ul>");
 
                for(var i=0;i<pageNumUp;i++)
                {
                    var link = $("<li><a class='statePageLink' href='#'>"+(i+1)+"</a></li>");
                    pageNation.append(link);
                }

                var table = reRenderTable(1);
 
                $("#stateModalTitle").html(state);
                $(".pageNationBody").html(pageNation);
                $(".stateRendering").html(table);
                $("#stateModal").modal();

              /*  @param: pageNum - when clicked the num
              *   replace old tr data to new one
              *
              */    


               function reRenderTable(pageNum)
               {
                  $(".stateRendering").empty();

                  var table = $("<table id='data_table' class='table table-responsive table-striped'></table>");

                  for(var i=0;i<tableObj[pageNum-1].length;i++)
                  {
                      table.append(tableObj[pageNum-1][i]);
                  }

                  $(".stateRendering").html(table);

                    var buttons = document.getElementsByClassName('each_city');

                      for(var i=0;i<buttons.length;i++)
                      {
                          (function(i){

                            buttons[i].addEventListener('click',viewDetailFunc);

                          })(i)
                      }

                  return table;

               } 

                   function viewDetailFunc(e)
                   {
                        e.preventDefault();

                        var url = this.id;

                        var win = makeNewWindow(600,680);

                        $("#stateModal").append(win.fadeIn());

                        self.refs["CityComponent"].showLoading();
 
                        self.refs["CityComponent"].getCityData(url);				            
                        		 
                   }  

                   function getUrl(e){

                   e.preventDefault();
                   var url = this.id;

                   return url;

                   }


               function makeNewWindow(width,height)
               {
                   var win = $("#city_view_detail");
                  
                   win.css({
                    display:"block",
                    width:width,
                    height:height,
                    background:"#ffffff",
                    position:"absolute",
                    top:21,
                    left:615

                   });

                   win.css("z-index",100);
                   win.css("border-top-right-radius",5);
                   win.css("border-bottom-right-radius",5);
                   win.css("border-bottom-right-radius",5);
                   win.css("box-shadow","4px 5px 5px -2px rgba(112,106,112,1)");

                   return win;
               }    

               /*  attch click events for each page numbers
               *   this has to be done after rendering initial links on DOM
               *   immediate invoke function used inside a loop to use closure
               */

               var pageLinks = document.getElementsByClassName('statePageLink');

                  for(var i=0;i<pageLinks.length;i++)
                  {
                    (function(i){
 
                        pageLinks[i].addEventListener('click',function(e){

                        e.preventDefault();

                        var pageNumber = $(this).html();

                        var buttons = document.getElementsByClassName('each_city');

                      /*
                      *  Due to duplicated event listeners to buttons
                      *  before render table, remove all listeners
                      *
                      */

                      for(var j=0;j<buttons.length;j++)
                      {
                          (function(j){

                            buttons[j].removeEventListener('click',viewDetailFunc);

                          })(j)
                      }

                          reRenderTable(pageNumber);

                       });
                    })(i);
                  }
            }

          });

      }
    },    

    CallFavouriteComponent(dataObj)
    {
        this.refs["FavouriteComponent"].addToFavourite(dataObj);
    },   

    register_clicked(e)
    {
        e.preventDefault();
        $(".register_input").attr("placeholder","Register an account to save your favourites");
        $(".common_submit_button").html("Register");
        this.setState({
          buttonClicked:"register"
        })
    },

    login_clicked(e)
    {
        e.preventDefault();
        $(".register_input").attr("placeholder","Enter your login ID");
        $(".common_submit_button").html("Log In");
        this.setState({
          buttonClicked:"login"
        })
    },

    logout_clicked(e)
    {
        e.preventDefault();
        $(".common_submit_button").html("Log out");
        this.setState({
          buttonClicked:"logout"
        })
    },

    common_submit_clicked(e)
    {
       e.preventDefault();
       var buttonStatus = this.state.buttonClicked;
       var url;
       var self = this;
       var value = $(".register_input").val();

       if(value=="")
       {
          $(".register_input_div").addClass("has-error");
       }
	 

       if(buttonStatus != "")
       {
            if(buttonStatus=="register" && value!="")
            { 
              url = "/LoginController/register_account"
            }

            if(buttonStatus=="login"  && value!="")
            {
              url = "/LoginController/login"
            }

            if(buttonStatus=="logout")
            {
              url = "/LoginController/logout"
            }

            $.ajax({
              url:url,
              type:"POST",
              data:{
                value:value
              },
              success:function(data)
              {

                  if(buttonStatus=="login" && data==value)
                  {
                      self.refs["FavouriteComponent"].updateFavourites();
						toastr.success("Login Successful");
						self.setState({

							is_logged_in:true

				  });
				     
                   $(".common_submit_button").html("Select");
                   $(".register_input").hide();
				           $('#loginUser').html('Successfully Logged in as ' + data);
                  }
				  else{
					         $('#loginUser').html(data);
					
				  }

				  if(buttonStatus=="login" && data!=value)
                  {

				  toastr.error("Login Unsuccessful. Please Register First.");
				  } 
				  			  

                  if(buttonStatus=="register")
                  {
                     toastr.success("Your account has been created. You can log in now.");
					            $('#loginUser').html(data);
                  }

                  if(buttonStatus=="logout")
                  {
                      self.refs["FavouriteComponent"].resetFavourites();

                  self.setState({
                    is_logged_in:false
                  });
                   
                   $(".common_submit_button").html("Select");
                   $(".register_input").show();
				           $('#loginUser').html(data);
                  }


              }
            })



       }

    },
 
  	render()
  	{

   var dropMenu;

   if(!this.state.is_logged_in)
   {
      dropMenu = <ul className="dropdown-menu"> 
    <li><a onClick={this.register_clicked} href="#">Register</a></li>
    <li><a onClick={this.login_clicked} href="#">Log In</a></li>
      </ul>
   }
  else
   {
      $(".register_input").hide();
      dropMenu = <ul className="dropdown-menu">
    <li><a onClick={this.logout_clicked} href="#">Log out</a></li>
      </ul>
   } 
  		return(

  			<div>
<nav className="navbar navbar-default navbar-static-top">
  <div className="container">
  <div id="loginUser"></div>
  <FavouriteComponent ref="FavouriteComponent"/>
    <form className="navbar-form navbar-right" role="search">
       <div className="register_inputWrapper">
        <div className="input-group register_input_div">
           <input type="text" className="form-control register_input" placeholder="Register or Login"/>
        </div>
      </div>

<div className="btn-group">
  <button type="button" onClick={this.common_submit_clicked} className="btn btn-info common_submit_button">Select</button>
  <button type="button" className="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span className="caret"></span>
    <span className="sr-only">Toggle Dropdown</span>
  </button>
  {dropMenu}
</div>
    </form>
  </div>
</nav>
       <div className="container">   
       <h1 className='title_h1'>Australia Weather</h1>
       <h3 className='title_h3'>View in real time </h3>
          <div className="statesWrapper row">
             <div className="col-md-12">
                <ul className="stateWrapperUL clearfix">
                 <div className="statesUp clearfix col-md-4">
                <li className="large"><a className="stateLink WA" href="WA">Western Australia</a></li>
                <li className="small left"><a className="stateLink VIC" href="VIC">Victoria</a></li>
                <li className="small"><a className="stateLink ACT" href="ACT">Canberra</a></li>
                <li className="large"><a className="stateLink SA" href="SA">South Australia</a></li>
                  </div>
                  <div className="statesDown clearfix col-md-4">
                <li className="large"><a className="stateLink QLD" href="QLD">Queensland</a></li>
                <li className="large"><a className="stateLink NSW" href="NSW">New South Wales</a></li>
                <li className="small left"><a className="stateLink TM" href="TM">Tasmania</a></li>
                <li className="small"><a className="stateLink NT" href="NT">NT</a></li> 
                  </div>
                  <div className="stateBottom clearfix col-md-4"> 
                <li className="ex_large"><a className="stateLink Antarctica" href="Antarctica">Antarctica</a></li>
                  </div>
                </ul>
             </div>
          </div>                  
        </div>

          <div id="stateModal" className="modal fade" role="dialog">
            <div className="modal-dialog">
                
                <div id='city_view_detail'><RenderCity CallFavouriteComponent={this.CallFavouriteComponent} ref="CityComponent"/></div>

              <div className="modal-content">
                <div className="modal-header">
                  <button type="button" className="close" data-dismiss="modal">&times;</button>
                  <h4 id="stateModalTitle" className="modal-title"></h4>
                </div>
                <div className="modal-body">
                    <div className="stateRendering"></div>
                    <div className="pageNationBody"></div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>

            </div>
          </div>
        </div>
  			);
  	}

  });

  var FavouriteComponent = React.createClass({

    addToFavourite(dataObj)
    {
        $(".myFavouritesWrapper").css("display","block");

        var myFavourites = this.state.myFavourites;

        myFavourites.push(dataObj);

        this.setState({
          myFavourites:myFavourites
        });
    },

    resetFavourites()
    {
        this.setState({
          myFavourites:[]
        });

        $(".myFavouritesWrapper").css("display","none");

    },  

    updateFavourites()
    {
        var self = this;
        $.ajax({

          url:"/WeatherController/getFavourites",
          type:"POST",
          dataType:"json",
          success:function(data)
          {

             if(data.length>0)
             {
                 $(".myFavouritesWrapper").css("display","block");
             }

             self.setState({
                myFavourites:data
             })
          }

        });
    },

    componentWillMount()
    {
       this.updateFavourites();
    },

    getInitialState()
    {
        return {
            myFavourites:[],
            city:"",
            state:"",
            date:"",
            cloudy:"",
            humidity:"",
            temp:"",
            wind:"",
            time:"",
            url:"",
            min_temp:0,
            max_temp:0,   
            dummyDATAURL:[
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94939.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.95937.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.95925.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.95935.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94750.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94915.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.95909.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94925.json",
                 "http://www.bom.gov.au/fwo/IDN60903/IDN60903.94938.json"
            ]
        }
    },

    renderCityDetail(e)
    {
          e.preventDefault();

          var url = e.target.id
          var self = this;
 
          this.setState({
            url:url
          });

          this.renderCityByUrl(url);

          setTimeout(function(){

            $('.myFavouritesUL').slideUp();

          },1500);
 
    },

    renderCityByUrl(url,currentCity)
    {

        var self = this;

            $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {
              if($("#cityDetailsWrapper").css("opacity")!="0.3")
              {    
                var wrapper = $("<div id='cityDetailsWrapper'></div>");
                wrapper.css({

                   width:$(window).width(),
                   height:$(window).height(),
                   position:"absolute",
                   background:"#9C9C9C",
                   top:0,
                   left:0,
                   opacity:"0.3",

                });

                wrapper.css("z-index",10);

              $(document.body).append(wrapper.fadeIn());

               }
              $("#CityChartWrapper").show();

              module().getSimpleGragh(data,self,null,50,"CityDetailChart",currentCity);
 
              $(".cityInfoWrapper .closeButton").on("click",closeBackGround);

              $('#cityDetailsWrapper').on("click",closeBackGround);

              function closeBackGround(e)
              {
                  e.preventDefault();

                  $("#CityChartWrapper").hide();
                  $("#cityDetailsWrapper").remove();  

              }

            }

          });

    },

    removeFavor(e)
    {
        e.preventDefault();

        var city = e.target.id;
        var self = this;
        $.ajax({

          url:"/CartController/removeFavorite",
          type:"POST",
          data:{
            city:city
          },
          success:function(data)
          {
             if(data=="true")
             {
                toastr.success(city + " has been removed from your favourite list");

                var myFavourites = self.state.myFavourites;
                var index;

                for(var i=0;i<myFavourites.length;i++)
                {
                    if(myFavourites[i].city==city)
                    {
                       index = i;
                       break;
                    }
                }

                myFavourites.splice(i,1);
 
                if(myFavourites.length==0)
                {
                    $(".myFavouritesWrapper").fadeOut();
                }

                self.setState({
                  myFavourites:myFavourites
                })
             }
          }
        });
    },

    toggleMenu(e)
    {
        e.preventDefault();
        $(".myFavouritesUL").slideToggle();
    },

    showPrevCalendar(e)
    {

        var self = this;
        var todayDate = new Date().getDate();

        if($("#hiddenField3").css("display")=="none")
        {
            $("#hiddenField3").css("display","block");
            $( "#hiddenField3" ).datepicker({
                changeMonth: false,
                changeYear: false,
                  dateFormat: 'dd/mm/yy',
                  duration: 'fast',
                  stepMonths: 0,
                  showOn: "button",
                  buttonText: "day",
                  minDate : "-9",
                  maxDate:"0",
                  onSelect:function(date)
                  {
                      self.refreshChart(date,todayDate);
                  }
            });
         }
       else
         {
            $("#hiddenField3").css("display","none");
            $('#hiddenField3').datepicker('setDate', null);
         }  


    },

    showNextCalendar(e)
    {
        var self = this;
        var todayDate = new Date().getDate();

        if($("#hiddenField4").css("display")=="none")
        {
            $("#hiddenField4").css("display","block");
            $( "#hiddenField4" ).datepicker({
                changeMonth: false,
                changeYear: false,
                  dateFormat: 'dd/mm/yy',
                  duration: 'fast',
                  stepMonths: 0,
                  showOn: "button",
                  buttonText: "day",
                  minDate : "0",
                  maxDate:"9",
                  onSelect:function(date)
                  {
                      self.refreshChart(date,todayDate);
                  }
            });
         }
       else
         {
            $("#hiddenField4").css("display","none");
            $('#hiddenField4').datepicker('setDate', null);
         }  
    },

    refreshChart(date,todayDate)
    {
         var parsedDate = parseInt(date.substring(0,date.indexOf("/")));
         var currentCity = {

             city:this.state.city,
             state:this.state.state,
             date:this.state.date

          };

        if(todayDate==parsedDate)
        {
            this.renderCityByUrl(this.state.url);
        }
       else
        {  
            var ran = parsedDate % 9;
            this.renderCityByUrl(this.state.dummyDATAURL[ran],currentCity);
        } 
    },
    
    render()
    {
       var myFavourites;
       var self = this; 

       if(typeof this.state.myFavourites == "object" && this.state.myFavourites.length>0)
       {
              myFavourites = this.state.myFavourites.map(function(data,index){
              return <li className="list-group-item" key={index}><a onClick={self.renderCityDetail} className='favouriteLinks' id={data.url} href="#">{data.city}</a><button id={data.city} onClick={self.removeFavor} className='favouritebuttons btn btn-default btn-sm'>Delete</button></li>
          })
       }
        return (
        <div>  
        <div className="myFavouritesWrapper"><button onClick={this.toggleMenu} className="btn btn-default btn-sm">My Favourites <span className="favouritesCounter">{this.state.myFavourites.length}</span></button><ul className="myFavouritesUL list-group">{myFavourites}</ul></div>
              
              <div className="animated fadeIn" id="CityChartWrapper">
              <div id="hiddenField3"></div>
              <div id="hiddenField4"></div>


                <div className="cityInfoWrapper">  

                <button type="button" onClick={this.showPrevCalendar} className="btn btn-default favouriteLeftButton" aria-label="Left Align">
                  <span className="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
               </button>
               <button type="button" onClick={this.showNextCalendar} className="btn btn-default favouriteRightButton" aria-label="Left Align">
                  <span className="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
               </button>
                 <p className="city">{this.state.city}</p><span className='closeButton'><button className='btn btn-default btn-sm'>Close</button></span>
                 <p className="date">{this.state.date} <span className="time">{this.state.time}</span></p> 
                 <p className="cloudy">{ this.state.cloudy=="-"?"": this.state.cloudy }</p> 
                 <p className="humidity">{this.state.humidity==null?"":"Humidity " + this.state.humidity +"%"}</p> 
                 <p className="temp">{this.state.temp==null?"":"Temp " + this.state.temp +" C"}</p> 
                 <p className="wind">{this.state.wind==0?"":"Wind " + this.state.wind}</p> 
                 <p className="min_temp">{this.state.min_temp!=0?"Min Temp " + this.state.min_temp : "Min Temp Unknown"}</p> 
                 <p className="max_temp">{this.state.max_temp!=0?"Max Temp " + this.state.max_temp : "Max Temp Unknown"}</p> 
                 <canvas id="CityDetailChart" width="1000" height="450"></canvas>
              </div>
              </div>
        </div>
        );
    }
  });
 

  var RenderLoading = React.createClass({

    show()
    {
        $(".loadingbarWrapper").fadeIn(); 
    },

    hide()
    {
        $(".loadingbarWrapper").fadeOut(); 
    },

    render()
    {
      return (
         <div className="loadingbarWrapper col-md-4 center-block">
         <img src={'/public/images/loading.gif'} className="loading_bar img-responsive"/></div>
        );
    }
  });

ReactDOM.render(<MainWrapper/>,document.getElementById('App'));
 
$(document).ready(function(){
	$.ajax({ url: "/LoginController/getLogin",
	        context: document.body,
	        success: function(data){
	             $('#loginUser').html(data);	             
	        }});		  
});

</script>
</head>
<body>  	  
    
  	<div class="container-fluid"> 
	   	<div id="App"></div>	   	
		   	<footer class="footer">
		  	  <div class="container">              
		      <p>Copyright</p>
		   	  </div> 
			</footer>
  	</div>
</body>
</html>