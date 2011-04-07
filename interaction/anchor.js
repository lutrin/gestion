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
        app = _c.ajaxList.interaction.button,
        href = anchor.attr( "href" ),
        target = $( href );

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
    }

    anchor.parents( "li:first" ).addClass( "selected" );
    target.addClass( "target" );
    return false;
  }
}
