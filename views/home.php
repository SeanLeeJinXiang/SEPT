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
            info:"",
            city:"",
            state:"",
            date:"",
            cloudy:"",
            humidity:"",
            temp:"",
            wind:"",
            time:"",
            url:"",
            summary:"",
            _temp:true,
            _hum:false,
            _wind:false,
            _cloud:false,
            _pressure:false,
            _value:"",
            _temp_arr:[],
            _hum_arr:[],
            _wind_arr:[],
            _cloud_arr:[],
            _pressure_arr:[],
            _label_arr:[],
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
            ],
            dummyFORECASTDATAURL:[
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-15.51,123.16",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-34.94,117.82",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-35.03,117.88",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-16.64,128.45",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-30.34,115.54",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-32.46,123.87",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-22.67,119.16",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-20.88,115.41",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-19.59,119.10"
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
          var info = "BOM";   
          this.setState({
            info:info,
            url:url
          })    

         $(".viewPanel2").css("display","none");

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

      getForecastioData(url,cityname){

       var self = this;   
       self.refs['loadingBar'].show();
       var info = "Forecast.io";   
          this.setState({
            info:info,
            url:url,             
            _temp:true,
            _hum:false,
            _wind:false,
            _cloud:false,
            _pressure:false
            
          })            

          $(".viewPanel2").css("display","inline");

          $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {
              var newObject;
              newObject = module2().parseData(data);
 
              /*
              *  @param self is referencing current react component
              *  Using chart.js , use received data from Forecast.io site
              *  Create a graph and display information
              */

              module2().getSimpleGragh(cityname,newObject,self,self.refs["loadingBar"],7,"myChart"); 
              self.refs["loadingBar"].hide();         
            }   
          });

      },

      getOpenWeatherData(url,cityname){

       var self = this;   
       self.refs['loadingBar'].show();
       var info = "OpenWeather";   
          this.setState({
            info:info,
            url:url
          })  

          $(".viewPanel2").css("display","none");

          $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {
              var newObject;

              newObject = module2().parseData(data); 

               /*
              *  @param self is referencing current react component
              *  Using chart.js , use received data from OpenWeatherMap site
              *  Create a graph and display information
              */
             
              module2().getSimpleGragh(cityname,newObject,self,self.refs["loadingBar"],7,"myChart"); 
              self.refs["loadingBar"].hide();                    
            }   
          });
      },

      refresh(e)
      {
          e.preventDefault();          
          var self = this; 
          var url = self.state.url;  
          var info = self.state.info;
          var cityname = self.state.city;           
      
          if(info =="BOM"){  
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
          }
          else
          { 
          self.refs["loadingBar"].show();
           $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {            
              var newObject;
              newObject = module2().parseData(data);
                            
              module2().getSimpleGragh(cityname,newObject,self,self.refs["loadingBar"],7,"myChart");               
              self.refs["loadingBar"].hide();                    
            }   
          }); 
          this.setState({
                  _temp:true,
                  _hum:false,
                  _wind:false,
                  _cloud:false,
                  _pressure:false
          })
                     
          }          
          self.refs["loadingBar"].hide();
          
      },   

      renderCityByUrl(url,info,city,currentCity)
    {
       var self = this;
        if(info =="BOM"){    

            $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {
              self.refs['loadingBar'].show();
              module().getSimpleGragh(data,self,null,7,"myChart",currentCity);  
                           
              self.refs["loadingBar"].hide();             
            }
          });

          }
          else
          {
          self.refs["loadingBar"].show();
           $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {            
              var newObject;
              newObject = module2().parseData(data);
                            
              module2().getSimpleGragh(city,newObject,self,self.refs["loadingBar"],7,"myChart");         
                            
              self.refs["loadingBar"].hide();  
            }   
          }); 
          this.setState({
                  _temp:true,
                  _hum:false,
                  _wind:false,
                  _cloud:false,
                  _pressure:false
          })
           self.refs["loadingBar"].hide();
          }
          
    },

       renderCityByTheUrl(url,info,city,currentCity,date)
    {

        var self = this;
        if(info=="BOM"){
            $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {
              self.refs['loadingBar'].show();
              module().getSimpleGragh(data,self,null,50,"myChart",currentCity);               
              self.refs["loadingBar"].hide();             

              self.setState({
                    date:date
                })
            }

          });
          }
          else
          {
           self.refs["loadingBar"].show();
           $.ajax({

            url:"/WeatherController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {            
              var newObject;
              newObject = module2().parseData(data);
                            
              module2().getSimpleGragh(city,newObject,self,self.refs["loadingBar"],7,"myChart");      
              self.setState({
                    date:date
              })                    
              self.refs["loadingBar"].hide();
            }   
          }); 
          this.setState({
                  _temp:true,
                  _hum:false,
                  _wind:false,
                  _cloud:false,
                  _pressure:false
          })
          
           self.refs["loadingBar"].hide();

          }
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
                  dateFormat: 'DD d MM yy',
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
                  dateFormat: 'DD d MM yy',
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
          var info = this.state.info;
        if(info == "BOM"){
         var s1 = date.split(' ');  
         var parsedDate = parseInt(s1[1]);       
       
         var currentCity = {
         
             city:this.state.city,
             state:this.state.state,
             date:this.state.date

          };

          this.setState({
                  _temp:true,
                  _hum:false,
                  _wind:false,
                  _cloud:false,
                  _pressure:false
              })          

        if(todayDate==parsedDate)
        {           
            this.renderCityByUrl(this.state.url,this.state.info,this.state.city);
            
        }
       else
        {              
            var ran = parsedDate % 9;
            this.renderCityByTheUrl(this.state.dummyDATAURL[ran],this.state.info,this.state.city,currentCity,date);
        } 
        }else{
         var s1 = date.split(' ');  
         var parsedDate = parseInt(s1[1]);       
       
         var currentCity = {
         
             city:this.state.city,
             state:this.state.state,
             date:this.state.date

          };

          this.setState({
                  _temp:true,
                  _hum:false,
                  _wind:false,
                  _cloud:false,
                  _pressure:false
              })          

        if(todayDate==parsedDate)
        {           
            this.renderCityByUrl(this.state.url,this.state.info,this.state.city);
            
        }
       else
        {              
            var ran = parsedDate % 9;
            this.renderCityByTheUrl(this.state.dummyFORECASTDATAURL[ran],this.state.info,this.state.city,currentCity,date);
        } 

        }
    },     

     handleChange(e)
    {
        var value = e.target.value;        

        this.updateRadio(value);

        var data = [];

        if(value=="_temp")
        {
           data = this.state._temp_arr;
        }

        if(value=="_hum")
        {
           data = this.state._hum_arr;           
        }

        if(value=="_wind")
        {
           data = this.state._wind_arr;
        }

        if(value=="_pressure")
        {
           data = this.state._pressure_arr;
        }

        if(value=="_cloud")
        {
           data = this.state._cloud_arr; 
                          
        }

        module2().getSimpleGragh(null,value,"myChart",data,this.state._label_arr);
    },  

     updateRadio(value)
    {
        if(value=="_temp")
        {
            this.setState({
               _temp:true,
               _hum:false,
               _wind:false,
               _pressure:false,
               _cloud:false,
               _value:"_temp"
            })            
        }
      else if(value=="_cloud")
      {
            this.setState({
               _temp:false,
               _hum:false,
               _wind:false,
               _pressure:false,
               _cloud:true,
               _value:"_cloud"
            })
      }   
      else if(value=="_hum")
      {
            this.setState({
               _temp:false,
               _hum:true,
               _wind:false,
               _pressure:false,
               _cloud:false,
               _value:"_hum"
            })
      }   
      else if(value=="_pressure")
      {
            this.setState({
               _temp:false,
               _hum:false,
               _wind:false,
               _pressure:true,
               _cloud:false,
               _value:"_pressure"
            })
      }   
      else if(value=="_wind")
      {
            this.setState({
               _temp:false,
               _hum:false,
               _wind:true,
               _pressure:false,
               _cloud:false,
               _value:"_wind"
            })
      }   
    },
        render()
      
      {
        return (    
             
          <div id = "detailsWrap"className='animated fadeIn'>
              <RenderLoading ref='loadingBar'/>           
               <div className="cityInfoWrapper">
                 <p className="city">    
                 <span className="infoID">
                {(() => {
                    switch (this.state.info){
                    case "BOM" : return "Information from BOM";
                    case "Forecast.io" : return "Information from Forecast.io";   
                    case "OpenWeather" : return "Information from OpenWeatherMap.org";         
                  }
                  })()} </span>                 
                     <button onClick={this.addToFavourite} className='add_to_favourite btn btn-default btn-sm'>Add to Favourite</button>
                 &nbsp;
                <button onClick={this.refresh} className='add_to_favourite btn btn-default btn-sm'>Refresh</button>
                <br/>
                <p className="stateID">{this.state.city}</p>
                 </p>
                    <div id="hiddenField"></div>
                    <div id="hiddenField2"></div>

                <button type="button" onClick={this.showPrevCalendar} className="btn btn-default favouriteLeftButton" aria-label="Left Align">
                <span className="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>Previous Days</button>
                <button type="button" onClick={this.showNextCalendar} className="btn btn-default favouriteRightButton" aria-label="Left Align">Forecasts
                <span className="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                </button>
                <div className="detailsWrapper">  
                <div className="ChartDetails">
                 <p className="date">{this.state.date} <span className="time">{this.state.time}</span></p> 

                 <p className="summary">
                 {(() => {
                    switch (this.state.info){                  
                    case "Forecast.io" : return this.state.summary==""?"":"Summary : " + this.state.summary; 
                    case "OpenWeather" : return this.state.summary==""?"":"Summary : " + this.state.summary;           
                  }
                  })()} 

                 </p>
                 <p className="cloudy">
                 {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.cloudy=="-"?"": "Summary :" + this.state.cloudy;
                    case "Forecast.io" : return this.state.cloudy=="-"?"":"Cloud Cover : " + this.state.cloudy; 
                    case "OpenWeather" : return this.state.cloudy=="-"?"":"Cloud Cover : " + this.state.cloudy;           
                  }
                  })()} 

                 </p> 
                 <p className="humidity">
                   {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.humidity==null?"":"Humidity : " + this.state.humidity +"%";
                    case "Forecast.io" : return this.state.humidity==null?"":"Humidity : " + this.state.humidity +"%";   
                    case "OpenWeather" : return this.state.humidity==null?"":"Humidity : " + this.state.humidity +"%";         
                  }
                  })()} 
                 </p> 
                 <p className="temp">
                   {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.temp==null?"":"Temp : " + this.state.temp +" C";
                    case "Forecast.io" : return this.state.temp==null?"":"Current Temp : " + this.state.temp +" C"; 
                    case "OpenWeather" : return this.state.temp==null?"":"Current Temp : " + this.state.temp +" Fahrenheit";  
                  }
                  })()} 
                 </p> 
                 <p className="wind">
                    {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.wind==0?"":"Wind : " + this.state.wind + " KM per hour";
                    case "Forecast.io" : return this.state.wind==0?"":"Current Wind : " + this.state.wind + " KM per hour"; 
                    case "OpenWeather" : return this.state.wind==0?"":"Current Wind : " + this.state.wind + " Miles per hour";                               
                  }
                  })()} 
                 </p> 
              </div>

                 <div className="viewPanel2">
                     <div>
                        <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._temp} onChange={this.handleChange} value="_temp" name="viewPanel2"/>Apprent Temperature</label>
                        </div>
                        <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._hum} onChange={this.handleChange} value="_hum" name="viewPanel2"/>Humidity</label>
                        </div>
                        <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._wind} onChange={this.handleChange} value="_wind" name="viewPanel2"/>Wind</label>
                        </div>
                         <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._cloud} onChange={this.handleChange} value="_cloud" name="viewPanel2"/>Cloud</label>
                        </div>
                        <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._pressure} onChange={this.handleChange} value="_pressure" name="viewPanel2"/>Pressure</label>
                        </div>                       
                     </div>
                 </div>
                 </div>
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

                        var lat = (data.stations[i+j].lat).trim();
                        var lon = (data.stations[i+j].lon).trim();

                        var forecast_io = "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/"+lat+","+lon;
                        var openweathermap = "http://api.openweathermap.org/data/2.5/forecast?lat=" + lat +"&lon=" + lon +"&APPID=475769fb9b0f81f6d88460f19de4c169"; 

                        var tr = $("<tr><td class='col-md-9'><span class='_city_name'>"+data.stations[i+j].city+"</span><div class='viewOptionDIV'><h4>Get weather information from</h4><button class='btn btn-default forecastButton' id="+forecast_io+">Forecast.io</button><button class='btn btn-default openweathermapButtons' id="+openweathermap+">OpenWeatherMap.org</button></div></td><td class='col-md-3'><button id="+data.stations[i+j].url+" class='each_city btn btn-info btn-sm'>View Detail</button><div class='viewOptionDIV'><button class='btn btn-default Close' >Close</button></div></td></tr>");
            
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
                            buttons[i].addEventListener('click',viewToggle);

                          })(i)
                      }

                     var buttons = document.getElementsByClassName('Close');

                      for(var i=0;i<buttons.length;i++)
                      {
                          (function(i){                            
                            
                            buttons[i].addEventListener('click',closeDiv);

                          })(i)
                      }
                    var forecastbuttons = document.getElementsByClassName('forecastButton');

                      for(var i=0;i<forecastbuttons.length;i++)
                      {
                          (function(i){

                            forecastbuttons[i].addEventListener('click',viewDetailForecastio);

                          })(i)
                      }

                   var openMapbuttons = document.getElementsByClassName('openweathermapButtons');

                      for(var i=0;i<openMapbuttons.length;i++)
                      {
                          (function(i){

                            openMapbuttons[i].addEventListener('click',viewDetailOpenWeather);

                          })(i)
                      }

                  return table;

               } 

               function viewToggle(e)
               {    
                   $(".viewOptionDIV").css("display","none");
                  $(e.target).parent().parent().find("div").show();
               

               }

                function closeDiv(e)
               {    
                   $(".viewOptionDIV").css("display","none");   
               
               }

                   function viewDetailFunc(e)
                   {
                        
                        e.preventDefault();

                        var url = this.id;

                        console.log(url);

                         var win = makeNewWindow(600,680);

                        $("#stateModal").append(win.fadeIn());

                        self.refs["CityComponent"].showLoading();
 
                       self.refs["CityComponent"].getCityData(url);                   
                             
                   }  

                   function viewDetailForecastio(e)
                   {
                          
                        e.preventDefault();

                        var url = this.id;                       

                        var cityname = $(e.target).parent().parent().find("._city_name").html();                    

                        var win = makeNewWindow(600,680);

                       $("#stateModal").append(win.fadeIn());

                      self.refs["CityComponent"].showLoading();
 
                       self.refs["CityComponent"].getForecastioData(url,cityname);                   
                             
                   }  

                   function viewDetailOpenWeather(e)
                   {
                          
                        e.preventDefault();

                        var url = this.id;                       

                        var cityname = $(e.target).parent().parent().find("._city_name").html();                      

                        var win = makeNewWindow(600,680);

                       $("#stateModal").append(win.fadeIn());

                      self.refs["CityComponent"].showLoading();
 
                       self.refs["CityComponent"].getOpenWeatherData(url,cityname);                   
                             
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
                 

                   if(buttonStatus=="login" && data!=value)
                  {

                    toastr.error("Login Unsuccessful. Please Register First.");
                    $('#loginUser').html("Login Unsuccessful.");
                  }                   

                  if(buttonStatus=="register"&& data == "Registration Successful")
                  {
                     toastr.success("Your account has been created. You can log in now.");
                      $('#loginUser').html(data);
                  }

                  if(buttonStatus=="register"&& data != "Registration Successful"){
                  toastr.error("Registration Unsuccessful. Please Enter valid User Id");
                  $('#loginUser').html("Registration Unsuccessful. Please Enter a valid User Id");
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
  <div id="inputStyle">
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
                <li className="large"><a className="stateLink Antarctica" href="Antarctica">Antarctica</a></li>
                <div id="myStation">
                     <NearStationComponent/>
                </div>
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
            _temp:true,
            _hum:false,
            _wind:false,
            _cloud:false,
            _pressure:false,
            _value:"",
            _temp_arr:[],
            _hum_arr:[],
            _wind_arr:[],
            _cloud_arr:[],
            _pressure_arr:[],
            _label_arr:[],
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
            ],
              dummyFORECASTDATAURL:[
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-15.51,123.16",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-34.94,117.82",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-35.03,117.88",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-16.64,128.45",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-30.34,115.54",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-32.46,123.87",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-22.67,119.16",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-20.88,115.41",
                 "https://api.forecast.io/forecast/ce608d65c49616e17f2994b31a5f5c18/-19.59,119.10"
            ]

        }
    },

    renderCityDetail(e)
    {
          e.preventDefault();
          var url = e.target.id;   
          var city = e.target.name;           
          var self = this;      
          var bom = url.indexOf("bom.gov.au");
          var forecast = url.indexOf("api.forecast.io");
          var openweather = url.indexOf("openweathermap.org");
          var info;          
       
          if(bom != -1){
            info = "BOM";
          }

          if(forecast != -1){
            info = "Forecast.io";
          }

          if(openweather != -1){
            info = "OpenWeather";
          }          
 
          this.setState({
            url:url,
            info:info,
            city:city,
            _temp:true,
            _hum:false,
            _wind:false,
            _cloud:false,
            _pressure:false 
          });     

          this.renderCityByUrl(url,info,city);

          setTimeout(function(){
            $('.myFavouritesUL').slideUp();
          },1500); 
    },

      refreshDetail(e)
      {
          e.preventDefault();          
          var self = this; 
          var url = self.state.url;  
          var info = self.state.info;
          var cityname = self.state.city;
          var value = self.state._value;   
          var data = [];            
        
          if(info =="BOM"){  
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
              module().getSimpleGragh(data,self,self.refs["loadingBar"],50,"CityDetailChart"); 
              self.refs["loadingBar"].hide();               
            }
           
          });         

          }else{    
         
          self.refs['loadingBar'].show();        
           
              $.ajax({

                url:"/WeatherController/getEachStationJSON",
                type:"POST",
                data:{url:url},
                dataType:"json",
                success:function(data)
                {            
                  self.refs['loadingBar'].show();
                  var newObject;
                  newObject = module2().parseData(data);               
                   module2().getSimpleGragh(cityname,newObject,self,self.refs["loadingBar"],50,"CityDetailChart");               
                  self.refs["loadingBar"].hide();                    
                }   
              });
             this.setState({
                  _temp:true,
                  _hum:false,
                  _wind:false,
                  _cloud:false,
                  _pressure:false
              })           
            self.refs["loadingBar"].hide();          
         }
      },   

    renderCityByUrl(url,info,city,currentCity)
    {

        var self = this;

        if(info == "BOM")
        {   
            $(".viewPanel").css("display","none");
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
              self.refs['loadingBar'].show();               
            
              module().getSimpleGragh(data,self,null,50,"CityDetailChart",currentCity);
              self.refs['loadingBar'].hide();
 
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
          }else if (info == "Forecast.io"){
          $(".viewPanel").css("display","inline");
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

              self.refs['loadingBar'].show();  

              var newObject;

              newObject = module2().parseData(data);          

              module2().getSimpleGragh(city,newObject,self,null,50,"CityDetailChart",currentCity);
              self.refs['loadingBar'].hide();
 
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
          }else{
           $(".viewPanel").css("display","none");
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

              self.refs['loadingBar'].show();  

              var newObject;

              newObject = module2().parseData(data);          

              module2().getSimpleGragh(city,newObject,self,null,50,"CityDetailChart",currentCity);
              self.refs['loadingBar'].hide();
 
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
          }
               
    },

     renderCityByTheUrl(url,info,city,currentCity,date)
    {

        var self = this;

        if(info == "BOM")
        {   
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
              self.refs['loadingBar'].show();               
            
              module().getSimpleGragh(data,self,null,50,"CityDetailChart",currentCity);
              self.refs['loadingBar'].hide();

              self.setState({
                    date:date
              }) 
 
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
          }else{

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

              self.refs['loadingBar'].show();  

              var newObject;

              newObject = module2().parseData(data);          

              module2().getSimpleGragh(city,newObject,self,null,50,"CityDetailChart",currentCity);
              self.refs['loadingBar'].hide();

              self.setState({
                    date:date
              }) 
 
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
          }       
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
                  dateFormat: 'DD d MM yy',
                  duration: 'fast',
                  stepMonths: 0,
                  showOn: "button",
                  buttonText: "day",
                  minDate : "-9",
                  maxDate:"0",
                  onSelect:function(date)
                  { 
                      self.refs['loadingBar'].show();
                      self.refreshChart(date,todayDate);
                      self.refs['loadingBar'].hide();   
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
                  dateFormat: 'DD d MM yy',
                  duration: 'fast',
                  stepMonths: 0,
                  showOn: "button",
                  buttonText: "day",
                  minDate : "0",
                  maxDate:"9",
                  onSelect:function(date)
                  {
                      self.refs['loadingBar'].show();
                      self.refreshChart(date,todayDate);
                      self.refs['loadingBar'].hide();  
                                        
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
        var info = this.state.info;
        if(info =="BOM"){
         var s1 = date.split(' ');  
         var parsedDate = parseInt(s1[1]); 
         var currentCity = {
             city:this.state.city,
             state:this.state.state,
             date:this.state.date
          };         

          this.setState({
                  _temp:true,
                  _hum:false,
                  _wind:false,
                  _cloud:false,
                  _pressure:false
              })

        if(todayDate==parsedDate)
        {
            this.renderCityByUrl(this.state.url,this.state.info,this.state.city);          
        }
       else
        {  
            var ran = parsedDate % 9;
            this.renderCityByTheUrl(this.state.dummyDATAURL[ran],this.state.info,this.state.city,currentCity,date);
        } 
        }else{
            var s1 = date.split(' ');  
         var parsedDate = parseInt(s1[1]); 
         var currentCity = {
             city:this.state.city,
             state:this.state.state,
             date:this.state.date
          };         

          this.setState({
                  _temp:true,
                  _hum:false,
                  _wind:false,
                  _cloud:false,
                  _pressure:false
              })

        if(todayDate==parsedDate)
        {
            this.renderCityByUrl(this.state.url,this.state.info,this.state.city);          
        }
       else
        {  
            var ran = parsedDate % 9;
            this.renderCityByTheUrl(this.state.dummyFORECASTDATAURL[ran],this.state.info,this.state.city,currentCity,date);
        }


        }
    },
    
     handleChange(e)
    {
        var value = e.target.value;        

        this.updateRadio(value);

        var data = [];

        if(value=="_temp")
        {
           data = this.state._temp_arr;
        }

        if(value=="_hum")
        {
           data = this.state._hum_arr;
        }

        if(value=="_wind")
        {
           data = this.state._wind_arr;
        }

        if(value=="_pressure")
        {
           data = this.state._pressure_arr;
        }

        if(value=="_cloud")
        {
           data = this.state._cloud_arr;                   
        }

        module2().getSimpleGragh(null,value,"CityDetailChart",data,this.state._label_arr);
    },  

     updateRadio(value)
    {
        if(value=="_temp")
        {
            this.setState({
               _temp:true,
               _hum:false,
               _wind:false,
               _pressure:false,
               _cloud:false,
               _value:"_temp"
            })            
        }
      else if(value=="_cloud")
      {
            this.setState({
               _temp:false,
               _hum:false,
               _wind:false,
               _pressure:false,
               _cloud:true,
               _value:"_cloud"
            })
      }   
      else if(value=="_hum")
      {
            this.setState({
               _temp:false,
               _hum:true,
               _wind:false,
               _pressure:false,
               _cloud:false,
               _value:"_hum"
            })
      }   
      else if(value=="_pressure")
      {
            this.setState({
               _temp:false,
               _hum:false,
               _wind:false,
               _pressure:true,
               _cloud:false,
               _value:"_pressure"
            })
      }   
      else if(value=="_wind")
      {
            this.setState({
               _temp:false,
               _hum:false,
               _wind:true,
               _pressure:false,
               _cloud:false,
               _value:"_wind"
            })
      }   
    },

    render()
    {
       var myFavourites;
       var self = this; 

       if(typeof this.state.myFavourites == "object" && this.state.myFavourites.length>0)
       {
              myFavourites = this.state.myFavourites.map(function(data,index){
              return <li className="list-group-item" key={index}><a onClick={self.renderCityDetail} className='favouriteLinks' id={data.url} name={data.city} href="#">{data.city}</a><button id={data.city} onClick={self.removeFavor} className='favouritebuttons btn btn-default btn-sm'>Delete</button></li>
          })
       }
        return ( 

           <div>     
        <div className="myFavouritesWrapper"><button onClick={this.toggleMenu} className="btn btn-default btn-sm">My Favourites <span className="favouritesCounter">{this.state.myFavourites.length}</span></button><ul className="myFavouritesUL list-group">{myFavourites}</ul></div>
              
              <div className="animated fadeIn" id="CityChartWrapper">
              <RenderLoading ref='loadingBar'/>
              <div id="hiddenField3"></div>
              <div id="hiddenField4"></div>     
                <div className="cityInfoWrapper">  
                  <span className='closeButton'><button className='btn btn-default btn-sm'>Close</button></span>               
                 <p className="stateID">{this.state.city}
                  <span className='refreshButton'><button className='btn btn-default btn-sm' onClick={this.refreshDetail}>Refresh</button></span></p>
                  <p className="city"><span class="infoID">
                    {(() => {
                    switch (this.state.info){
                    case "BOM" : return "This information is recieved from BOM";
                    case "Forecast.io" : return "This information is recieved from Forecast.io";   
                    case "OpenWeather" : return "This information is recieved from OpenWeatherMap.org";         
                  }
                  })()} </span>  
                 </p>
                   <button type="button" onClick={this.showPrevCalendar} className="btn btn-default favouriteLeftButton" aria-label="Left Align">
                  <span className="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>Previous Days
                  </button>
                 <button type="button" onClick={this.showNextCalendar} className="btn btn-default favouriteRightButton" aria-label="Left Align">Forecasts
                  <span className="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                 </button>      
               <div className="detailsWrapper">  
                <div className="FavouriteDetails">            
                 <p className="date">{this.state.date} <span className="time">{this.state.time}</span></p>
                 <p className="summary">
                 {(() => {
                    switch (this.state.info){                  
                    case "Forecast.io" : return this.state.summary==""?"":"Summary : " + this.state.summary; 
                    case "OpenWeather" : return this.state.summary==""?"":"Summary : " + this.state.summary;           
                  }
                  })()} 

                 </p> 
                 <p className="cloudy">
                 {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.cloudy=="-"?"": "Summary :" + this.state.cloudy;
                    case "Forecast.io" : return this.state.cloudy=="-"?"":"Cloud Cover : " + this.state.cloudy; 
                    case "OpenWeather" : return this.state.cloudy=="-"?"":"Cloud Cover : " + this.state.cloudy;           
                  }
                  })()} 

                 </p>  
                 <p className="humidity">
                   {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.humidity==null?"":"Humidity : " + this.state.humidity +"%";
                    case "Forecast.io" : return this.state.humidity==null?"":"Humidity : " + this.state.humidity +"%";   
                    case "OpenWeather" : return this.state.humidity==null?"":"Humidity : " + this.state.humidity +"%";         
                  }
                  })()} 
                 </p> 
                 <p className="temp">
                   {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.temp==null?"":"Temp : " + this.state.temp +" C";
                    case "Forecast.io" : return this.state.temp==null?"":"Current Temp : " + this.state.temp +" C"; 
                    case "OpenWeather" : return this.state.temp==null?"":"Current Temp : " + this.state.temp +" Fahrenheit";  
                  }
                  })()} 
                 </p>  
                 <p className="wind">
                    {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.wind==0?"":"Wind : " + this.state.wind + " KM per hour";
                    case "Forecast.io" : return this.state.wind==0?"":"Current Wind : " + this.state.wind + " KM per hour"; 
                    case "OpenWeather" : return this.state.wind==0?"":"Current Wind : " + this.state.wind + " Miles per hour";                               
                  }
                  })()} 
                 </p> 
                 <p className="min_temp">
                 {(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.min_temp==0?"":"Min Temp : " + this.state.min_temp +" C";
                    case "Forecast.io" : return this.state.min_temp==0?"":"Min Temp : " + this.state.min_temp +" C"; 
                    case "OpenWeather" : return this.state.min_temp==0?"":"Min Temp : " + this.state.min_temp +" Fahrenheit";  
                  }
                  })()}                   
                  </p> 
                 <p className="max_temp">{(() => {
                    switch (this.state.info){
                    case "BOM" : return   this.state.max_temp==0?"":"Max Temp : " + this.state.max_temp +" C";
                    case "Forecast.io" : return this.state.max_temp==0?"":"Max Temp : " + this.state.max_temp +" C"; 
                    case "OpenWeather" : return this.state.max_temp==0?"":"Max Temp : " + this.state.max_temp +" Fahrenheit";  
                  }
                  })()}
                  </p> 
                 </div>

                 <div className="viewPanel">
                     <div>
                        <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._temp} onChange={this.handleChange} value="_temp" name="viewPanel"/>Apprent Temperature</label>
                        </div>
                        <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._hum} onChange={this.handleChange} value="_hum" name="viewPanel"/>Humidity</label>
                        </div>
                        <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._wind} onChange={this.handleChange} value="_wind" name="viewPanel"/>Wind</label>
                        </div>
                         <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._cloud} onChange={this.handleChange} value="_cloud" name="viewPanel"/>Cloud</label>
                        </div>
                        <div className="radio">
                          <label><input className="radioView" type="radio" checked={this.state._pressure} onChange={this.handleChange} value="_pressure" name="viewPanel"/>Pressure</label>
                        </div>                       
                     </div>
                 </div>
                </div>
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

/*
*   Find nearest station from a user's location
*   get a user's latitude and longitude 
*   then calculate the closest two points
*/

  var NearStationComponent = React.createClass({

    getInitialState()
    { 
       return {
          currentState:"loading"
       }  
    },

    componentWillMount()
    {
        var self = this;

        $.getJSON("http://ip-api.com/json/", function(data) {
 
              var state = data.regionName;
              var lat = data.lat.toFixed(2);
              var lon = data.lon.toFixed(2);

              $.ajax({

                url:"/WeatherController/getCities",
                type:"POST",
                dataType:"JSON",
                data:{state:state},
                success:function(data2)
                {
                     var stations = data2.stations;
                     var target = { 

                        "city":"Target",
                        "lat": lat,
                        "lon": lon
                      };

                       var resultArray = [];
 
                       for(var i=0;i<stations.length;i++)
                       {
                           var obj = {};
                           var tmp = Math.sqrt(Math.pow(stations[i].lat-target.lat,2) + Math.pow(stations[i].lon-target.lon,2));
 
                           obj.city = stations[i].city;
                           obj.distance = tmp.toFixed(2);
                           obj.url = stations[i].url;

                           resultArray.push(obj);
                       }

                      var nearestStation = resultArray.sort(sortByKey("distance"))[0];

                        $.ajax({

                           url:"/WeatherController/getEachStationJSON",
                           type:"POST",
                           dataType:"JSON",
                           data:{url:nearestStation.url},
                           success:function(data3)
                           {
                              var header = data3.observations.header[0];
                              var time = header.refresh_message.substr(header.refresh_message.indexOf("Issued at")+10,9).trim();
                              var date = header.refresh_message.substr(header.refresh_message.indexOf("m")+6).trim();
                              var city = header.name;
                              var state = header.state;
                              var cloudy = data3.observations.data[0].cloud==undefined?"":data3.observations.data[0].cloud;
                              var humidity = data3.observations.data[0].rel_hum;
                              var temp = data3.observations.data[0].air_temp;
                              var wind = data3.observations.data[0].wind_spd_kmh;
                              var min_temp = data3.observations.data[0].air_temp || 0;
                              var max_temp = data3.observations.data[0].air_temp || 0;

                               var render = <div id = "myCityDiv">
                                 <p id="myCity">{city}</p>
                                 <p className="date">{date} <span className="time">{time}</span></p> 
                                 <p className="cloudy">{cloudy=="-"?"": cloudy}</p> 
                                 <p className="humidity">{humidity==null?"":"Humidity " + humidity +"%"}</p> 
                                 <p className="temp">{temp==null?"":"Temp " + temp +" C"}</p> 
                                 <p className="wind">{wind==0?"":"Wind " + wind}</p> 
                                </div>

                                self.setState({

                                  currentState:render

                                });
                           }
                        })
                }
              });   
              });
                    function sortByKey(key) {  
                        return function(a, b) {  
                            if (a[key] > b[key]) {  
                                return 1;  
                            } else if (a[key] < b[key]) {  
                                return -1;  
                            }  
                            return 0;  
                        }  
                    }  
    },

    render()
    {
        if(this.state.currentState == "loading")
        {
            var render = <img id="loadingGif" src="/public/images/loading.gif"/>
        }
      else
        {
            var render = this.state.currentState;
        }  

        return (
 
            <li className="ex_large">
                <div className="nearStationWrapper">{render}</div> 
            </li>
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

          $(".container").click(function(){
             $("#hiddenField").css("display","none");
             $("#hiddenField2").css("display","none");
             $("#hiddenField3").css("display","none");
             $("#hiddenField4").css("display","none");
          });

          $(".modal-content").click(function(){
              $("#hiddenField").css("display","none");
              $("#hiddenField2").css("display","none");
          });

          $("#detailsWrap").click(function(){
              $("#hiddenField").css("display","none");
              $("#hiddenField2").css("display","none");
          });            

});

</script>
</head>
<body>   
    <div class="container-fluid"> 
      <div id="App"></div>      
        <footer class="footer">
          <div class="container">              
          <p id="footerFloat2">Copyright &copy; 2016 </p>
          <p id ="footerFloat">Australian Weather App</p>
          </div> 
      </footer>
    </div>
</body>
</html>