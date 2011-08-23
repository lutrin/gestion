{
  initialize: function() {
    var contentEditable = $( this ),
        div = this,
        app = _c.ajaxList.interaction.contentEditable;

    // already initiated
    if( contentEditable.hasClass( "initiated" ) ) {
      return false;
    }

    // exec command
    contentEditable.parent().find( "[data-command]" ).click( app.callExecCommand );

    // exec function
    contentEditable.parent().find( "[data-trigger]" ).click( app.callExecTrigger );

    // toggle Tag
    contentEditable.bind( "toggleTag", function() {
      contentEditable.toggleClass( "viewTag" );
    } );

    // stripTag
    contentEditable.bind( "stripTags", app.stripTags );

    contentEditable.addClass( "initiated" );
  },

  /****************************************************************************/
  callExecCommand: function() {
    var button = $( this );
    button.parents( "fieldset:first" ).find( "[contentEditable]" ).focus();
    document.execCommand( button.data( "command" ), false, null );
  },

  /****************************************************************************/
  callExecTrigger: function() {
    var button = $( this ),
        contentEditable = button.parents( "fieldset:first" ).find( "[contentEditable]" );
    contentEditable.focus();
    contentEditable.trigger( button.data( "trigger" ) );
  },

  /****************************************************************************/
  stripTags: function() {
    var contentEditable = $( this ).parents( "fieldset:first" ).find( "[contentEditable]" );
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
  }
}
