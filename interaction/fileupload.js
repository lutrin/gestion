{
  initialize: function() {
    var fileupload = $( this ),
        app = _c.ajaxList.interaction.fileupload,
        file = fileupload.find( ":file" ),
        button = fileupload.find( ":button" );

    // already initiated
    if( fileupload.hasClass( "initiated" ) ) {
      return false;
    }

    // change
    file.change( app.change );

    fileupload.addClass( "initiated" );
  },

  /****************************************************************************/
  change: function() {
    var inputFile = $( this ),
        files = this.files;
    return _c.callAjax( [
          { folder: "data",     name: "filetype" },
          { folder: "data",     name: "texttype" },
          { folder: "template", name: "uploadrow" }
        ], function( ajaxItem ) {
      var app = _c.ajaxList.interaction.fileupload,
          appForm = _c.ajaxList.interaction.form,
          fileupload = inputFile.parents( ".fileUpload:first" ),
          uploadedList = fileupload.find( ".uploadedList" ),
          maxfilesize = fileupload.find( "[name=MAX_FILE_SIZE]:first" ).val(),
          fields = _c.jsonToUrl( appForm.serialize( inputFile.parents( "form:first" ) ) ),
          i, l;
      fileupload.find( "input" ).addClass( "hidden" );
      for( i = 0, l = files.length; i < l; ) {
        app.send( files[i++], uploadedList, fields, maxfilesize );
      }
    } );
  },

  /****************************************************************************/
  send: function( file, uploadedList, fields, maxfilesize ) {
    var app = _c.ajaxList.interaction.fileupload,
        xhr = new XMLHttpRequest(),
        filename = file.name || file.fileName,
        filesize = file.size || file.fileSize,
        filehumansize = _c.getHumanSize( filesize ),
        filetype = file.type,
        index = uploadedList.find( ".row" ).size(),
        className = app.getClass( filename, filetype ),
        progress;

    // template
    uploadedList.append(
      _c.ajaxList.template.uploadrow
        .replace( /{className}/g, className )
        .replace( /{filename}/g, filename )
        .replace( /{filesize}/g, filesize )
        .replace( /{filetype}/g, filetype )
        .replace( /{index}/g, index )
        .replace( /{uploading}/g, "Téléversement" )
    );

    //progress
    progress = uploadedList.find( "progress[data-index=" + index + "]" );

    //validation
    if( filesize > maxfilesize ) {
      progress.parents( ".row:first" ).addClass( "invalid" );
      progress.replaceWith( "<div class='alertMsg'>" + _edit.msg( "toobig" ) + "</span>" );
      return false;
    }

    xhr.upload.addEventListener('progress', function( ev ) {
      var percent = parseInt( ev.loaded / ev.total * 100 );
      progress.attr( "value", percent );
      progress.children( "span" ).html( percent );
    }, false);
    xhr.upload.onload = function( ev ) {
      progress.attr( "value", 100 );
      progress.children( "span" ).html( 100 );
    };
    xhr.upload.onerror = function( ev ) {
      progress.after( "<div>Erreur</div>" );
      progress.remove();
    };
    xhr.open( "POST", "procedure/controller.php?filename=" + encodeURIComponent( filename ) + "&" + fields, true );

    xhr.setRequestHeader("Content-Type", "application/octet-stream" );

    if( 'getAsBinary' in file ) {
      // Firefox 3.5
      xhr.sendAsBinary(file.getAsBinary());
    } else {
      // W3C-blessed interface
      xhr.send(file);
    }
  },

  /****************************************************************************/
  getClass: function( file, type ) {
    var classList = _c.ajaxList.data.filetype,
        className = "file",
        decomposed;
    for( key in classList ) {
      if( _c.inList( type, classList[key] ) ) {
        className = key;
        break;
      }
    }
    if( className != "text" ) {
      return className;
    }
    decomposed = file.split( /\./ );
    return _c.ajaxList.data.texttype[decomposed.pop()] || className;
  }
}
