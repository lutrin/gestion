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
    form.find( "input:submit" ).each( function() {
      return $( this ).click( app.submit );
    } );

    // autofocus
    if( !Modernizr.input.autofocus ) {
      form.find( "input[autofocus]" ).focus();
    }

    // radio and checkbox
    form.find( "input:checkbox,input:radio" ).click( function() {
      var checkbox = $( this ),
          selected = checkbox.is(":checked");
      if( checkbox.is(":radio" ) ) {
        form.find( "[name=" + checkbox.attr( "name" ) + "]" ).parent().removeClass( "selected" );
      }
      if( checkbox.is(":checked") ) {
        checkbox.parent().addClass( "selected" );
      } else {
        checkbox.parent().removeClass( "selected" );
      }
    } );

    // apply change
    form.find( "input:text,input:password,textarea" ).change( function() {
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
    if( form.find( "input:password" ).size() ) {
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
            [ { folder: "procedure", name: action, params: fields, method: "POST" } ],
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
              form.find( ":password" ).val( "" );
              if( ajaxItem.values ) {
                for( key in ajaxItem.values ) {
                  form.find( "[name=" + key + "]:first" ).val( ajaxItem.values[key] );
                }
              }

              // replacement
              if( ajaxItem.replacement ) {
                _c.eachItem( ajaxItem.replacement, _edit.replaceContent );
              }

              // details
              if( ajaxItem.details ) {
                _edit.showDetails( ajaxItem.details );
              }

              // close dialog
              _edit.closeDialog();
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
    form.find( "input:text,input:hidden,input:password,input:checkbox,select,textarea" ).each( function() {
      var object = $( this ),
          type = object.attr( "type" ) || object.tagName,
          value = object.val(),
          name, compareObject;

      // password
      if( value !== "" && type == "password" ) {
        value = _c.ajaxList.interaction.sha1.get( value );
      }

      // checkbox
      if( type == "checkbox" ) {
        if( object.is(":checked") ) {
          value = value || 1;
        } else {
          value = null;
        }
      }

      // required
      if( object.attr( "required" ) && ( value === "" || value === null ) ) {
        error = true;
        app.showMsg( object, _edit.msg( "required" ) );
      }

      // pattern
      if( object.attr( "pattern" ) && !value.match( object.attr( "pattern" ) ) ) {
        error = true;
        app.showMsg( object, _edit.msg( "wrongformat" ) );
      }

      // equal
      if( object.data( "equal" ) ) {
        compareObject = form.find( "[name=" + object.data( "equal" ) + "]" );
        if( compareObject.size() ) {
          if( object.val() != compareObject.val() ) {
            error = true;
            app.showMsg( object, _edit.msg( "notequal" ) );
          }
        }
      }

      if( !error && value !== null ) {
        name = object.attr( "name" );
        if( fields[name] ) {
          fields[name] = _c.makeArray( fields[name] );
          fields[name].push( value );
        } else {
          fields[name] = value;
        }
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
