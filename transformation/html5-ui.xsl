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

<xsl:template match="ui.dataitem" mode="select">
  <option>
    <xsl:if test="@value">
      <xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
      <xsl:if test="../../ui.value">
        <xsl:if test="@value=../../ui.value">
          <xsl:attribute name="selected">
            <xsl:text>selected</xsl:text>
          </xsl:attribute>
        </xsl:if>
      </xsl:if>
    </xsl:if>
    <xsl:value-of select="@label" />
  </option>
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
          </li>
        </xsl:for-each>
      </menu>
    </nav>
    <xsl:call-template name="apply-itemlist" />
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
    <xsl:if test="ui.option">
      <fieldset class="option">
        <legend>Options</legend>
        <xsl:for-each select="ui.option">
          <xsl:if test="@count > 1">
            <xsl:apply-templates select="ui.field" />
          </xsl:if>
        </xsl:for-each>
      </fieldset>
      <hr />
    </xsl:if>

    <xsl:call-template name="apply-listpart" />

    <!-- addable -->
    <xsl:if test="@addable">
      <hr />
      <div>
        <button class="add" data-action="add" data-params="object={@id}">Ajouter</button>
      </div>
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
        <div class="cell">
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
    <xsl:for-each select="ui.row">
      <div class="row">
        <xsl:call-template name="apply-attributelist" />

        <!-- k -->
        <xsl:variable name="k">
          <xsl:value-of select="@id" />
        </xsl:variable>

        <!-- rowId -->
        <xsl:variable name="rowId">
          <xsl:value-of select="concat($object,'-',$k)" />
        </xsl:variable>

        <!-- id -->
        <xsl:attribute name="id">
           <xsl:value-of select="$rowId" />
        </xsl:attribute>

        <!-- rowAction -->
        <xsl:if test="../@rowAction">
          <xsl:attribute name="data-action">
            <xsl:value-of select="../@rowAction"/>
          </xsl:attribute>
        </xsl:if>

        <!-- selectable -->
        <xsl:if test="../@selectable">
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
              <xsl:if test="@key=../../@main">
                <xsl:text> main</xsl:text>
              </xsl:if>
              <xsl:if test="@class">
                <xsl:value-of select="concat(' ', @class)"/>
              </xsl:if>
              <xsl:for-each select="../../ui.headercolumn[position()=$position]">
                <xsl:if test="@class">
                  <xsl:value-of select="concat(' ', @class)"/>
                </xsl:if>
              </xsl:for-each>
            </xsl:attribute>  
            <xsl:for-each select="../../ui.headercolumn[position()=$position]">
              <xsl:if test="@hidden">
                <xsl:attribute name="class">
                  <xsl:text>hidden</xsl:text>
                </xsl:attribute>
              </xsl:if>
            </xsl:for-each>

            <!-- mainAction -->
            <xsl:choose>
              <xsl:when test="@key=../../@main">
                <a href="#{$rowId}" data-action="{../../@mainAction}" data-params="object={$object},k={$k}" title="{.}">
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
        <xsl:for-each select="../ui.action">
          <div class="cell action">
            <button id="{@key}-{$rowId}" class="{@key}" data-action="{@key}" data-params="object={$object},k={$k}" title="{@title}">
              <span class="hidden"><xsl:value-of select="@title" /></span>
            </button>
          </div>
        </xsl:for-each>
        <hr class="hidden" />
      </div>
    </xsl:for-each>
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
  <xsl:if test="@label">
    <label for="{@id}">
       <xsl:value-of select="@label" />
    </label>
  </xsl:if>
</xsl:template>

<xsl:template name="apply-value-attribute">
  <xsl:if test="ui.value">
    <xsl:attribute name="value"><xsl:value-of select="ui.value"/></xsl:attribute>
  </xsl:if>
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
    </xsl:for-each>
</xsl:template>

</xsl:stylesheet>
