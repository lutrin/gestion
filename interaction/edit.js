"use strict";

var _edit = {
  compatibilityList: ["fontface", "svg"],
  lang: "fr",
  msg: false,
  transformation: false,

  /****************************************************************************/
  observeList: [
    { query: "form",              script: "form" },
    { query: "button",            script: "button" },
    { query: "a[href^=#]",        script: "anchor" },
    { query: ".list-container",   script: "list" },
    { query: ".picklist",         script: "pick" },
    { query: ".fileUpload",       script: "fileupload" },
    { query: "audio",             script: "audio" },
    { query: "img[data-src]",     script: "image" },
    { query: "[contentEditable]", script: "contentEditable" }
  ],

  /****************************************************************************/
  load: function() {

    // debug mode
    //$( "html" ).addClass( "holmes-debug" );

    // initialize language
    this.lang = _c.select( "#title" ).attr( "lang" );

    // initialize message
    return _c.callAjax( [
      { folder: "data", name: "msg" },
      { folder: "transformation", name: "html5-ui" }
    ], function( ajaxItem ) {

      // msg
      _edit.msg = function( msg ) {
        return _c.ajaxList.data.msg[msg][_edit.lang];
      };

      // is compatible
      if( !_edit.isCompatible() ) {
        return false;
      }

      // common error
      _c.showAjaxError = function( XMLHttpRequest, textStatus, errorThrown ) {
        _edit.showError( XMLHttpRequest.responseText || _edit.msg( textStatus ) || _edit.msg( "emptyresponse" ) );
      }

      // display and observe
      _c.eachItem( ["#main", "#header-buttons"], function( object ) {
        _edit.observe( _c.select( object ) );
      } );

      $( "#contextMenu" ).click( _edit.closeContextMenu );

      // transformation
      _edit.transformation = function() {
        return _c.ajaxList.transformation["html5-ui"];
      };
    } );
  },

  /****************************************************************************/
  isCompatible: function() {
    var i = 0,
        l = this.compatibilityList.length;
    for( i = 0, l = this.compatibilityList.length; i < l; ) {
      if( !Modernizr[this.compatibilityList[i++]] ) {
        return this.showError( this.msg( "notcompatible" ) );
      }
    }
    return true;
  },

  /****************************************************************************/
  showError: function( msg ) {
    _c.select( "#main" ).hide();
    _c.select( "#header-buttons" ).html( "" );
    _c.select( "#dialog" ).addClass( "hidden" );
    _c.select( "#error-msg" ).html( msg ).show();
    window.location.hash = "";
  },

  /****************************************************************************/
  showDialog: function( dialog ) {
    _edit.replaceContent( { query: "#dialog-content", innerHtml: dialog } );
    _c.select( "#dialog" ).removeClass( "hidden" ).click( function( ev ) {
      var target = $( ev.target );
      if( target.attr( "id" ) && target.attr( "id" ) == "dialog-content" ) {
        target.parent().addClass( "hidden" );
        target.html( "" );
      }
    } );
  },

  /****************************************************************************/
  closeDialog: function() {
    _c.select( "#dialog" ).addClass( "hidden" );
    _c.select( "#dialog-content" ).html( "" );
  },

  /****************************************************************************/
  showContextMenu: function( targetList, event ) {
    var actionList = [], optionList, contextMenu;

    // build
    _c.eachItem( targetList, function( targetItem ) {
      if( targetItem.tag && targetItem.tag == "select" ) {
        optionList = [];
        $( "#" + targetItem.id ).find( "option" ).each( function() {
          var option = $( this );
          optionList.push( option.attr( "value" ) + "'>" + option.html() );
        } )
        actionList.push(
          "<span>" + targetItem.title + "</span>" +
          "<menu data-select='#" + targetItem.id + "'><li><a data-value='" + optionList.join( "</a></li><li><a data-value='" ) + "</a></li></menu>"
        );
      } else {
        actionList.push( "<a data-query='#" + targetItem.id + "'>" + targetItem.title + "</a>" );
      }
    } );
    $( "#contextMenu" ).html( "<menu><li>" + actionList.join( "</li><li>" ) + "</li></menu>" ).removeClass( "hidden" );

    // menu
    contextMenu = $( "#contextMenu > menu" );
    contextMenu.css( {
      "top": event.pageY + "px",
      "left": event.pageX + "px"
    } );
    contextMenu.find( "a[data-query]" ).click( function() {
      $( $( this ).data( "query" ) ).trigger( "click" );
    } );
    contextMenu.find( "a[data-value]" ).click( function() {
      var option = $( this ),
          select = $( option.parent().parent().data( "select" ) );
      select.val( option.data( "value" ) );
      select.trigger( "change" );
    } );
  },

  /****************************************************************************/
  closeContextMenu: function() {
    $( "#contextMenu" ).addClass( "hidden" ).html( "" );
  },

  /****************************************************************************/
  showDetails: function( details ) {
    _edit.replaceContent( { query: "#details", innerHtml: details } );
    /*$( "#details > *" ).resizable().draggable( {
      handle: "h2"
    } );*/
    $( "#details" ).removeClass( "hidden" );
  },

  /****************************************************************************/
  replaceContent: function( replacement ) {
    var object = $( replacement.query ),
        clone;

    // inner html
    if( replacement.innerHtml ) {
      object.xslt(
        "<app.start>" + replacement.innerHtml + "</app.start>",
        _edit.transformation(),
        function() {
          _edit.observe( object );
          if( replacement.callback ) {
            replacement.callback( object );
          }
        }
      );
    }

    // attribute list
    if( replacement.attributeList ) {
      _c.eachItem( replacement.attributeList, function( attribute ) {
        object.attr( attribute.name, attribute.value );
      } ); 
    }
  },

  /****************************************************************************/
  observe: function( object ) {
    _c.eachItem( _edit.observeList, function( observeItem ) {
      var subObjectList = object.find( observeItem.query );
      if( subObjectList.size() ) {
        _edit.initialize( subObjectList, observeItem.script );
      }
    } );
  },

  /****************************************************************************/
  initialize: function( objectList, script ) {
    return _c.callAjax(
      [ { folder: "interaction", name: script } ],
      function( ajaxItem ) {
        objectList.each( ajaxItem.initialize );
      }
    );
  }
};

$( document ).ready( _edit.load );
