<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" encoding="utf-8"/>

<xsl:variable name="currency_before" select="//*/currency_symbol[@position = 'before']" />
<xsl:variable name="currency_after" select="//*/currency_symbol[@position = 'after']" />

<xsl:template match="orderform">
	<style type="text/css">
	table.choices { border-collapse: collapse; border: none; width: 100%}
	td.choices { 
		border-top: thin solid black ;
		border-bottom: thin solid black ;
		font-weight: bold
	}
	td.lineitem { 
		padding-top: 1ex ; 
		padding-bottom: 1ex ;
		padding-right: 3ex ;
		vertical-align: top
	}
	</style>
	<table class="choices" >
	<xsl:apply-templates select="paytime_selection" />
	<xsl:apply-templates select="membership_selection" />
	<tr><td style="width: 100%" colspan="3"><br /></td></tr>
	<tr style="width: 100%">
		<td class="choices" style="width: 40%" >
			Choice
		</td>
		<td class="choices" style="width: 40%" >
			Selection
		</td>
		<td class="choices" style="width: 40% ; text-align: right" >
			Price
		</td>
	</tr>
	<xsl:apply-templates select="regtype_selection" />
	<xsl:apply-templates select="choice_selection" />
	<xsl:apply-templates select="total" />
	</table>
</xsl:template>

<xsl:template match="total">
<tr>
	<td class="choices" >
	&#160;
	</td>
	<td class="choices" >
		Total
	</td>
	<td class="choices" style="text-align: right; font-weight: normal">
	<xsl:value-of select="$currency_before" />
		<xsl:value-of select="format-number(number(.), '#.00')"/>
	<xsl:value-of select="$currency_after" />
	</td>
</tr>
</xsl:template>

<xsl:template match="paytime_selection">
	<xsl:variable name="paytimeid" select="paytimeid" />
	<xsl:apply-templates select="/*//paytime[paytimeid = $paytimeid]"/>
</xsl:template>

<xsl:template match="paytime">
<tr>
<td style="font-weight: bold">
Payment Time :
</td>
<td>
	<span style="font-weight: bold">
	<xsl:value-of select="name" />
	</span>
	<br />
	<xsl:value-of select="description" />
</td>
</tr>
</xsl:template>

<xsl:template match="membership_selection">
<tr>
<td>
	<span style="font-weight: bold">
	Relevant Memberships :
	</span>
	<xsl:variable name="memberid" select="memberid" />
	<ul>
	<xsl:apply-templates select="/*//membership[memberid = $memberid]"/>
	</ul>
</td>
</tr>
</xsl:template>

<xsl:template match="membership">
<li>
	<xsl:value-of select="name" />
</li>
</xsl:template>

<xsl:template match="regtype_selection">
	<xsl:variable name="regtypeid" select="regtypeid" />
	<xsl:apply-templates select="/*//regtype[regtypeid = $regtypeid]"/>
</xsl:template>

<xsl:template match="regtype">
<tr>
<td class="lineitem" style="font-weight: bold">
	Registration Type
</td>
<td class="lineitem">
	<span style="font-weight: bold">
	<xsl:value-of select="name" />
	</span>
	<br />
	<xsl:value-of select="description" />
	<xsl:apply-templates select="included" />
</td>
<td style="vertical-align: top; text-align: right" >
	<xsl:value-of select="$currency_before" />
	<xsl:call-template name="totalprice">
		<xsl:with-param name="base" select="." />
		<xsl:with-param name="paytime" select="/*//paytime_selection/paytimeid" />
		<xsl:with-param name="memberid" select="/*//membership_selection/memberid" />
	</xsl:call-template>
	<xsl:value-of select="$currency_after" />
	<br />
</td>
</tr>
</xsl:template>

<xsl:template match="included">
	<div style="margin-left: 10">
	Included:
	<ul style="margin-top: 0">
	<xsl:apply-templates select="includedchoice" />
	</ul>
	</div>
</xsl:template>

<xsl:template match="includedchoice">
	<xsl:variable name="choiceid" select="choiceid" />
	
	<xsl:apply-templates select="number">
		<xsl:with-param name="choiceid" select="$choiceid" />
	</xsl:apply-templates>
	
	<xsl:for-each select="optionid">
		<li>
		<xsl:variable name="optionid" select="." />
		<xsl:value-of select="/*//choice[choiceid = $choiceid]/option[optionid = $optionid]/name" />
		</li>
	</xsl:for-each>
</xsl:template>

<xsl:template match="number">
	<xsl:param name="choiceid" />
	<li>
	<xsl:value-of select="." />x 
	<xsl:value-of select="/*//choice[choiceid = $choiceid]/name" />
	</li>
</xsl:template>

<xsl:template match="choice_selection">
	<xsl:variable name="choiceid" select="choiceid" />
	<tr>
	<td class="lineitem">
		<xsl:apply-templates select="/*//choice[choiceid = $choiceid]" />
	</td>
	
	<xsl:variable name="optionid" select="optionid" />
	<xsl:apply-templates select="/*//choice[choiceid = $choiceid]/option[optionid = $optionid]" />
	
	<xsl:apply-templates select="/*//choice[choiceid = $choiceid]/quantity">
		<xsl:with-param name="number" select="number" />
	</xsl:apply-templates>
	</tr>
</xsl:template>

<xsl:template match="choice">
	<b>
	<xsl:value-of select="name" />
	</b>
	<br />
	<xsl:value-of select="description" />
</xsl:template>

<xsl:template match="option">
	<td class="lineitem">
	<b>
	<xsl:value-of select="name" />
	</b>
	<br />
	<xsl:value-of select="description" />
	</td>
	<td style="vertical-align: top; text-align: right" >
	<xsl:if test="count(price) > 0" >
		<xsl:value-of select="$currency_before" />
		<xsl:call-template name="totalprice">
			<xsl:with-param name="base" select="." />
			<xsl:with-param name="paytime" select="/*//paytime_selection/paytimeid" />
			<xsl:with-param name="regtypeid" select="/*//regtype_selection/regtypeid" />
			<xsl:with-param name="memberid" select="/*//membership_selection/memberid" />
		</xsl:call-template>
		<xsl:value-of select="$currency_after" />
	</xsl:if>
	<br />
	</td>
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
	<td style="vertical-align: top" >
	<xsl:value-of select="$number" />
	<b>	@ </b>
	<xsl:value-of select="$unitprice" />
	</td>
	<td style="vertical-align: top; text-align: right; margin-right: 10" >
	<xsl:value-of select="$currency_before" />
	<xsl:value-of select="format-number($number * $unitprice, '#.00')" />
	<xsl:value-of select="$currency_after" />
	<br />
	</td>
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
