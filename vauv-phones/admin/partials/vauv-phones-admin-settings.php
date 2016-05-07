<div class="wrap">
	<h2>Телефонный справочник</h2>
	<form action="options.php" method="post" enctype="multipart/form-data">
		<?php settings_fields( 'vauv-phones-settings-group' ); ?>
		<?php $vauv_phones_options = get_option( 'vauv-phones-options' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row">Импорт из файла</th>
				<td>
					<input type="file" name="phones-import"/>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="Импортировать" />
		</p>
	</form>

	<div>
		<h4>пример xml-файла:</h4>
				<!-- HTML generated using hilite.me -->
		<div style="background: #272822; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;"><pre style="margin: 0; line-height: 125%"><span style="color: #75715e">&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;</span>
		<span style="color: #f92672">&lt;root&gt;</span>
			<span style="color: #f92672">&lt;columns&gt;</span>
				<span style="color: #f92672">&lt;colum</span> <span style="color: #a6e22e">Name=</span><span style="color: #e6db74">&quot;Телефон&quot;</span> <span style="color: #a6e22e">Type=</span><span style="color: #e6db74">&quot;Телекомунікаційний ресурс, Null&quot;</span><span style="color: #f92672">/&gt;</span>
				<span style="color: #f92672">&lt;colum</span> <span style="color: #a6e22e">Name=</span><span style="color: #e6db74">&quot;Абонент&quot;</span> <span style="color: #a6e22e">Type=</span><span style="color: #e6db74">&quot;Null, Співробітник&quot;</span><span style="color: #f92672">/&gt;</span>
				<span style="color: #f92672">&lt;colum</span> <span style="color: #a6e22e">Name=</span><span style="color: #e6db74">&quot;Посада&quot;</span> <span style="color: #a6e22e">Type=</span><span style="color: #e6db74">&quot;Рядок, Null&quot;</span><span style="color: #f92672">/&gt;</span>
				<span style="color: #f92672">&lt;colum</span> <span style="color: #a6e22e">Name=</span><span style="color: #e6db74">&quot;Організація&quot;</span> <span style="color: #a6e22e">Type=</span><span style="color: #e6db74">&quot;Контрагент, Null&quot;</span><span style="color: #f92672">/&gt;</span>
				<span style="color: #f92672">&lt;colum</span> <span style="color: #a6e22e">Name=</span><span style="color: #e6db74">&quot;Підрозділ&quot;</span> <span style="color: #a6e22e">Type=</span><span style="color: #e6db74">&quot;Null, Підрозділ організації&quot;</span><span style="color: #f92672">/&gt;</span>
				<span style="color: #f92672">&lt;colum</span> <span style="color: #a6e22e">Name=</span><span style="color: #e6db74">&quot;ВідділЦехДільниця&quot;</span> <span style="color: #a6e22e">Type=</span><span style="color: #e6db74">&quot;Null, Підрозділ організації&quot;</span><span style="color: #f92672">/&gt;</span>
				<span style="color: #f92672">&lt;colum</span> <span style="color: #a6e22e">Name=</span><span style="color: #e6db74">&quot;ГрупаСлужба&quot;</span> <span style="color: #a6e22e">Type=</span><span style="color: #e6db74">&quot;Null, Підрозділ організації&quot;</span><span style="color: #f92672">/&gt;</span>
			<span style="color: #f92672">&lt;/columns&gt;</span>
			<span style="color: #f92672">&lt;records&gt;</span>
				<span style="color: #f92672">&lt;record&gt;</span>
					<span style="color: #f92672">&lt;Телефон&gt;</span><span style="color: #75715e">&lt;![CDATA[456]]&gt;</span><span style="color: #f92672">&lt;/Телефон&gt;</span>
					<span style="color: #f92672">&lt;Абонент&gt;</span><span style="color: #75715e">&lt;![CDATA[]]&gt;</span><span style="color: #f92672">&lt;/Абонент&gt;</span>
					<span style="color: #f92672">&lt;Посада&gt;</span><span style="color: #75715e">&lt;![CDATA[Водій КрАЗ-6510 АІ 16-34 СР]]&gt;</span><span style="color: #f92672">&lt;/Посада&gt;</span>
					<span style="color: #f92672">&lt;Організація&gt;</span><span style="color: #75715e">&lt;![CDATA[ДCП &quot;Чорнобильський спецкомбінат&quot;]]&gt;</span><span style="color: #f92672">&lt;/Організація&gt;</span>
					<span style="color: #f92672">&lt;Підрозділ&gt;</span><span style="color: #75715e">&lt;![CDATA[КТЗ]]&gt;</span><span style="color: #f92672">&lt;/Підрозділ&gt;</span>
					<span style="color: #f92672">&lt;ВідділЦехДільниця&gt;</span><span style="color: #75715e">&lt;![CDATA[Автоколона № 2]]&gt;</span><span style="color: #f92672">&lt;/ВідділЦехДільниця&gt;</span>
					<span style="color: #f92672">&lt;ГрупаСлужба&gt;</span><span style="color: #75715e">&lt;![CDATA[]]&gt;</span><span style="color: #f92672">&lt;/ГрупаСлужба&gt;</span>
				<span style="color: #f92672">&lt;/record&gt;</span>
				<span style="color: #f92672">&lt;record&gt;</span>
					<span style="color: #f92672">&lt;Телефон&gt;</span><span style="color: #75715e">&lt;![CDATA[459]]&gt;</span><span style="color: #f92672">&lt;/Телефон&gt;</span>
					<span style="color: #f92672">&lt;Абонент&gt;</span><span style="color: #75715e">&lt;![CDATA[]]&gt;</span><span style="color: #f92672">&lt;/Абонент&gt;</span>
					<span style="color: #f92672">&lt;Посада&gt;</span><span style="color: #75715e">&lt;![CDATA[Водій КрАЗ-6510 АІ 16-12 СР]]&gt;</span><span style="color: #f92672">&lt;/Посада&gt;</span>
					<span style="color: #f92672">&lt;Організація&gt;</span><span style="color: #75715e">&lt;![CDATA[ДCП &quot;Чорнобильський спецкомбінат&quot;]]&gt;</span><span style="color: #f92672">&lt;/Організація&gt;</span>
					<span style="color: #f92672">&lt;Підрозділ&gt;</span><span style="color: #75715e">&lt;![CDATA[КТЗ]]&gt;</span><span style="color: #f92672">&lt;/Підрозділ&gt;</span>
					<span style="color: #f92672">&lt;ВідділЦехДільниця&gt;</span><span style="color: #75715e">&lt;![CDATA[Автоколона № 2]]&gt;</span><span style="color: #f92672">&lt;/ВідділЦехДільниця&gt;</span>
					<span style="color: #f92672">&lt;ГрупаСлужба&gt;</span><span style="color: #75715e">&lt;![CDATA[]]&gt;</span><span style="color: #f92672">&lt;/ГрупаСлужба&gt;</span>
				<span style="color: #f92672">&lt;/record&gt;</span>
			<span style="color: #f92672">&lt;/records&gt;</span>
		<span style="color: #f92672">&lt;/root&gt;</span>
		</pre>
		</div>

	</div> <!-- / пример xml-файла -->

</div>