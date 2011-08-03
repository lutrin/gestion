{
  initialize: function() {
    var contentEditable = $( this ),
        dom = this,
        app = _c.ajaxList.interaction.contentEditable;

    // already initiated
    if( contentEditable.hasClass( "initiated" ) ) {
      return false;
    }

    contentEditable.parent().find( "button" ).click( function() {
      var button = $( this );
      document.execCommand( button.data( "command" ), "", "" );
    } );
/*
    contentEditable.bind( "change", function() {
    } );
*/
    contentEditable.addClass( "initiated" );
  },
}
