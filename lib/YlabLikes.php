<?php

namespace Ylab\Likes;

use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Entity;

/**
 * Class YlabLikesTable - ОРМ класс для таблицы лайков
 * @package Ylab\Likes
 */
class YlabLikesTable extends Entity\DataManager
{
    const VoteDislike = -1;
    const VoteLike = 1;

    /**
     * @inheritdoc
     */
    public static function getTableName()
    {
        return 'ylab_likes_votes';
    }

    /**
     * @inheritdoc
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Entity\IntegerField('CONTENT_ID', [
                'required' => true,
            ]),
            new Entity\IntegerField('CONTENT_TYPE', [
                'required' => true,
            ]),
            new Entity\IntegerField('USER_ID', [
                'required' => true,
            ]),
            new Entity\IntegerField('VOTE', [
                'required' => true,
            ]),
        ];
    }

    /**
     * Поставить лайк контенту
     *
     * @param $iContentId - ID Контента
     * @param $iContentType - Тип контента см. константы класса Ylab\Likes\YlabLikesTable
     * @param $iUserId - ID пользователя
     * @return Entity\AddResult|Entity\UpdateResult
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public static function setLike($iContentId, $iContentType, $iUserId)
    {
        $oResultGet = self::getList([
            'filter' => [
                'CONTENT_ID' => $iContentId,
                'CONTENT_TYPE' => $iContentType,
                'USER_ID' => $iUserId
            ],
            'select' => ['ID', 'VOTE']
        ]);
        if ($oResultGet->getSelectedRowsCount() > 0) {
            $arResultGet = $oResultGet->fetch();
            if ($arResultGet['VOTE'] == self::VoteLike) {
                $oResult = self::delete($arResultGet['ID']);
            } else {
                $oResult = self::update($arResultGet['ID'], ['VOTE' => self::VoteLike]);
            }
        } else {
            $oResult = self::add([
                'CONTENT_ID' => $iContentId,
                'CONTENT_TYPE' => $iContentType,
                'USER_ID' => $iUserId,
                'VOTE' => self::VoteLike
            ]);
        }

        return $oResult;
    }

    /**
     * Поставить дизлайк контенту
     *
     * @param $iContentId - ID Контента
     * @param $iContentType - Тип контента см. константы класса Ylab\Likes\YlabLikesTable
     * @param $iUserId - ID пользователя
     * @return Entity\AddResult|Entity\UpdateResult
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public static function setDislike($iContentId, $iContentType, $iUserId)
    {
        $oResultGet = self::getList([
            'filter' => [
                'CONTENT_ID' => $iContentId,
                'CONTENT_TYPE' => $iContentType,
                'USER_ID' => $iUserId
            ],
            'select' => ['ID', 'VOTE']
        ]);
        if ($oResultGet->getSelectedRowsCount() > 0) {
            $arResultGet = $oResultGet->fetch();
            if ($arResultGet['VOTE'] == self::VoteDislike) {
                $oResult = self::delete($arResultGet['ID']);
            } else {
                $oResult = self::update($arResultGet['ID'], ['VOTE' => self::VoteDislike]);
            }
        } else {
            $oResult = self::add([
                'CONTENT_ID' => $iContentId,
                'CONTENT_TYPE' => $iContentType,
                'USER_ID' => $iUserId,
                'VOTE' => self::VoteDislike
            ]);
        }

        return $oResult;
    }

    /**
     * Получить статистику голосов контента
     *
     * @param mixed $mContentId - ID Контента
     * @param $iContentType - Тип контента см. константы класса Ylab\Likes\YlabLikesTable
     * @param null $iUserLike - в результате будет добавлено поле IS_VOTED со значением голоса пользователя
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getContentStat($mContentId, $iContentType, $iUserLike = null)
    {
        $arRuntime = [
            new Entity\ExpressionField('COUNT_LIKE',
                'COUNT(IF(`ylab_likes_ylab_likes`.`VOTE`=' . self::VoteLike . ', `ylab_likes_ylab_likes`.`VOTE`, NULL))'),
            new Entity\ExpressionField('COUNT_DISLIKE',
                'COUNT(IF(`ylab_likes_ylab_likes`.`VOTE`=' . self::VoteDislike . ', `ylab_likes_ylab_likes`.`VOTE`, NULL))')
        ];
        $arSelect = ['CONTENT_ID', 'CONTENT_TYPE', 'COUNT_LIKE', 'COUNT_DISLIKE'];

        if ($iUserLike) {
            $arRuntime[] = new Entity\ExpressionField('IS_VOTED', '(
                    SELECT `ylab_likes_user`.`VOTE` FROM `' . self::getTableName() . '` AS ylab_likes_user WHERE 
                    `ylab_likes_user`.`CONTENT_ID`=`ylab_likes_ylab_likes`.`CONTENT_ID` AND
                    `ylab_likes_user`.`USER_ID`=' . new SqlExpression("?i", $iUserLike) . '
                )');
            $arSelect[] = 'IS_VOTED';
        }

        $oResult = self::getList([
            'runtime' => $arRuntime,
            'group' => ['CONTENT_ID', 'CONTENT_TYPE'],
            'filter' => [
                'CONTENT_ID' => $mContentId,
                'CONTENT_TYPE' => $iContentType,
            ],
            'select' => $arSelect
        ]);
        $arContentStat = $oResult->fetchAll();

        if ($iUserLike) {
            foreach ($arContentStat as &$item) {
                if ($item['IS_VOTED'] == self::VoteLike) {
                    $item['IS_VOTED'] = 'LIKE';
                }
                if ($item['IS_VOTED'] == self::VoteDislike) {
                    $item['IS_VOTED'] = 'DISLIKE';
                }
            }
        }

        return $arContentStat;
    }
}