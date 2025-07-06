Track = function(){
    
    var timer;
    
    var xhr;
    
    var counter = 0;
    
    var uidList;

    window.addEventListener("load", (event) => {
        console.log("page is fully loaded");
        timer = setInterval(myTimer, 1000);
        xhr = setInterval(loadXMLDoc, 1000);
        setUidList();
    });

    function myTimer() {

        const date = new Date();
        document.getElementById("time").innerHTML = date.toLocaleTimeString();
        counter++;
        if(counter === 10){
            clearInterval(timer);
        }
    }
    
    function loadXMLDoc() {
        for(const prop in uidList ){
            console.log('Track().loadXMLDoc() check - ',uidList[prop]);
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                let response = getResponse(this.responseText);
                document.getElementById("response").innerHTML = response;
                if(response === 'FINISH' || response === 'ERROR'){
                    clearInterval(xhr);
                }
              }
            };
            xhttp.open("GET", "/status.php?uid="+uidList[prop], true);
            xhttp.send();
        }
        
    }
    
    function getResponse(responseText)
    {
        try{
            const obj = JSON.parse(responseText);
            console.log(obj);
            if(!obj.hasOwnProperty('success')){
                return "ERROR";
            }
            if(obj.success !== true){
                return "ERROR";
            }
            if(!obj.hasOwnProperty('message')){
                return "ERROR`";
            }
            let messageType = typeof obj.message;
                console.log(messageType);
            if(messageType !== 'string'){
                return "ERROR";
            }
            return obj.message;
        }
        catch(e){
            console.error(e);
            return "ERROR";
        }
        return "ERROR";
    }

    function setUidList()
    {
        console.log(window.uid_list);
        uidList = window.uid_list;
        console.log(window.uid_list[0]);
    }

}();