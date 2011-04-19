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

    // apply change
    form.find( "input[type=text],input[type=password],textarea" ).change( function() {
      $( this ).addClass( "changed" );
    } );

    form.addClass( "initiated" );
  },

  /****************************************************************************/
  submit: function() {
    var form = $( this ).parents( "form:first" ),
        app = _c.ajaxList.interaction.form,
        scriptList = [];

    // remove alert message
    form.find( ".alertMsg,.formMsg" ).remove();
    form.find( ".invalid" ).removeClass( "invalid" );

    // prepare
    if( form.find( "input[type=password]" ).size() ) {
      scriptList.push( { folder: "interaction", url: "../../external/", name: "sha1" } );
    }

    // serialize
    _c.callAjax( scriptList, function( ajaxItem ) {
      var fields = app.serialize( form, app ),
          action, trigger;

      if( fields ) {
        if( fields.action ) {
          action = fields.action;
          delete fields.action;

          // send
          return _c.callAjax(
            [ { folder: "procedure", name: action, params: fields } ],
            function( ajaxItem ) {
              var key;

              // fatal error
              if( ajaxItem.fatalError ) {
                _edit.showError( _edit.msg( ajaxItem.fatalError ) );
                return false;
              }

              // fields error
              if( ajaxItem.errorList ) {
                _c.eachItem( ajaxItem.errorList, function( errorItem ) {
                  return app.showMsg(
                    form.find( "[name=" + errorItem.name + "]:first" ),
                    _edit.msg( errorItem.msg )
                  );
                } );
              }

              // form error
              if( ajaxItem.formError ) {
                form.append( "<div class='formMsg'>" + _edit.msg( ajaxItem.formError ) + "</div>" );
              }

              // values
              form.find( "[type=password]" ).val( "" );
              if( ajaxItem.values ) {
                for( key in ajaxItem.values ) {
                  form.find( "[name=" + key + "]:first" ).val( ajaxItem.values[key] );
                }
              }

              // replacement
              if( ajaxItem.replacement ) {
                _c.eachItem( ajaxItem.replacement, _edit.replaceContent );
              }
              _c.select( "#dialog" ).hide();
              return false;
            }
          );
        } else if( fields.trigger ) {
          trigger = fields.trigger;
          delete fields.trigger;
          form.trigger( trigger, fields );
        }
      }
    } );
    return false;
  },

  /****************************************************************************/
  serialize: function( form, app ) {
    var fields = {},
        error = false;
    form.find( "input[type=text],input[type=hidden],input[type=password],select" ).each( function() {
      var object = $( this ),
          type = object.attr( "type" ) || object.tagName,
          value = _c.trim( object.val() ),
          compareObject;

      // trim
      if( _c.inList( type, ["text"] ) ) {
        value = _c.trim( value );
      }

      // password
      if( value !== "" && type == "password" ) {
        value = _c.ajaxList.interaction.sha1.get( value );
      }

      // required
      if( object.attr( "required" ) && value === "" ) {
        error = true;
        app.showMsg( object, _edit.msg( "required" ) );
      }

      // equal
      if( object.attr( "data-equal" ) ) {
        compareObject = form.find( object.attr( "data-equal" ) );
        if( compareObject.size() ) {
          if( object.val() != compareObject.val() ) {
            error = true;
            app.showMsg( object, _edit.msg( "notequal" ) );
          }
        }

        // equal
        /*
        error = true;
        app.showMsg( object, _edit.msg( "required" ) +  );*/
      }

      if( !error ) {
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
  }
}
