if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, '');
  }
}

var jobTitleInput = document.getElementById("jobtitle-input");
var agreementSubmit = document.getElementById("agreement-submit");

function checkAllowSubmit(){
    if(jobTitleInput.value.trim() !== ''){
        agreementSubmit.disabled = false;
    }else{
        agreementSubmit.disabled = true;
    }
}


