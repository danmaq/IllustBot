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
					<li>ぼっとにお題を与えてください。</li>
					<li>ぼっとは超ど素人です。できるだけ記号的で単純なお題を与えてあげると、成長が早いです(丸とか星形とか)。</li>
				</ul>
				<form action="./" method="post" enctype="multipart/form-data">
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
								<option value="32">32 x 32</option>
							</select>
							(小さい方が早く覚えるよ！)
						</li>
						<li>
							<label for="childs">ぼっとの性格</label>
							<select id="childs" name="childs">
								<option value="20">飲み込み早いけど、偏屈さん</option>
								<option value="50" selected="selected">バランス型</option>
								<option value="100">気変わりしやすく、物覚え悪い</option>
							</select>
						</li>
						<li>
							<label for="example">予備学習</label>
							<input type="file" id="example" name="example" value="" placeholder="予備学習させたい場合のみ選択" />
						</li>
						<li>
							<input type="hidden" name="f" value="core/newGamePOST" />
							<input type="submit" value="これ描いて！" />
						</li>
					</ul>
				</form>
				<h2>
					予備学習の注意
				</h2>
				<ul>
					<li>あんまりマイナーなフォーマットの画像は嫌がります。</li>
					<li>大きすぎたり、AS比が大きく食い違う画像も嫌がります。</li>
					<li>予め食べやすいようにリサイズしてあげると喜びます。</li>
					<li>一回学習するのに、10～20秒程度かかることがあります。ボタンを連打しないでください。</li>
					<li>予備学習中は公開されません。また古い画像は削除されますので、記念に残したい場合はDLしてください。</li>
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
