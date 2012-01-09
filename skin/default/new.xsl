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
				<ul>
					<li>ぼっとにお題を与えてください。<li>
					<li>できるだけ記号的で単純なお題を与えてあげると、成長が早いです(丸とか星形とか)。<li>
				</ul>
				<form action="./" method="post">
					<ul>
						<li>
							<label for="theme">お題</label>
							<input type="text" id="theme" name="theme" value="" maxlength="255" placeholder="255字以内" />
						</li>
						<li>
							<label for="x">サイズ</label>
							<select id="x" name="x">
								<option value="8">8 x 8</option>
								<option value="16">16 x 16</option>
							</select>
						</li>
						<li>
							<label for="childs">ぼっとの性格</label>
							<select id="childs" name="childs">
								<option value="10">飲み込み早いけど、偏屈さん</option>
								<option value="40">バランス型</option>
								<option value="100">気変わりしやすく、物覚え悪い</option>
							</select>
						</li>
						<li>
							<input type="hidden" name="f" value="core/newGamePOST" />
							<input type="submit" value="これ描いて！" />
						</li>
					</ul>
				</form>
				<footer>
					<hr />
					<address>by danmaq</address>
				</footer>
			</body>
		</html>
	</xsl:template>

	<!-- トピック。 -->
	<xsl:template name="topic" match="topic">
		<p>
			<xsl:value-of select="@title" />: <xsl:value-of select="." />
		</p>
	</xsl:template>

</xsl:stylesheet>
