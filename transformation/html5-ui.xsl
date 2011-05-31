<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="*">
  <xsl:copy><xsl:call-template name="apply-tag" /></xsl:copy>
</xsl:template>

<!-- Not supported in HTML5 -->
<xsl:template match="acronym">
  <abbr><xsl:call-template name="apply-tag" /></abbr>
</xsl:template>

<xsl:template match="applet">
  <object><xsl:call-template name="apply-tag" /></object>
</xsl:template>

<xsl:template match="basefont|big|center|font|s|strike">
  <span><xsl:call-template name="apply-tag" /></span>
</xsl:template>

<xsl:template match="b">
  <strong><xsl:call-template name="apply-tag" /></strong>
</xsl:template>

<xsl:template match="i">
  <em><xsl:call-template name="apply-tag" /></em>
</xsl:template>

<xsl:template match="u">
  <span><xsl:call-template name="apply-tag" /></span>
</xsl:template>

<xsl:template match="dir">
  <ul><xsl:call-template name="apply-tag" /></ul>
</xsl:template>

<xsl:template match="frame|frameset|noframes" />

<xsl:template match="xmp">
  <pre><xsl:call-template name="apply-tag" /></pre>
</xsl:template>

<!-- adaptation -->
<!--<xsl:template match="a">
  <a>
    <xsl:call-template name="apply-tag" />
    <xsl:if test=".=''">
      <xsl:value-of select="@href" />
    </xsl:if>
  </a>
</xsl:template>-->

<!-- application -->
<xsl:template match="app.start">
  <xsl:apply-templates/>
</xsl:template>

<!-- user interface -->
<xsl:template match="ui.form">
  <form>
    <xsl:call-template name="apply-attributelist" />
    <xsl:if test="@closable">
      <button class="close" data-trigger="close">
        <span class="hidden"><xsl:text>Fermer</xsl:text></span>
      </button>
    </xsl:if>
    <xsl:apply-templates/>
  </form>
</xsl:template>

<xsl:template match="ui.field">
  <xsl:choose>

    <!-- textarea -->
    <xsl:when test="@type='textarea'">
      <div>
        <xsl:call-template name="apply-topfield" />
        <textarea>Ceci est un text area</textarea>
      </div>
    </xsl:when>

    <!-- select -->
    <xsl:when test="@type='select'">
      <div>
        <xsl:call-template name="apply-topfield" />
        <select>
          <xsl:for-each select="@id|@name|@required|@autofocus|@autocomplete|@multiple|@class">
            <xsl:call-template name="apply-attribute" />
          </xsl:for-each>
          <xsl:apply-templates select="ui.datalist" mode="select" />
        </select>
      </div>
    </xsl:when>

    <!-- hidden -->
    <xsl:when test="@type='hidden'">
      <input>
        <xsl:for-each select="@id|@name|@type|@value">
          <xsl:call-template name="apply-attribute" />
        </xsl:for-each>
        <xsl:call-template name="apply-value-attribute" />
      </input>
    </xsl:when>

    <!-- checkbox -->
    <xsl:when test="@type='checkbox'">
      <div>
        <xsl:attribute name="class">
          <xsl:text>field checkbox</xsl:text>
        </xsl:attribute>
        <input>
          <xsl:for-each select="@id|@name|@type|@required|@value|@disabled">
            <xsl:call-template name="apply-attribute" />
          </xsl:for-each>
          <xsl:if test="ui.value=@value">
            <xsl:attribute name="checked"><xsl:text>checked</xsl:text></xsl:attribute>
          </xsl:if>
        </input>
        <xsl:call-template name="apply-label" />
      </div>
    </xsl:when>

    <!-- checklist -->
    <xsl:when test="@type='checklist'">
      <fieldset>
        <xsl:if test="@label">
          <legend><xsl:value-of select="@label" /></legend>
        </xsl:if>
        <xsl:apply-templates select="ui.datalist" mode="checklist" />
      </fieldset>
    </xsl:when>

    <!-- diplist -->
    <xsl:when test="@type='diplist'">
      <fieldset>
        <xsl:if test="@label">
          <legend><xsl:value-of select="@label" /></legend>
        </xsl:if>
        <xsl:apply-templates select="ui.datalist" mode="diplist" />
      </fieldset>
    </xsl:when>

    <!-- input -->
    <xsl:otherwise>
      <div>
        <xsl:call-template name="apply-topfield" />
        <input>
          <xsl:for-each select="@id|@name|@type|@required|@autofocus|@autocomplete|@maxlength|@size|@value">
            <xsl:call-template name="apply-attribute" />
          </xsl:for-each>
          <xsl:if test="@equal">
            <xsl:attribute name="data-equal">
              <xsl:value-of select="@equal" />
            </xsl:attribute>
          </xsl:if>
          <xsl:call-template name="apply-value-attribute" />
        </input>
      </div>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template match="ui.datalist" mode="select">
  <xsl:apply-templates select="ui.dataitem" mode="select" />
</xsl:template>

<xsl:template match="ui.datalist" mode="checklist">
  <ul class="checklist">
    <xsl:apply-templates select="ui.dataitem" mode="checklist" />
  </ul>
</xsl:template>

<xsl:template match="ui.datalist" mode="diplist">
  <ul class="diplist">
    <xsl:for-each select="../@id">
      <xsl:call-template name="apply-attribute" />
    </xsl:for-each>
    <xsl:attribute name="data-name">
      <xsl:value-of select="../@name" />
    </xsl:attribute>
    <xsl:apply-templates select="ui.dataitem" mode="diplist" />
  </ul>
  <button data-action="dip" data-params="object={../@object},for=#{../@id}">Piger</button>
</xsl:template>

<xsl:template match="ui.dataitem" mode="select">
  <option>
    <xsl:if test="@value">
      <xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
      <xsl:if test="../../ui.value">
        <xsl:variable name="isInList">
          <xsl:call-template name="inList">
            <xsl:with-param name="list" select="../../ui.value" /> 
            <xsl:with-param name="search" select="@value" />
          </xsl:call-template>
        </xsl:variable>
        <xsl:if test="$isInList=1">
          <xsl:attribute name="selected">
            <xsl:text>selected</xsl:text>
          </xsl:attribute>
        </xsl:if>
      </xsl:if>
    </xsl:if>
    <xsl:value-of select="@label" />
  </option>
</xsl:template>

<xsl:template match="ui.dataitem" mode="checklist">
  <li>
    <xsl:variable name="id">
      <xsl:value-of select="../../@id"/>
      <xsl:text>-</xsl:text>
      <xsl:value-of select="@key"/>
    </xsl:variable>

    <input type="checkbox">
      <xsl:attribute name="id">
        <xsl:value-of select="$id"/>
      </xsl:attribute>
      <xsl:attribute name="name">
        <xsl:value-of select="../../@name"/>
      </xsl:attribute>
      <xsl:for-each select="@disabled">
        <xsl:call-template name="apply-attribute" />
      </xsl:for-each>
      <xsl:if test="@value">
        <xsl:attribute name="value">
          <xsl:value-of select="@value"/>
        </xsl:attribute>
        <xsl:if test="../../ui.value">
          <xsl:variable name="isInList">
            <xsl:call-template name="inList">
              <xsl:with-param name="list" select="../../ui.value" /> 
              <xsl:with-param name="search" select="@value" />
            </xsl:call-template>
          </xsl:variable>
          <xsl:if test="$isInList=1">
            <xsl:attribute name="checked">
              <xsl:text>checked</xsl:text>
            </xsl:attribute>
          </xsl:if>
        </xsl:if>
      </xsl:if>
    </input>
    <xsl:call-template name="apply-label">
      <xsl:with-param name="for" select="$id" />
    </xsl:call-template>
  </li>
</xsl:template>

<xsl:template match="ui.dataitem" mode="diplist">
  <xsl:if test="../../ui.value">
    <xsl:variable name="isInList">
      <xsl:call-template name="inList">
        <xsl:with-param name="list" select="../../ui.value" /> 
        <xsl:with-param name="search" select="@value" />
      </xsl:call-template>
    </xsl:variable>
    <xsl:if test="$isInList=1">
      <li class="dipitem">
        <span><xsl:value-of select="@label"/></span>
        <input type="hidden">
          <xsl:attribute name="id">
            <xsl:value-of select="../../@id"/>
            <xsl:text>-</xsl:text>
            <xsl:value-of select="@key"/>
          </xsl:attribute>
          <xsl:attribute name="name">
            <xsl:value-of select="../../@name"/>
          </xsl:attribute>
          <xsl:attribute name="value">
            <xsl:value-of select="@key"/>
          </xsl:attribute>
        </input>
        <a class="remove" title="Exclure"></a>
      </li>
    </xsl:if>
  </xsl:if>
</xsl:template>

<xsl:template match="ui.dock">
  <div class="dock-container">
    <xsl:call-template name="apply-attributelist" />
    <nav class="dock">
      <xsl:if test="ui.headtitle">
        <h2><xsl:value-of select="ui.headtitle"/></h2>
      </xsl:if>
      <menu>
        <xsl:for-each select="ui.item">
          <li>
            <xsl:call-template name="apply-nav-attributelist" />
            <a>
              <xsl:attribute name="href">
                <xsl:value-of select="concat('#',@href)" />
              </xsl:attribute>
              <xsl:if test="@label">
                <xsl:attribute name="title">
                  <xsl:value-of select="@label" />
                </xsl:attribute>
              </xsl:if>
              <span>
                <xsl:if test="@label">
                  <xsl:value-of select="@label"/>
                </xsl:if>
              </span>
            </a>
          </li>
        </xsl:for-each>
      </menu>
    </nav>
    <xsl:call-template name="apply-itemlist" />
  </div>
</xsl:template>

<xsl:template match="ui.tabs">
  <div class="tabs-container">
    <xsl:call-template name="apply-attributelist" />
    <nav class="tabs">
      <xsl:if test="ui.headtitle">
        <h2><xsl:value-of select="ui.headtitle"/></h2>
      </xsl:if>
      <menu>
        <xsl:for-each select="ui.item">
          <li>
            <xsl:call-template name="apply-itemAnchor" />
          </li>
        </xsl:for-each>
      </menu>
    </nav>
    <xsl:call-template name="apply-itemlist" />
  </div>
</xsl:template>

<xsl:template match="ui.accordion">
  <div class="accordion-container accordion">
    <xsl:if test="ui.headtitle">
      <h2><xsl:value-of select="ui.headtitle"/></h2>
    </xsl:if>
    <xsl:for-each select="ui.item">
      <h3>
        <xsl:call-template name="apply-itemAnchor" />
      </h3>
      <xsl:call-template name="apply-itemSection" />
      <hr />
    </xsl:for-each>
  </div>
</xsl:template>

<xsl:template match="ui.list">
  <div id="{@id}">

    <!-- class -->
    <xsl:attribute name="class">
      <xsl:text>list-container</xsl:text>
      <xsl:for-each select="ui.option">
        <xsl:value-of select="concat( ' ', @value )" />
      </xsl:for-each>
      <!--<xsl:value-of select="concat( 'list-container ', ui.option/@value )" />-->
    </xsl:attribute>

    <!-- headtitle -->
    <xsl:if test="ui.headtitle">
      <h3><xsl:value-of select="ui.headtitle"/></h3>
    </xsl:if>

    <!-- option -->
    <xsl:if test="ui.option/@count > 1">
      <fieldset class="option">
        <legend>Options</legend>
        <xsl:for-each select="ui.option">
          <xsl:if test="@count > 1">
            <xsl:apply-templates select="ui.field" />
          </xsl:if>
        </xsl:for-each>
      </fieldset>
    </xsl:if>

    <xsl:call-template name="apply-listpart" />

    <!-- addable -->
    <xsl:if test="@addable|@refreshable">
      <fieldset>
        <legend>Fonctions</legend>
        <xsl:if test="@addable">
          <button class="add" data-action="add" data-params="object={@id}">Ajouter</button>
        </xsl:if>
        <xsl:if test="@refreshable">
          <button class="refresh" data-action="refresh" data-params="object={@id}">Actualiser</button>
        </xsl:if>
      </fieldset>
    </xsl:if>
  </div>
</xsl:template>

<xsl:template match="ui.listpart">
  <xsl:call-template name="apply-listpart" />
</xsl:template>

<xsl:template name="apply-listpart">
  <!-- object -->
  <xsl:variable name="object">
    <xsl:value-of select="@id" />
  </xsl:variable>

  <!-- list -->
  <div>

    <!-- class -->
    <xsl:attribute name="class">
      <xsl:text>list</xsl:text>
      <xsl:if test="@selectable">
        <xsl:text> selectable</xsl:text>
      </xsl:if>
    </xsl:attribute>

    <!-- header -->
    <div class="header">

      <!-- selectable -->
      <xsl:if test="@selectable">
        <div class="cell">
          <input id="selectAll-{$object}" type="checkbox" class="selectAll" name="selectAll" title="Sélectionner tous" />
          <label for="selectAll-{$object}" class="hidden">Sélectionner tous</label>
        </div>
      </xsl:if>

      <!-- headercolumn -->
      <xsl:for-each select="ui.headercolumn">
        <div class="cell">

          <!-- hidden -->
          <xsl:if test="@hidden">
            <xsl:attribute name="class"><xsl:text>hidden</xsl:text></xsl:attribute>
          </xsl:if>

          <!-- sortable -->
          <xsl:choose>
            <xsl:when test="@sortable">
              <a id="{$object}-sort-{@id}" data-value="{@id}" href="#sort" class="sortable" title="Trier"><xsl:value-of select="." /></a>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="." />
            </xsl:otherwise>
          </xsl:choose>

          <!-- filtrable -->
          <xsl:if test="@filtrable">
            <div class='filtrable'>
              <button class="setFilter" data-trigger="setFilter"/>
            </div>
          </xsl:if>
        </div>
      </xsl:for-each>

      <!-- multipleAction -->
      <xsl:for-each select="ui.action">
        <div class="cell action">
          <xsl:choose>
            <xsl:when test="@multiple">
              <button id="{@key}-{$object}-selection" class="{@key}" data-action="{@key}" data-params="object={$object},row=selection" title="{@title}">
                <span class="hidden"><xsl:value-of select="@title" /></span>
              </button>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text> </xsl:text>
            </xsl:otherwise>
          </xsl:choose>
        </div>
      </xsl:for-each>
    </div>
    <hr class="hidden" />

    <!-- row -->
    <xsl:apply-templates select="ui.row">
      <xsl:with-param name="object" select="$object"/>
      <xsl:with-param name="rowAction" select="@rowAction"/>
      <xsl:with-param name="selectable" select="@selectable"/>
      <xsl:with-param name="expandable" select="@expandable"/>
      <xsl:with-param name="main" select="@main"/>
      <xsl:with-param name="mainAction" select="@mainAction"/>
      <xsl:with-param name="mainTrigger" select="@mainTrigger"/>
      <xsl:with-param name="mainHref" select="@mainHref"/>
      <xsl:with-param name="action" select="ui.action"/>
      <xsl:with-param name="headercolumn" select="ui.headercolumn"/>
    </xsl:apply-templates>
  </div>
</xsl:template>

<xsl:template match="ui.row">
  <xsl:param name="object" select="0"/>
  <xsl:param name="rowAction" select="0"/>
  <xsl:param name="selectable" select="0"/>
  <xsl:param name="expandable" select="0"/>
  <xsl:param name="main" select="0"/>
  <xsl:param name="mainAction" select="0"/>
  <xsl:param name="mainTrigger" select="0"/>
  <xsl:param name="mainHref" select="0"/>
  <xsl:param name="action" select="0"/>
  <xsl:param name="headercolumn" select="0"/>
  <xsl:param name="level" select="1"/>
  <xsl:param name="parentId" select="concat($object,'-0')"/>

  <!-- k -->
  <xsl:variable name="k">
    <xsl:value-of select="@id" />
  </xsl:variable>

  <!-- rowId -->
  <xsl:variable name="rowId">
    <xsl:value-of select="concat($object,'-',$k)" />
  </xsl:variable>

  <div>

    <!-- attributes -->
    <xsl:for-each select="@id|@data-action">
      <xsl:call-template name="apply-attribute" />
    </xsl:for-each>

    <!-- class -->
    <xsl:attribute name="class">
      <xsl:text>row level</xsl:text>
      <xsl:value-of select="$level" />
      <xsl:if test="@childList">
        <xsl:choose>
          <xsl:when test="@expanded">
            <xsl:text> expanded</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text> collapsed</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:if>
      <!--<xsl:if test="$level &gt; 1">
        <xsl:text> hidden</xsl:text>
      </xsl:if>-->
    </xsl:attribute>

    <!-- id -->
    <xsl:attribute name="id">
       <xsl:value-of select="$rowId" />
    </xsl:attribute>

    <!-- parent id -->
    <xsl:attribute name="data-parentId">
       <xsl:value-of select="$parentId" />
    </xsl:attribute>

    <!-- rowAction -->
    <xsl:if test="$rowAction != 0">
      <xsl:attribute name="data-action">
        <xsl:value-of select="$rowAction"/>
      </xsl:attribute>
    </xsl:if>

    <!-- selectable -->
    <xsl:if test="$selectable != 0">
      <div class="cell">
        <input id="selectRow-{$rowId}" type="checkbox" class="selectRow" name="selectRow" value="{@id}" title="Sélectionner"/>
        <label for="selectRow-{$rowId}" class="hidden">Sélectionner</label>
      </div>
    </xsl:if>

    <!-- cell -->
    <xsl:for-each select="ui.cell">
      <div>
        <xsl:variable name="position" select="position()"/> 

        <!-- class -->
        <xsl:attribute name="class">
          <xsl:text>cell</xsl:text>
          <xsl:if test="@key=$main">
            <xsl:text> main</xsl:text>
          </xsl:if>
          <xsl:if test="@class">
            <xsl:value-of select="concat(' ', @class)"/>
          </xsl:if>
          <xsl:for-each select="$headercolumn[position()=$position]">
            <xsl:if test="@class">
              <xsl:value-of select="concat(' ', @class)"/>
            </xsl:if>
          </xsl:for-each>
        </xsl:attribute>  
        <xsl:for-each select="$headercolumn[position()=$position]">
          <xsl:if test="@hidden">
            <xsl:attribute name="class">
              <xsl:text>hidden</xsl:text>
            </xsl:attribute>
          </xsl:if>
        </xsl:for-each>

        <!-- main cell -->
        <xsl:choose>
          <xsl:when test="@key=$main">
            <xsl:if test="$expandable != 0">
              <a href="#expand-{$rowId}" class="toggleExpand" title="Agrandir/minimiser">&amp;nbsp;</a>
            </xsl:if>
            <span class="icon">&amp;nbsp;</span>

            <a data-params="object={$object},k={$k}" title="{.}">

              <!-- mainHref -->
              <xsl:attribute name="href">
                <xsl:choose>
                  <xsl:when test="$mainHref != 0">
                    <xsl:value-of select="$mainHref" />
                  </xsl:when>
                  <xsl:otherwise>
                    <xsl:text>#</xsl:text>
                    <xsl:value-of select="$rowId" />
                  </xsl:otherwise>
                </xsl:choose>
              </xsl:attribute>

              <!-- mainAction -->
              <xsl:if test="$mainAction != 0">
                <xsl:attribute name="data-action">
                  <xsl:value-of select="$mainAction" />
                </xsl:attribute>
              </xsl:if>

              <!-- mainTrigger -->
              <xsl:if test="$mainTrigger != 0">
                <xsl:attribute name="data-trigger">
                  <xsl:value-of select="$mainTrigger" />
                </xsl:attribute>
              </xsl:if>
              <xsl:value-of select="." />
            </a>
          </xsl:when>
          <xsl:when test="string-length(.) &gt; 0">
            <xsl:value-of select="." />
          </xsl:when>
          <xsl:otherwise>
            <xsl:text> </xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      </div>
    </xsl:for-each>

    <!-- action -->
    <xsl:for-each select="$action">
      <div class="cell action">
        <button id="{@key}-{$rowId}" class="{@key}" data-action="{@key}" data-params="object={$object},k={$k}" title="{@title}">
          <span class="hidden"><xsl:value-of select="@title" /></span>
        </button>
      </div>
    </xsl:for-each>
    <hr class="hidden" />
      <!-- TODO each child -->
      <xsl:apply-templates select="ui.row">
        <xsl:with-param name="object" select="$object"/>
        <xsl:with-param name="rowAction" select="$rowAction"/>
        <xsl:with-param name="selectable" select="$selectable"/>
        <xsl:with-param name="expandable" select="$expandable"/>
        <xsl:with-param name="main" select="$main"/>
        <xsl:with-param name="mainAction" select="$mainAction"/>
        <xsl:with-param name="mainTrigger" select="$mainTrigger"/>
        <xsl:with-param name="mainHref" select="$mainHref"/>
        <xsl:with-param name="action" select="$action"/>
        <xsl:with-param name="headercolumn" select="$headercolumn"/>
        <xsl:with-param name="level" select="$level + 1"/>
        <xsl:with-param name="parentId" select="$rowId"/>
      </xsl:apply-templates>
  </div>
</xsl:template>

<xsl:template match="ui.dialog">
  <div>
    <xsl:if test="@title">
      <h2><xsl:value-of select="@title" /></h2>
    </xsl:if>
    <p><xsl:apply-templates/></p>
    <div class="buttonList">
      <xsl:if test="@close">
        <button class="closeDialog"><xsl:value-of select="@close" /></button>
      </xsl:if>
    </div>
  </div>
</xsl:template>

<!-- common template -->
<xsl:template name="apply-tag">
  <xsl:call-template name="apply-attributelist" />
  <xsl:apply-templates/>
</xsl:template>

<xsl:template name="apply-attributelist">
  <xsl:for-each select="@*">
    <xsl:call-template name="apply-attribute" />
  </xsl:for-each>
</xsl:template>

<xsl:template name="apply-attribute">
  <xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
</xsl:template>

<xsl:template name="apply-topfield">
  <xsl:call-template name="apply-class-field" />
  <xsl:call-template name="apply-label" />
</xsl:template>

<xsl:template name="apply-class-field">
  <xsl:attribute name="class">
    <xsl:text>field input</xsl:text>
  </xsl:attribute>
</xsl:template>

<xsl:template name="apply-label">
  <xsl:param name="for" />
  <xsl:if test="@label">
    <label>
      <xsl:attribute name="for">
        <xsl:choose>
          <xsl:when test="@id">
            <xsl:value-of select="@id" />
          </xsl:when>
          <xsl:when test="$for">
            <xsl:value-of select="$for" />
          </xsl:when>
        </xsl:choose>
      </xsl:attribute>
      <xsl:value-of select="@label" />
    </label>
  </xsl:if>
</xsl:template>

<xsl:template name="apply-value-attribute">
  <xsl:if test="ui.value">
    <xsl:attribute name="value"><xsl:value-of select="ui.value"/></xsl:attribute>
  </xsl:if>
</xsl:template>

<xsl:template name="apply-itemAnchor">
  <xsl:call-template name="apply-nav-attributelist" />
  <a>
    <xsl:attribute name="href">
      <xsl:value-of select="concat('#',@href)" />
    </xsl:attribute>
    <xsl:attribute name="title">
      <xsl:value-of select="@label" />
    </xsl:attribute>
    <xsl:if test="@label">
      <xsl:value-of select="@label"/>
    </xsl:if>
  </a>
</xsl:template>

<xsl:template name="apply-nav-attributelist">
  <xsl:for-each select="@id">
    <xsl:call-template name="apply-attribute" />
  </xsl:for-each>
  <xsl:if test="@selected">
    <xsl:attribute name="class">
      <xsl:text>selected</xsl:text>
    </xsl:attribute>
  </xsl:if>
</xsl:template>

<xsl:template name="apply-itemlist">
  <xsl:for-each select="ui.item">
    <xsl:call-template name="apply-itemSection" />
  </xsl:for-each>
</xsl:template>

<xsl:template name="apply-itemSection">
  <section id="{@href}">
    <xsl:if test="(@empty) or (@selected)">
      <xsl:if test="@empty">
        <xsl:attribute name="class">
          <xsl:text>empty</xsl:text>
        </xsl:attribute>
      </xsl:if>
      <xsl:if test="@selected">
        <xsl:attribute name="class">
          <xsl:text> target</xsl:text>
        </xsl:attribute>
      </xsl:if>
    </xsl:if>
    <xsl:if test="@empty">
      <xsl:text> </xsl:text>
    </xsl:if>
    <xsl:apply-templates/>
  </section>
</xsl:template>

<xsl:template name="inList">
  <xsl:param name="list" />
  <xsl:param name="search" />
  <xsl:choose>
    <xsl:when test="contains($list, ',')">
      <xsl:if test="substring-before($list, ',')=$search">
        <xsl:text>1</xsl:text>
      </xsl:if>
      <xsl:call-template name="inList">
        <xsl:with-param name="list" select="substring-after($list, ',')"/>
        <xsl:with-param name="search" select="$search"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:if test="$list=$search">
        <xsl:text>1</xsl:text>
      </xsl:if>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

</xsl:stylesheet>
