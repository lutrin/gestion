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

    // sort choice
    listContainer.find( ".sort:first" ).change( app.changeSort );

    // sortable
    listContainer.find( ".sortable" ).click( app.sort );

    // fitrable
    listContainer.find( ".setFilter" ).bind( "setFilter", app.setFilter );

    // initialize
    app.initializeRowList( listContainer );

    // list to pick
    if( listContainer.hasClass( "listToPick" ) ) {
      listContainer.find( ".level1" ).each( app.removeDisabled );
    }

    listContainer.addClass( "initiated" );
  },

  /****************************************************************************/
  reinitialize: function( listContainer ) {
    var app = _c.ajaxList.interaction.list;
    app.initializeRowList( listContainer );

    // re-observe
    listContainer.find( ".row" ).each( function() {
      var row = $( this );
      row.find( ".initiated" ).removeClass( "initiated" );
      _edit.observe( row );
    } );
  },

  /****************************************************************************/
  initializeRowList: function( listContainer ) {
    var app = _c.ajaxList.interaction.list;

    // selectable
    if( listContainer.find( ".selectable:first" ).size() ) {
      listContainer.find( ".selected .selectRow" ).each( function() {
        $( this ).attr( "checked", true );
      } );
      listContainer.find( ".row > .cell" ).click( app.setSelectRow );
      listContainer.find( ".selectRow" ).change( app.changeSelectRow );
      listContainer.find( ".selectAll" ).click( app.changeSelectAll );
    }

    // row action
    listContainer.find( ".row[data-action]" ).dblclick( app.rowDblclick );

    // context menu
    listContainer.find( ".row" ).rightClick( app.rowRightClick );
    listContainer.find( ".icon" ).click( app.iconClick );

    // expand
    listContainer.find( ".toggleExpand" ).click( app.toggleExpand );
  },

  /****************************************************************************/
  changeMode: function() {
    var select = $( this ),
        mode = select.val(),
        listContainer = select.parents( ".list-container:first" );
    _c.eachItem( ["table", "compact", "tree", "gallery", "resume"], function( oldMode ) {
      listContainer.removeClass( oldMode );
    } );
    listContainer.addClass( mode );
    _c.setAccountStorage( select.attr( "id" ), mode );
  },

  /****************************************************************************/
  changeSort: function() {
    var select          = $( this ),
        value           = select.val();

    // click header column
    $(
      "#" +
      select.parents( ".list-container:first" ).attr( "id" ) +
      "-sort-" +
      select.val()
    ).trigger( "click" );

    // set acount storage
    _c.setAccountStorage( select.attr( "id" ), value );
  },

  /****************************************************************************/
  sort: function() {
    var app       = _c.ajaxList.interaction.list,
        anchor    = $( this ),
        cell      = anchor.parent(),
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

    // select
    cell.parents( ".list-container:first" ).find( ".sort:first" ).val( anchor.data( "value" ) );

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
        row = $( this ).parent(),
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
    var app =  _c.ajaxList.interaction.list,
        checkbox = $( this ),
        row      = checkbox.parents( ".row:first" );
    if( checkbox.is(":checked") ) {
      row.addClass( "selected" );
      row.parents( ".row" ).each( app.unselectLineage );
      row.find( ".row" ).each( app.unselectLineage );
    } else {
      row.removeClass( "selected" );
    }
  },

  /****************************************************************************/
  unselectLineage: function() {
    $( this ).removeClass( "selected" ).children( ".cell > .selectRow" ).attr( "checked", false );
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
  iconClick: function( event ) {
    var row = $( this ).parents( ".row:first" ),
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
    row.children( ".action" ).children( "button" ).each( function() {
      var object = $( this );
      targetList.push( { "id": object.attr( "id" ), "title": object.attr( "title" ) } );
    } );

    _edit.showContextMenu( targetList, event );
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
    row.children( ".action" ).children( "button" ).each( function() {
      var object = $( this );
      targetList.push( { "id": object.attr( "id" ), "title": object.attr( "title" ) } );
    } );

    _edit.showContextMenu( targetList, event );
  },

  /****************************************************************************/
  toggleExpand: function() {
    var app = _c.ajaxList.interaction.list,
        row = $( this ).parents( ".row:first" ),
        id = row.attr( "id" ),
				listContainer = row.parents( ".list-container:first" ),
				listContainerId = ( listContainer.attr( "id" ) + "-" ),
				expandedList = [];
    if( row.hasClass( "collapsed" ) ) {
      row.removeClass( "collapsed" ).addClass( "expanded" );
    } else if( row.hasClass( "expanded" ) ) {
      row.removeClass( "expanded" ).addClass( "collapsed" );
    }
		listContainer.find( ".expanded" ).each( function() {
			expandedList.push( $( this ).attr( "id" ).replace( listContainerId, "" ) );
		} );
    _c.setAccountStorage( listContainer.attr( "id" ) + "-expanded", expandedList );
  },

  /****************************************************************************/
  removeDisabled: function() {
    var row = $( this ),
        app = _c.ajaxList.interaction.list,
        children = row.children( ".row" ),
        main, inner;
    children.each( app.removeDisabled );
    children = row.children( ".row" );
    if( row.hasClass( "disabled" ) && !children.size() ) {
      row.remove();
    } else {
      main = row.children( ".main:first > a:last" );
      inner = main.html();
      main.replaceWith( "<span>" + inner + "</span>" );
    }
  }
  
}
