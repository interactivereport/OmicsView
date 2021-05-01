// JavaScript Document
function message_alert(type, msg, url) {

	if (type=='error'){
	bootbox.alert('<h2><i class="fa fa-exclamation-triangle" style="color:red;"></i> Error</h2><div class="jumbotron" style="padding:10px;padding-top:25px;"><p>'+msg+'</p></div>');
	}

	else if (type=='success') {
		bootbox.alert('<h2><i class="fa fa-check-square-o" style="color:green;"></i> Message</h2><div class="jumbotron" style="padding:10px;padding-top:25px;"><p>'+msg+'</p></div>', function(){
		window.location.replace(url);
		} );
	}

	else if (type=='info') {
		bootbox.alert('<h2><i class="fa fa-info-circle" style="color:#EC971F;"></i> Notice</h2><div class="jumbotron" style="padding:10px;padding-top:25px;"><p>'+msg+'</p></div>');
	}
}


serialize = function(obj) {
 var str = [];
 for(var p in obj)
   if (obj.hasOwnProperty(p)) {
     str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
   }
 return str.join("&");
};

function objToString(obj, ndeep) {
  switch(typeof obj){
    case "string": return '"'+obj+'"';
    case "function": return obj.name || obj.toString();
    case "object":
      var indent = Array(ndeep||1).join('\t'), isArray = Array.isArray(obj);
      return ('{['[+isArray] + Object.keys(obj).map(function(key){
           return '\n\t' + indent +(isArray?'': key + ': ' )+ objToString(obj[key], (ndeep||1)+1);
         }).join(',') + '\n' + indent + '}]'[+isArray]).replace(/[\s\t\n]+(?=(?:[^\'"]*[\'"][^\'"]*[\'"])*[^\'"]*$)/g,'');
    default: return obj.toString();
  }
}
