$(function () {
    $.validator.setDefaults({
        submitHandler: function (form) {
            form.submit();
        }
    });
    // 字元驗證
    jQuery.validator.addMethod("stringCheck", function (value, element) {
        return this.optional(element) || /^[\u0391-\uFFE5\w] $/.test(value);
    }, "只能包括中文字、英文字母、數字和下劃線");
    // 中文字兩個位元組
    jQuery.validator.addMethod("byteRangeLength", function (value, element, param) {
        var length = value.length;
        for (var i = 0; i < value.length; i) {
            if (value.charCodeAt(i) > 127) {
                length;
            }
        }
        return this.optional(element) || (length >= param[0] && length <= param[1]);
    }, "請確保輸入的值在3-15個位元組之間(一箇中文字算2個位元組)");
    // 身份證號碼驗證
    jQuery.validator.addMethod("isIdCardNo", function (value, element) {
        return this.optional(element) || idCardNoUtil.checkIdCardNo(value);
    }, "請正確輸入您的身份證號碼");
    //護照編號驗證
    jQuery.validator.addMethod("passport", function (value, element) {
        return this.optional(element) || checknumber(value);
    }, "請正確輸入您的護照編號");
    // 手機號碼驗證
    jQuery.validator.addMethod("isMobile", function (value, element) {
        var length = value.length;
        var mobile = /^(((13[0-9]{1})|(15[0-9]{1})) \d{8})$/;
        return this.optional(element) || (length == 11 && mobile.test(value));
    }, "請正確填寫您的手機號碼");
    // 電話號碼驗證
    jQuery.validator.addMethod("isTel", function (value, element) {
        var tel = /^\d{3,4}-?\d{7,9}$/; //電話號碼格式010-12345678
        return this.optional(element) || (tel.test(value));
    }, "請正確填寫您的電話號碼");
    // 聯絡電話(手機/電話皆可)驗證
    jQuery.validator.addMethod("isPhone", function (value, element) {
        var length = value.length;
        var mobile = /^(((13[0-9]{1})|(15[0-9]{1})) \d{8})$/;
        var tel = /^\d{3,4}-?\d{7,9}$/;
        return this.optional(element) || (tel.test(value) || mobile.test(value));
    }, "請正確填寫您的聯絡電話");
    // 郵政編碼驗證
    jQuery.validator.addMethod("isZipCode", function (value, element) {
        var tel = /^[0-9]{6}$/;
        return this.optional(element) || (tel.test(value));
    }, "請正確填寫您的郵政編碼");
    //開始驗證
    $('#commentForm').validate({
        rules: {
            username: {
                required: true,
                stringCheck: true,
                byteRangeLength: [3, 15]
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                isMobile: true
            },
            address: {
                required: true,
                stringCheck: true,
                byteRangeLength: [3, 100]
            },
            card: {
                required: true,
                isIdCardNo: true
            },
            passport: {
                required: true,
                passport: true
            }
        },
        messages: {
            username: {
                required: "請填寫使用者名稱",
                stringCheck: "使用者名稱只能包括中文字、英文字母、數字和下劃線",
                byteRangeLength: "使用者名稱必須在3-15個字元之間(一箇中文字算2個字元)"
            },
            email: {
                required: "<font color=red>請輸入一個Email地址</fond>",
                email: "請輸入一個有效的Email地址"
            },
            phone: {
                required: "請輸入您的聯絡電話",
                isPhone: "請輸入一個有效的聯絡電話"
            },
            address: {
                required: "請輸入您的聯絡地址",
                stringCheck: "請正確輸入您的聯絡地址",
                byteRangeLength: "請詳實您的聯絡地址以便於我們聯絡您"
            },
            card: {
                required: "請輸入身份證號",
                isIdCardNo: "請輸入正確的身份證號"
            },
            passport: {
                required: "請輸入護照編號",
                passport: "請輸入正確的護照編號"
            }
        },
        focusInvalid: false,
        onkeyup: false,
        errorPlacement: function (error, element) {
            error.appendTo(element.parent());
        },
        errorElement: "em",
        error: function (label) { label.text(" ").addClass("error"); }
    });
})