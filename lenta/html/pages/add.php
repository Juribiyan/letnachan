<?php 
if (USE_TELEGRAM) {
  require_once 'api/boolk_api.php';
  list($telegram_logon, $may_post) = telegramLogon();
}
else {
  list($telegram_logon, $may_post) = ['', true];
}
?>
<div class="cont-header">
	Добавить новость
</div>
<?= $telegram_logon ?>
<form id="createnews" method="post" class="onlogin-reveal" action="api/nnews.php" <?= (!$may_post) ? 'style="display:none"' : '' ?>>
	<div class="add-block">
		<h2>Заголовок:</h2>
		<input type="text" name="title" placeholder="Не более <?=$title_lim;?> символов">
	</div>
	<div class="add-block">
		<h2>Категория:</h2>
		<select name="category" class="block-option">
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
		<textarea name="text" rows="7" class="add-text"></textarea>
		<p>
			Полный текст (если надо)
		</p>
		<textarea name="text2" rows="11" class="add-text"></textarea>
	</div>
	<div class="add-block">
		<h2>Ссылка:</h2>
		<input type="text" name="link" placeholder="https://lentachan.ru/">
	</div>
	<?php require_once 'inc/embeds.php'; ?>
	<script>const embeds = <?= json_encode($embeds) ?></script>
	<div class="add-block">
		<h2>Вложение:</h2>
		<div class="stretchy-input">
			<input type="text" name="video" placeholder="Ссылка на <?= implode(', ', array_keys($embeds)) ?>">
		</div>
	</div>
	<div class="add-block">
		<h2>Принадлежность:</h2>
		<label title="Нет">
			<input type="radio" name="chan" checked>
			<span class="hb-img">Нет</span>
		</label>
		<?php require_once 'custom/homeboards.php';
		foreach($homeboards as $brd_id => $brd): ?>
			<?php if (!@$brd['disabled']): ?>
			<label title="<?= $brd['name'] ?>">
				<input type="radio" name="chan" value="<?= $brd_id ?>">
				<img class="hb-img" src="<?= ROOT_URL . '/images/' . $brd['icon'] ?>" alt="<?= $brd_id ?>">
			</label>
			<?php endif;?>
		<?php endforeach; ?>
	</div>
	<?php if (USE_HCAPTCHA): ?>
		<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
		<div class="h-captcha" data-sitekey="<?= HCAPTCHA_SITEKEY ?>" data-callback="enable_submit"></div>
	<?php else: ?>
		<div class="add-block">
			<h2>Капча:</h2>
			<a id="cchange"><img src="captcha.php" id="captchaimage"></a>
			<input type="text" name="captcha" id="captcha" autocomplete="off">
		</div>
	<?php endif; ?>
	<div class="add-block">
		<input type="submit" class="button" value="Отправить"<?= USE_HCAPTCHA ? ' disabled' : '' ?>>
	</div>
	<br>
</form>