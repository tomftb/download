Download = function(){

    let formData = new Object();

    window.addEventListener("load", (event) => {
        console.log("Download() page is fully loaded");
        setSubmitButton();
        
    });
    
    function setSubmitButton(){
        console.log("Download().setSubmitButton()");
        let submit = document.getElementById('submit');
        submit.onclick = function(event){
            event.preventDefault();
            console.log('on click');
            getFormData();
            loadXMLDoc();
        };
    }

    function getFormData()
    {
        console.log("Download().getFormData()");
        let form = document.getElementById('form');
        let textareaList = form.querySelectorAll("textarea");
        let inputList = form.querySelectorAll("input");
        console.log(textareaList);
        console.log(inputList);
        formData = new FormData();
        textareaList.forEach((e) => {
           console.log("Download().getFormData() TEXTAREA");
           console.log(e.name,e.value);
           formData.append(e.name,e.value);
           saveLog(e.value);
           e.value = '';
        });
        inputList.forEach((e) => {
           console.log("Download().getFormData() INPUT");
           console.log(e.name,e.value);
           formData.append(e.name,e.value);
        });
    }

    function loadXMLDoc(){
       try{
            console.log("Download().loadXMLDoc()");
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                try{
                    if (this.readyState == 4 && this.status == 200) {
                        let response = getResponse(this.responseText);
                            console.log(response);
                        let track = new Track();
                            track.setUidList(response.data);
                    }
                }
                catch(e){
                    
                    console.error(e);
                }
            };
            xhttp.open("POST", "/post.php", true);
            xhttp.send(formData);
        }
        catch(e){
            console.error(e);
        }
    }

    function getResponse (responseText)
    {
        console.log("Download().getResponse()",responseText);
        const obj = JSON.parse(responseText);
        console.log(obj);
        if(!obj.hasOwnProperty('success')){
            throw new Error("ERROR - MISSING `success` KEY");
        }
        if(!obj.hasOwnProperty('message')){
            throw new Error("ERROR - MISSING `message` KEY");
        }
        if(!obj.hasOwnProperty('data')){
            throw new Error("ERROR - MISSING `message` KEY");
        }
        let messageType = typeof obj.message;
        if(messageType !== 'string'){
            throw new Error("ERROR `message` KEY TYPE - `"+messageType+"` EXPECTING `string`");
        }
        let dataType = typeof obj.data;
        if(dataType !== 'object'){
            throw new Error("ERROR `data` KEY TYPE - `"+dataType+"` EXPECTING `object`");
        }
        if(obj.success !== true){
            throw new Error(obj.message);
        }
        return obj;
    }

    function saveLog(value)
    {
        try{
            if(value.trim() === ''){
                return;
            }
            let logList = document.getElementById('log');
            let p = document.createElement('p');
            let text = document.createTextNode(value);
                p.appendChild(text);
                logList.prepend(p);
        }
        catch(e){
            console.error('Download.saveLog()',value);
        }
        
    }
}();