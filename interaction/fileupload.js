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
        files = this.files,
        fileupload = $( this ).parents( ".fileupload:first" ),
        i, l;
    for( i = 0, l = files.length; i < l; ) {
      app.send( files[i++], fileupload );
    }
  },

  /****************************************************************************/
  send: function( file, fileupload ) {
    var app = _c.ajaxList.interaction.fileupload,
        xhr = new XMLHttpRequest(),
        progress;

    xhr.upload.onprogress = app.progress;
    xhr.upload.onloadstart = function( ev ) {
      progress = fileupload.append( "<progress value='0' max='100'>Téléchargement...</progress>" );
    };    
    xhr.upload.onload = app.success;
    xhr.upload.onerror = app.error;

    xhr.open( "POST", "procedure/controller.php", true ); //TODO PHP: file_get_contents("php://input")
    xhr.setRequestHeader("Content-Type", "application/octet-stream" );
    xhr.setRequestHeader( "X-Filename", ( file.name || file.fileName ) ); //TODO PHP: $_SERVER['HTTP_X_Filename']

    if( 'getAsBinary' in file ) {
      // Firefox 3.5
      xhr.sendAsBinary(file.getAsBinary());
    } else {
      // W3C-blessed interface
      xhr.send(file);
    }
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
