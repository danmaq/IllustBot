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
		<ul>
			<li>ぼっとにお題を与えてください。</li>
			<li>ぼっとは超ど素人です。できるだけ記号的で単純なお題を与えてあげると、成長が早いです(丸とか星形とか)。</li>
		</ul>
		<form action="./" method="post" enctype="multipart/form-data">
			<ul>
				<li>
					<label for="theme">お題</label>
					<input type="text" class="text" id="theme" name="theme" value="" maxlength="255" placeholder="255字以内" />
				</li>
				<li>
					<label for="x">サイズ</label>
					<select id="x" name="x">
						<option value="8">8 x 8</option>
						<option value="16">16 x 16</option>
						<option value="32">32 x 32</option>
					</select>(小さい方が早く覚えるよ！)
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
					<label for="example">予備学習※</label>
					<input type="file" id="example" name="example" value="" placeholder="予備学習させたい場合のみ選択" />
				</li>
				<li>
					<input type="hidden" name="f" value="core/newGamePOST" />
					<input type="submit" class="submit" value="これ描いて！" />
				</li>
			</ul>
		</form>
		<h2>※予備学習の注意</h2>
		<ul>
			<li>あんまりマイナーなフォーマットの画像は嫌がります。</li>
			<li>大きすぎたり、AS比が大きく食い違う画像も嫌がります。</li>
			<li>予め食べやすいようにリサイズしてあげると喜びます。</li>
			<li>一回学習するのに、10～20秒程度かかることがあります。ボタンを連打しないでください。</li>
			<li>予備学習中は公開されません。また古い画像は削除されますので、記念に残したい場合はDLしてください。</li>
		</ul>
	</xsl:template>
</xsl:stylesheet>
