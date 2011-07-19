{
  initialize: function() {
    var image = $( this ),
        app = _c.ajaxList.interaction.image;

    // already initiated
    if( image.hasClass( "initiated" ) ) {
      return false;
    }

    // replace src
    image.attr( "src", image.data( "src" ) );

    image.addClass( "initiated" );
  },
}
