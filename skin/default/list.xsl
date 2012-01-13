<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns="http://www.w3.org/1999/xhtml"
	exclude-result-prefixes="xhtml">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" />

	<xsl:include href="common.xsl" />

	<!-- メイン。 -->
	<xsl:template match="body">
		<h2>検索</h2>
		<p>近日実装予定。</p>
		<h2>新着お題</h2>
		<xsl:apply-templates select="new" />
		<h2>高評価順ランキング</h2>
		<xsl:apply-templates select="score" />
		<h2>覚えが早い順ランキング</h2>
		<xsl:apply-templates select="gene" />
	</xsl:template>

	<!-- ぼっと一覧。 -->
	<xsl:template match="score|gene|new">
		<ol>
			<xsl:apply-templates select="item" />
		</ol>
	</xsl:template>

	<!-- ぼっと。 -->
	<xsl:template match="item">
		<li><a><xsl:attribute name="href">?<xsl:value-of select="@id" /></xsl:attribute><xsl:value-of select="@theme" /></a></li>
	</xsl:template>

</xsl:stylesheet>
