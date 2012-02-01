function initPieGraph(id, data)
{
  $.jqplot(id, data,{
	  gridPadding: {top:0, bottom:38, left:0, right:0},
      seriesDefaults:{
		renderer:$.jqplot.PieRenderer, 
		trendline:{
		  show:false
		}, 
		rendererOptions: { 
		  padding: 8, 
		  showDataLabels: true
		}
	  },
      legend:{
		show:true, 
        placement: 'outside', 
        rendererOptions: {
		  numberRows: 1
        }, 
        location:'s',
        marginTop: '15px'
      } 
  });
}

var myAxisGraphList = new Array();

function resetZoom(id)
{
  myAxisGraphList[parseInt(id)].resetZoom();
  return true;
}

function initAxisGraph(id, data, myNumber)
{
  
  myAxisGraphList[parseInt(myNumber)] = $.jqplot (id, data, {
      // Give the plot a title.
      title: 'Historia del Elo Rating',
      // You can specify options for all axes on the plot at once with
      // the axesDefaults object.  Here, we're using a canvas renderer
      // to draw the axis label which allows rotated text.
      axesDefaults: {
        labelRenderer: $.jqplot.LogAxisRenderer
      },
      // An axes object holds options for all axes.
      // Allowable axes are xaxis, x2axis, yaxis, y2axis, y3axis, ...
      // Up to 9 y axes are supported.
      axes: {
        // options for each axis are specified in seperate option objects.
        xaxis: {
          label: "Partidos",
          // Turn off "padding".  This will allow data point to lie on the
          // edges of the grid.  Default padding is 1.2 and will keep all
          // points inside the bounds of the grid.
          pad: 0
        },
        yaxis: {
          label: "Puntos"
        }
      },
      cursor:{
        zoom:true
        },
      highlighter:{
        show:true
        }
    });
}
