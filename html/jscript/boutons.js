
userAgent = window.navigator.userAgent;
browserVers = parseInt(userAgent.charAt(userAgent.indexOf("/")+1),10);
function findElement(n,ly) {
  if (browserVers < 4)  return document[n];
  var curDoc = ly ? ly.document : document;
  var elem = curDoc[n];
  if (!elem) {
    for (var i=0;i<curDoc.layers.length;i++) {
      elem = findElement(n,curDoc.layers[i]);
      if (elem) return elem;
    }
  }
  return elem;
}

function newImage(arg) {
  if (document.images) {
    rslt = new Image();
    rslt.src = arg;
    return rslt;
  }
}

function changeImages() {
  if (document.images) {
    var img;
    for (var i=0; i<changeImages.arguments.length; i+=2) {
      img = null;
      if (document.layers) {
        img = findElement(changeImages.arguments[i],0);
      }
      else {
        img = document.images[changeImages.arguments[i]];
      }
      if (img) {
        img.src = changeImages.arguments[i+1];
      }
    }
  }
}
var preloadFlag = false;
