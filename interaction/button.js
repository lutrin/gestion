{
  initialize: function() {
    var button = $( this ),
        app = _c.ajaxList.interaction.button;

    // already initiated
    if( button.hasClass( "initiated" ) ) {
      return false;
    }

    // click
    button.click( app.click );

    button.addClass( "initiated" );
    //return false;
  },

  /****************************************************************************/
  click: function() {
    var button = $( this ),
        app = _c.ajaxList.interaction.button,
        action = button.attr( "data-action" );        

    return _c.callAjax(
      [ { folder: "procedure", name: action } ],
      function( ajaxItem ) {
        var key;

        // fatal error
        if( ajaxItem.fatalError ) {
          _edit.showError( _edit.msg( ajaxItem.fatalError ) );
          return false;
        }

        // replacement
        if( ajaxItem.replacement ) {
          _c.eachItem( ajaxItem.replacement, _edit.replaceContent );
        }

        // dialog
        if( ajaxItem.dialog ) {
          _edit.showDialog( ajaxItem.dialog );
        }
        return false;
      }
    );
  }
}
