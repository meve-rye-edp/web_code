<?php

?>
<script type="text/javascript">
 $(document).ready(function(){
   s1 = [52200];
		 
	$.jqplot('chartdiv',  [[[1, 2],[3,5.12],[5,13.1],[7,33.6],[9,85.9],[11,219.9]]],
		{ title:'Test Graph',
		  axes:{yaxis:{renderer: $.jqplot.LogAxisRenderer}},
		  series:[{color:'#5FAB78'}]
		});
		
	$.jqplot('chartdiv2',  [[[1, 2],[3,5.12],[5,13.1],[7,33.6],[9,85.9],[11,219.9]]],
		{ title:'Test Graph 2',
		  axes:{yaxis:{renderer: $.jqplot.LogAxisRenderer}},
		  series:[{color:'#5FAB78'}]
		});
});
</script>
<div id="chartdiv"></div>
<div id="chartdiv2"></div>


