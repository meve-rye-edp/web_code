<?php

?>
<style>
    
    @media all and (max-width: 50em) {
	.my-breakpoint .ui-block-a, 
	.my-breakpoint .ui-block-b, 
	.my-breakpoint .ui-block-c,
	.my-breakpoint .ui-block-d,
	.my-breakpoint .ui-block-e { 
		width: 100%; 
		float:none; 
	}
}
</style>

<script type="text/javascript">
    
       
        $("document").ready(function () {
				var letter_array = new Array("A", "B");
				var output = '';
				var id="";
                for(var i = 0; i < 6; i++){
					output += '<div class="ui-grid-a my-breakpoint">'
					for(var j = 0; j < 2; j++){
						output += '<div class="ui-block-b"><div class="ui-bar ui-bar-c" style="height:100% width:100%; margin:0px;">'
						if(bat_info[2*i+j]["key"] == "State of Charge"){
							output +='<div id = "item_soc"><strong>'+ bat_info[2*i+j]["key"] +'</strong>: '+ bat_info[2*i+j]["value"]+'</div>'
						}else{
							output +='<div><strong>'+ bat_info[2*i+j]["key"] +'</strong>: '+ bat_info[2*i+j]["value"]+'</div>'
						}
						
						output +='</div></div>'
					}
					output += '</div>'
                }
				$('#bat_info_grid').append(output).trigger('create');
				var current_soc = parseInt(bat_info[0]["value"].substring(0,2))
				if(current_soc > 70){
					$("#item_soc").css("color", "green").show();
				}else if(current_soc < 70 && current_soc > 40){
					$("#item_soc").css("color", "orange").show();
				}else{
					$("#item_soc").css("color", "red").show();
				}
        });
		
	
        
        
        

</script>
<!--<ul  id="batinfo" data-role="listview" data-filter="true"></ul>-->
<div id="bat_info_grid"></div>
