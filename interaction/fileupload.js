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
    var app = _c.ajaxList.interaction.fileupload,
        appForm = _c.ajaxList.interaction.form,
        inputFile = $( this ),
        files = this.files,
        fileupload = inputFile.parents( ".fileUpload:first" ),
        fields = _c.jsonToUrl( appForm.serialize( inputFile.parents( "form:first" ) ) ),
        i, l;
    for( i = 0, l = files.length; i < l; ) {
      app.send( files[i++], fileupload, fields );
    }
  },

  /****************************************************************************/
  send: function( file, fileupload, fields ) {
    var app = _c.ajaxList.interaction.fileupload,
        xhr = new XMLHttpRequest(),
        filename = file.name || file.fileName,
        progress = fileupload.append( "<progress value='0' max='100'>Téléchargement...</progress>" );



/*    xhr.upload.addEventListener('progress', app.progress, false);*/
    /*xhr.onreadystatechange = function( ev ) {
      if( xhr.readyState == 4 ) {
        app.success();
      }
    };*/
    xhr.open( "PUT", "procedure/controller.php?filename=" + encodeURIComponent( filename ) + "&" + fields, true ); //TODO PHP: file_get_contents("php://input")

    xhr.upload.onprogress = function( ev ) {
      progress.attr( "value", ev.loaded / ev.total * 100 );
    };
    xhr.upload.onload = app.success;
    xhr.upload.onerror = app.error;
    xhr.setRequestHeader("Content-Type", "application/octet-stream" );

    if( 'getAsBinary' in file ) {
      // Firefox 3.5
      xhr.sendAsBinary(file.getAsBinary());
    } else {
      // W3C-blessed interface
      xhr.send(file);
    }
    xhr.upload.onload = app.success;
  },

  /****************************************************************************/
  progress: function( ev ) {
    var percent = ev.loaded / ev.total * 100;
console.log('Upload progress: ' + percent + '%');
  },

  /****************************************************************************/
  success: function( ev ) {
console.log('Success');
  },

  /****************************************************************************/
  error: function( ev ) {
console.log('Error');
  }
}
