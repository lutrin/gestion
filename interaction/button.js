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
          _edit.showError( _edit.msg[ajaxItem.fatalError][_edit.lang] );
          return false;
        }

        // replacement
        if( ajaxItem.replacement ) {
          _c.eachItem( ajaxItem.replacement, function( replacement ) {
            $( replacement.query ).xslt( "<app.start>" + replacement.innerHtml + "</app.start>", "transformation/all.xsl", function( object ) {
              _edit.observe( object );
            } );
          } );
        }

        // dialog
        if( ajaxItem.dialog ) {
          _c.select( "#dialog" ).xslt( "<app.start>" + ajaxItem.dialog + "</app.start>", "transformation/all.xsl", function( object ) {
            _edit.observe( object.show() );
            object.click( function() {
              $( this ).hide();
            } );
          } );
        }
        return false;
      }
    );
  }
}
