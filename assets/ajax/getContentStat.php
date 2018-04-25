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
    $arResult = ['ERROR' => true, 'STAT' => []];

    if (check_bitrix_sessid() && $oRequest->isAjaxRequest() && !empty($iContentId) && !empty($iContentType)) {
        $arResultStat = \Ylab\Likes\YlabLikesTable::getContentStat($iContentId, $iContentType, $USER->GetID());

        if ($arResultStat) {
            $arResult['ERROR'] = false;
            $arResult['STAT'] = $arResultStat[0];
        }
    }

    echo json_encode($arResult);
} catch (\Exception $e) {
    echo json_encode(['ERROR' => true, 'STAT' => [], 'MESSAGE' => $e->getMessage()]);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
