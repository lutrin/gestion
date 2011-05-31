{
  initialize: function() {
    var dip = $( this ),
        app = _c.ajaxList.interaction.dip;

    // already initiated
    if( dip.hasClass( "initiated" ) ) {
      return false;
    }

    // remove
    dip.find( ".dipitem > .remove" ).click( app.remove );

    // add
    dip.bind( "add", app.add );
    dip.addClass( "initiated" );
  },

  /****************************************************************************/
  remove: function() {
    $( this ).parent().remove();
  },

  /****************************************************************************/
  add: function( event, params ) {
    var app = _c.ajaxList.interaction.dip,
        value = params.k,
        row = $( "#" + params.object + "-" + params.k ),
        text = [ _c.trim( row.children( ".main:first" ).text() ) ],
        ul = $( this ),
        html = "<li class='dipitem'>" +
                 "<span>###text###</span>" +
                 "<input type='hidden' value='###value###' name='###name###' id='###id###-###value###'>" +
                 "<a title='Exclure' class='remove'></a>" +
               "</li>";

    // text
    row.parents( ".row" ).each( function() {
      text.unshift( _c.trim( $( this ).children( ".main:first" ).text() ) );
    } );

    html = html.replace( "###text###", text.join( "/" ) )
               .replace( "###value###", params.k )
               .replace( "###name###", ul.data( "name" ) )
               .replace( "###id###", ul.attr( "id" ) );
    row.parents( ".row" ).remove();
    row.remove();
    ul.append( html ).find( ".remove" ).click( app.remove );

  }
}
