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
		<form action="./" method="get">
			<p>
					<label for="theme">お題</label>
					<input type="text" class="text" id="theme" name="theme" maxlength="80" placeholder="80字以内">
						<xsl:attribute name="value"><xsl:value-of select="theme/@expr" /></xsl:attribute>
					</input>
					<input type="hidden" name="f" value="core/list" />
					<input type="submit" class="submit" value="これ描いた？" />
			</p>
		</form>
		<xsl:apply-templates select="theme" />
		<h2>新着お題</h2>
		<xsl:apply-templates select="new" />
		<h2>高評価順ランキング</h2>
		<xsl:apply-templates select="score" />
		<h2>覚えが早い順ランキング</h2>
		<xsl:apply-templates select="gene" />
	</xsl:template>

	<!-- ぼっと一覧。 -->
	<xsl:template match="theme">
		<h2>検索: [<xsl:value-of select="@expr" />]</h2>
		<xsl:choose>
			<xsl:when test="count(item) = 0">
				<p>1件も見つかりませんでした。</p>
			</xsl:when>
			<xsl:otherwise>
				<ol>
					<xsl:apply-templates select="item" />
				</ol>
			</xsl:otherwise>
		</xsl:choose>
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
