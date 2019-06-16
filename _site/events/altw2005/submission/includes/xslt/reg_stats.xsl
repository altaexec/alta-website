<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" />

<xsl:variable name="currency_before" select="//*/currency_symbol[@position = 'before']" />
<xsl:variable name="currency_after" select="//*/currency_symbol[@position = 'after']" />

<xsl:template match="orderform">
	<table align="center" width="80%">
	<tr>
		<td></td>
		<td></td>
		<td width="20%"></td>
	</tr>
	<xsl:apply-templates select="regtypes" />
	<xsl:apply-templates select="paytimes" />
	<xsl:apply-templates select="memberships" />
	<tr>
		<td style="padding-top: 20" colspan="3">
			<span style="font-size: medium" >
			Choices
			</span>
		</td>
	</tr>
	<xsl:apply-templates select="//*/choice" />
	<tr>
		<td style="padding-top: 20" colspan="2">
			<span style="font-size: medium" >
			Total Amount Paid
			</span>
		</td>
		<td style="text-align: right ; vertical-align: bottom">
			<span style="font-weight: bold ; font-size: medium" >
			<xsl:value-of select="$currency_before" />
			<xsl:value-of select="format-number(sum(total), '#.00')" />
			<xsl:value-of select="$currency_after" />
			</span>
		</td>
	</tr>
	</table>
</xsl:template>

<xsl:template match="regtypes">
	<tr>
		<td style="padding-top: 20" colspan="3">
		<span style="font-size: medium" >
		Registration Types
		</span>
		</td>
	</tr>
	<xsl:apply-templates select="regtype" />
</xsl:template>

<xsl:template match="regtype">
	<tr>
		<td>
			<b><xsl:value-of select="name" /></b>
		</td>
		<td style="text-align: right" colspan="2">
		<xsl:variable name="regtypeid" select="regtypeid" />
		<xsl:value-of select="count(//*/regtype_selection[regtypeid=$regtypeid])" />
		</td>
	</tr>
</xsl:template>

<xsl:template match="paytimes">
	<tr>
		<td style="padding-top: 20" colspan="3">
		<span style="font-size: medium" >
		Payment Times
		</span>
		</td>
	</tr>
	<xsl:apply-templates select="paytime" />
</xsl:template>

<xsl:template match="paytime">
	<tr>
		<td>
			<b><xsl:value-of select="name" /></b>
		</td>
		<td style="text-align: right" colspan="2">
		<xsl:variable name="paytimeid" select="paytimeid" />
		<xsl:value-of select="count(//*/paytime_selection[paytimeid=$paytimeid])" />
		</td>
	</tr>
</xsl:template>

<xsl:template match="memberships">
	<tr>
		<td style="padding-top: 20" colspan="3">
		<span style="font-size: medium" >
		Memberships
		</span>
		</td>
	</tr>
	<xsl:apply-templates select="membership" />
</xsl:template>

<xsl:template match="membership">
	<tr>
		<td>
			<b><xsl:value-of select="name" /></b>
		</td>
		<td style="text-align: right" colspan="2">
		<xsl:variable name="memberid" select="memberid" />
		<xsl:value-of select="count(//*/membership_selection[memberid=$memberid])" />
		</td>
	</tr>
</xsl:template>

<xsl:template match="choice">
	<tr>
	<xsl:element name="td">
	<xsl:attribute name="rowspan">
		<xsl:value-of select="count(option) + count(quantity)+1" />
	</xsl:attribute>
	<b>
	<xsl:value-of select="name" />
	</b>
	</xsl:element>
	</tr>
	<xsl:choose>
		<xsl:when test="count(quantity) = 0">
			<xsl:apply-templates select="option" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="quantity" />
		</xsl:otherwise>
	</xsl:choose>
	
</xsl:template>

<xsl:template match="option">
	<tr>
	<td>
	<b>
	<xsl:value-of select="name" />
	</b>
	</td>
	<td style="text-align: right">
	<xsl:variable name="choiceid" select="../choiceid" />
	<xsl:variable name="optionid" select="optionid" />
	<xsl:variable name="included" >
		<xsl:call-template name="included_options" >
			<xsl:with-param name="choiceid" select="$choiceid" />
			<xsl:with-param name="optionid" select="$optionid" />
		</xsl:call-template>
	</xsl:variable>
	<xsl:variable name="extra" select="count(//*/choice_selection[choiceid=$choiceid and optionid=$optionid])" />
	<xsl:value-of select="$included + $extra" />
	</td>
	</tr>
</xsl:template>

<xsl:template match="quantity">
	<tr>
	<td>
	</td>
	<td style="text-align: right">
	<xsl:variable name="choiceid" select="../choiceid" />
	<xsl:variable name="included" >
		<xsl:call-template name="included_quantity" >
			<xsl:with-param name="choiceid" select="$choiceid" />
		</xsl:call-template>
	</xsl:variable>
	<xsl:variable name="extra" select="sum(//*/choice_selection[choiceid=$choiceid]/number)" />
	<xsl:value-of select="$included + $extra" />
	</td>
	</tr>
</xsl:template>

<xsl:template name="included_options">
	<xsl:param name="choiceid" />
	<xsl:param name="optionid" />
	<xsl:variable name="included" >
		<xsl:for-each select="//*/regtype_selection">
			<xsl:variable name="regtypeid" select="regtypeid" />
			<xsl:copy-of select="//*/regtypes/regtype[regtypeid=$regtypeid]/included/includedchoice[choiceid=$choiceid and optionid=$optionid]" />
		</xsl:for-each>
	</xsl:variable>
	<xsl:value-of select="count($included/includedchoice)" />
</xsl:template>

<xsl:template name="included_quantity">
	<xsl:param name="choiceid" />
	<xsl:variable name="included" >
		<xsl:for-each select="//*/regtype_selection">
			<xsl:variable name="regtypeid" select="regtypeid" />
			<xsl:copy-of select="//*/regtypes/regtype[regtypeid=$regtypeid]/included/includedchoice[choiceid=$choiceid]" />
		</xsl:for-each>
	</xsl:variable>
	<xsl:value-of select="count($included/includedchoice/number)" />
</xsl:template>

</xsl:stylesheet>
