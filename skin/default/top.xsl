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
		<h2>メインメニュー</h2>
		<ul id="mainMenu">
			<li><a href="?f=core/newGame">依頼する</a></li>
			<li><a href="?f=core/list">育てる</a></li>
			<li><a href="./">ヘルプ<br />(工事中)</a></li>
		</ul>
		<p id="version">
			<a href="HISTORY">VERSION <xsl:value-of select="@ver" /></a>
			サポートは<a href="http://twitter.com/danmaq">Twitter(@danmaq)</a>へ@ツイートしてください。
		</p>
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
