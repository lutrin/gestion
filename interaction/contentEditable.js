{
  initialize: function() {
    var contentEditable = $( this ),
        app = _c.ajaxList.interaction.contentEditable;

    // already initiated
    if( contentEditable.hasClass( "initiated" ) ) {
      return false;
    }

    // exec command
    contentEditable.parent().find( "[data-command]" ).click( contentEditable.execCommand );

    // exec function
    contentEditable.parent().find( "[data-trigger]" ).click( function() {
      contentEditable.trigger( $( this ).data( "trigger" ) );
    } );

    // toggle Tag
    contentEditable.bind( "toggleTag", function() {
      contentEditable.toggleClass( "viewTag" );
    } );

    // stripTag
    contentEditable.bind( "stripTags", function() {
      contentEditable.find( "span[style]" ).each( function() {
        var span = $( this );
        if( span.attr( "style" ) == "font-style: italic;" ) {
          span.replaceWith( "<em>" + span.html() + "</em>" );
        }
      } );
      contentEditable.find( "i" ).each( function() {
        var i = $( this );
        i.replaceWith( "<em>" + i.html() + "</em>" );
      } );
      contentEditable.html( _c.stripTags( contentEditable.html(), "<" + contentEditable.data( "allowed" ).split( /,/ ).join( "><" ) + ">" ) );
    } );

    contentEditable.addClass( "initiated" );
  },

  /****************************************************************************/
  execCommand: function() {
    document.execCommand( $( this ).data( "command" ), false, null );
  }
}
