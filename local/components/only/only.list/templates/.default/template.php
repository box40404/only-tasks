<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>


<div id="barba-wrapper">
    <div class="article-list">
		<?php foreach ($arResult['ITEMS'] as $id => $arItem): ?>
			<? foreach ($arItem as $item): ?>
				<a class="article-item article-list__item" href=""
									data-anim="anim-3">
					<div class="article-item__wrapper">
						<div class="article-item__title"><?= $item['NAME'] ?></div>
						<div class="article-item__content"><?= $item['PREVIEW_TEXT'] ?></div>
					</div>
				</a>
			<? endforeach; ?>
		<?php endforeach; ?>
	</div>
</div>