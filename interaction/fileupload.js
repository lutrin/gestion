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

    // change http://igstan.ro/posts/2009-01-11-ajax-file-upload-with-pure-javascript.html
    file.change( function() {
console.log( this );
console.log( fileupload.find( ".status" ) );
      fileupload.find( ".status" ).removeClass( "hidden" );
      fileupload.append( "<img src='" + this.files[0].getAsDataURL() + "' />" );
      fileupload.parents( "form:first" ).submit();
    } );

    fileupload.addClass( "initiated" );
  }
}
