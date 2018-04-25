function UpdateCounters(conf) {

    this.conf = conf;
    this.result = null;
    var $this = this;

    this.readData = function (data) {
        $this.result = BX.parseJSON(data, {});
        if (!$this.result.ERROR) {
            $this.update();
        }
    };

    this.update = function () {
        var iElementId = this.conf.ELEMENT_ID;
        var iEntityId = this.conf.ENTITY_ID;
        var elStatBar = document.querySelector('[data-element="' + iElementId + ',' + iEntityId + '"]');
        var elLikeCounter = elStatBar.querySelector('.js-like-counter');
        var elDislikeCounter = elStatBar.querySelector('.js-dislike-counter');
        var elLikeAction = elStatBar.querySelector('.js-like-action');
        var elDislikeAction = elStatBar.querySelector('.js-dislike-action');
        BX.Ylab.Likes.getContentStat(iElementId, iEntityId, function (data) {
            var result = BX.parseJSON(data, {});
            if (result.STAT.length == 0) {
                elLikeCounter.innerText = '0';
                elDislikeCounter.innerText = '0';
                elLikeAction.classList.remove('is-active');
                elDislikeAction.classList.remove('is-active');
                return;
            }
            elLikeCounter.innerText = result.STAT.COUNT_LIKE;
            elDislikeCounter.innerText = result.STAT.COUNT_DISLIKE;
            if (result.STAT.IS_VOTED == 'LIKE') {
                elLikeAction.classList.add('is-active');
                elDislikeAction.classList.remove('is-active');
            } else {
                elLikeAction.classList.remove('is-active');
                elDislikeAction.classList.add('is-active');
            }
        });
    }
}