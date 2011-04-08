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
    <xsl:call-template name="apply-tag" />
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
          <xsl:for-each select="@id|@name|@required|@autofocus|@autocomplete|@multiple">
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

    <!-- input -->
    <xsl:otherwise>
      <div>
        <xsl:call-template name="apply-topfield" />
        <input>
          <xsl:for-each select="@id|@name|@type|@required|@autofocus|@autocomplete|@maxlength|@size|@value">
            <xsl:call-template name="apply-attribute" />
          </xsl:for-each>
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
    <xsl:if test="ui.headtitle">
      <h2><xsl:value-of select="ui.headtitle"/></h2>
    </xsl:if>
    <nav class="dock">

      <menu>
        <xsl:for-each select="ui.item">
          <li>
            <xsl:call-template name="apply-nav-attributelist" />
            <span>
              <xsl:if test="@label">
                <xsl:value-of select="@label"/>
              </xsl:if>
            </span>
            <a>
              <xsl:attribute name="href">
                <xsl:value-of select="concat('#',@href)" />
              </xsl:attribute>
              <xsl:text> </xsl:text>
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
    <xsl:attribute name="class">
      <xsl:value-of select="concat( 'list-container ', ui.mode[1]/@name )" />
    </xsl:attribute>
    <xsl:if test="ui.headtitle">
      <h3><xsl:value-of select="ui.headtitle"/></h3>
    </xsl:if>
    <xsl:if test="count(ui.mode) > 1">
      <select class="mode">
        <xsl:for-each select="ui.mode">
          <option value="{@name}"><xsl:value-of select="." /></option>
        </xsl:for-each>
      </select>
    </xsl:if>
    <div class="list">
      <div class="header">
        <xsl:for-each select="ui.headercolumn">
          <div class="cell"><xsl:value-of select="." /></div>
        </xsl:for-each>
      </div>
      <xsl:for-each select="ui.row">
        <div class="row">
          <xsl:call-template name="apply-attributelist" />
          <xsl:for-each select="ui.cell">
            <div class="cell">
              <xsl:call-template name="apply-attributelist" />
              <xsl:value-of select="." />
            </div>
          </xsl:for-each>
        </div>
      </xsl:for-each>
    </div>
  </div>
</xsl:template>

<xsl:template match="ui.dialog">
  <div>
    <xsl:if test="@title">
      <h2><xsl:value-of select="@title" /></h2>
    </xsl:if>
    <p>
      <xsl:apply-templates/>
    </p>
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
  <xsl:attribute name="class">
    <xsl:text>field</xsl:text>
  </xsl:attribute>
  <xsl:if test="@label">
    <label for="@id">
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
