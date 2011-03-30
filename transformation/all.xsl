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

<xsl:template match="basefont">
  <span><xsl:call-template name="apply-tag" /></span>
</xsl:template>

<xsl:template match="b">
  <strong><xsl:call-template name="apply-tag" /></strong>
</xsl:template>

<xsl:template match="i">
  <em><xsl:call-template name="apply-tag" /></em>
</xsl:template>

<!-- adaptation -->
<xsl:template match="a">
  <a>
    <xsl:call-template name="apply-tag" />
    <xsl:if test=".=''">
      <xsl:value-of select="@href" />
    </xsl:if>
  </a>
</xsl:template>

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
            <xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
          </xsl:for-each>
          <xsl:for-each select="ui.datalist">
            <xsl:for-each select="ui.dataitem">
              <option>
                <xsl:if test="@value">
                  <xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
                </xsl:if>
                <xsl:value-of select="@label" />
              </option>
            </xsl:for-each>
          </xsl:for-each>
        </select>
      </div>
    </xsl:when>

    <!-- hidden -->
    <xsl:when test="@type='hidden'">
      <input>
        <xsl:for-each select="@id|@name|@type|@value">
          <xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
        </xsl:for-each>
      </input>
    </xsl:when>

    <!-- input -->
    <xsl:otherwise>
      <div>
        <xsl:call-template name="apply-topfield" />
        <input>
          <xsl:for-each select="@id|@name|@type|@required|@autofocus|@autocomplete|@maxlength|@size|@value">
            <xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
          </xsl:for-each>
        </input>
      </div>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- common template -->
<xsl:template name="apply-tag">
  <xsl:call-template name="apply-attributes" />
  <xsl:apply-templates/>
</xsl:template>

<xsl:template name="apply-attributes">
  <xsl:for-each select="@*">
    <xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
  </xsl:for-each>
</xsl:template>

<xsl:template name="apply-topfield">
  <xsl:attribute name="class">
    <xsl:text>field</xsl:text>
  </xsl:attribute>
  <xsl:if test="@label">
    <label>
      <xsl:attribute name="for">
        <xsl:value-of select="@id" />
      </xsl:attribute>
       <xsl:value-of select="@label" />
    </label>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>
