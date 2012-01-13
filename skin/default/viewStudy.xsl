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
		<h2>ぼっとは見本画像を観察して、<xsl:value-of select="@title" />の予備学習をしました。</h2>
		<p>
			<img alt="" title="ぼっとが描いてみた画像">
				<xsl:attribute name="src">?f=core/rawImage&amp;id=<xsl:value-of select="bot/@hash" /></xsl:attribute>
				<xsl:attribute name="width">100</xsl:attribute>
				<xsl:attribute name="height">100</xsl:attribute>
			</img>
			&lt;-生成 | 見本-&gt;
			<img alt="" title="見本として提示した画像">
				<xsl:attribute name="src">?f=core/rawImage&amp;id=<xsl:value-of select="bot/@example" /></xsl:attribute>
				<xsl:attribute name="width">100</xsl:attribute>
				<xsl:attribute name="height">100</xsl:attribute>
			</img><br />
			完全に一致率：<xsl:value-of select="bot/@same" /> パーセント<br />
			このぼっとのレベルは<xsl:value-of select="bot/@generation" />です。
		</p>
		<h2>さらに予備学習を続けますか？</h2>
		<form action="./" method="post">
			<p>
				<input type="hidden" name="f" value="core/botStudy" />
				<input type="hidden" name="id">
					<xsl:attribute name="value"><xsl:value-of select="bot/@owner" /></xsl:attribute>
				</input>
				<input type="submit" class="submit" name="continue" value="YES" />
				<input type="submit" class="submit" name="continue" value="NO" />
			</p>
		</form>
		<ul>
			<li>一回学習するのに、10秒～30秒程度かかります。ボタンを二度押ししないでください。</li>
			<li>予備学習を終えるまで公開されません。</li>
			<li>古い画像は削除されますので、記念に残したい場合はDLしてください。</li>
		</ul>
	</xsl:template>

</xsl:stylesheet>
