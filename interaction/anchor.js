{
  initialize: function() {
    var anchor = $( this ),
        app = _c.ajaxList.interaction.anchor,
        href = anchor.attr( "href" );

    // click
    anchor.click( app.click );

    // already in
    if( window.location.hash == href ) {
      anchor.each( app.click );
    }

    anchor.addClass( "initiated" );
  },

  /****************************************************************************/
  click: function() {
    var anchor = $( this ),
        app = _c.ajaxList.interaction.anchor,
        href = anchor.attr( "href" ),
        target = $( href ),
        action = anchor.data( "action" ),
        trigger = anchor.data( "trigger" ),
        ajaxObject, dataParams, params;

    // empty
    if( target.hasClass( "empty" ) ) {
      _c.callAjax(
        [ { folder: "procedure", name: "getContent", params: { id: target.attr( "id" ) } } ],
        function( ajaxItem ) {

          // fatal error
          if( ajaxItem.fatalError ) {
            _edit.showError( _edit.msg( ajaxItem.fatalError ) );
            return false;
          }

          // replacement
          if( ajaxItem.replacement ) {
            _c.eachItem( ajaxItem.replacement, _edit.replaceContent );
            if( ajaxItem.hash ) {
              window.location.hash = href;
            }
            target.removeClass( "empty" );
          }

          // dialog
          if( ajaxItem.dialog ) {
            _edit.showDialog( ajaxItem.dialog );
          }
          return false;
        }
      );
    } else {

      // action      
      if( action ) {
        ajaxObject = { folder: "procedure", name: action };

        // params
        dataParams = anchor.data( "params" );
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

            // dialog
            if( ajaxItem.dialog ) {
              _edit.showDialog( ajaxItem.dialog );
            }

            // replacement
            if( ajaxItem.replacement ) {
              _c.eachItem( ajaxItem.replacement, _edit.replaceContent );
            }

            // details
            if( ajaxItem.details ) {
              _edit.showDetails( ajaxItem.details );
            }
            return false;
          }
        );
      } else if( trigger ) {
        anchor.trigger( trigger );
        return false;
      }
    }

    anchor.parents( "li:first" ).addClass( "selected" ).siblings().removeClass( "selected" );
    target.addClass( "target" ).siblings().removeClass( "target" );
    return false;
  }
}
