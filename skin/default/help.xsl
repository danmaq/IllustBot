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
		<h2>ヘルプ</h2>
		<ul id="mainMenu">
			<li><a href="#overview">概要</a></li>
			<li><a href="#description">解説</a></li>
			<li><a href="#foresight">今後</a></li>
		</ul>
		<h2 id="#overview">概要</h2>
		<p>
			ぼっとにお題を与えると、絵を描き始めるので、みなさんで上手く描けているか投票してあげてください。
			みなさんの投票によって、ぼっとは成長していきます。
			ぼっとは最初のうちは超ど素人なので、温かい目で見てあげてください。
		</p>
		<h2 id="#description">解説</h2>
		<ul>
			<li>画像の各ピクセルを遺伝子とみなし、遺伝的アルゴリズムを使用して成長しています。</li>
			<li>一様交叉、生存率15％、突然変異率0.98％です。</li>
		</ul>
		<h2 id="#foresight">今後</h2>
		<ul>
			<li>学習の高速化(ピクセル総なめするのが重いこと重いこと)。</li>
			<li>レンタルサーバ業者からそのうち過負荷で怒られると思うので高速なところへ移転する。</li>
			<li>途中から別の絵を覚えさせたりとか。</li>
		</ul>
	</xsl:template>

</xsl:stylesheet>
