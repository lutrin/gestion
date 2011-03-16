var _tag = {
  transform: function( objects ) {
    var list, attributeList, innerHtml;

    // object
    if( _c.typeOf( objects ) == "object" ) {
      return _tag.transformIndividual( objects );

    // array
    } else if( _c.typeOf( objects ) == "array" ) {
      list = [];
      _c.eachItem( objects, function( object ) {
        list.push( _tag.transform( object ) );
        return false;
      } );
      return tagList.join();
    }

    // others
    return objects.toString();
  },

  /****************************************************************************/
  transformIndividual: function( object ) {
    return _c.format(
      "<{1}{2}{3}>",
      [
        object.tag,
        _tag.getAttributeList( object.attribute ),
        _tag.getInnerHtml( object.tag, object.innerHtml )
      ]
    );
  },

  /****************************************************************************/
  getAttributeList: function( attributes ) {
    var attributeList = [];
    attributes = _c.makeArray( attributes );
    for( attribute in attributes ) {
      attributeList.push( _c.format(
        "{1}='{2}'",
        [ attribute, attributes[attribute] ]
      ) );
    }
    return ( attributeList? ( " " + attributeList.join( " " ) ): "" );
  },

  /****************************************************************************/
  getInnerHtml: function( tag, innerHtml ) {
    return (
      ( object.innerHtml || object.innerHtml === "" )?
      _c.format( ">{1}</{2}", [
        _tag.transform( object.innerHtml ),
        object.tag
      ] ):
      "/"
    );
  }
};
