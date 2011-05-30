{
  initialize: function() {
    var dip = $( this ),
        app = _c.ajaxList.interaction.dip;

    // already initiated
    if( dip.hasClass( "initiated" ) ) {
      return false;
    }

    // remove
    dip.find( ".dipitem > .remove" ).click( app.remove );

    // add
    dip.bind( "add", app.add );
    dip.addClass( "initiated" );
  },

  /****************************************************************************/
  remove: function() {
    $( this ).parent().remove();
  },

  /****************************************************************************/
  add: function( event, params ) {
    var value = params.k,
        row = $( "#" + params.object + "-" + params.k ),
        text = _c.trim( row.children( ".main:first" ).text() );
    row.remove();
/*
<li class="dipitem">
    <span>###text###</span>
    <input type="hidden" value="###value###" name="###name###" id="###id###-###value###">
    <a title="Exclure" class="remove"></a>
</li>
*/
  }
}
