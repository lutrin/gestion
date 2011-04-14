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
        action = button.attr( "data-action" ),
        trigger = button.attr( "data-trigger" ),
        ajaxObject, dataParams, params;

    // action      
    if( action ) {
      ajaxObject = { folder: "procedure", name: action };

      // params
      dataParams = button.attr( "data-params" );
      if( dataParams ) {
        params = {};
        _c.eachItem( dataParams.split( /\,/g ), function( param ) {
          var paramSplit = param.split( /=/g );
          params[paramSplit[0]] = paramSplit[1];
        } );
        ajaxObject["params"] = params;
      }
      return _c.callAjax(
        [ ajaxObject ],
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
    } else if( trigger ) {
      button.trigger( trigger );
    }
  }
}
