{
  initialize: function() {
    var pick = $( this ),
        app = _c.ajaxList.interaction.pick;

    // already initiated
    if( pick.hasClass( "initiated" ) ) {
      return false;
    }

    // remove
    pick.find( ".pickitem > .remove" ).click( app.remove );

    // add
    pick.bind( "add", app.add );
    pick.addClass( "initiated" );
  },

  /****************************************************************************/
  remove: function() {
    var object = $( this );
    object.parents( ".picklist:first" ).trigger( "change" );
    object.parent().remove();
  },

  /****************************************************************************/
  add: function( event, params ) {
    var app = _c.ajaxList.interaction.pick,
        value = params.k,
        row = $( "#" + params.object + "-" + params.k ),
        text = [ _c.trim( row.children( ".main:first" ).text() ) ],
        ul = $( this ),
        html = "<li class='pickitem'>" +
                 "<span>###text###</span>" +
                 "<input type='hidden' value='###value###' name='###name###' id='###id###-###value###'>" +
                 "<a title='Exclure' class='remove'></a>" +
               "</li>",
        inner, main, toRemove;

    // text
    row.parents( ".row" ).each( function() {
      text.unshift( _c.trim( $( this ).children( ".main:first" ).text() ) );
    } );

    html = html.replace( "###text###", text.pop() )
               .replace( "###value###", params.k )
               .replace( "###name###", ul.data( "name" ) )
               .replace( "###id###", ul.attr( "id" ) );
    if( row.find( ".row" ).size() ) {
      main = row.find( ".main:first > a:last" );
      inner = main.html();
      main.replaceWith( "<span>" + inner + "</span>" );
      row.addClass( "disabled" );
    } else {
      toRemove = row;
      while( toRemove.parent().hasClass( "disabled" ) ) {
        toRemove = toRemove.parent();
      }
      toRemove.remove();
    }
    ul.append( html ).find( ".remove" ).click( app.remove );
    ul.trigger( "change" );
  }
}
