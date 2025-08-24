class Track {
    
    timer;
  
    uidList = new Object();

    progressElements = new Object();

    myTimer() {
        const date = new Date();
        document.getElementById("time").innerHTML = date.toLocaleTimeString();
    }
    setUidList(data)
    {
        try{
            console.log('Track.setUidList() data ',data);

            let parent = this;

            this.uidList = data;

            let div = document.getElementById("response");

            let xhr = function (prop,parent) {
                parent.load(prop,parent);
            };

            for(const prop in this.uidList){
                let p = document.createElement('p');
                    p.setAttribute('id',this.uidList[prop]);
                    div.prepend(p);
                this.uidList[prop] = {
                    'uid' : this.uidList[prop]
                    ,'ele' : p
                    ,'run' : setInterval(xhr, 1000,prop,parent)
                };
            }
        }
        catch(e){
            console.error(e);
        }
        
    }

    load(prop,parent)
    {
      try{
            console.log('Track.load() prop ',prop,'parent',parent.uidList[prop]);
            console.log('Track().loadXMLDoc() check - ',parent.uidList[prop]);
            console.log('Track().loadXMLDoc() check - ',parent.uidList[prop].ele);

            //console.log('Track().loadXMLDoc() check - ',uidList[prop]);
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                try{
                    if (this.readyState == 4 && this.status == 200) {
                        let response = parent.getResponse(this.responseText);
                        //document.getElementById(uidList[prop]).innerHTML = response;
                        parent.uidList[prop].ele.innerHTML = response;
                     }
                }
                catch(e){
                    console.error(e);
                    clearInterval(parent.uidList[prop].run);
                    //document.getElementById(uidList[prop]).innerHTML = e.message;
                    parent.uidList[prop].ele.innerHTML = e.message;
                }
            };
            xhttp.open("GET", "/status.php?uid="+parent.uidList[prop].uid, true);
            xhttp.send();
        }
        catch(e){
            console.error(e,parent.uidList);
            clearInterval(parent.uidList[prop].run);
        }
    }
    getResponse (responseText)
    {
                    console.log(responseText);
                    const obj = JSON.parse(responseText);
                        console.log(obj);
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
};