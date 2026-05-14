<?php

class MyCProp
{
    private static $showedJs = false;
    private static $showedCss = false;

    public static function GetUserTypeDescription()
    {
        return
        [
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'CProp',
            'DESCRIPTION' => 'Комплексное свойство',
            'GetSettingsHTML' => array(__CLASS__, 'getSettingsHTML'),
            'GetPropertyFieldHtml' => array(__CLASS__,  'getPropertyFieldHtml'),
            'PrepareSettings' => array(__CLASS__, 'prepareSettings'),
            'ConvertToDB' => array(__CLASS__, 'convertToDB'),
            'ConvertFromDB' => array(__CLASS__,  'convertFromDB'),
        ];
    }

    public static function getSettingsHTML(
        array $arProperty,
        array $strHTMLControlName,
        array &$arPropertyFields
    ) {
        $arPropertyFields = array(
            'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SEARCHABLE', 'SMART_FILTER', 'WITH_DESCRIPTION', 'FILTRABLE', 'MULTIPLE_CNT', 'IS_REQUIRED'),
            'SET' => array(
                'MULTIPLE_CNT' => 1,
                'SMART_FILTER' => 'N',
                'FILTRABLE' => 'N',
            ),
        );

        $result = '<tr><td colspan="2" align="center">
            <table id="many-fields-table" class="many-fields-table internal">        
                <tr valign="top" class="heading mf-setting-title">
                <td>XML_ID</td>
                <td>Название</td>
                <td>Сорт.</td>
                <td>Тип</td>
                </tr>';

        $userSettings = $arProperty['USER_TYPE_SETTINGS'] ??
            $_POST[$strHTMLControlName["NAME"]] ??
            [];

        if (!empty($userSettings)) {
            $inputName = $strHTMLControlName['NAME'];
            foreach ($userSettings as $code => $arItem) {
                $result .=
                    '<tr valign="top">
                        <td><input type="text" class="inp-code" size="20" value="'. $code .'"></td>
                        <td><input type="text" class="inp-title" size="35"
                            name="'. "{$inputName}[$code][TITLE]" .'" value="'. $arItem['TITLE'] .'">
                        </td>
                        <td><input type="text" class="inp-sort" size="5" value="500"
                            name="'. "{$inputName}[$code][SORT]" .'" value="'. $arItem['SORT'] .'">
                        </td>
                        <td>
                            <select class="inp-type" name="'. "{$inputName}[$code][TYPE]" .'">
                                '.self::getOptionList($arItem['TYPE']).'
                            </select>                        
                        </td>
                    </tr>';
            }
        } else {
            $result .= '
                <tr valign="top">
                    <td><input type="text" class="inp-code" size="20"></td>
                    <td><input type="text" class="inp-title" size="35"></td>
                    <td><input type="text" class="inp-sort" size="5" value="500"></td>
                    <td>
                        <select class="inp-type"> '.self::getOptionList().'</select>                        
                    </td>
                </tr>';
        }

        $result .= '
            </table>                     
            <tr>
                <td colspan="2" style="text-align: center;">
                    <input id="addRowBtn" type="button" value="Добавить" onclick="addNewRows()">
                </td>
            </tr>
            </td></tr>';
    
        self::addJsToPropSettings($strHTMLControlName['NAME']);

        return $result;
    }

    public static function getPropertyFieldHtml(
        array $arProperty,
        array $value,
        array $strHTMLControlName
    ) {
        if (!empty($arProperty['USER_TYPE_SETTINGS'])){
            $arFields = $arProperty['USER_TYPE_SETTINGS'];
        } else{
            return '<span>Не заполнен список полей в настройках свойства</span>';
        }

        self::addJsToElementSettings();
        self::addCss();

        $result = '';
        $result .= '<div class="mf-gray"><a class="cl mf-toggle">Скрыть</a>';
        if($arProperty['MULTIPLE'] === 'Y'){
            $result .= ' | <a class="cl mf-delete">Удалить</a></div>';
        }
        $result .= '<table class="mf-fields-list active">';

        foreach ($arFields as $code => $arItem){
            if($arItem['TYPE'] === 'string'){
                $result .= self::showString($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
            else if($arItem['TYPE'] === 'text'){
                $result .= self::showTextarea($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
            else if($arItem['TYPE'] === 'date'){
                $result .= self::showDate($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
            else if($arItem['TYPE'] === 'editor'){
                $result .= self::showEditor($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
        }

        $result .= '</table>';

        return $result;
    }

    public static function prepareSettings(array $arFields)
    {
        $result = $arFields['USER_TYPE_SETTINGS'] ?? $arFields;
        
        if (is_array($result)) {
            foreach ($result as $code => $data) {
                if (empty($code) || (empty($data['TITLE']))) {
                    unset($result[$code]);
                }
            }
        }

        return is_array($result) ? $result : [];
    }

    public static function convertToDB(array $arProperty, array $value)
    {
        $arData = $value['VALUE'];
        $isEmpty = true;

        if (is_array($arData)) {
            foreach ($arData as $code => $fields) {
                if (is_array($fields)) {
                    $filteredFields = array_filter($fields, function($v) {
                        return !is_null($v) && $v !== '';
                    });

                    if (empty($filteredFields)) {
                        unset($arData[$code]);
                    } else {
                        $isEmpty = false;
                    }
                } elseif ($fields !== '') {
                    $isEmpty = false;
                }
            }
        }

        if (!$isEmpty) {
            return [
                'VALUE' => json_encode($arData),
                'DESCRIPTION' => ''
            ];
        }

        return ['VALUE' => '', 'DESCRIPTION' => ''];
    }

    public static function convertFromDB(array $arProperty, array $value)
    {
        $arResult = [];

        if (!empty($value['VALUE'])) {
            $arResult['VALUE'] = json_decode($value['VALUE'], true);
        }
        return $arResult;
    }

    private static function getOptionList($selected = 'string')
    {
        $result = '';
        $arOption = [
            'string' => 'Строка',
            'text' => "Текст",
            'date' => "Дата",
            'editor' => 'HTML Редактор'
        ];

        foreach ($arOption as $code => $name){
            $s = '';
            if($code === $selected){
                $s = 'selected';
            }

            $result .= '<option value="'.$code.'" '.$s.'>'.$name.'</option>';
        }

        return $result;
    }

    private static function addJsToPropSettings(string $inputName)
    {
        ?>
        <script>
            function addNewRows()
            {
                let rowsTable = document.getElementById("many-fields-table"); rowsTable.append()
                let row = 
                    '<tr valign="top">' +
                        '<td><input type="text" class="inp-code" size="20"></td>' +
                        '<td><input type="text" class="inp-title" size="35"></td>' +
                        '<td><input type="text" class="inp-sort" size="5" value="500"></td>' +
                        '<td> <select class="inp-type"> <?= self::getOptionList() ?> </select> </td>' +
                    '</tr>'
                rowsTable.insertAdjacentHTML('beforeend', row);
            }
            document.addEventListener('change', function(event) {
                if (event.target && event.target.classList.contains('inp-code')) {
                    const input = event.target;
                    const code = input.value;
                    const row = input.closest('tr');

                    const titleInp = row.querySelector('input.inp-title');
                    const sortInp = row.querySelector('input.inp-sort');
                    const typeSelect = row.querySelector('select.inp-type');

                    if (code.length <= 0) {
                        titleInp.removeAttribute('name');
                        sortInp.removeAttribute('name');
                        typeSelect.removeAttribute('name');
                    } else {
                        titleInp.setAttribute('name', '<?=$inputName?>'+'['+ code +']'+'[TITLE]');
                        sortInp.setAttribute('name', '<?=$inputName?>'+'['+ code +']'+'[SORT]');
                        typeSelect.setAttribute('name', '<?=$inputName?>'+'['+ code +']'+'[TYPE]');
                    }
                }
            });
        </script>
        <?php
    }

    private static function addJsToElementSettings()
    {
        if(!self::$showedJs) {
            self::$showedJs = true;
            ?>
            <script>
                document.addEventListener('click', function (e) {
                    if (e.target && e.target.matches('a.mf-toggle')) {
                        e.preventDefault();
                        
                        const link = e.target;
                        const parentTr = link.closest('tr');
                        const table = parentTr ? parentTr.querySelector('table.mf-fields-list') : null;

                        if (table) {
                            table.classList.toggle('active');
                            
                            if (table.classList.contains('active')) {
                                link.textContent = 'Свернуть';
                            } else {
                                link.textContent = 'Показать';
                            }
                        }
                    }

                    if (e.target && e.target.matches('a.mf-delete')) {
                        e.preventDefault();

                        const link = e.target;
                        const row = link.closest('tr');
                        if (!row) return;

                        const textInputs = row.querySelectorAll('input[type="text"]');
                        textInputs.forEach(item => {
                            item.value = '';
                        });

                        const textareas = row.querySelectorAll('textarea');
                        textareas.forEach(item => {
                            item.value = '';
                        });

                        row.style.transition = 'opacity 0.5s ease';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.style.display = 'none';
                        }, 500);
                    }
                });
            </script>
            <?
        }
    }

    private static function addCss()
    {
        if(!self::$showedCss) {
            self::$showedCss = true;
            ?>
            <style>
                .cl {cursor: pointer;}
                .mf-gray {color: #797777;}
                .mf-fields-list {display: none; padding-top: 10px; margin-bottom: 10px!important; margin-left: -300px!important; border-bottom: 1px #e0e8ea solid!important;}
                .mf-fields-list.active {display: block;}
                .mf-fields-list td {padding-bottom: 5px;}
                .mf-fields-list td:first-child {width: 300px; color: #616060;}
                .mf-fields-list td:last-child {padding-left: 5px;}
                .mf-fields-list input[type="text"] {width: 350px!important;}
                .mf-fields-list textarea {min-width: 350px; max-width: 650px; color: #000;}
                .mf-fields-list img {max-height: 150px; margin: 5px 0;}
                .mf-img-table {background-color: #e0e8e9; color: #616060; width: 100%;}
                .mf-fields-list input[type="text"].adm-input-calendar {width: 170px!important;}
                .mf-file-name {word-break: break-word; padding: 5px 5px 0 0; color: #101010;}
                .mf-fields-list input[type="text"].mf-inp-bind-elem {width: unset!important;}
            </style>
            <?
        }
    }

    private static function showString(
        $code,
        string $title,
        array $arValue,
        array $strHTMLControlName
    ) {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                    <td align="right">'.$title.': </td>
                    <td><input type="text" value="'.$v.'" name="'.$strHTMLControlName['VALUE'].'['.$code.']"/></td>
                </tr>';

        return $result;
    }

    private static function showTextarea($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                    <td align="right" valign="top">'.$title.': </td>
                    <td><textarea rows="8" name="'.$strHTMLControlName['VALUE'].'['.$code.']">'.$v.'</textarea></td>
                </tr>';

        return $result;
    }

    private static function showDate($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                        <td align="right" valign="top">'.$title.': </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="padding: 0;">
                                        <div class="adm-input-wrap adm-input-wrap-calendar">
                                            <input class="adm-input adm-input-calendar" type="text" name="'.$strHTMLControlName['VALUE'].'['.$code.']" size="23" value="'.$v.'">
                                            <span class="adm-calendar-icon"
                                                  onclick="BX.calendar({node: this, field:\''.$strHTMLControlName['VALUE'].'['.$code.']\', form: \'\', bTime: true, bHideTime: false});"></span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';

        return $result;
    }

    private static function showEditor($code, $title, $arValue, $strHTMLControlName)
    {
        $v = isset($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $fieldName = $strHTMLControlName['VALUE'].'['.$code.']';
        
        $editorId = 'editor_' . $fieldName . uniqid();

        ob_start();
        ?>
        <tr>
            <td align="right" valign="top"><?=$title?>: </td>
            <td>
                <?php
                    $editor = new \CHTMLEditor;
                    $editor->Show(array(
                        'id' => $editorId,
                        'width' => '100%',
                        'height' => '200px',
                        'inputName' => $fieldName,
                        'content' => $v,
                    ));
                ?>
            </td>
        </tr>
        <?php
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}
