"use strict";

var _edit = {
  compatibilityList: ["fontface", "svg"],
  lang: "fr",
  msg: false,
  transformation: false,

  /****************************************************************************/
  observeList: [
    { query: "form",            script: "form" },
    { query: "button",          script: "button" },
    { query: "a[href^=#]",      script: "anchor" },
    { query: ".list-container", script: "list" }
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

      _c.select( "#contextMenu" ).click( _edit.closeContextMenu );

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
    _c.select( "#dialog" ).hide();
    _c.select( "#error-msg" ).html( msg ).show();
    window.location.hash = "";
  },

  /****************************************************************************/
  showDialog: function( dialog ) {
    _edit.replaceContent( { query: "#dialog-content", innerHtml: dialog } );
    _c.select( "#dialog" ).css( { display: "table" } ).click( function( ev ) {
      var target = $( ev.target );
      if( target.attr( "id" ) && target.attr( "id" ) == "dialog-content" ) {
        target.parent().hide();
        target.html( "" );
      }
    } );
  },

  /****************************************************************************/
  closeDialog: function() {
    _c.select( "#dialog" ).addClass( "hidden" );;
    _c.select( "#dialog-content" ).html( "" );
  },

  /****************************************************************************/
  showContextMenu: function( targetList, event ) {
    var actionList = [];

    // build
    _c.eachItem( targetList, function( targetItem ) {
      actionList.push( "<li><a data-query='#" + targetItem.id + "'>" + targetItem.title + "</a></li>" );
    } );
    _c.select( "#contextMenu" ).html( "<menu>" + actionList.join( "" ) + "</menu>" ).removeClass( "hidden" );

    // menu
    $( "#contextMenu > menu" ).css( {
      "top": ( event.pageY - 20 ) + "px",
      "left": event.pageX + "px"
    } )
    .find( "a" ).click( function() {
      $( $( this ).data( "query" ) ).trigger( "click" );
    } );
  },

  /****************************************************************************/
  closeContextMenu: function() {
    _c.select( "#contextMenu" ).addClass( "hidden" ).html( "" );
  },

  /****************************************************************************/
  showDetails: function( details ) {
    _edit.replaceContent( { query: "#details", innerHtml: details } );
    $( "#details > *" ).resizable().draggable( {
      handle: "h2"
    } );
    _c.select( "#details" ).removeClass( "hidden" );
  },

  /****************************************************************************/
  replaceContent: function( replacement ) {
    var object = $( replacement.query );

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
