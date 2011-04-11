{
  initialize: function() {
    var button = $( this ),
        app = _c.ajaxList.interaction.button;

    // already initiated
    if( button.hasClass( "initiated" ) ) {
      return false;
    }

    // click
    if( button.hasClass( "closeDialog" ) ) {
      button.click( _edit.closeDialog );
    } else {
      button.click( app.click );
    }

    button.addClass( "initiated" );
    //return false;
  },

  /****************************************************************************/
  click: function() {
    var button = $( this ),
        app = _c.ajaxList.interaction.button,
        action = button.attr( "data-action" );        
    if( action ) {
      return _c.callAjax(
        [ { folder: "procedure", name: action } ],
        function( ajaxItem ) {

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
}
