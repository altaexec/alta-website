<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" />

<xsl:variable name="currency_before" select="//*/currency_symbol[@position = 'before']" />
<xsl:variable name="currency_after" select="//*/currency_symbol[@position = 'after']" />

<xsl:template match="orderform">
	<xsl:apply-templates select="regtypes" />
	<xsl:apply-templates select="paytimes" />
	<xsl:apply-templates select="memberships" />
	<xsl:apply-templates select="section" />
</xsl:template>

<xsl:template match="regtypes">
	<div>
	<div style="font-weight: bold">
	Registration Type
	</div>
	<div style="padding-left: 20">
		<xsl:apply-templates select="regtype" />
	</div>
	</div>
</xsl:template>

<xsl:template match="regtype">
	<div style="padding-bottom: 20">
	<xsl:element name="input">
		<xsl:attribute name="name">regtype</xsl:attribute>
		<xsl:attribute name="type">radio</xsl:attribute>
		<xsl:if test="position() = 1">
			<xsl:attribute name="checked">1</xsl:attribute>
		</xsl:if>
	<xsl:attribute name="value">
		<xsl:value-of select="regtypeid" />
	</xsl:attribute>
	</xsl:element>
	<span>
	<xsl:apply-templates select="name" />
	<xsl:apply-templates select="description" />
	</span>
	<xsl:apply-templates select="included" />
	<div style="padding-left: 20">
	<xsl:call-template name="pricelist">
		<xsl:with-param name="prices" />
		<xsl:with-param name="discounts" />
	</xsl:call-template>
	</div>
	</div>
</xsl:template>

<xsl:template match="included">
	<div style="padding-left: 40">
	Includes:
		<div style="padding-left: 20">
		<xsl:apply-templates select="includedchoice" />
		</div>
	</div>
</xsl:template>

<xsl:template match="includedchoice">
	<xsl:variable name="choiceid" select="choiceid" />
	
	<xsl:apply-templates select="number">
		<xsl:with-param name="choiceid" select="$choiceid" />
	</xsl:apply-templates>
	
	<xsl:for-each select="optionid">
		<div>
		<xsl:variable name="optionid" select="." />
		<xsl:value-of select="/*//choice[choiceid = $choiceid]/option[optionid = $optionid]/name" />
		</div>
	</xsl:for-each>
</xsl:template>

<xsl:template match="number">
	<xsl:param name="choiceid" />
	<div>
	<xsl:value-of select="." />x 
	<xsl:value-of select="/*//choice[choiceid = $choiceid]/name" />
	</div>
</xsl:template>

<xsl:template match="paytimes">
	<div style="padding-bottom: 20">
	<div style="font-weight: bold">
	Payment Time
	</div>
	<div style="padding-left: 20">
		<xsl:apply-templates select="paytime" />
	</div>
	</div>
</xsl:template>

<xsl:template match="paytime">
	<div>
	<xsl:element name="input">
		<xsl:attribute name="name">paytime</xsl:attribute>
		<xsl:attribute name="type">radio</xsl:attribute>
		<xsl:if test="position() = 1">
			<xsl:attribute name="checked">1</xsl:attribute>
		</xsl:if>
	<xsl:attribute name="value">
		<xsl:value-of select="paytimeid" />
	</xsl:attribute>
	</xsl:element>
	<span>
	<xsl:apply-templates select="name" />
	<xsl:apply-templates select="description" />
	</span>
	</div>
</xsl:template>

<xsl:template match="memberships">
	<div style="padding-bottom: 20">
	<div style="font-weight: bold">
	Membership
	<br />
	</div>
	<div style="padding-left: 20">
		<xsl:apply-templates select="membership" />
	</div>
	</div>
</xsl:template>

<xsl:template match="membership">
	<div>
	<xsl:element name="input">
		<xsl:attribute name="name">membership[]</xsl:attribute>
		<xsl:attribute name="type">checkbox</xsl:attribute>
	<xsl:attribute name="value">
		<xsl:value-of select="memberid" />
	</xsl:attribute>
	</xsl:element>
	<xsl:apply-templates select="name" />
	<xsl:apply-templates select="description" />
	</div>
</xsl:template>

<xsl:template match="section">
	<div style="padding-top: 20 ; padding-bottom: 10">
		<xsl:apply-templates select="name" />
		<xsl:apply-templates select="description">
			<xsl:with-param name="newline" select="true()" />
		</xsl:apply-templates>
	</div>
	<div style="padding-left: 20">
		<xsl:apply-templates select="choice" />
		<xsl:apply-templates select="section" />
	</div>
</xsl:template>

<xsl:template match="choice">
	<div>
		<xsl:apply-templates select="name" />
		<xsl:apply-templates select="description">
			<xsl:with-param name="newline" select="true()" />
		</xsl:apply-templates>
	</div>
	<span>
	<xsl:if test="count(quantity) = 0">
		<div style="padding-left: 20">
		<xsl:apply-templates select="option" />
		</div>
	</xsl:if>
	<xsl:apply-templates select="quantity" />
	</span>
</xsl:template>

<xsl:template match="option">
	<div style="padding-bottom: 20">
	<xsl:element name="input">
	<xsl:attribute name="name">
		<xsl:value-of select="parent::choice/choiceid" />[optionid]
	</xsl:attribute>
	<xsl:attribute name="value">
		<xsl:value-of select="optionid" />
	</xsl:attribute>
	<xsl:choose>
		<xsl:when test="count(parent::choice/option) = 1" >
			<xsl:attribute name="type">checkbox</xsl:attribute>
		</xsl:when>
		<xsl:otherwise>
			<xsl:attribute name="type">radio</xsl:attribute>
			<xsl:if test="position() = 1">
				<xsl:attribute name="checked">1</xsl:attribute>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>
	</xsl:element>
	<span>
	<xsl:apply-templates select="name" />
	<xsl:apply-templates select="description" />
	</span>
	<xsl:call-template name="pricelist">
		<xsl:with-param name="prices" />
		<xsl:with-param name="discounts" />
	</xsl:call-template>
	</div>
</xsl:template>

<xsl:template match="quantity">
	<table style="border-style: none">
	<tr>
	<td>
	<xsl:element name="select">
		<xsl:attribute name="name">
			<xsl:value-of select="parent::choice/choiceid" />[quantity]
		</xsl:attribute>
		<xsl:choose>
			<xsl:when test="count(@maximum)=0">
				<xsl:call-template name="quantities" />
			</xsl:when>
			<xsl:when test="count(@minimum)=0">
				<xsl:call-template name="quantities">
					<xsl:with-param name="maximum" select="@maximum"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="quantities">
					<xsl:with-param name="minimum" select="@minimum"/>
					<xsl:with-param name="maximum" select="@maximum"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:element>
	<span>
	at the cost per each of
	</span>
	</td>
	<td>
	<xsl:call-template name="pricelist" />
	</td>
	</tr>
	</table>
</xsl:template>

<xsl:template name="pricelist">
	<style type="text/css">
	.pricelist { border-collapse: collapse; border: thin solid black }
	th.pricelist { font-weight: bold; font-size: smaller; text-align: center }
	</style>
	<table class="pricelist" style="margin-top: 10">
		<tr>
			<xsl:if test="count(price) > 0">
				<th class="pricelist">
					Price
				</th>
			</xsl:if>
			<xsl:if test="count(discount) > 0">
				<th class="pricelist">
					Discounts
				</th>
			</xsl:if>
		</tr>
	<tr>
		<xsl:if test="count(price) > 0">
		<td class="pricelist" style="vertical-align: top">
			<xsl:variable name="diffs">
				<xsl:call-template name="differences" />
			</xsl:variable>
			<xsl:choose>
				<xsl:when test="string-length($diffs) = 0">
					<div style="padding: 1">
					<xsl:value-of select="$currency_before" />
					<xsl:value-of select="price[position() = 1]/cost" />
					<xsl:value-of select="$currency_after" />
					</div>
				</xsl:when>
				<xsl:otherwise>
					<table>
					<xsl:apply-templates select="price" />
					</table>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		</xsl:if>
		<xsl:if test="count(discount) > 0">
		<td class="pricelist" style="vertical-align: top">
			<table style="border-style: none">
			<xsl:apply-templates select="discount" />
			</table>
		</td>
		</xsl:if>
	</tr>
	</table>
</xsl:template>


<xsl:template match="price">
	<tr>
	<td style="font-style: italic">
	<xsl:variable name="paytimeid" select="paytimeid" />
	<xsl:value-of select="//*/paytimes/paytime[paytimeid=$paytimeid]/name" />
	</td>
	<td>
	 	<xsl:value-of select="$currency_before" />
		<xsl:value-of select="cost" />
		<xsl:value-of select="$currency_after" />
	</td>
	</tr>
</xsl:template>

<xsl:template match="discount">
	<tr>
	<td style="font-style: italic">
	<xsl:variable name="memberid" select="memberid" />
	<xsl:value-of select="//*/memberships/membership[memberid=$memberid]/name" />
	<xsl:variable name="regtypeid" select="regtypeid" />
	<xsl:value-of select="//*/regtypes/regtype[regtypeid=$regtypeid]/name" />
	</td>
	<td>
	 	<xsl:value-of select="$currency_before" />
		<xsl:value-of select="cost" />
		<xsl:value-of select="$currency_after" />
	</td>
	</tr>
</xsl:template>

<xsl:template name="quantities">
	<xsl:param name="minimum" >0</xsl:param>
	<xsl:param name="maximum" >1</xsl:param>
	<xsl:element name="option">
		<xsl:attribute name="value">
			<xsl:value-of select="$minimum" />
		</xsl:attribute>
		<xsl:value-of select="$minimum" />
	</xsl:element>
	<xsl:if test="$minimum &lt; $maximum">
		<xsl:call-template name="quantities">
			<xsl:with-param name="minimum" select="$minimum+1"/>
			<xsl:with-param name="maximum" select="$maximum"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<xsl:template match="name">
<span style="font-weight: bold">
<xsl:value-of select="." />
</span>
</xsl:template>

<xsl:template match="description">
	<xsl:param name="newline" select="false()" />
	<xsl:choose>
		<xsl:when test="$newline">
		<br />
		</xsl:when>
		<xsl:otherwise>
		- 
		</xsl:otherwise>
	</xsl:choose>
	<xsl:value-of select="." />
</xsl:template>

<xsl:template name="differences">
	<xsl:variable name="first" select="price[position() = 1]/cost" />
	<xsl:for-each select="price[position() != 1]/cost">
		<xsl:if test="number($first) != number(.)">
			<xsl:copy-of select="." />
		</xsl:if>
	</xsl:for-each>
</xsl:template>


</xsl:stylesheet>
