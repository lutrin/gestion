{
  initialize: function() {
    var listContainer = $( this ),
        app = _c.ajaxList.interaction.list;

    // already initiated
    if( listContainer.hasClass( "initiated" ) ) {
      return false;
    }

    // mode choice
    listContainer.find( ".mode:first" ).change( app.changeMode );

    // sortable
    listContainer.find( ".sortable" ).click( app.sort );

    // fitrable
    listContainer.find( ".setFilter" ).bind( "setFilter", app.setFilter );

    // selectable
    if( listContainer.find( ".selectable:first" ).size() ) {
      listContainer.find( ".row" ).click( app.setSelectRow );
      listContainer.find( ".selectRow" ).change( app.changeSelectRow );
      listContainer.find( ".selectAll" ).change( app.changeSelectAll );
    }

    // row action
    listContainer.find( ".row[data-action]" ).dblclick( app.rowDblclick );

    // context menu
    listContainer.find( ".row" ).rightClick( app.rowRightClick );

    listContainer.addClass( "initiated" );
  },

  /****************************************************************************/
  reinitialize: function( listContainer ) {
    var app = _c.ajaxList.interaction.list;

    // selectable
    if( listContainer.find( ".selectable:first" ).size() ) {
      listContainer.find( ".selected .selectRow" ).each( function() {
        $( this ).attr( "checked", true );
      } );
      listContainer.find( ".row" ).click( app.setSelectRow );
      listContainer.find( ".selectRow" ).change( app.changeSelectRow );
    }
    listContainer.find( ".row" ).each( function() {
      var row = $( this );
      row.find( ".initiated" ).removeClass( "initiated" );
      _edit.observe( row );
    } );
  },

  /****************************************************************************/
  changeMode: function() {
    var select = $( this ),
        mode = select.val(),
        listContainer = select.parents( ".list-container:first" );
    _c.eachItem( ["table", "compact", "tree", "gallery"], function( oldMode ) {
      listContainer.removeClass( oldMode );
    } );
    listContainer.addClass( mode );
  },

  /****************************************************************************/
  sort: function() {
    var app       = _c.ajaxList.interaction.list,
        cell      = $( this ).parent(),
        header    = cell.parent(),
        list      = header.parent(),
        index     = cell.index() - 1,
        order     = cell.hasClass( "sorted_asc" )? "desc": "asc",
        valueList = [];

    // get item index
    list.find( ".row" ).each( function() {
      var row = $( this );
      valueList.push( {
        "compare": $( row.find( ".cell" ).get( index ) ).text(),
        "outerHtml": row.clone().wrap('<div></div>').parent().html()
      } );
    } ).remove();

    // order
    header.find( ".cell" ).each( function() {
      var cell = $( this );
      cell.removeClass( "sorted_asc" );
      cell.removeClass( "sorted_desc" );
    } );

    // sort
    _c.eachItem(
      valueList.sort( app["sort_" + order] ),
      function( value ) {
        list.append( value.outerHtml );
      }
    );
    cell.addClass( "sorted_" + order );
    app.reinitialize( list.parent() );
  },

  /****************************************************************************/
  sort_asc: function( a, b ) {
    return ( a.compare > b.compare )? 1: -1;
  },

  /****************************************************************************/
  sort_desc: function( a, b ) {
    return ( a.compare < b.compare )? 1: -1;
  },

  /****************************************************************************/
  setFilter: function() {
    var app = _c.ajaxList.interaction.list;
    _edit.replaceContent( {
      query: this.parentNode,
      innerHtml: "<ui.form><ui.field type='hidden' name='trigger' value='applyFilter' /><ui.field type='search' name='filter' autofocus='autofocus'/></ui.form>",
      callback: app.initFilterForm
    } );
  },

  /****************************************************************************/
  initFilterForm: function( form ) {
    var search = form.find( "input[type=search]" );
    search.focus();
    search.blur( function() {
      form.remove();
    } );
  },

  /****************************************************************************/
  setSelectRow: function( event ) {
    var app = _c.ajaxList.interaction.list,
        row = $( this ),
        checkbox = row.find( ".selectRow:first" ),
        target = $( event.target );
    if( target.is( "button, .selectRow" ) ) {
      return true;
    } else if( target.is( "a" ) ) {
      return false;
    }
    if( checkbox.is(":checked") ) {
      checkbox.attr( "checked", false );
    } else {
      checkbox.attr( "checked", true );
    }
    checkbox.each( app.changeSelectRow );
  },

  /****************************************************************************/
  changeSelectRow: function() {
    var checkbox = $( this );
    if( checkbox.is(":checked") ) {
      checkbox.parent().parent().addClass( "selected" );
    } else {
      checkbox.parent().parent().removeClass( "selected" );
    }
  },

  /****************************************************************************/
  changeSelectAll: function() {
    var app = _c.ajaxList.interaction.list,
        selectAll = $( this ),
        list = selectAll.parents( ".list:first" ),
        selectAllValue = selectAll.is(":checked");
    list.find( ".row" ).each( function() {
      var checkbox = $( this ).find( ".selectRow" );
      checkbox.attr( "checked", selectAllValue );
      checkbox.each( app.changeSelectRow );
    } );
  },

  /****************************************************************************/
  rowDblclick: function( event ) {
    var row = $( this );

    // selectable
    if( row.parents( ".list.selectable:first" ).size() ) {
      row.parent().children( ".selected" ).each( function() {
        var selectedRow = $( this );
        selectedRow.removeClass( "selected" );
        selectedRow.find( ".selectRow:first" ).attr( "checked", false );
      } );
      row.find( ".selectRow:first" ).attr( "checked", true );
      row.addClass( "selected" );
    }
    row.find( "[data-action=" + row.data( "action" ) + "]:first" ).trigger( "click" );
  },

  /****************************************************************************/
  rowRightClick: function( event ) {
    var row = $( this ),
        targetList = [];

    // selectable
    if( row.parents( ".list.selectable:first" ).size() ) {
      row.find( ".selectRow:first" ).attr( "checked", true );
      row.addClass( "selected" );
      if( row.parent().children( ".selected" ).size() > 1 ) {
        row = row.parent().children( ".header:first" );
      }
    }

    // action list
    row.find( "button" ).each( function() {
      var object = $( this );
      targetList.push( { "id": object.attr( "id" ), "title": object.attr( "title" ) } );
    } );

    _edit.showContextMenu( targetList, event );
  }
}
