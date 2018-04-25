BX.namespace("Ylab.Likes");

BX.Ylab.Likes = new function () {
    this.AjaxPath = {
        'setLike': '/bitrix/themes/ylab.likes/ajax/setLike.php',
        'setDislike': '/bitrix/themes/ylab.likes/ajax/setDislike.php',
        'getContentStat': '/bitrix/themes/ylab.likes/ajax/getContentStat.php',
    };

    this.setAjaxPath = function (AjaxPath) {
        this.AjaxPath = AjaxPath;
    };

    this.setLike = function (iContentId, iContentType, oCallback) {
        var callback = this.isFunction(oCallback) ? oCallback : function () {};
        BX.ajax.post(this.AjaxPath.setLike, {
            'sessid': BX.bitrix_sessid(),
            'iContentId': iContentId,
            'iContentType': iContentType
        }, callback);
    };

    this.setDislike = function (iContentId, iContentType, oCallback) {
        var callback = this.isFunction(oCallback) ? oCallback : function () {};
        BX.ajax.post(this.AjaxPath.setDislike, {
            'sessid': BX.bitrix_sessid(),
            'iContentId': iContentId,
            'iContentType': iContentType
        }, callback);
    };

    this.getContentStat = function (iContentId, iContentType, oCallback) {
        var callback = this.isFunction(oCallback) ? oCallback : function () {};
        BX.ajax.post(this.AjaxPath.getContentStat, {
            'sessid': BX.bitrix_sessid(),
            'iContentId': iContentId,
            'iContentType': iContentType
        }, callback);
    };

    this.isFunction = function (functionToCheck) {
        return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
    }

};