function openNav() {
  document.getElementById("mySidenav").style.width = "25%";
  document.getElementById("main").style.marginLeft = "25%";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}

function openNavMap() {
    document.getElementById("mySidenavMap").style.width = "25%";
    document.getElementById("main").style.marginLeft = "25%";
  }
  
function closeNavMap() {
    document.getElementById("mySidenavMap").style.width = "0";
    document.getElementById("main").style.marginLeft= "0";
}

var loadFile = function(event, imgId) {
  var output = document.getElementById(imgId);
  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function() {
    URL.revokeObjectURL(output.src)
  }
};
