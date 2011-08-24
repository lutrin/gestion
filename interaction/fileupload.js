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
          { folder: "data",     name: "exttype" },
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
        .replace( /{filesize}/g, filehumansize )
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
      progress.replaceWith( "<span class='alertSuccess'>" + _edit.msg( "finishsuccess" ) + "</span>" );
    };
    xhr.upload.onerror = function( ev ) {
      progress.replaceWith( "<div class='alertMsg'>Erreur</div>" );
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
    var list = _c.ajaxList.data.filetype,
        className = "file",
        decomposed, extension;
    for( key in list ) {
      if( _c.inList( type, list[key] ) ) {
        className = key;
        break;
      }
    }
console.log( [file,type,className] );
    if( _c.inList( className, ["file", "ogg", "text"] ) ) {
      decomposed = file.split( /\./ );
      extension = decomposed.pop().toLowerCase()
      list = _c.ajaxList.data.exttype;
      for( key in list ) {
        if( _c.inList( extension, list[key] ) ) {
          className = key;
          break;
        }
      }
    }
    return className;
  }
}
