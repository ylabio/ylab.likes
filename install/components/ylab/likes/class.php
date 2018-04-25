<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Likes\YlabLikesTable;

Loc::loadMessages(__FILE__);

/**
 * Class ylab_likes_component - Класс компонента реализует получение данных о голосах за контент
 * Стандарный шаблон уже содержит все необходимое для типового решения
 */
class ylab_likes_component extends CBitrixComponent
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * @param $arParams
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!Loader::includeModule('ylab.likes')) {
            $this->errors[] = Loc::getMessage('YLAB_LIKES_MODULE_EXISTS');
        }

        if (empty($arParams['ELEMENT_ID'])) {
            $this->errors[] = Loc::getMessage('YLAB_LIKES_PARAM_ELEMENT_ID_EMPTY');
        }

        if (empty($arParams['ENTITY_ID'])) {
            $this->errors[] = Loc::getMessage('YLAB_LIKES_PARAM_ENTITY_ID_EMPTY');
        }

        return parent::onPrepareComponentParams($arParams);
    }

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\ArgumentException
     */
    public function executeComponent()
    {
        /** @var \CUser $USER */
        global $USER;

        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                ShowError($error);
            }

            return;
        }

        if (!$USER->IsAuthorized()) {
            //Голосовать могут только авторизованные пользователи
            return;
        }


        $iUserId = $USER->GetID();
        $iElementId = $this->arParams['ELEMENT_ID'];
        $iEntityId = $this->arParams['ENTITY_ID'];

        if ($this->StartResultCache()) {
            $this->arResult['STAT'] = YlabLikesTable::getContentStat($iElementId, $iEntityId, $iUserId)[0];
        }

        $this->includeComponentTemplate();
    }
}
