<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div id="barba-wrapper">
    <div class="article-list">
		<?php foreach ($arResult['ITEMS'] as $item): ?>
			<a class="article-item article-list__item" href="<?= $item['DETAIL_PAGE_URL'] ?>"
                                 data-anim="anim-3">
				<div class="article-item__background"><img src="<?= $item['PREVIEW_PICTURE']['SAFE_SRC'] ?>"
														   alt=""/></div>
				<div class="article-item__wrapper">
					<div class="article-item__title"><?= $item['NAME'] ?></div>
					<div class="article-item__content"><?= $item['PREVIEW_TEXT'] ?></div>
				</div>
    		</a>
		<?php endforeach; ?>
	</div>
</div>
