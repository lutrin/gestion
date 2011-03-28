"use strict";

var _edit = {
  compatibilityList: [""],
  lang: "fr",
  msg: false,

  /****************************************************************************/
  /*uiForm: function( caller ) {
    return _c.callAjax(
      [ { folder: "interaction", name: "form" } ],
      function( ajaxItem ) {
        objectList.each( ajaxItem["caller"] );
        return false;
      }
    );
  },*/

  /****************************************************************************/
  observeList: [
    { query: "form", script: "form" }
  ],

  /****************************************************************************/
  load: function() {
    // initialize language
    this.lang = _c.select( "#title" ).attr( "lang" );

    // initialize message
    return _c.callAjax( [ { folder: "data", name: "msg" } ], function( ajaxItem ) {
      _edit.msg = ajaxItem;

      // is compatible
      if( !_edit.isCompatible() ) {
        return false;
      }

      // display main
      _c.select( "#main" ).fadeIn();

      // observe
      _edit.observe( _c.select( "#main" ) );

      return false;
    } );
  },

  /****************************************************************************/
  isCompatible: function() {
    var i = 0,
        l = this.compatibilityList.length;
    for( i = 0, l = this.compatibilityList.length; i < l; ) {
      if( !Modernizr[this.compatibilityList[i++]] ) {
        return this.showError( this.msg.notcompatible[this.lang] );
      }
    }
    return true;
  },

  /****************************************************************************/
  showError: function( msg ) {
    _c.select( "#main" ).hide();
    _c.select( "#error-msg" ).html( msg ).show();
    return false;
  },

  /****************************************************************************/
  observe: function( object ) {
    _c.eachItem( _edit.observeList, function( observeItem ) {
      var subObjectList = object.find( observeItem.query );
      if( subObjectList.size() ) {
        _edit.initialize( subObjectList, observeItem.script );
      }
    } );
    return false;
  },

  /****************************************************************************/
  initialize: function( objectList, script ) {
    return _c.callAjax(
      [ { folder: "interaction", name: script } ],
      function( ajaxItem ) {
        objectList.each( ajaxItem.initialize );
        return false;
      }
    );
  },
};

$( document ).ready( function() {
  return _edit.load();
} );
