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
		<h2>今度は<xsl:value-of select="@title" />を描いてみたようです。</h2>
		<p>
			<img alt="" title="ぼっとが描いてみた画像">
				<xsl:attribute name="src">?f=core/rawImage&amp;id=<xsl:value-of select="bot/@hash" /></xsl:attribute>
				<xsl:attribute name="width">320</xsl:attribute>
				<xsl:attribute name="height">320</xsl:attribute>
			</img>
		</p>
		<h2>「<xsl:value-of select="@title" />」に見えますか？評価してあげてください。</h2>
		<form action="./" method="post" onsubmit="return onSubmit(false)">
			<p>
				<input type="hidden" name="f" value="core/botVote" />
				<input type="hidden" name="id">
					<xsl:attribute name="value"><xsl:value-of select="bot/@id" /></xsl:attribute>
				</input>
				<input type="hidden" name="max" value="4" />
				<input type="submit" class="submit" name="score" value="4" />
				<input type="submit" class="submit" name="score" value="3" />
				<input type="submit" class="submit" name="score" value="2" />
				<input type="submit" class="submit" name="score" value="1" />
				<input type="submit" class="submit" name="score" value="0" />
				点
			</p>
		</form>
		<p>
			このぼっとのレベルは<xsl:value-of select="bot/@generation" />です。
			次のレベルまで必要な経験値は<xsl:value-of select="bot/@amount" />です。
		</p>
	</xsl:template>

</xsl:stylesheet>
