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
		<xsl:choose>
			<xsl:when test="bot/@generation = bot/@ownergeneration">
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
						<input type="submit" class="submit" name="score" value="0" />点
					</p>
				</form>
				<ul>
					<li>このぼっとのレベルは<xsl:value-of select="bot/@generation + 1" />で、次のレベルまで必要な経験値は<xsl:value-of select="bot/@amount" />です。</li>
					<li>みなさんの評価によって、ぼっとが成長していきます。(<a href="?f=core/help" target="_blank">詳しくはヘルプ参照</a>)</li>
					<li>一回学習するのに、3秒～4秒程度かかりることがあります。ボタンを二度押ししないでください。</li>
					<li><a><xsl:attribute name="href">?f=core/newGame&amp;id=<xsl:value-of select="bot/@owner" /></xsl:attribute>このぼっとに別のお題を教えることもできます</a>。(コピーが作られるため、このお題は消えません)</li>
				</ul>
			</xsl:when>
			<xsl:otherwise>
				<h2>このお絵かきの投票は〆め切りました。</h2>
				<p><em>現在このぼっとは<a><xsl:attribute name="href">?<xsl:value-of select="bot/@owner" /></xsl:attribute>更なる進化を遂げています</a></em>ので、良かったら見てあげてください。</p>
			</xsl:otherwise>
		</xsl:choose>
		<ul>
			<li>
				<form action="./" method="post">
					<label for="comment">コメント</label>
					<input type="text" class="text" id="comment" name="comment" maxlength="80" placeholder="80字以内" />
					<input type="hidden" name="f" value="core/commentPOST" />
					<input type="hidden" name="id">
						<xsl:attribute name="value"><xsl:value-of select="bot/@id" /></xsl:attribute>
					</input>
					<input type="submit" class="submit" value="投稿する" />
				</form>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-size="large" data-count="none" data-hashtags="IB_01"><xsl:attribute name="data-text">ぼっとが<xsl:value-of select="@title" />を描いてみたようです。(レベル<xsl:value-of select="bot/@generation + 1" />)</xsl:attribute>このリンクでTwitterにて共有できます。</a>
			</li>
			<xsl:apply-templates select="comment/item" />
		</ul>
	</xsl:template>

	<!-- コメント。 -->
	<xsl:template match="item">
		<li><xsl:value-of select="@comment" />[<xsl:value-of select="@datetime" />]</li>
	</xsl:template>

</xsl:stylesheet>
