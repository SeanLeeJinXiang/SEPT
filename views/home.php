<script src="https://code.jquery.com/jquery-2.2.2.js"></script>
<script src="https://fb.me/react-0.14.2.js"></script>
<script src="https://fb.me/react-dom-0.14.2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.34/browser.js"></script>
<script type="text/babel">

  var MainWrapper = React.createClass({

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

  			<div>Hello</div>

  			);
  	}

  });
  	
ReactDOM.render(<MainWrapper/>,document.getElementById('App'));

</script>
<body>
<div id="App">
</div>
</body>