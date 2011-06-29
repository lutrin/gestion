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
    } else if( button.hasClass( "close" ) ) {
      button.click( function() {
        $( this ).closest( "form,.tabs-container,.accordion-container" ).remove();
      } );
    } else {
      button.click( app.click );
    }

    // close form
    /*form.find( "button.close" ).bind( "close", function() {
      form.remove();
    } );*/

    button.addClass( "initiated" );
    //return false;
  },

  /****************************************************************************/
  click: function() {
    var button = $( this ),
        app = _c.ajaxList.interaction.button,
        action = button.data( "action" ),
        trigger = button.data( "trigger" ),
        ajaxObject, dataParams, params, rowList, field;

    // action      
    if( action ) {
      ajaxObject = { folder: "procedure", name: action };

      // params
      dataParams = button.data( "params" );
      if( dataParams ) {
        params = {};
        _c.eachItem( dataParams.split( /\,/g ), function( param ) {
          var paramSplit = param.split( /=/g );
          params[paramSplit[0]] = paramSplit[1];
        } );

        // selection
        if( params.object && params.row && params.row == "selection" ) {
          rowList = [];
          $( "#" + params.object + " input.selectRow:checked" ).each( function() {
            rowList.push( this.value );
          } );
          params["k"] = rowList;

        // for
        } else if( params["for"] ) {
          field = $( params["for"] );
          rowList = [];
          if( field.attr( "name" ) ) {
            rowList = field.attr( "name" );
          } else {
            field.find( "[name]" ).each( function() {
              rowList.push( $( this ).val() );
            } );
          }
          params["k"] = rowList;
        }
        
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

          // details
          if( ajaxItem.details ) {
            _edit.showDetails( ajaxItem.details );
          }
          return false;
        }
      );
    } else if( trigger ) {
      button.trigger( trigger );
      return false;
    }
  }
}
