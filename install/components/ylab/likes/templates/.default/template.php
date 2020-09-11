<?php
CJSCore::Init(['jquery', 'ajax', 'json', 'session']);
?>

<script>
    $(document).ready(function () {
        BX.namespace('Ylab.Components.SignedParameters');
        BX.Ylab.Components.SignedParameters["<?=$this->getComponent()->getEditAreaId("")?>"] = "<?=$this->getComponent()->getSignedParameters()?>";
    });
</script>

<div class="votes_bar" data-element="<?= $arParams['ELEMENT_ID'] . "," . $arParams['ENTITY_ID'] ?>">
    <div class="vote_action">
        <sup class="counter js-like-counter"><?= (!empty($arResult['STAT']['COUNT_LIKE']) ? $arResult['STAT']['COUNT_LIKE'] : 0 ) ?></sup>
        <button class="like js-like-action <?= ($arResult['STAT']['IS_VOTED'] == 'LIKE' ? "is-active" : "")?>" data-eid="<?=$this->getComponent()->getEditAreaId("")?>">Like</button>
    </div>
    <div class="vote_action">
        <sup class="counter js-dislike-counter"><?= (!empty($arResult['STAT']['COUNT_DISLIKE']) ? $arResult['STAT']['COUNT_DISLIKE'] : 0 ) ?></sup>
        <button class="dislike js-dislike-action <?= ($arResult['STAT']['IS_VOTED'] == 'DISLIKE' ? "is-active" : "")?>" data-eid="<?=$this->getComponent()->getEditAreaId("")?>">Dislike</button>
    </div>
</div>
