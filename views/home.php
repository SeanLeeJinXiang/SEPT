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
 
<script src="https://code.jquery.com/jquery-2.2.2.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="https://fb.me/react-0.14.2.js"></script>
<script src="https://fb.me/react-dom-0.14.2.js"></script>
<script src="https://npmcdn.com/react-router/umd/ReactRouter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.34/browser.js"></script>

<script src="public/js/raphael.min.js" type="text/javascript"></script>
<script src="public/js/scale.raphael.js" type="text/javascript"></script>
<script src="public/js/paths.js" type="text/javascript"></script>
<script src="public/js/init.js" type="text/javascript"></script>
<script type="text/babel">

  var MainWrapper = React.createClass({

    componentDidMount()
    {

      // testing for generating contents and rendering

      if(window.location.href=="http://localhost/WA")
      {
          $.ajax({

            url:"/HomeController/getCities",
            type:"POST",
            data:{state:"Antarctica"},
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

                        console.log(win);
                   }  


               function makeNewWindow(width,height)
               {
                   var win = $("<div id='city_view_detail'></div>");
                  
                   win.css({
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

  	componentWillMount()
  	{
  		$.ajax({

  			url:"/HomeController/getEachStationJSON",
  			type:"POST",
  			data:{url:'http://www.bom.gov.au/fwo/IDN60901/IDN60901.94768.json'},
  			dataType:"json",
  			success:function(data)
  			{
  				console.log(data);
  			}

  		});
  	},

  	render()
  	{
  		return(

  			<div>
          <div id="stateModal" className="modal fade" role="dialog">
            <div className="modal-dialog">
 
              <div className="modal-content">
                <div className="modal-header">
                  <button type="button" className="close" data-dismiss="modal">&times;</button>
                  <h4 className="modal-title">Modal Header</h4>
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

ReactDOM.render(<MainWrapper/>,document.getElementById('App'));
 
 

</script>
<body>
 
  
    <div id="container">
    
        <div class="mapWrapper">
                <div id="map"></div>
                <div id="text"></div>
        </div>
        
    </div>

    <div id="App">




    </div>



</body>

 