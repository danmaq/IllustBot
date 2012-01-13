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
		<p id="version"><a href="HISTORY">VERSION <xsl:value-of select="@ver" /></a></p>
		<h2 id="overview">概要</h2>
		<p>
			ぼっとにお題を与えると、絵を描き始めるので、みなさんで上手く描けているか投票してあげてください。
			みなさんの投票によって、ぼっとは成長していきます。
			ぼっとは最初のうちは超ど素人なので、温かい目で見てあげてください。
		</p>
		<h3>サポート</h3>
		<p>
			このWebアプリは制作期間1週間程度のアルファ版です。
			思わぬところからいろいろなバグが出てくるかもしれません。
			もしお気づきの点や、その他サポートしてほしい場合は<a href="http://twitter.com/danmaq" target="_blank">Twitter(@danmaq)</a>へ@ツイートしてください。
		</p>
		<h2 id="description">技術的解説</h2>
		<ul>
			<li>画像の各ピクセルを遺伝子とみなし、<a href="http://www.youtube.com/watch?v=QE3SSKBdI00" target="_blank">遺伝的アルゴリズム</a>を使用して成長しています。</li>
			<li>一様交叉、生存率15％、突然変異率0.98％です。</li>
		</ul>
		<h3>作った理由とか</h3>
		<ul>
			<li><em>遺伝的アルゴリズムの学習および特性理解、ゲームへの応用可能性追求のため。</em></li>
			<li><em>Web上でMetro UI風なインターフェイスを作ってみたかったｗ</em></li>
			<li>GDグラフィックライブラリの学習のため(11年前から知っていたが、実際に使ったのは初めて)</li>
			<li>ここ暫くサーバサイド言語触ってなかったのでリハビリのため。</li>
			<li>クリエイターとしての売名行為←結論</li>
		</ul>
		<h2 id="foresight">課題・今後の予定</h2>
		<ul>
			<li>学習の高速化(ピクセル総なめするのが重いこと重いこと)</li>
			<li>学習の高効率化(スコアの高い遺伝子を隣の遺伝子が真似し始めたりとか)</li>
			<li>高速なサーバへの移転(箱庭並に重いのででそのうちレンタルサーバ業者から怒られるに違いない)</li>
			<li>途中から別の絵を覚えさせたりとか、画像サイズ変えたりとか</li>
			<li>お題の検索機能</li>
		</ul>
	</xsl:template>

</xsl:stylesheet>
