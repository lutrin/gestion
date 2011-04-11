{
  initialize: function() {
    var listContainer = $( this ),
        app = _c.ajaxList.interaction.list;

    // already initiated
    if( listContainer.hasClass( "initiated" ) ) {
      return false;
    }

    // mode choice
    listContainer.children( ".mode:first" ).change( app.changeMode );

    // TODO sortable
    listContainer.find( ".sortable" ).click( app.sort );

    listContainer.addClass( "initiated" );
  },

  /****************************************************************************/
  changeMode: function() {
    var select = $( this ),
        mode = select.val(),
        listContainer = select.parent();
    _c.eachItem( ["table", "compact", "tree", "gallery"], function( oldMode ) {
      listContainer.removeClass( oldMode );
    } );
    listContainer.addClass( mode );
  },

  /****************************************************************************/
  sort: function() {
    // get item index
    var header = $( this ).parent(),
        index = header.index() - 1,
        list =  header.parents( ".list:first" ),
        valueList = [];
    list.find( ".row" ).each( function() {
      var row = $( this );
      valueList.push( {
        "compare": $( row.find( ".cell" ).get( index ) ).text(),
        "outerHtml": row.clone().wrap('<div></div>').parent().html()
      } );
    } ).remove();
    _c.eachItem( valueList.sort(
      function( a, b ) {
        return a.compare > b.compare;
      } ),
      function( value ) {
        list.append( value.outerHtml );
      }
    );
  }
}
