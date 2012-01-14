<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns="http://www.w3.org/1999/xhtml"
	exclude-result-prefixes="xhtml">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" />

	<!-- メイン。 -->
	<xsl:template match="/">

		<!-- HTML5のためのDOCTYPE宣言。 -->
		<xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;
</xsl:text>
		<!-- 出力のインデントが乱れるため、意図して改行しています。 -->

		<html xml:lang="ja">
			<head>
				<meta charset="UTF-8" />
				<xsl:if test="contains(body/@ua, ' IE ') or contains(body/@ua, ' MSIE ')">
					<meta http-equiv="X-UA-Compatible" content="IE=edge" />
					<meta name="msapplication-navbutton-color" content="#4378B6" />
				</xsl:if>
				<meta name="viewport" content="width=420" />
				<meta name="application-name" content="ぼっと[IB-01]がお絵かきするようです。" />
				<title><xsl:if test="body/@title and string-length(body/@title) > 0"><xsl:value-of select="body/@title" /> - </xsl:if>ぼっと[IB-01]がお絵かきするようです。</title>
				<link href="skin/default/default.css" rel="StyleSheet" />
				<script type="text/javascript"><xsl:attribute name="src">skin/default/jquery.<xsl:choose><xsl:when test="contains(body/@ua, 'Safari')">js</xsl:when><xsl:otherwise>jgz</xsl:otherwise></xsl:choose></xsl:attribute>;</script>
				<script type="text/javascript" src="skin/default/default.js">;</script>
				<xsl:comment> 評価中 </xsl:comment>
			</head>
			<body>
				<header>
					<h1><a href="./"><ruby>ぼっと<rt><rp>[</rp>IB-01<rp>]</rp></rt></ruby>がお絵かきするようです。</a></h1>
				</header>
				<xsl:apply-templates select="body/topic" />
				<xsl:apply-templates select="body" />
				<footer>
					<hr />
					<address>by Meiko</address>
				</footer>
			</body>
		</html>
	</xsl:template>

	<!-- トピック。 -->
	<xsl:template match="topic">
		<p><xsl:value-of select="@title" />: <xsl:value-of select="." /></p>
	</xsl:template>

</xsl:stylesheet>
