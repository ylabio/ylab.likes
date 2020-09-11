<?php

use Bitrix\Main\Component\ParameterSigner;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Likes\YlabLikesTable;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Entity;

/** @var \CUser $USER */
global $USER;

Loc::loadMessages(__FILE__);

/**
 * Class ylab_likes_component - Класс компонента реализует получение данных о голосах за контент
 * Стандарный шаблон уже содержит все необходимое для типового решения
 */
class ylab_likes_component extends CBitrixComponent implements Controllerable
{
    const VoteDislike = -1;
    const VoteLike = 1;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @return array|array[][]
     */
    public function configureActions()
    {
        return [
            'setLike' => [
                'prefilters' => [],
            ],
            'setDislike' => [
                'prefilters' => [],
            ],
            'getContentStat' => [
                'prefilters' => [],
            ],
        ];
    }

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
     * @param $sSignedParameters
     * @return false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\Security\Sign\BadSignatureException
     * @throws \Bitrix\Main\SystemException
     */
    public function setLikeAction($sSignedParameters)
    {
        /** @var \CUser $USER */
        global $USER;

        if(!$USER->IsAuthorized()) return false;

        $signer = new ParameterSigner;
        $this->arParams = $signer->unsignParameters($this->__name, $sSignedParameters);
        $this->arParams = $this->onPrepareComponentParams($this->arParams);

        $oResultGet = YlabLikesTable::getList([
            'filter' => [
                'CONTENT_ID' => $this->arParams['ELEMENT_ID'],
                'CONTENT_TYPE' => $this->arParams['ENTITY_ID'],
                'USER_ID' => $USER->GetID()
            ],
            'select' => ['ID', 'VOTE']
        ]);
        if ($oResultGet->getSelectedRowsCount() > 0) {
            $arResultGet = $oResultGet->fetch();
            if ($arResultGet['VOTE'] == self::VoteLike) {
                $oResult = YlabLikesTable::delete($arResultGet['ID']);
            } else {
                $oResult = YlabLikesTable::update($arResultGet['ID'], ['VOTE' => self::VoteLike]);
            }
        } else {
            $oResult = YlabLikesTable::add([
                'CONTENT_ID' => $this->arParams['ELEMENT_ID'],
                'CONTENT_TYPE' => $this->arParams['ENTITY_ID'],
                'USER_ID' => $USER->GetID(),
                'VOTE' => self::VoteLike
            ]);
        }

        return $oResult;

    }

    /**
     * @param $sSignedParameters
     * @return mixed
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\Security\Sign\BadSignatureException
     */
    public function setDislikeAction($sSignedParameters)
    {
        /** @var \CUser $USER */
        global $USER;

        if(!$USER->IsAuthorized()) return false;

        $signer = new ParameterSigner;
        $this->arParams = $signer->unsignParameters($this->__name, $sSignedParameters);
        $this->arParams = $this->onPrepareComponentParams($this->arParams);

        $oResultGet = YlabLikesTable::getList([
            'filter' => [
                'CONTENT_ID' => $this->arParams['ELEMENT_ID'],
                'CONTENT_TYPE' => $this->arParams['ENTITY_ID'],
                'USER_ID' => $USER->GetID()
            ],
            'select' => ['ID', 'VOTE']
        ]);
        if ($oResultGet->getSelectedRowsCount() > 0) {
            $arResultGet = $oResultGet->fetch();
            if ($arResultGet['VOTE'] == self::VoteDislike) {
                $oResult = YlabLikesTable::delete($arResultGet['ID']);
            } else {
                $oResult = YlabLikesTable::update($arResultGet['ID'], ['VOTE' => self::VoteDislike]);
            }
        } else {
            $oResult = YlabLikesTable::add([
                'CONTENT_ID' => $this->arParams['ELEMENT_ID'],
                'CONTENT_TYPE' => $this->arParams['ENTITY_ID'],
                'USER_ID' => $USER->GetID(),
                'VOTE' => self::VoteDislike
            ]);
        }

        return $oResult;

    }

    /**
     * @param $sSignedParameters
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\Security\Sign\BadSignatureException
     */
    public function getContentStatAction($sSignedParameters)
    {
        /** @var \CUser $USER */
        global $USER;

        $signer = new ParameterSigner;
        $this->arParams = $signer->unsignParameters($this->__name, $sSignedParameters);
        $this->arParams = $this->onPrepareComponentParams($this->arParams);

        $arContentStat = $this->getContentStat($this->arParams['ELEMENT_ID'], $this->arParams['ENTITY_ID'], $USER->GetID());

        return ['STAT' =>$arContentStat, 'CONTENT_ID' => $this->arParams['ELEMENT_ID'], 'CONTENT_TYPE' => $this->arParams['ENTITY_ID']];
    }

    /**
     * @param $elementId
     * @param $entityId
     * @param null $userId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getContentStat($elementId, $entityId, $userId = null)
    {
        $arRuntime = [
            new Entity\ExpressionField('COUNT_LIKE',
                'COUNT(IF(`ylab_likes_ylab_likes`.`VOTE`=' . self::VoteLike . ', `ylab_likes_ylab_likes`.`VOTE`, NULL))'),
            new Entity\ExpressionField('COUNT_DISLIKE',
                'COUNT(IF(`ylab_likes_ylab_likes`.`VOTE`=' . self::VoteDislike . ', `ylab_likes_ylab_likes`.`VOTE`, NULL))')
        ];
        $arSelect = ['CONTENT_ID', 'CONTENT_TYPE', 'COUNT_LIKE', 'COUNT_DISLIKE'];

        if ($userId) {
            $arRuntime[] = new Entity\ExpressionField('IS_VOTED', '(
                    SELECT `ylab_likes_user`.`VOTE` FROM `' . YlabLikesTable::getTableName() . '` AS ylab_likes_user WHERE 
                    `ylab_likes_user`.`CONTENT_ID`=`ylab_likes_ylab_likes`.`CONTENT_ID` AND
                    `ylab_likes_user`.`USER_ID`=' . new SqlExpression("?i", $userId) . '
                )');
            $arSelect[] = 'IS_VOTED';
        }

        $oResult = YlabLikesTable::getList([
            'runtime' => $arRuntime,
            'group' => ['CONTENT_ID', 'CONTENT_TYPE'],
            'filter' => [
                'CONTENT_ID' => $elementId,
                'CONTENT_TYPE' => $entityId,
            ],
            'select' => $arSelect
        ]);
        $arContentStat = $oResult->fetchAll();

        if ($userId) {
            foreach ($arContentStat as &$item) {
                if ($item['IS_VOTED'] == self::VoteLike) {
                    $item['IS_VOTED'] = 'LIKE';
                }
                if ($item['IS_VOTED'] == self::VoteDislike) {
                    $item['IS_VOTED'] = 'DISLIKE';
                }
            }
        }

        return $arContentStat[0];

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
            /*return;*/
        }


        $iUserId = $USER->GetID();
        $iElementId = $this->arParams['ELEMENT_ID'];
        $iEntityId = $this->arParams['ENTITY_ID'];

        if ($this->StartResultCache()) {
            $this->arResult['STAT'] = $this->getContentStat($iElementId, $iEntityId, $iUserId);
        }

        $this->includeComponentTemplate();
    }

    /**
     * Параметры компонента для сохранения и последующего вызова в ajax
     *
     * @return array|string[]|null
     */
    protected function listKeysSignedParameters()
    {
        return [
            'ELEMENT_ID',
            'ENTITY_ID',
            'CACHE_TYPE',
            'CACHE_TIME'
        ];
    }
}
