{
  initialize: function() {
    var form = $( this ),
        app = _c.ajaxList.interaction.form;

    // already initiated
    if( form.hasClass( "initiated" ) ) {
      return false;
    }

    /* ... */

    // autofocus
    if( !Modernizr.input.autofocus ) {
      form.find( "input[autofocus]" ).focus();
    }

    form.addClass( "initiated" );
    return false;
  }
}
