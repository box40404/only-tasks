<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

/**
 * @var array $arResult
 */

if ($arResult["isFormErrors"] == "Y"):?><?=$arResult["FORM_ERRORS_TEXT"];?><?endif;?>
<?= $arResult["FORM_NOTE"] ?? '' ?>
<?if ($arResult["isFormNote"] != "Y")
{
?>

<div class="contact-form">
	<?
	if ($arResult["isFormTitle"])
	{
	?>
		<div class="contact-form__head">
			<div class="contact-form__head-title"><?=$arResult["FORM_TITLE"]?></div>
	<?
	} //endif ;
	?>
	<?
	if ($arResult["isFormDescription"])
	{
	?>
			<div class="contact-form__head-text"><?=$arResult["FORM_DESCRIPTION"]?></div>
	<?
	} //endif ;
	?>
		</div>


	<form class="contact-form__form" name="<?= $arResult["arForm"]["SID"] ?>" action="<?= POST_FORM_ACTION_URI ?>" method="POST" enctype="multipart/form-data">
		<?= bitrix_sessid_post() . '<input type="hidden" name="WEB_FORM_ID" value="' . $arResult['arForm']['ID'] . '" />' ?>
		<div class="contact-form__form-inputs">
	<?
	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
		{
			echo $arQuestion["HTML_CODE"];
		}
		else
		{
	?>
			<div class="input contact-form__input"><label class="input__label" for="<?= $FIELD_SID ?>">
				<?if (isset($arResult["FORM_ERRORS"][$FIELD_SID])):?>
				<span class="error-fld" title="<?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>"></span>
				<?endif;?>
				<div class="input__label-text"><?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y") echo '*'?></div>

				<?php
				$input = match($FIELD_SID)
				{
					'medicine_name' => '<input class="input__input" type="text" id="medicine_name" name="medicine_name" required>',
					'medicine_email' => '<input class="input__input" type="email" id="medicine_email" name="medicine_email" required>',
					'medicine_company' => '<input class="input__input" type="text" id="medicine_company" name="medicine_company" required="">',
					'medicine_phone' => "<input class='input__input' type='tel' id='medicine_phone'
											data-inputmask=\"'mask': '+79999999999', 'clearIncomplete': 'true'\" maxlength='12'
                       						x-autocompletetype='phone-full' name='medicine_phone' required>",
					'medicine_message' => '<textarea class="input__input" type="text" id="medicine_message" name="medicine_message"></textarea>',
					default => '<input class="input__input" type="<?= $arQuestion[0]["FIELD_TYPE"] ?>" id="<?= $FIELD_SID ?>" name="$FIELD_SID"
									<?php if ($arQuestion["REQUIRED"] == "Y") required ?>'
				};
				?>

				<?php if ($arQuestion[0]["FIELD_TYPE"] == 'textarea'): ?>
					<div class="contact-form__form-message">
						<div class="input"><label class="input__label" for="<?= $FIELD_SID ?>">
							<div class="input__label-text"><?= $arQuestion["CAPTION"] ?></div>
								<?= $input ?>
							<div class="input__notification"></div>
						</label></div>
					</div>
				<?php else: ?>
					<?= $input ?>
					<div class="input__notification"></div>
				</label></div>
				<?php endif; ?>
	<?
		}
	} //endwhile
	?>
		</div>

		<div class="contact-form__bottom">
            <div class="contact-form__bottom-policy">Нажимая &laquo;Отправить&raquo;, Вы&nbsp;подтверждаете, что
                ознакомлены, полностью согласны и&nbsp;принимаете условия &laquo;Согласия на&nbsp;обработку персональных
                данных&raquo;.
            </div>
            <button class="form-button contact-form__bottom-button" data-success="Отправлено" data-error="Ошибка отправки"
					<?php (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "") ?> type="submit">
                <div class="form-button__title"><?= $arResult['arForm']['BUTTON'] ?></div>
            </button>
        </div>


<?
if($arResult["isUseCaptcha"] == "Y")
{
?>
		<tr>
			<th colspan="2"><b><?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?></b></th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" /><img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" width="180" height="40" alt=""/></td>
		</tr>
		<tr>
			<td><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?><?=$arResult["REQUIRED_SIGN"];?></td>
			<td><input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" /></td>
		</tr>
<?
} // isUseCaptcha
?>

</div>
<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)
