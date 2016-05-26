
/*
*  @author Jihun Nam
*  @param weather data in json format 
*  @return render chart using chart.js
*/

  var module = function()
  {

  	  return {

  	getSimpleGragh:function(data,reactObj,loadingBar,numberofData,chartHolerName,currentCity)
  	{
 
			        if(data.observations.data.length>0)
              {
 
                var header = data.observations.header[0];
                var time = header.refresh_message.substr(header.refresh_message.indexOf("Issued at")+10,9).trim();
                var date = header.refresh_message.substr(header.refresh_message.indexOf("m")+6).trim();
                var city = header.name;
                var state = header.state;
                var cloudy = data.observations.data[0].cloud==undefined?"":data.observations.data[0].cloud;
                var humidity = data.observations.data[0].rel_hum;
                var temp = data.observations.data[0].air_temp;
                var wind = data.observations.data[0].wind_spd_kmh;
                var min_temp = data.observations.data[0].air_temp || 0;
                var max_temp = data.observations.data[0].air_temp || 0;
                var dataInterval = 0;
                var totalNumberOfData = numberofData;
                var dataLabels = [];
                var airTemp = [];
                var apparentTemp = [];
                var totalDataLength = data.observations.data.length;
                var localTime = "0";

               dataInterval = Math.ceil(data.observations.data.length/totalNumberOfData);

               for(var i=0;i<data.observations.data.length;i++)
               {
               	   if(data.observations.data[i].air_temp<min_temp)
               	   {
               	   	  min_temp = data.observations.data[i].air_temp;
               	   }

               	   if(data.observations.data[i].air_temp>max_temp)
               	   {
               	   	  max_temp = data.observations.data[i].air_temp;
               	   }

                    if(totalDataLength>totalNumberOfData && chartHolerName == "myChart")
                    {
                      if(i%dataInterval==0)
                      {
                          dataLabels.push(data.observations.data[i].local_date_time);
                          airTemp.push(data.observations.data[i].air_temp);
                          apparentTemp.push(data.observations.data[i].apparent_t);
                      }
                    }
                  else{

                    var DATE = data.observations.data[i].local_date_time.substring(0,data.observations.data[i].local_date_time.indexOf("/"));

                   if((localTime == DATE) || (localTime=="0"))
                   {
                       localTime = data.observations.data[i].local_date_time.substring(0,data.observations.data[i].local_date_time.indexOf("/"));

                    dataLabels.push(data.observations.data[i].local_date_time);
                    airTemp.push(data.observations.data[i].air_temp);
                    apparentTemp.push(data.observations.data[i].apparent_t);

                   }
                 else
                   {
                      break;
                   }  

                  }
               }

               if(currentCity!=undefined)
               {
                   state = currentCity.state;
                   city = currentCity.city;
                   date = null;
               }

 
                reactObj.setState({
                  state:state,
                  city:city,
                  date:date,
                  cloudy:cloudy,
                  humidity:humidity,
                  temp:temp,
                  wind:wind,
                  time:time,
                  min_temp:min_temp,
                  max_temp:max_temp
                });

      


              var weatherData = {
                  datasets: [
                      {
                          label: "Air Temp",
                          fillColor: "rgba(151,187,205,0.2)",
                          strokeColor: "rgba(122, 169, 214, 1)",
                          pointColor: "rgba(122, 169, 214, 1)",
                          pointStrokeColor: "#fff",
                          pointHighlightFill: "#fff",
                          pointHighlightStroke: "rgba(151,187,205,1)",
                      },
                      {
                          label: "Apparent Temp",
                          fillColor: "rgba(151,187,205,0.2)",
                          strokeColor: "rgba(122, 169, 70, 0.9)",
                          pointColor: "rgba(122, 169, 70, 0.9)",
                          pointStrokeColor: "#fff",
                          pointHighlightFill: "#fff",
                          pointHighlightStroke: "rgba(151,187,205,1)",
                      }
                  ]
              };

                    weatherData.labels = dataLabels;
                    weatherData.datasets[0].data = airTemp;
                    weatherData.datasets[1].data = apparentTemp;
   

               
           


                var canvas = document.getElementById(chartHolerName);
                var context = canvas.getContext("2d");
                window.myLineChart; 
 				        window.myLineChart && window.myLineChart.destroy();
                window.myLineChart = new Chart(context).Line(weatherData, null);

               if(loadingBar!=null)
               {
                 loadingBar.hide();  	
               }
  	}

  }

}
  };

 

 var module2 = function()
  {

      return {


      parseData:function(data)
      {

         var object = {

            "observations":{

              "header":[],
              "data":[]

            }

         }

         if(data.currently)
         {    
              // forecast.io data
 
             var dt = new Date(data.currently.time*1000);
             var localTime = dt.toLocaleString();

              object.observations.header.push({
                "refresh_message":localTime,
                "cloud":data.currently.cloudCover,
			        	"summary":data.currently.summary,
                "rel_hum":  data.currently.humidity,
                "air_temp": Number((5/9) * (data.currently.temperature-32)).toFixed(2),
                "wind_spd_kmh": data.currently.windSpeed
              });
 
              for(var i=0;i<data.hourly.data.length;i++)
              {
                  object.observations.data.push({

                  "local_date_time": new Date(data.hourly.data[i].time*1000).toLocaleString(),
                  "apparent_t": Number((5/9) * (data.hourly.data[i].apparentTemperature-32)).toFixed(2),  
                  "air_temp":Number((5/9) * (data.hourly.data[i].temperature-32)).toFixed(2),
                  "cloudCover":data.hourly.data[i].cloudCover,
                  "humidity":data.hourly.data[i].humidity,
                  "pressure":data.hourly.data[i].pressure,
                  "windSpeed":data.hourly.data[i].windSpeed
                  });
              }


         }
       else
         {
             
             var dataLength = data.list.length;
             var dt = new Date(data.list[0].dt*1000);
             var localTime = dt.toLocaleString();

              object.observations.header.push({
                "refresh_message":localTime,
                "cloud":data.list[0].clouds.all,
				        "summary":data.list[0].weather[0].description,
                "rel_hum":  data.list[0].main.humidity,
                "air_temp": Number((5/9) * (data.list[0].main.temp-32)).toFixed(2),
                "wind_spd_kmh": data.list[0].wind.speed
              });
 
              for(var i=0;i<dataLength;i++)
              {
                  object.observations.data.push({

                  "local_date_time": new Date(data.list[i].dt*1000).toLocaleString(),
                  "apparent_t": Number((5/9) * (data.list[i].main.temp-32)).toFixed(2),  
                  "air_temp":Number((5/9) * (data.list[i].main.temp-32)).toFixed(2)

                  });
              }

         }  
 
            return object;
 
      },    

    getSimpleGragh:function(cityname,data,reactObj,loadingBar,numberofData,chartHolerName,currentCity,viewPanel)
    {
          var graphData = [];

          if(cityname==null)
          {
              var value = data;
              chartHolerName = reactObj;
              graphData = loadingBar;
              var dataLabels = numberofData;

          }
        else
          {  
              if(data.observations.data.length>0)
              {
 
                var header = data.observations.header[0];
                var time = header.refresh_message.split(",")[1];
                var date = header.refresh_message.split(",")[0];
                var city = cityname;
                var state = header.state;
                var cloudy = header.cloud==undefined?"":header.cloud;
				var summary = header.summary==undefined?"":header.summary;
                var humidity = header.rel_hum;
                var temp = header.air_temp;
                var wind = header.wind_spd_kmh;
                var min_temp = data.observations.data[0].air_temp || 0;
                var max_temp = data.observations.data[0].air_temp || 0;
                var dataInterval = 0;
                var totalNumberOfData = numberofData;
                var dataLabels = [];
                var airTemp = [];
                var apparentTemp = [];
                var humidity_arr = [];
                var wind_arr = [];
                var clouds = [];
                var pressures = [];
                var totalDataLength = data.observations.data.length;
                var localTime = "0";

               dataInterval = Math.ceil(data.observations.data.length/totalNumberOfData);

               for(var i=0;i<data.observations.data.length;i++)
               {
                   if(data.observations.data[i].air_temp<min_temp)
                   {
                      min_temp = data.observations.data[i].air_temp;
                   }

                   if(data.observations.data[i].air_temp>max_temp)
                   {
                      max_temp = data.observations.data[i].air_temp;
                   }

                    if(totalDataLength>totalNumberOfData && chartHolerName == "myChart")
                    {	
					

                      if(i%dataInterval==0)
                      {				  
					  
                          dataLabels.push(data.observations.data[i].local_date_time);
                          graphData.push(data.observations.data[i].air_temp);
                          apparentTemp.push(data.observations.data[i].apparent_t);
						  humidity_arr.push(data.observations.data[i].humidity);
						  wind_arr.push(data.observations.data[i].windSpeed);
						  clouds.push(data.observations.data[i].cloudCover);
						  pressures.push(data.observations.data[i].pressure);
                      }

                    }
                  else{

                    var DATE = data.observations.data[i].local_date_time.substring(0,data.observations.data[i].local_date_time.indexOf("/"));

                   if((localTime == DATE) || (localTime=="0"))
                   {
                       localTime = data.observations.data[i].local_date_time.substring(0,data.observations.data[i].local_date_time.indexOf("/"));

                    dataLabels.push(data.observations.data[i].local_date_time);
                    graphData.push(data.observations.data[i].air_temp);
                    apparentTemp.push(data.observations.data[i].apparent_t);
                    humidity_arr.push(data.observations.data[i].humidity);
                    wind_arr.push(data.observations.data[i].windSpeed);
                    clouds.push(data.observations.data[i].cloudCover);
                    pressures.push(data.observations.data[i].pressure);

                   }
                 else
                   {
                      break;
                   }  

                  }
               }

               if(currentCity!=undefined)
               {
                   state = currentCity.state;
                   city = currentCity.city;
                   date = null;
               }
 
              if(chartHolerName=="CityDetailChart" || "myChart")
              {

                  reactObj.setState({
                    state:state,
                    city:city,
                    date:date,
                    cloudy:cloudy,
                    summary:summary,
                    humidity:humidity,
                    temp:temp,
                    wind:wind,
                    time:time,
                    min_temp:min_temp,
                    max_temp:max_temp,
                    _temp_arr:apparentTemp,
                    _hum_arr:humidity_arr,
                    _wind_arr:wind_arr,
                    _cloud_arr:clouds,
                    _pressure_arr:pressures,
                    _label_arr:dataLabels
                  });

              }
             else
             {
                reactObj.setState({
                  state:state,
                  city:city,
                  date:date,
                  cloudy:cloudy,
                  summary:summary,
                  humidity:humidity,
                  temp:temp,
                  wind:wind,
                  time:time,
                  min_temp:min_temp,
                  max_temp:max_temp
                });

             }
 
             }
 
    }

             var weatherData = {
                  datasets: [
                      {
                          label: "Air Temp",
                          fillColor: "rgba(151,187,205,0.2)",
                          strokeColor: "rgba(122, 169, 214, 1)",
                          pointColor: "rgba(122, 169, 214, 1)",
                          pointStrokeColor: "#fff",
                          pointHighlightFill: "#fff",
                          pointHighlightStroke: "rgba(151,187,205,1)",
                      }
                  ]
              };

                    weatherData.labels = dataLabels;
                    weatherData.datasets[0].data = graphData;
 
                var canvas = document.getElementById(chartHolerName);
                var context = canvas.getContext("2d");
                window.myLineChart; 
                window.myLineChart && window.myLineChart.destroy();
                window.myLineChart = new Chart(context).Line(weatherData, null);

               if(chartHolerName=="CityDetailChart" && cityname!=null)
               {

                 if(loadingBar!=null)
                 {
                   loadingBar.hide();   
                 }

               }
  }

}
  }
 

  


  

