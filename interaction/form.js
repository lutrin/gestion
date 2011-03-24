{
  initialize: function() {
    var form = $( this ),
        app = _c.ajaxList.interaction.form;

    // already initiated
    if( form.hasClass( "initiated" ) ) {
      return false;
    }

    // submit
    form.submit( function() {
      return false;
    } );
    form.find( "input[type=submit]" ).each( function() {
      return $( this ).click( app.submit );
    } );

    // autofocus
    if( !Modernizr.input.autofocus ) {
      form.find( "input[autofocus]" ).focus();
    }

    form.addClass( "initiated" );
    return false;
  },

  /****************************************************************************/
  submit: function() {
    var form = $( this ).parents( "form:first" ),
        app = _c.ajaxList.interaction.form,
        scriptList = [];

    // remove alert message
    form.find( ".alertMsg" ).remove();
    form.find( ".invalid" ).removeClass( "invalid" );

    // prepare
    if( form.find( "input[type=password]" ).size() ) {
      scriptList.push( { folder: "interaction", url: "../../external/", name: "sha1" } );
    }

    // serialize
    _c.callAjax( scriptList, function( ajaxItem ) {
      var fields = app.serialize( form, app ),
          action;

      if( fields ) {
        action = fields.action;
        delete fields.action;

        // send
        return _c.callAjax(
          [ { folder: "procedure", name: action, params: fields } ],
          function( ajaxItem ) {

            // fatal error
            if( ajaxItem.fatalError ) {
              _edit.showError( _edit.msg[ajaxItem.fatalError][_edit.lang] );
              return false;
            }

            // fields error
            if( ajaxItem.errorList ) {
              _c.eachItem( ajaxItem.errorList, function( errorItem ) {
                return app.showMsg( form.find( "[name=" + errorItem.name + "]:first" ), _edit.msg[errorItem.msg][_edit.lang] );
              } );
            }
            return false;
          }
        );
      }
      return false;
    } );
    return false;
  },

  /****************************************************************************/
  serialize: function( form, app ) {
    var fields = {},
        error = false;
    form.find( "input[type=text],input[type=hidden],input[type=password]" ).each( function() {
      var object = $( this ),
          type = object.attr( "type" ),
          value = _c.trim( object.val() );

      // trim
      if( _c.inList( type, ["hidden", "text"] ) ) {
        value = _c.trim( value );
      }

      // password
      if( value !== "" && type == "password" ) {
        value = _c.ajaxList.interaction.sha1.get( value );
      }

      // required
      if( object.attr( "required" ) && value === "" ) {
        error = true;
        app.showMsg( object, _edit.msg.required[_edit.lang] );
      } else if( !error ) {
        fields[object.attr( "name" )] = value;
      }
    } );
    if( error ) {
      return false;
    }
    return fields;
  },

  /****************************************************************************/
  showMsg: function( field, msg ) {
    var div = field.parents( ".field:first" );
    if( div.hasClass( "invalid" ) ) {
      return false;
    }
    div.addClass( "invalid" );
    div.append( "<div class='alertMsg'>" + msg + "</div>" );
    return false;
  }
}
