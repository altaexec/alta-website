<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="text" />

<xsl:template match="orderform">
	<xsl:apply-templates select="regtype_selection" />
	<xsl:apply-templates select="choice_selection" />
</xsl:template>

<xsl:template match="regtype_selection">
	<xsl:variable name="regtypeid" select="regtypeid" />
	<xsl:apply-templates select="/*//regtype[regtypeid = $regtypeid]"/>
</xsl:template>

<xsl:template match="regtype">
	<xsl:call-template name="totalprice">
		<xsl:with-param name="base" select="." />
		<xsl:with-param name="paytime" select="/*//paytime_selection/paytimeid" />
		<xsl:with-param name="memberid" select="/*//membership_selection/memberid" />
	</xsl:call-template>
	;
</xsl:template>

<xsl:template match="choice_selection">
	<xsl:variable name="choiceid" select="choiceid" />
	<xsl:for-each select="optionid">
		<xsl:variable name="optionid" select="." />
		<xsl:apply-templates select="/*//choice[choiceid = $choiceid]/option[optionid = $optionid]" />
	</xsl:for-each>
	<xsl:apply-templates select="/*//choice[choiceid = $choiceid]/quantity">
		<xsl:with-param name="number" select="number" />
	</xsl:apply-templates>
</xsl:template>

<xsl:template match="option">
	<xsl:call-template name="totalprice">
		<xsl:with-param name="base" select="." />
		<xsl:with-param name="paytime" select="/*//paytime_selection/paytimeid" />
		<xsl:with-param name="regtypeid" select="/*//regtype_selection/regtypeid" />
		<xsl:with-param name="memberid" select="/*//membership_selection/memberid" />
	</xsl:call-template>
	;
</xsl:template>

<xsl:template match="quantity">
	<xsl:param name="number" />
	<xsl:variable name="unitprice">
		<xsl:call-template name="totalprice">
			<xsl:with-param name="base" select="." />
			<xsl:with-param name="paytime" select="/*//paytime_selection/paytimeid" />
			<xsl:with-param name="regtypeid" select="/*//regtype_selection/regtypeid" />
			<xsl:with-param name="memberid" select="/*//membership_selection/memberid" />
		</xsl:call-template>
	</xsl:variable>
	<xsl:value-of select="format-number($number * $unitprice, '#.00')" />
	;
</xsl:template>

<xsl:template name="totalprice">
	<xsl:param name="base" />
	<xsl:param name="paytime" />
	<xsl:param name="memberid"></xsl:param>
	<xsl:param name="regtypeid"></xsl:param>
	
	<xsl:variable name="price">
		<xsl:value-of select="$base/price[paytimeid = $paytime]/cost" />
	</xsl:variable>
	
	<xsl:variable name="regtype_discount">
		<xsl:if test="count($base/discount[regtypeid = $regtypeid]/cost) = 0">
			0
		</xsl:if>
		<xsl:for-each select="$base/discount[regtypeid = $regtypeid]/cost" >
			<xsl:sort data-type="number" select="cost" order="descending"/>
			<xsl:if test="position() = 1">
				<xsl:value-of select="number()"/>
			</xsl:if>
		</xsl:for-each>
	</xsl:variable>
	
	<xsl:variable name="member_discount">
		<xsl:if test="count($base/discount[memberid = $memberid]/cost) = 0">
			0
		</xsl:if>
		<xsl:for-each select="$base/discount[memberid = $memberid]/cost" >
			<xsl:sort data-type="number" select="cost" order="descending"/>
			<xsl:if test="position() = 1">
				<xsl:value-of select="number()"/>
			</xsl:if>
		</xsl:for-each>
	</xsl:variable>
	
	<xsl:value-of select="format-number($price - $regtype_discount - $member_discount, '#.00')" />
</xsl:template>

 </xsl:stylesheet>
