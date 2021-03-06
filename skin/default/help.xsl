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
		<p>ここに生息するぼっと「IB-01」はお題を与えるとひたすら絵を描き始めますので、みなさんで上手く描けているかどうか投票してあげてください。みなさんの投票によって、ぼっとはすくすくと成長していきます。ただ最初のうちは超ど素人なので、温かい目で見てあげてください。なかなか物覚えが悪い場合は、参考画像を食べさせてあげると早く成長します。</p>
		<h3>サポート</h3>
		<p>このWebアプリは制作期間1週間程度のアルファ版です。思わぬところからいろいろなバグが出てくるかもしれません。もしお気づきの点や、その他サポートしてほしい場合はmeikoｱｯｰとkagaminer.inへメールしてください。</p>
		<h2 id="description">技術的解説</h2>
		<ul>
			<li>画像の各ピクセルを遺伝子とみなし、<em><a href="http://www.youtube.com/watch?v=19M_LHsmgug" target="_blank">遺伝的アルゴリズム(GA)</a></em>を使用して成長しています。</li>
			<li>一様交叉、生存率10％、突然変異率0.39％です。</li>
			<li>予備学習は、各ピクセルのRGB毎の輝度を比較して算出しています。</li>
		</ul>
		<h3>遺伝的アルゴリズムの解りやすい解説(<a href="http://dic.nicovideo.jp/a/%E9%81%BA%E4%BC%9D%E7%9A%84%E3%82%A2%E3%83%AB%E3%82%B4%E3%83%AA%E3%82%BA%E3%83%A0" target="_blank">ニコニコ大百科より転載</a>)</h3>
		<ol>
			<li>少年少女を100人ずつ用意します。</li>
			<li>一生懸命歌っていただきます。</li>
			<li>歌の上手い上位5人ずつを残して残りは抹殺します。</li>
			<li>互いに交配して彼らの子供を男女100人ずつ用意します。</li>
			<li>2～4を何回も繰り返します。</li>
			<li>どっかで停止し、その時一番うまかった一組を残して抹殺します。</li>
			<li>残った2人が鏡音リン・レンです。</li>
			<li>なんだってー！？</li>
		</ol>
		<h3>作った理由とか</h3>
		<ul>
			<li><em>遺伝的アルゴリズムの学習および特性理解のため(現在構想中のブラゲ開発に絶対外せない要素の一つなので、制作に先立つ基礎研究)</em></li>
			<li>GDグラフィックライブラリの学習のため(割と以前からどういう代物か知っていたが、実際に使ったのは初めて)</li>
			<li><em>Web上でMetro UI風なインターフェイスを作ってみたかったｗ</em>←結論</li>
		</ul>
		<h2 id="foresight">課題・今後の予定</h2>
		<ul>
			<li>学習の高速化(GDでピクセル総なめするのが重いこと重いこと)</li>
			<li>学習の高効率化(スコアの高い遺伝子を隣の遺伝子が真似し始めたりとか)</li>
			<li>↑と半分被るけど初期収束が起こりやすいのを何とかしたいなぁ……</li>
			<li>高速なサーバへの移転(箱庭諸島クラスの重さなので、そのうちレンタルサーバ業者から怒られかねない)</li>
			<li>大きなサイズの画像対応(試算上ではおぞましいレベルの容量食う上に学習に数か月かかるので、上記3点が解決するまではちょっとやりたくないな……)</li>
		</ul>
	</xsl:template>

</xsl:stylesheet>
