var eesset = eesset || {};
(function(sset){
    "use strict";
    sset.init = function () {
        sset.listenForExpand();
        sset.listenForPayloadDownload();
        sset.listenForTypeChange();
        sset.listenForCreateCodeClick();
    };

    sset.get = function(elem) {
      return top.document.getElementById(elem);
    };

    // XHR object

    // Get readme info for payload and set in readmeContainer
    sset.getReadme = function(payload, el) {
      var xhrObj = function(){
        try {return new XMLHttpRequest();}catch(e){}
        try {return new ActiveXObject("Msxml3.XMLHTTP");}catch(e){}
        try {return new ActiveXObject("Msxml2.XMLHTTP.6.0");}catch(e){}
        try {return new ActiveXObject("Msxml2.XMLHTTP.3.0");}catch(e){}
        try {return new ActiveXObject("Msxml2.XMLHTTP");}catch(e){}
        try {return new ActiveXObject("Microsoft.XMLHTTP");}catch(e){}
        return null;
      }

      var xhr = xhrObj();
      // Display the briefing text on screen when we get our response
      xhr.onreadystatechange=function() {
        if (xhr.readyState==4 && xhr.status==200) {
          var responseText = xhr.responseText;
          var responseJSON = JSON.parse(responseText);
          // Display the info...
          el.innerHTML = responseJSON['payload']['instructions'];
        }
      }
      xhr.open("POST","payload-readme",true);
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhr.send("readmeType="+payload+"&csrf="+ document.querySelectorAll('.csrfToken')[0].value);
    }


    sset.listenForExpand = function(){
        var elements = document.querySelectorAll('[data-click-key]');
        Array.prototype.forEach.call(elements, function(el, i){
          var clickKey = el.dataset.clickKey;
          el.addEventListener("click",function(e) {
              e.preventDefault();
              var expanderLink = sset.get('expander_' + clickKey);
              if (expanderLink) {
                var extraRow = el.nextElementSibling,
                expanderArrow =  expanderLink.querySelectorAll('span')[1];
                if (extraRow.classList.contains('hidden')){
                  expanderArrow.innerHTML = "&#9650;";
                } else {
                  expanderArrow.innerHTML = "&#9660;";
                }
                extraRow.classList.toggle('hidden');
              }
          });
        });
    };

    sset.listenForCreateCodeClick = function(){
        var elements = document.querySelectorAll('[data-generate-group]');
        Array.prototype.forEach.call(elements, function(el, i){
          var groupName = el.dataset.generateGroup;
          var link = sset.get(groupName + '_expander');
          link.addEventListener("click",function(e) {
              e.preventDefault();
              var extraRow = el.nextElementSibling,
              expanderArrow =  el.querySelector('span');
              if (extraRow.classList.contains('hidden')){
                expanderArrow.innerHTML = "&#9650;";
              } else {
                expanderArrow.innerHTML = "&#9660;";
              }
              extraRow.classList.toggle('hidden');

          });
        });
    };

    sset.listenForPayloadDownload = function(){
      var elements = document.querySelectorAll('[data-payload-download]');
      Array.prototype.forEach.call(elements, function(el, i){
        var groupName = el.dataset.payloadDownload;
        el.addEventListener("click",function(e) {
          e.preventDefault();
          var type = sset.get(groupName + "_type").value,
              secure_id = sset.get(groupName + "_secureId").value,
              token = sset.get(groupName + "_csrfToken").value,
              baseurl= sset.get(groupName + "_urlInput").value;
          baseurl = baseurl.replace(/\/+$/, "") + "/secure?secure_id=" + secure_id;
          var url = '/admin/payload?type=' + type +  '&url=' + baseurl +'&csrf=' + token ;
          if(type === 'hta'){
            url += '&htaAppname=' +  sset.get(groupName + "_htaInput").value;
          }
          window.open(url , '_blank');
        });
      });

    };
    sset.removeClass = function(el, className){
      if (el.classList){
        el.classList.remove(className);
      } else {
        el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
      }
    };

    sset.addClass = function(el, className){
      if (el.classList){
        el.classList.add(className);
      } else {
        el.className += ' ' + className;
      }
    };

    sset.listenForTypeChange = function(){
      var elements = document.querySelectorAll('[data-group-dropdown]');
      Array.prototype.forEach.call(elements, function(el, i){
        var groupName = el.dataset.groupDropdown;
        el.onchange = function(e){
          var label = sset.get(groupName+"_htaLabel"),
            readMeEl = sset.get(groupName+"_readme"),
            input = sset.get(groupName+"_htaInput");
            sset.getReadme(el.value, readMeEl);
          if(el.value === "hta"){
            sset.removeClass(label,'hidden');
            sset.removeClass(input,'hidden');
          } else {
            sset.addClass(label,'hidden');
            sset.addClass(input,'hidden');
          }
        };
      });
    };
})(eesset);

document.addEventListener("DOMContentLoaded", function(event) {
  //do work
  eesset.init();
});
