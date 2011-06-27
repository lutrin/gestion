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
//TODO call ajax data file type and text type
  /****************************************************************************/
  change: function() {
    var inputFile = $( this ),
        files = this.files;
    return _c.callAjax( [
          { folder: "data", name: "filetype" },
          { folder: "data", name: "texttype" }
        ], function( ajaxItem ) {
      var app = _c.ajaxList.interaction.fileupload,
          appForm = _c.ajaxList.interaction.form,
          fileupload = inputFile.parents( ".fileUpload:first" ),
          uploadedList = fileupload.find( ".uploadedList" ),
          fields = _c.jsonToUrl( appForm.serialize( inputFile.parents( "form:first" ) ) ),
          i, l;
      fileupload.find( "input" ).addClass( "hidden" );
      for( i = 0, l = files.length; i < l; ) {
        app.send( files[i++], uploadedList, fields );
      }
    } );
  },

  /****************************************************************************/
  send: function( file, uploadedList, fields ) {
    var app = _c.ajaxList.interaction.fileupload,
        xhr = new XMLHttpRequest(),
        filename = file.name || file.fileName,
        filesize = _c.getHumanSize( file.size || file.fileSize ),
        filetype = file.type,
        index = uploadedList.find( "li" ).size(),
        className = app.getClass( filename, filetype ),
        progress;
    uploadedList.append( "<li class='" + className + "'>" +
                           "<dl>" +
                              "<dt>Nom de fichier</dt><dd>" + filename + "</dd>" +
                              "<dt>Taille</dt><dd>" + filesize + "</dd>" +
                              "<dt>Type</dt><dd>" + filetype + "</dd>" +
                           "</dl>" +
                           "<progress data-index='" + index + "' value='0' max='100'>" +
                             "Téléversement...<span>0</span>%" +
                           "</progress>" +
                           "<hr />" +
                         "</li>" );
    progress = uploadedList.find( "progress[data-index=" + index + "]" );
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
    xhr.open( "POST", "procedure/controller.php?filename=" + encodeURIComponent( filename ) + "&" + fields, true ); //TODO PHP: file_get_contents("php://input")

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
