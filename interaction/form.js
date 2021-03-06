{
  changeable: "input:text,input:password,input:checkbox,input:radio,select,textarea",

  /****************************************************************************/
  initialize: function() {
    var form = $( this ),
        app = _c.ajaxList.interaction.form,
        changeable;

    // already initiated
    if( form.hasClass( "initiated" ) ) {
      return false;
    }

    // submit
    form.submit( function() {
      app.submit();
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

    // changeable
    changeable = form.find( app.changeable );

    // data-display
    form.find( "[data-display]" ).each( function() {
      app.setDisplay( $( this ), changeable );
    } );

    // apply change
    form.find( app.changeable ).change( app.change );
    form.find( ".picklist" ).on( "change", app.change );

    form.addClass( "initiated" );
  },

  /****************************************************************************/
  setDisplay: function( object, changeable ) {
    var app = _c.ajaxList.interaction.form,
        displayList = object.data( "display" ).split( "=" ),
        inputValue = _c.trim( displayList[1] ),
        inputList = changeable.filter( "[name=" + _c.trim( displayList[0] ) + "]" );

    // get value
    app.setDisplayVisibility( inputList, object, inputValue );
    inputList.change( function() {
      app.setDisplayVisibility( inputList, object, inputValue );
    } );
  },

  /****************************************************************************/
  setDisplayVisibility: function( inputList, object, inputValue ) {
    var finaleValue,
        app = _c.ajaxList.interaction.form;
    inputList.each( function() {
      var input = $( this ),
          value = input.val();

      // checkbox
      if( input.attr( "type" ) && _c.inList( input.attr( "type" ), ["checkbox","radio"] ) ) {
        value = app.getCheckedValue( input );
      }
      if( value !== null ) {
        if( finaleValue ) {
          finaleValue = _c.makeArray( finaleValue );
          finaleValue.push( value );
        } else {
          finaleValue = value;
        }
      }
    } );
    if( finaleValue == inputValue ) {
      object.removeClass( "hidden" );
    } else {
      object.addClass( "hidden" );
    }
  },

  /****************************************************************************/
  change: function() {
    var object = $( this );
    object.addClass( "changed" );
    object.parents( "form:first" ).find( "input[type=submit][disabled=disabled]" ).attr( "disabled", false );
  },

  /****************************************************************************/
  getCheckedValue: function( input ) {
    return input.is(":checked")? ( input.val() || 1 ): null;
  },

  /****************************************************************************/
  submit: function() {
    var form = $( this ).parents( "form:first" ),
        app = _c.ajaxList.interaction.form,
        scriptList = [];

    // remove alert message
    form.find( ".alertMsg,.formMsg" ).remove();
    form.find( ".invalid" ).removeClass( "invalid" );
    form.find( "input[type=submit]" ).attr( "disabled", true );

    // prepare
    if( form.find( "input:password" ).size() ) {
      scriptList.push( { folder: "interaction", url: "../../external/", name: "sha1" } );
    }

    // serialize
    _c.callAjax( scriptList, function( ajaxItem ) {
      var fields = app.serialize( form ),
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
  serialize: function( form ) {
    var app = _c.ajaxList.interaction.form,
        fields = {},
        error = false;
    form.find( "input:text,input:hidden,input:password,input:checkbox,input:radio,select,textarea,[contentEditable]" ).each( function() {
      var object = $( this ),
          type = object.attr( "type" ) || ( object.attr( "contentEditable" )? "contentEditable": false ) ||object.tagName,
          value = object.val(),
          name, compareObject;

      // password
      if( value !== "" && type == "password" ) {
        value = _c.ajaxList.interaction.sha1.get( value );
      }

      // checkbox
      if( _c.inList( type, ["checkbox","radio"] ) ) {
        value = app.getCheckedValue( object );
      }

      // contentEditable
      if( type == "contentEditable" ) {
        value = object.html();
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
        name = object.attr( "name" ) || object.data( "name" );
        if( fields[name] ) {
          fields[name] = _c.makeArray( fields[name] );
          fields[name].push( value );
        } else {
          fields[name] = value;
        }
      }
    } );
    form.find( "[data-required]" ).each( function() {
      var object = $( this ),
          name = object.data( "name" );
      if( !fields[name] ) {
        error = true;
        app.showMsg( object, _edit.msg( "required" ) );
      }
    } );
    if( error ) {
      return false;
    }
    return fields;
  },

  /****************************************************************************/
  showMsg: function( field, msg ) {
    var div = field.parents( ".field:first, .formlist:first" );
    if( div.hasClass( "invalid" ) ) {
      return false;
    }
    div.addClass( "invalid" );
    div.append( "<div class='alertMsg'>" + msg + "</div>" );
  }
}
