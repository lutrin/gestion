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
          _c.eachItem( ajaxItem.replacement, function( replacement ) {
            $( replacement.query ).xslt(
              "<app.start>" + replacement.innerHtml + "</app.start>",
              _edit.transformation(),
              _edit.observe
            );
          } );
        }

        // dialog
        if( ajaxItem.dialog ) {
          _c.select( "#dialog" ).xslt(
            "<app.start>" + ajaxItem.dialog + "</app.start>",
            _edit.transformation(),
            function( object ) {
              _edit.observe( object.show() );
              object.click( function( ev ) {
                var target = $( ev.target );
                if( target.attr( "id" ) && target.attr( "id" ) == "dialog" ) {
                  target.hide();
                }
              } );
            }
          );
        }
        return false;
      }
    );
  }
}
