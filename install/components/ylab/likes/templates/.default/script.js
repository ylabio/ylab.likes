$(document).ready(function () {
    $('.vote_action').on('click', '.js-like-action', function (event) {
        event.preventDefault();
        let $sEid = $(this).data('eid');
        setVote($sEid, 'setLike');
    });

    $('.vote_action').on('click', '.js-dislike-action', function (event) {
        event.preventDefault();
        let $sEid = $(this).data('eid');
        setVote($sEid, 'setDislike');
    });

    function setVote($sEid, voteAction) {
        BX.ajax.runComponentAction('ylab:likes',
            voteAction, {
                mode: 'class',
                data: {
                    sSignedParameters: BX.Ylab.Components.SignedParameters[$sEid]
                },
            })
            .then(function (response) {
                if (response.status === 'success') {
                    if (response.data === false){
                        alert('Для голосования необходимо авторизироваться');
                    } else {
                        getContentStat(BX.Ylab.Components.SignedParameters[$sEid])
                    }
                }
            });
    }

    function getContentStat(eID) {
        BX.ajax.runComponentAction('ylab:likes',
            'getContentStat', {
                mode: 'class',
                data: {
                    sSignedParameters: eID
                },
            })
            .then(function (response) {

                if (response.status === 'success') {
                    var elStatBar = document.querySelector('[data-element="' + response.data.CONTENT_ID + ',' + response.data.CONTENT_TYPE + '"]');
                    var elLikeCounter = elStatBar.querySelector('.js-like-counter');
                    var elDislikeCounter = elStatBar.querySelector('.js-dislike-counter');
                    var elLikeAction = elStatBar.querySelector('.js-like-action');
                    var elDislikeAction = elStatBar.querySelector('.js-dislike-action');

                    if (response.data.STAT === null) {
                        elLikeCounter.innerText = '0';
                        elDislikeCounter.innerText = '0';
                        elLikeAction.classList.remove('is-active');
                        elDislikeAction.classList.remove('is-active');
                        return;
                    }

                    elLikeCounter.innerText = response.data.STAT.COUNT_LIKE;
                    elDislikeCounter.innerText = response.data.STAT.COUNT_DISLIKE;

                    if (response.data.STAT.IS_VOTED == 'LIKE') {
                        elLikeAction.classList.add('is-active');
                        elDislikeAction.classList.remove('is-active');
                    } else {
                        elLikeAction.classList.remove('is-active');
                        elDislikeAction.classList.add('is-active');
                    }
                }
            });
    }
});