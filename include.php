<?php

$arJsLibs = [
    'YlabLikesForm' => [
        'js' => '/bitrix/themes/ylab.likes/js/YlabLikesForm.js',
        'lang' => '/bitrix/themes/ylab.likes/lang/' . LANGUAGE_ID . '/YlabLikesForm.php',
        'rel' => ['ajax']
    ],
];

foreach ($arJsLibs as $jsLib => $arJsLib) {
    CJSCore::RegisterExt($jsLib, $arJsLib);
}