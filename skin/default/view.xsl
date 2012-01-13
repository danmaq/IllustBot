<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
	xmlns="http://www.w3.org/1999/xhtml"
	exclude-result-prefixes="xhtml">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" media-type="application/xhtml+xml" />

	<!-- メイン。 -->
	<xsl:template match="/body">

		<!-- HTML5のためのDOCTYPE宣言。 -->
		<xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;
</xsl:text>
		<!-- 出力のインデントが乱れるため、意図して改行しています。 -->

		<html xml:lang="ja">
			<head>
				<meta charset="UTF-8" />
				<xsl:if test="contains(@ua, ' IE ') or contains(@ua, ' MSIE ')">
					<meta http-equiv="X-UA-Compatible" content="IE=edge" />
					<meta name="msapplication-navbutton-color" content="#BCC0DD" />
				</xsl:if>
				<meta name="application-name" content="Network Utterance Environment" />
				<meta name="author" content="danmaq" />
				<title>
					<xsl:if test="@title and string-length(@title) > 0"><xsl:value-of select="@title" /> - </xsl:if>
					ぼっと[IB-01]がお絵かきするようです。
				</title>
				<link href="skin/default/default.css" rel="StyleSheet" />
				<link href="http://twitter.com/danmaq" rel="Author" />
				<xsl:comment> 評価中 </xsl:comment>
			</head>
			<body>
				<header>
					<h1>
						<a href="./">ぼっと[IB-01]がお絵かきするようです。</a>
					</h1>
				</header>
				<xsl:apply-templates select="topic" />
				<h2>
					今度は<xsl:value-of select="@title" />を描いてみたようです。
				</h2>
				<p>
					このぼっとのレベルは<xsl:value-of select="bot/@generation" />です。
					次のレベルまで必要な経験値は<xsl:value-of select="bot/@amount" />です。
				</p>
				<p>
					<img alt="" title="ぼっとが描いてみた画像">
						<xsl:attribute name="src">?f=core/rawImage&amp;id=<xsl:value-of select="bot/@hash" /></xsl:attribute>
						<xsl:attribute name="width">320</xsl:attribute>
						<xsl:attribute name="height">320</xsl:attribute>
					</img>
				</p>
				<h2>
					「<xsl:value-of select="@title" />」に見えますか？評価してあげてください。
				</h2>
				<form action="./" method="post">
					<p>
						<input type="hidden" name="f" value="core/botVote" />
						<input type="hidden" name="id">
							<xsl:attribute name="value"><xsl:value-of select="bot/@id" /></xsl:attribute>
						</input>
						<input type="hidden" name="max" value="4" />
						<input type="submit" name="score" value="4" />
						<input type="submit" name="score" value="3" />
						<input type="submit" name="score" value="2" />
						<input type="submit" name="score" value="1" />
						<input type="submit" name="score" value="0" />
						点
					</p>
				</form>
				<footer>
					<hr />
					<address>by danmaq</address>
				</footer>
			</body>
		</html>
	</xsl:template>

	<!-- トピック。 -->
	<xsl:template match="topic">
		<p>
			<xsl:value-of select="@title" />: <xsl:value-of select="." />
		</p>
	</xsl:template>

</xsl:stylesheet>
