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
}
