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
					ぼっとは見本画像を観察して、<xsl:value-of select="@title" />の予備学習をしました。
				</h2>
				<p>
					このぼっとのレベルは<xsl:value-of select="bot/@generation" />です。
				</p>
				<p>
					<img alt="" title="ぼっとが描いてみた画像">
						<xsl:attribute name="src">?f=core/rawImage&amp;id=<xsl:value-of select="bot/@hash" /></xsl:attribute>
						<xsl:attribute name="width">100</xsl:attribute>
						<xsl:attribute name="height">100</xsl:attribute>
					</img>
					&lt;生成 | 見本&gt;
					<img alt="" title="見本として提示した画像">
						<xsl:attribute name="src">?f=core/rawImage&amp;id=<xsl:value-of select="bot/@example" /></xsl:attribute>
						<xsl:attribute name="width">100</xsl:attribute>
						<xsl:attribute name="height">100</xsl:attribute>
					</img><br />
					完全に一致率：<xsl:value-of select="bot/@same" /> パーセント
				</p>
				<h2>
					さらに予備学習を続けますか？
				</h2>
				<form action="./" method="post">
					<p>
						<input type="hidden" name="f" value="core/botStudy" />
						<input type="hidden" name="id">
							<xsl:attribute name="value"><xsl:value-of select="bot/@owner" /></xsl:attribute>
						</input>
						<input type="submit" name="continue" value="YES" />
						<input type="submit" name="continue" value="NO" />
					</p>
				</form>
				<ul>
					<li>一回学習するのに、10～20秒程度かかることがあります。ボタンを連打しないでください。</li>
					<li>予備学習を終えるまで公開されません。</li>
					<li>古い画像は削除されますので、記念に残したい場合はDLしてください。</li>
				</ul>
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
