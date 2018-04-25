<?php
CJSCore::Init(['YlabLikesForm']);
?>

<div class="votes_bar" data-element="<?= $arParams['ELEMENT_ID'] . "," . $arParams['ENTITY_ID'] ?>">
    <div class="vote_action">
        <sup class="counter js-like-counter"><?= (!empty($arResult['STAT']['COUNT_LIKE']) ? $arResult['STAT']['COUNT_LIKE'] : 0 ) ?></sup>
        <button class="like js-like-action <?= ($arResult['STAT']['IS_VOTED'] == 'LIKE' ? "is-active" : "")?>"
                onclick="BX.Ylab.Likes.setLike(<?= $arParams['ELEMENT_ID'] ?>,<?= $arParams['ENTITY_ID'] ?>, (new UpdateCounters(<?= CUtil::PhpToJSObject($arParams)?>)).readData)">Like</button>
    </div>
    <div class="vote_action">
        <sup class="counter js-dislike-counter"><?= (!empty($arResult['STAT']['COUNT_DISLIKE']) ? $arResult['STAT']['COUNT_DISLIKE'] : 0 ) ?></sup>
        <button class="dislike js-dislike-action <?= ($arResult['STAT']['IS_VOTED'] == 'DISLIKE' ? "is-active" : "")?>"
                onclick="BX.Ylab.Likes.setDislike(<?= $arParams['ELEMENT_ID'] ?>,<?= $arParams['ENTITY_ID'] ?>, (new UpdateCounters(<?= CUtil::PhpToJSObject($arParams)?>)).readData)">Dislike</button>
    </div>
</div>
