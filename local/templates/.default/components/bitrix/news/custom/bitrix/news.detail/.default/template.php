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

<div class="article-card">
	<?php if ($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]): ?>
    	<div class="article-card__title"><?=$arResult["NAME"]?></div>
	<?php endif; ?>
	<?php if ($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]): ?>
    	<div class="article-card__date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div>
	<?php endif; ?>
    <div class="article-card__content">
		<?php if ($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])): ?>
			<div class="article-card__image sticky">
				<img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>" data-object-fit="cover"/>
			</div>
		<?php endif; ?>
        <div class="article-card__text">
			<?php if (($arResult["DETAIL_TEXT"] <> '')): ?>
				<div class="block-content" data-anim="anim-3">
					<?=$arResult["DETAIL_TEXT"]?>
				</div>
			<?php else:?>
				<div class="block-content" data-anim="anim-3">
					<?=$arResult["PREVIEW_TEXT"]?>
				</div>
			<?php endif; ?>
            <?= $arParams['~BACK_LINK'] ?>
		</div>
    </div>
</div>
