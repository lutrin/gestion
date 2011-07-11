{
  initialize: function() {
    var audio = $( this ),
        app = _c.ajaxList.interaction.audio,
        srcList;

    // already initiated
    if( audio.hasClass( "initiated" ) ) {
      return false;
    }

    // each audio type
    audio.find( "source" ).each( function() {
      var src = $( this );
      if( !Modernizr.audio[src.data( "type" )] ) {
        src.remove();
      } else {
        if( !audio.attr( "src" ) ) {
         audio.attr( "src", src.attr( "src" ) );
        }

      }
    } );
console.log( this.error.code );
    if( !Modernizr.audio || !audio.find( "source" ).size() ) {
      $( audio.html() ).insertAfter( audio );
      audio.remove();
    }    

    // add
    audio.addClass( "initiated" );
  }
}
