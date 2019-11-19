function onThumbClick( idx ) {
  console.log( 'onThumbClick( ' + idx + ' )' )
  var selector = 'img.img-thumbnail'
  var imgDOMs = document.querySelectorAll( selector )
  var imgs = [] 
  for( var i = 0; i < imgDOMs.length; i++ ) {
    var img = imgDOMs[ i ]
    var src = img.attributes[ 'src' ].value
    imgs.push( {
      src: src.substring( 7 ),
      w: parseInt( img.attributes[ 'x' ].value ),
      h: parseInt( img.attributes[ 'y' ].value ),
      msrc: src
    } )
  }
  var options = {
    index: idx, 
    preload: [ 1, 3 ]
  } 
  var ps = document.querySelectorAll( '.pswp' )[ 0 ];
  gallery = new PhotoSwipe( ps, PhotoSwipeUI_Default, imgs, options );
  gallery.init();
}
