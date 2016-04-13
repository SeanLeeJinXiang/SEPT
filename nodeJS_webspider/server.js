var request = require("request");
var fs = require("fs");
var readline = require("readline");
var mkdirp = require("mkdirp");
var jsonfile = require('jsonfile');
var util = require('util');
var file = './stations.json';
var stations = jsonfile.readFileSync('./stations.json');
var stationsObjec = [];
var j = 0;
jsonfile.spaces = 4;
  	 	
 var stateArray = [

 	{City:"Canberra",URL:"http://www.bom.gov.au/act/observations/canberra.shtml"},
 	{City:"New South Wales",URL:"http://www.bom.gov.au/nsw/observations/nswall.shtml"},
 	{City:"Victoria",URL:"http://www.bom.gov.au/vic/observations/vicall.shtml"},
 	{City:"Queensland",URL:"http://www.bom.gov.au/qld/observations/qldall.shtml"},
 	{City:"South Australia",URL:"http://www.bom.gov.au/sa/observations/saall.shtml"},
 	{City:"Western Australia",URL:"http://www.bom.gov.au/wa/observations/waall.shtml"},
 	{City:"Tasmania",URL:"http://www.bom.gov.au/tas/observations/tasall.shtml"},
 	{City:"Antarctica",URL:"http://www.bom.gov.au/ant/observations/antall.shtml"},
 	{City:"Northern Territory",URL:"http://www.bom.gov.au/nt/observations/ntall.shtml"}


 ];

/*
*   @author Jihun Nam
*   part of the weather app project 
*   get every station's json url from Australia weather website and generate a json file
* 	each city is mapped to its json url and separated by states
*
*	How to use
*   1. Make a folder and make a subfoler named weather
*   2. put this file on root folder and execute 
*    
*   Currently there is s bug that downloadProduct function doesn't finish after downloading stations
*   Bug not fixed yet. 
*/

webSpider(stateArray);
getStations();

function getStations()
{ 
     console.log("start downloading stations....")
  	 fs.readdir("weather",function(err,dir){


  	 	  for(var i=0;i<dir.length;i++)
  	 	  {
  	 	  	  var stationHtmlArrays = fs.readdirSync("./weather/"+dir[i]);
  	 	  	  var eachStationsArray = []; 

  	 	  	  for(var j=0;j<stationHtmlArrays.length;j++)
  	 	  	  {
  	 	  	  	  var url;
  	 	  	  	  fs.readFileSync("./weather/"+dir[i]+"/"+stationHtmlArrays[j])
  	 	  	  	  .toString()
  	 	  	  	  .split('\n')
  	 	  	  	  .forEach(function(line){

		 		 	var reg = /<a href="\/fwo\/\w+\/\w+.\d+.json">/;

		 		 	if(reg.test(line))
		 		 	{
		 		 		url = line.substring(line.indexOf("/fwo"),line.lastIndexOf(".json"));
		 				url = "http://www.bom.gov.au" + url + ".json";
 
		  	 	  	  	  var obj = {

		  	 	  	  	  	city:stationHtmlArrays[j].substring(0,stationHtmlArrays[j].indexOf(".html")),
		  	 	  	  	  	url:url
		  	 	  	  	  };

  	 	  	  			  eachStationsArray.push(obj);		 				 

		 		 	}

  	 	  	  	  });
 

  	 	  	  }
 
  	 	  	 var object = {
  	 	  	  	 state:dir[i],
  	 	  	  	 stations:eachStationsArray
  	 	  	 }
  	 	  	  stationsObjec.push(object);

  	 	  }

  	 	  jsonfile.writeFileSync(file,stationsObjec);

  	 })
}


function webSpider(urlArray,callback)
{

    var iterator = urlArray.length;

	for(var i=0;i<urlArray.length;i++)
	{
		(function(i){

			request(urlArray[i].URL,function(err,response,html){

				if(err) console.log(err);
				iterator--;
				fs.writeFileSync(urlArray[i].City+".html",html);
		 
				if(iterator==0)
				{
					makeFiles();
				}

			});

		})(i);
	}

	function makeFiles()
	{

		for(var i=0;i<stateArray.length;i++)
		{
			(function(i){

		    mkdirp('./weather/'+stateArray[i].City,function(err){

		 	if(err) console.err(err);

		 	 var lineReader = readline.createInterface({
		     input:fs.createReadStream(stateArray[i].City+".html")
             })
		 
		     lineReader.on('line',function(line){

			   var reg = /\/products\/\w+\/\w+.\d+.shtml/; 

			   if(reg.test(line))
			   {
			   		 var illegalSymbol = /\w*\s*\(*\/*\)*\**/g;
					 var lineFound = line;
					 var stationName = lineFound
					 .substring(lineFound.indexOf("shtml\">")+7,lineFound.lastIndexOf("</a>"));

					 if(illegalSymbol.test(stationName))
					 {
					 	stationName = stationName.replace("(","").replace(")","").replace("*","").replace("\/","");
					 }

			         lineFound = line.substring(line.indexOf("/products"),line.lastIndexOf(".shtml"));
					 var eachProductUrl = "http://www.bom.gov.au/" + lineFound + ".shtml";

					 downloadProduct(stateArray[i].City,stationName,eachProductUrl);

			   }


			  })


			 })



			})(i)
		}

	}

function downloadProduct(CityName,stationName,eachProductUrl)
{

	    j++;

		request(eachProductUrl,function(err,response,html){

			if(j==0)
			{
				getStations();
				return;
			}

		  j--;

		  console.log(j);

			if(err)
			{
				j--;
				console.log(err);
			}

			fs.stat("./weather/"+CityName+"/"+stationName+".html",function(err,stat){

				if(err)
				{
					j--;
					console.log(err);
				}

				if(err==null)
				{
					console.log("file exists");
				}
			  else if(err.code=='ENOENT')
			  {
					fs.writeFile("./weather/"+CityName+"/"+stationName+".html",html,function(err){
						if(err) console.log(err);
					});
			  }	

			})
 
		});
	}
}
