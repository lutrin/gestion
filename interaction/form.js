{
  initialize: function() {
    var form = $( this ),
        app = _c.ajaxList.interaction.form;

    // already initiated
    if( form.hasClass( "initiated" ) ) {
      return false;
    }

    // submit
    form.submit( function() {
      return false;
    } );
    form.find( "input[type=submit]" ).each( function() {
      return $( this ).click( app.submit );
    } );

    // autofocus
    if( !Modernizr.input.autofocus ) {
      form.find( "input[autofocus]" ).focus();
    }

    form.addClass( "initiated" );
    return false;
  },

  /****************************************************************************/
  submit: function() {
    return false;
  }
}
