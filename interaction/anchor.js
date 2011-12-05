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
        dataParams = anchor.data( "params" ),
        makeParams = function( data ) {
          var params = {};
          _c.eachItem( data.split( /\,/g ), function( param ) {
            var paramSplit = param.split( /=/g );
            params[paramSplit[0]] = paramSplit[1];
          } );
          return params;
        },
        params = {},
        ajaxObject, dataParams, navigator;


    // disabled
    if( anchor.hasClass( "disabled" ) ) {
      return false;
    }

    // empty
    if( target.hasClass( "empty" ) ) {
      if( dataParams ) {
        params = makeParams( dataParams );
      }
      params["id"] = target.attr( "id" );
      _c.callAjax(
        [ { folder: "procedure", name: "getContent", params: params } ],
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
              anchor.addClass( "hash" );
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

      // params
      if( dataParams ) {
        params = makeParams( dataParams );
      }

      // action      
      if( action ) {
        ajaxObject = { folder: "procedure", name: action };
        if( params ){
          ajaxObject["params"] = params;
        }

        _c.callAjax(
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
              if( ajaxItem.hash ) {
                window.location.hash = href;
                anchor.addClass( "hash" );
              }
              target.removeClass( "empty" );
            }

            // details
            if( ajaxItem.details ) {
              _edit.showDetails( ajaxItem.details );
            }
            return false;
          }
        );
        return false;
      } else if( trigger ) {
        target.trigger( trigger, params );
        return false;
      }
    }

    navigator = anchor.parent();
    navigator.addClass( "selected" );
    if( navigator.get(0) ) {
      navigator.siblings( navigator.get(0).tagName ).removeClass( "selected" );
    }
    target.addClass( "target" );
    if( target.get(0) ) {
      target.siblings( target.get(0).tagName ).removeClass( "target" );
    }
    if( anchor.hasClass( "hash" ) ) {
      return true;
    }
    return false;
  }
}
