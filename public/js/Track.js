Track = function(){
    
    var timer;
    
    var xhr;
  
    var uidList = new Object();

    var progressElements = new Object();

    window.addEventListener("load", (event) => {
        console.log("page is fully loaded");
        setUidList();
        console.log(uidList);
        //return;
        //timer = setInterval(myTimer, 1000);
        //xhr = setInterval(loadXMLDoc, 1000);
    });

    function myTimer() {

        const date = new Date();
        document.getElementById("time").innerHTML = date.toLocaleTimeString();
    }



    function setUidList()
    {
        console.log(window.uid_list);
        const list = window.uid_list;
        console.log(window.uid_list[0]);
        let div = document.getElementById("response");
        
        let xhr = function loadXMLDoc(prop) {
            try{
                let getResponse = function (responseText)
                {
                        const obj = JSON.parse(responseText);

                        if(!obj.hasOwnProperty('success')){
                            throw new Error("ERROR");
                        }
                        if(!obj.hasOwnProperty('message')){
                             throw new Error("ERROR");
                        }
                        let messageType = typeof obj.message;
                        if(messageType !== 'string'){
                            throw new Error("ERROR MESSAGE TYPE");
                        }
                        if(obj.success !== true){
                             throw new Error(obj.message);
                        }
                        return obj.message;
                }

                console.log('Track().loadXMLDoc() check - ',uidList[prop]);
                console.log('Track().loadXMLDoc() check - ',uidList[prop].ele);

                //console.log('Track().loadXMLDoc() check - ',uidList[prop]);
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {

                    try{
                       if (this.readyState == 4 && this.status == 200) {
                            let response = getResponse(this.responseText);
                            //document.getElementById(uidList[prop]).innerHTML = response;
                            uidList[prop].ele.innerHTML = response;
                        }
                    }
                    catch(e){
                        console.error(e);
                        clearInterval(uidList[prop].run);
                        //document.getElementById(uidList[prop]).innerHTML = e.message;
                        uidList[prop].ele.innerHTML = e.message;
                    }
                };
                xhttp.open("GET", "/status.php?uid="+uidList[prop].uid, true);
                xhttp.send();
            }
            catch(e){
                console.error(e);
                clearInterval(uidList[prop].run);
            }
        }
        
        for(const prop in list){
            let p = document.createElement('p');
                p.setAttribute('id',list[prop]);
                div.appendChild(p);
            uidList[prop] = {
                'uid' : list[prop]
                ,'ele' : p
                ,'run' : setInterval(xhr, 1000,prop)
            };
        }
    }
}();