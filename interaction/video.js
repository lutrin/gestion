{
  initialize: function() {
    var video = $( this ),
        app = _c.ajaxList.interaction.video,
        srcList;

    // already initiated
    if( video.hasClass( "initiated" ) ) {
      return false;
    }

    // each video type
    video.find( "source" ).each( function() {
      var src = $( this );
      if( !Modernizr.video[src.data( "type" )] ) {
        src.remove();
      } else {
        if( !video.attr( "src" ) ) {
         video.attr( "src", src.attr( "src" ) );
        }

      }
    } );
    if( !Modernizr.video || !video.find( "source" ).size() ) {
      $( video.html() ).insertAfter( video );
      video.remove();
    }    

    // add
    video.addClass( "initiated" );
  }
}
