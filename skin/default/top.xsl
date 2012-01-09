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
				<h2>
					メインメニュー
				</h2>
				<ul>
					<li><a href="?f=core/newGame">依頼する</a></li>
					<li><a href="?f=core/list">育てる</a></li>
					<li>ヘルプ(工事中)</li>
				</ul>
				<h2>
					新着お題
				</h2>
				<xsl:apply-templates select="new" />
				<h2>
					高評価順ランキング
				</h2>
				<xsl:apply-templates select="score" />
				<h2>
					覚えが早い順ランキング
				</h2>
				<xsl:apply-templates select="gene" />
				<footer>
					<hr />
					<address>by danmaq</address>
				</footer>
			</body>
		</html>
	</xsl:template>

	<!-- トピック。 -->
	<xsl:template match="score|gene|new">
		<ol>
			<xsl:apply-templates select="item" />
		</ol>
	</xsl:template>

	<!-- トピック。 -->
	<xsl:template match="item">
		<li>
			<a>
				<xsl:attribute name="href">?<xsl:value-of select="@id" /></xsl:attribute>
				<xsl:value-of select="@theme" />
			</a>
		</li>
	</xsl:template>

</xsl:stylesheet>
