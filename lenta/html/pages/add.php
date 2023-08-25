<div class="cont-header">
	Добавить новость
</div>
<form id="createnews" method="post" action="api/nnews.php">
	<div class="add-block">
		<h2>Заголовок:</h2>
		<input type="text" name="title" style="width:98%" placeholder="Не более <?=$title_lim;?> символов">
	</div>
	<div class="add-block">
		<h2>Категория:</h2>
		<select name="category" class="block-option" style="width:98%">
			<option value="no">Без категории</option>
			<option value="aib">Новости АИБ</option>
			<option value="irl">Новости ИРЛ</option>
			<option value="int">Новости Интернета</option>
			<option value="all">Обсуждение</option>
		</select>
	</div>
	<div class="add-block">
		<h2>Текст:</h2>
		<p>
			Не более 1024 символов
		</p>
		<textarea name="text" rows="7" class="add-text" style="width:98%"></textarea>
		<p>
			Полный текст (если надо)
		</p>
		<textarea name="text2" rows="11" class="add-text" style="width:98%"></textarea>
	</div>
	<div class="add-block">
		<h2>Ссылка:</h2>
		<input type="text" name="link" style="width:98%" placeholder="https://lentachan.ru/">
	</div>
	<div class="add-block">
		<h2>Видео YouTube:</h2>
		<input type="text" name="video" style="width:98%" placeholder="http://www.youtube.com/watch?v=b1WWpKEPdT4">
	</div>
	<div class="add-block">
		<h2>Принадлежность:</h2>
		<select name="chan" class="block-option">
			<option>Нет</option>
			<option value="Анонимус">Анонимус</option>
			<option value="Двач">Два.Ч</option>
			<option value="0chan">Øchan</option>
			<option value="Ычан">Ычан</option>
			<option value="Доброчан">Доброчан</option>
			<option value="1chan.ru">1chan.ru</option>
			<option value="Оланет">Оланет</option>
			<option value="4chan">4chan</option>
			<option value="Lenta">Лента.ру</option>
			<option value="ICQ">ICQ</option>
			<option value="ВКонтакте">ВКонтакте</option>
		</select>
	</div>
	<div class="add-block">
		<h2>Капча:</h2>
		<a id="cchange"><img src="captcha.php" id="captchaimage"></a>
		<input type="text" name="captcha" id="captcha" autocomplete="off">
	</div>
	<div class="add-block">
		<input type="submit" class="button" value="Отправить">
	</div>
	<br>
</form>