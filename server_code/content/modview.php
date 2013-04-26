<?php

?>
<style>
    #menu_title{
        text-align: center;
    }
</style>
<script type="text/javascript">

        $("document").ready(function () {
			var letter_array = new Array("A", "B", "C");
			var output = '';
                for(var i = 0; i < 8; i++){
					output += '<div class="ui-grid-b">'
					for(var j = 0; j < 3; j++){
						output += '<div class="ui-block-'+letter_array[j]+'"><div style="height:100%; margin:0px;">'+
                                                '<div data-role="controlgroup">'+
                                                 '<div id="menu_title" data-swatch="a" class="ui-li ui-li-divider ui-btn ui-bar-a ui-corner-top ui-btn-up-undefined" data-role="list-divider" data-form="ui-bar-a" style="font-align:center;"><div id="menu_title">Mod '+(3*i+j+1)+'</div></div>'+
						'<a href="#popupBasic'+(i+j)+'" data-role="button" data-rel="popup">'+mod_arr[i+j][0]["value"]+' </a><div data-role="popup" id="popupBasic'+(i+j)+'">'+
						'<p>Overview for Module '+(3*i+j+1)+'<p>'+
						'<ul data-role="listview">'+getModParam(i+j)+'</ul>'+
						'</div></div></div></div>'
					}
					output += '</div>'
                }
				$('#mod_info_grid').append(output).trigger('create');
                
        });
		
		function getModParam(index){
			var mod_param = "";
			for(var k = 0; k < mod_arr[index].length; k++){
				mod_param+='<li data-corners="false" data-shadow="false" >'+ mod_arr[index][k]["key"] + ':     '+ mod_arr[index][k]["value"] + '</li>'
			}
			return mod_param;
		}
        
        
        $("#mod_select").change(function() {
            $.mobile.showPageLoadingMsg();
            var output = '';
                output += '<ul data-role="listview">'
                        $.each(mod_arr[$(this).val()], function(index, obj){
                           output += '<li data-corners="false" data-shadow="false" >'+ obj.key + ':     '+ obj.value + '</li>'
                        });
                output += '</ul>'
                $('#modinfo').html(output).trigger('create');
                $.mobile.hidePageLoadingMsg();
        });
         
</script>
<!--<select name="select-choice-0" id="mod_select"></select>
<div id="modinfo"></div>-->
<div id="mod_info_grid"></div>






