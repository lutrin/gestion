var _tag = {
  build: function( tagName, attributes, innerHtml ) {
    return "<" +
              tagName +
              this.getAttributes( attributes ) +
              this.getInnerHtml( tagName, innerHtml ) +
            ">";
  },

  /****************************************************************************/
  getAttributes: function( attributes ) {
    var key;
    if( !attributes ) {
      return "";
    }
    attributeList = [];
    for( key in attributes ) {
      attributeList.push( key + "='" + attributes[key] + "'" );
    }
    return " " + attributeList.join( " " );
  },

  /****************************************************************************/
  getInnerHtml: function( tagName, innerHtml ) {
    if( innerHtml === false ) {
      return "/";
    }
    return ">" + ( $.isArray( innerHtml )? innerHtml.join( "" ): innerHtml ) + "</" + tagName;
  }
};
