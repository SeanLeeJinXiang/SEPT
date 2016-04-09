<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Weather App</title>
  
<meta name="description" content="SVG/VML Interactive Australia map">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,900"/>
<link href="public/css/reset.css" rel="stylesheet" type="text/css" />
<link href="public/css/fonts.css" rel="stylesheet" type="text/css" />
<link href="public/css/style.css" rel="stylesheet" type="text/css" />
<link href="public/css/map.css" rel="stylesheet" type="text/css" />
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" type="text/css" />
 
<script src="https://code.jquery.com/jquery-2.2.2.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="https://fb.me/react-0.14.2.js"></script>
<script src="https://fb.me/react-dom-0.14.2.js"></script>
<script src="https://npmcdn.com/react-router/umd/ReactRouter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.34/browser.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js" type="text/javascript"></script>
<script src="public/js/lib/lib.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" type="text/javascript"></script>

<script src="public/js/raphael.min.js" type="text/javascript"></script>
<script src="public/js/scale.raphael.js" type="text/javascript"></script>
<script src="public/js/paths.js" type="text/javascript"></script>
<script src="public/js/init.js" type="text/javascript"></script>
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
            max_temp:0


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
            
            url:"/HomeController/addToFavourite",
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
               else
                {
                   toastr.error(self.state.city + " is already in your favourites");
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

            url:"/HomeController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {

              /*
              *  @param self is referencing current react component
              *  Using chart.js , use received data from BOM site make
              *  an interval if the data objects are more than abound 10
              *  last digit is for how many data objects to be shown
              *  Make a graph and render it
              */

              module().getSimpleGragh(data,self,self.refs["loadingBar"],7,"myChart");
 
              self.refs["loadingBar"].hide(); 
 
            }

          });

      },

      render()
      {
        return (

          <div className='animated fadeIn'>
             <RenderLoading ref='loadingBar'/>
               <div className="cityInfoWrapper">
                 <p className="city">{this.state.city}<button onClick={this.addToFavourite} className='add_to_favourite btn btn-default btn-sm'>Add to Favourite</button></p>
                 <p className="date">{this.state.date} <span className="time">{this.state.time}</span></p> 
                 <p className="cloudy">{ this.state.cloudy=="-"?"": this.state.cloudy }</p> 
                 <p className="humidity">{this.state.humidity==null?"":"Humidity " + this.state.humidity +"%"}</p> 
                 <p className="temp">{this.state.temp==null?"":"Temp " + this.state.temp +" C"}</p> 
                 <p className="wind">{this.state.wind==0?"":"Wind " + this.state.wind}</p> 
              </div>
              <canvas id="myChart" width="570" height="330"></canvas>
          </div>

          );
      }


  });

  var MainWrapper = React.createClass({
 
    componentDidMount()
    {
       
       var url = window.location.pathname.substring(1);
       var state = StateArray[url];

       if(state!=undefined)
       {

         var self = this;

          $.ajax({

            url:"/HomeController/getCities",
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

                  var table = $("<table id='data_table' class='table table-responsive'></table>");

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

                        var win = makeNewWindow(600,600);

                        $("#stateModal").append(win.fadeIn());

                        self.refs["CityComponent"].showLoading();
 
                        self.refs["CityComponent"].getCityData(url);
 
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
 
  	render()
  	{
  		return(

  			<div>

        <FavouriteComponent ref="FavouriteComponent"/>

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

    componentWillMount()
    {
       var self = this;
        $.ajax({

          url:"/HomeController/getFavourites",
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
            max_temp:0

        }
    },

    renderCityDetail(e)
    {
          e.preventDefault();

          var url = e.target.id
          var self = this;
 
          $.ajax({

            url:"/HomeController/getEachStationJSON",
            type:"POST",
            data:{url:url},
            dataType:"json",
            success:function(data)
            {
 
              var wrapper = $("<div id='cityDetailsWrapper'></div>");
              wrapper.css({

                 width:$(document.body).width(),
                 height:$(document.body).height(),
                 position:"absolute",
                 background:"#9C9C9C",
                 top:0,
                 left:0,
                 opacity:"0.3",

              });

              wrapper.css("z-index",10);

              $(document.body).append(wrapper.fadeIn());

              $("#CityChartWrapper").show();

              module().getSimpleGragh(data,self,null,50,"CityDetailChart");
 
               if(self.state.city)
               {
                   $(".cityInfoWrapper .city").html(self.state.city + " " + self.state.state);
               }    

               if(self.state.date)
               {
                   $(".cityInfoWrapper .date").html(self.state.date + "  " + self.state.time);
               }

               if(self.state.cloudy!="-")
               {
                   $(".cityInfoWrapper .cloudy").html(self.state.cloudy);
               }

               if(self.state.humidity)
               {
                   $(".cityInfoWrapper .humidity").html("Humidity " + self.state.humidity);
               }

               if(self.state.temp)
               {
                   $(".cityInfoWrapper .temp").html("Temp " + self.state.temp + " C");
               }

               if(self.state.wind)
               {
                   $(".cityInfoWrapper .wind").html("Wind " + self.state.wind);
               }
    
               if(self.state.min_temp)
               {
                   $(".cityInfoWrapper .min_temp").html("Low Temp " + self.state.min_temp + " C");
               }

               if(self.state.max_temp)
               {
                   $(".cityInfoWrapper .max_temp").html("High Temp " + self.state.max_temp + " C");
               }
 
              $(document.body).append($("#CityChartWrapper"));

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

          url:"/HomeController/removeFavorite",
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

                self.setState({
                  myFavourites:myFavourites
                })


             }
          }

        });
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
        <div className="myFavouritesWrapper"><ul className="list-group"><li className="list-group-item">My Favourites</li>{myFavourites}</ul></div>
              
              <div className="animated fadeIn" id="CityChartWrapper">
                <div className="cityInfoWrapper">
                 <p className="city"></p><span className='closeButton'><button className='btn btn-default btn-sm'>Close</button></span>
                 <p className="date"><span className="time"></span></p> 
                 <p className="cloudy"></p> 
                 <p className="humidity"></p> 
                 <p className="temp"></p> 
                 <p className="wind"></p> 
                 <p className="min_temp"></p> 
                 <p className="max_temp"></p> 
                 <canvas id="CityDetailChart" width="1250" height="600"></canvas>
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
 
 

</script>
<body>
 
   <div class="container-fluid">
    <div class="AusMap col-md-9" id="container">

        <div class="mapWrapper">
                <div id="map"></div>
                <div id="text"></div>
        </div>
            
      <div class="bear"><a href="/Antarctica"><img class="bear_img" src="/public/images/bear.png"></a></div>

    </div>

    <div class="col-md-3" id="App">

    </div>
  </div>


</body>

 