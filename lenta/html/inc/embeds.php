<?php
$embeds = [
	"Youtube" => "/(?:youtu(?:\.be|be\.com)\/(?:.*v(?:\/|=)|(?:.*\/)?)([\w'-]+))/",
	"Catbox" => "/(?:https?:\/\/)?(files\.catbox\.moe\/[a-zA-Z0-9]+\.(gif|jpe?g|png|webm|mp4))/"
];

function embed_Youtube($matches) {
	$code = $matches[1];
	$img = "https://img.youtube.com/vi/$code/0.jpg";
	return <<<EOT
<figure class="embed embed-youtube">
	<label>
		<input type="checkbox">
		<div class="expandable-thumb">
			<div class="et-collapsed"><img src="https://img.youtube.com/vi/{$code}/mqdefault.jpg"></div>
			<div class="et-expanded" data-code="{$code}">
				<img src="https://img.youtube.com/vi/{$code}/sddefault.jpg">
				<noscript>
					<a href="https://www.youtube.com/watch?v={$code}" target="_blank">Смотреть на Youtube</a>
				</noscript>
			</div>
		</div>
		<div class="figure-controls">
			<a href="https://www.youtube.com/watch?v={$code}" target="_blank" title="Открыть на Youtube"><img src="/images/embeds/Youtube.png"></a>
			<div class="close-button" title="Свернуть"></div>
		</div>
	</label>
</figure>
EOT;
}

function embed_Catbox($matches) {
	$url = $matches[1];
	$ext = $matches[2];
	$url = "https://$url";
	$is_video = in_array($ext, array('mp4', 'webm'));
	$media_tag = $is_video
		? 'video controls autoplay loop muted'
		: 'img';
	$closing_tag = $is_video ? '</video>' : '';
	$collapse = $is_video ? "<div title=\"Свернуть\" class=\"close-button\"></div>" : '';

	return <<<EOT
<figure class="embed embed-catbox">
	<label>
		<input type="checkbox">
		<{$media_tag} src="{$url}">{$closing_tag}
		<div class="figure-controls">
			<a href="{$url}" target="_blank" title="Открыть на Catbox"><img src="/images/embeds/Catbox.png"></a>
			{$collapse}
		</div>
	</label>
</figure>
EOT;
}

// function generic_figure()