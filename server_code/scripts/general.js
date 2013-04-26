var key, value;
var bat_info = new Array();
var jsonData = new Array()
var mod_arr = new Array();
var mod_info = new Array();
var ecarID, ecarName;
var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");

//prevent browser from caching json files 
// $.ajaxSetup({
//    cache:false
//  });

$(document).ready(function() {
    
    if(testSupport()){
        console.log("Local storage accessable and supported.");
        if(window.localStorage.length == 0){
            console.log("Local storage is empty, requesting latest data.")
            getJSONInfo();
        }else{
            console.log("Local storage data found, creating data objects.")
            objCreator();
        }
    }else{
        alert("Local storage is not supported on this device, this app cannot be used.")
    }

    //load main content 
    $('#content').load('content/mainview.php', function(){
                    $('#content').trigger('create');
                    }).hide().fadeIn('slow');	

    //handle menu clicks
    $('ul#nav li a').click(function(){
            var page = $(this).attr('href');
            $('#content').load('content/'+page+'.php', function(){
                    $('#content').trigger('create');
            }).hide().fadeIn('fast');
            //need to prevent default action
            return false;
    });
        
});

var ecar_id;
function requestData(ecar_id){
    this.ecar_id = ecar_id;
}

function keyvalpair(key, value){
        this.key = key;
        this.value = value;
}

var batModes = ["Standby", "Charge", "Discharge", "Complete"];
var batStates = ["Main", "Equalize", "Float", "None"];
var batFaults = ["Low Capacity", "SOC Mismatch", "Discharge Pre-charge Failure", "Charge Pre-charge Failure", "Sanity Error", "Over Voltage Protection"];
var batVStates = ["Ok", "Discharge Warning", "Discharge Alarm", "Discharge Shutdown", "Over Voltage Warning", "Over Voltage Alarm", "Over Voltage Shutdown"];
var batCTs = ["Main", "Charge", "Discharge", "Discharge CDA"];
var batTStates = ["Ok", "Warning", "Alarm", "Shutdown"];
var batDischarge = ["120A Warning", "120A Alarm", "150A Warning", "150A Alarm", "200A Warning", "200A Alarm", "250A Warning", "250A Alarm", "300A Warning", "300A Alarm"];
var batSensors = ["Temperature", "Voltage", "Current"];

//requests the state.json file and sets each of the nessecary objects into approriate key valye pairs for the webpage
function getJSONInfo(){
    bat_info = [];
    mod_arr = [];
    var req = new requestData(ecarID);
    var postData = {bms_request:req};
     console.log("Requesting data from server");
        $.ajax({
             type: 'GET',
             url: "http://dev.arcx.com/ecar/cron/state/state.json",
             data: postData,
         success: function(data) {
             console.log("Storing the json object");
             storeJSON(data); 
             //check SOC level
             if(data["battery"]["soc"] < 30){
                 console.log("Low SOC, notifying android device...");
                 notifySOC(data["battery"]["soc"]);
             }
             if(data["battery"]["mode"] == 1){
                 console.log("Ecar charging, notifying deviec");
                 notifyCharging();
             }
              //load main content 
                $('#content').load('content/mainview.php', function(){
                    $('#content').trigger('create');
                    }).hide().fadeIn('slow');

     },
    error: function(jxhr, status, err) {
        console.log("Error, status = " + status + ", err = " + err);
      }
    });

}

function objCreator(){
         
    var soc = new keyvalpair("State of Charge", localStorage.getItem("soc") + "%");
    bat_info.push(soc);

    var mode = new keyvalpair("Mode", batModes[localStorage.getItem("mode")]);
    bat_info.push(mode);

    var state = new keyvalpair("State", batStates[localStorage.getItem("state")]);
    bat_info.push(state);

    var faultmap = new keyvalpair("Faults", batFaults[localStorage.getItem("faultMap")]);
    bat_info.push(faultmap);

    var vstate = new keyvalpair("Voltage State", batVStates[localStorage.getItem("vState")]);
    bat_info.push(vstate);

    var voltage = new keyvalpair("Voltage", localStorage.getItem("voltage") + "V");
    bat_info.push(voltage);

    var vcellrange = new keyvalpair("Cell Voltage Range", localStorage.getItem("vCellMin")/1000 + " - " + localStorage.getItem("vCellMax")/1000 + " V");
    bat_info.push(vcellrange);

    var temprange = new keyvalpair("Cell Temperature Range", localStorage.getItem("cTempMin") + " - " + localStorage.getItem("cTempMax") + " C");
    bat_info.push(temprange);

    var ptemprange = new keyvalpair("PCB Temperature Range", localStorage.getItem("pTempMin") + " - " + localStorage.getItem("pTempMax") + " C");
    bat_info.push(ptemprange);

    var tstate = new keyvalpair("Temperature State", batTStates[localStorage.getItem("tState")]);
    bat_info.push(tstate);

    var current = new keyvalpair("Current", localStorage.getItem("current") + "A");
    bat_info.push(current);

    var discharge = new keyvalpair("Discharge", batDischarge[localStorage.getItem("discharge")]);
    bat_info.push(discharge);


    for(var i = 0; i < 24 ; i++){
        mod_info = [];

        var msoc = new keyvalpair("SOC", Math.round(100*localStorage.getItem("Mod "+ i + " " + "soc")/255) + "%");
        mod_info.push(msoc);

        var volt = new keyvalpair("Voltage", localStorage.getItem("Mod "+ i + " " + "voltage")/1000+"V");
        mod_info.push(volt);

        var curr = new keyvalpair("Current", localStorage.getItem("Mod "+ i + " " + "current")+"A");
        mod_info.push(curr);

        var temp = new keyvalpair("Temperature", Math.round((localStorage.getItem("Mod "+ i + " " + "temp"))/1000)+"C");
        mod_info.push(temp);

        var pcbtemp = new keyvalpair("PCB Temperature", Math.round((localStorage.getItem("Mod "+ i + " " + "pcbTemp"))/1000)+"C");
        mod_info.push(pcbtemp);

        var flags = new keyvalpair("Flags", localStorage.getItem("Mod "+ i + " " + "flags"));
        mod_info.push(flags);

        var cell1 = new keyvalpair("Cell 1", localStorage.getItem("Mod "+ i + " " + "cell 0")/1000+"V");
        mod_info.push(cell1);

        var cell2 = new keyvalpair("Cell 2", localStorage.getItem("Mod "+ i + " " + "cell 1")/1000+"V");
        mod_info.push(cell2);

        var cell3 = new keyvalpair("Cell 3", localStorage.getItem("Mod "+ i + " " + "cell 2")/1000+"V");
        mod_info.push(cell3);

        var cell4 = new keyvalpair("Cell 4", localStorage.getItem("Mod "+ i + " " + "cell 3")/1000+"V");
        mod_info.push(cell4);

        mod_arr.push(mod_info);

       }
       
}

/*
 * Android Interface Functions
 *	
 */

function notifySOC(soc){
    if(isAndroid) 
        AndroidFunction.notifySOC(soc);
}

function notifyCharging(){
    if(isAndroid) 
        AndroidFunction.notifyCharging();
}


function clearAndroidCache(){
    if(isAndroid) 
        AndroidFunction.clearCache();
}

function transmitECarInfo(){
    if(isAndroid) 
        AndroidFunction.transmitECarInfo(ecarID, ecarName);
}


/*
 * Local Storage Interface 
 *	
 */

function storeJSON(data){
    //battery
    for( var key in data["battery"]){
        //console.log(key + ': ' + data["json"]["battery"][key]);
        if(key != "modules"){
            localStorage.setItem(key, data["battery"][key]);
		}
    }
    //modules
    for(var i = 0; i < 24 ; i++){
        for( var key_1 in data["battery"]["modules"][i]){
            if(key_1 == "cellVolt"){
                for(var j = 0; j < 4; j++){
                    localStorage.setItem("Mod "+ i + " cell " + j, data["battery"]["modules"][i][key_1][j]);
                }			
            }else{
                localStorage.setItem("Mod "+ i + " " + key_1, data["battery"]["modules"][i][key_1]);
            }
        }
    }
    //location
    localStorage.setItem("latitude", data["general"]["lat"]);
    localStorage.setItem("longitude", data["general"]["lon"]);
    
    //post_time
    localStorage.setItem("post_time", data["post_time"]);
    
    objCreator(); //run inital object create to get variables 
}

function clearLocalStorage(){
    alert("Local Storage Cleared!");
    localStorage.clear();  
}

function testSupport()  {  
    if (localStorage)  
        return true;  
    else  
        return false;  
}  
