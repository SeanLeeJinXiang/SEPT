
/*
*  @author Jihun Nam
*  @param weather data in json format 
*  @return render chart using chart.js
*/

  var module = function()
  {

  	  return {

  	getSimpleGragh:function(data,reactObj,loadingBar,numberofData,chartHolerName)
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

                    if(totalDataLength>totalNumberOfData)
                    {

                      if(i%dataInterval==0)
                      {
                          dataLabels.push(data.observations.data[i].local_date_time);
                          airTemp.push(data.observations.data[i].air_temp);
                          apparentTemp.push(data.observations.data[i].apparent_t);
                      }
                    }
                   else
                    { 
                          dataLabels.push(data.observations.data[i].local_date_time);
                          airTemp.push(data.observations.data[i].air_temp);
                          apparentTemp.push(data.observations.data[i].apparent_t);
                    } 
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
  }

 

  

