{
  initialize: function() {
    var contentEditable = $( this ),
        dom = this,
        app = _c.ajaxList.interaction.contentEditable;

    // already initiated
    if( contentEditable.hasClass( "initiated" ) ) {
      return false;
    }

    contentEditable.parent().find( "[data-command]" ).click( function() {
      document.execCommand( $( this ).data( "command" ), false, null );
      return false;
    } );
/*
    contentEditable.bind( "change", function() {
    } );
*/
    contentEditable.addClass( "initiated" );
  },
}
