<?php
define("NO_KEEP_STATISTIC", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

/** @var \CUser $USER */
global $USER;

try {
    \Bitrix\Main\Loader::includeModule('ylab.likes');

    $oApp = \Bitrix\Main\Application::getInstance();
    $oRequest = $oApp->getContext()->getRequest();
    $iContentId = $oRequest->get('iContentId');
    $iContentType = $oRequest->get('iContentType');
    $arResult = ['ERROR' => true];

    if (check_bitrix_sessid() && $oRequest->isAjaxRequest() && !empty($iContentId) && !empty($iContentType)) {
        $oResultSet = \Ylab\Likes\YlabLikesTable::setLike($iContentId, $iContentType, $USER->GetID());
        $arResult['ERROR'] = !$oResultSet->isSuccess();

        if ($oResultSet->isSuccess()) {
            $arResult['MESSAGE'] = $oResultSet->getErrorMessages();
        }
    }

    echo json_encode($arResult);
} catch (\Exception $e) {
    echo json_encode(['ERROR' => true, 'MESSAGE' => $e->getMessage()]);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
