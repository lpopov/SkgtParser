
var addOnChange = function (id, type) {
    
    var select = document.getElementById(id);
    
    select.addEventListener("change", function(e) {
	
        document.forms[0].lc.value = id;
        
        // There is no 'break', so all fields are cleared
        switch(id){
            //transport type
            case 'tt':
            //stop code
            case 'sc':
                document.forms[0].l.value = '';
            //line
            case 'l':
                if(type === 'byLine')
                    document.forms[0].r.value = '';
                document.forms[0].lc.value = '';
            //route
            case 'r':
                if(type === 'byLine')
                    document.forms[0].s.value = '';
        }
        
        if(type === 'byLine' && (id !== 's' || (id === 's' && document.forms[0].c === undefined))){
            document.forms[0].submit();
        }else if(type === 'byStop' && (id !== 'l' || (id === 'l' && document.forms[0].c === undefined))){
            document.forms[0].submit();
        }
	e.preventDefault();
    });
    
};

var initByLine = function(){
    var type = 'byLine';
    addOnChange('tt', type);
    addOnChange('l', type);
    addOnChange('r', type);
    addOnChange('s', type);
};

var initByStop = function(){
    var type = 'byStop';
    addOnChange('sc', type);
    addOnChange('l', type);
};

window.onload = function(){
    document.getElementById("toggle_form").addEventListener("click", function(e) {
	form_div = document.getElementById("form_div");
        if(form_div.style.display==="block"){
            form_div.style.display="none";
        }else{
            form_div.style.display="block";
        }
	e.preventDefault();
    });
    
    if(document.getElementById('bystop') === null){
        initByLine();
    }else{
        initByStop();
    }
};

