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
        picklist = $( this ),
        html, inner, main, toRemove;

    // html
    if( picklist.hasClass( "multiple" ) ) {
      html = "<li class='pickitem'>" +
               "<span>{text}</span>" +
               "<input type='hidden' value='{value}' name='{name}' id='{id}-{value}'>" +
               "<a title='Exclure' class='remove'></a>" +
             "</li>";
    } else {
      html = "<div class='pickitem'>" +
               "<span>{text}</span>" +
               "<input type='hidden' value='{value}' name='{name}' id='{id}-{value}'>" +
               "<a title='Exclure' class='remove'></a>" +
             "</div>";
    }

    // text
    row.parents( ".row" ).each( function() {
      text.unshift( _c.trim( $( this ).children( ".main:first" ).text() ) );
    } );

    html = html.replace( /\{text\}/g, text.pop() )
               .replace( /\{value\}/g, params.k )
               .replace( /\{name\}/g, picklist.data( "name" ) )
               .replace( /\{id\}/g, picklist.attr( "id" ) );
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
    if( picklist.hasClass( "multiple" ) ) {
      picklist.append( html ).find( ".remove" ).click( app.remove );
    } else {
      picklist.html( html ).find( ".remove" ).click( app.remove );
      _edit.closeDialog();
    }
    picklist.trigger( "change" );

  }
}
