<?php
class ui_Frame {
  protected static $defaultFields = array(
    "lang"        => "",
    "charset"     => "",
    "title"       => "",
    "description" => "",
    "author"      => "",
    "meta"        => "",
    "stylesheet"  => "",
    "head_script" => "",
    "class"       => "",
    "body"        => "",
    "body_script" => ""
  );

  /****************************************************************************/
  public static function buildHtml( $params = false ) {
    global $FRAME;

    Includer::add( "tag" );

    # empty fields
    $fields = self::$defaultFields;

    # set parameters
    foreach( $params as $key => $item ) {

      # stylesheet and javascript
      if( in_array( $key, array( "stylesheet", "head_script", "body_script", "meta" ) ) ) {
        if( $item ) {

          # make array
          $item = is_array( $item )? $item: array( $item );
          $fieldList = array();

          # build meta list
          if( $key == "meta" ) {
            foreach( $item as $name => $content ) {
              $fieldList[] = Tag::build( "meta", array( "name" => $name, "content" => $content ) );
            }

          # build style list
          } elseif( $key == "stylesheet" ) {
            $fieldList = array_map( function( $href ) {
              return Tag::build( "link", array( "rel" => "stylesheet", "href" => $href ) );
            }, $item );

          # build script list
          } else {
            $fieldList = array_map( function( $src ) {
              return Tag::build( "script", array( "src" => $src ), " " );
            }, $item );
          }

          $fields[$key] = join( "", $fieldList );
        }

      # body
      } elseif( $key == "body" ) {

        # xml
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML( "<app.start>" . str_replace( "&", "&amp;", $item ) . "</app.start>" );

        # xsl
        $xslDoc = new DOMDocument();
        $xslDoc->load( "../transformation/all.xsl" );
        $proc = new XSLTProcessor();
        $proc->importStylesheet( $xslDoc );
        $fields[$key] = str_replace( "&amp;", "&", $proc->transformToXML( $xmlDoc ) );

      } else {
        $fields[$key] = $item;
      }
    }

    # replace
    return replaceFields( $fields, file_get_contents( $FRAME ) );
  }
}
