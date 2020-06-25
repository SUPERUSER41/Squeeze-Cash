(function ($) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: squeezeCashAjax.ajaxUrl,
        data:{
            action:'auth_merchant',
            _ajax_nonce: squeezeCashAjax.nonce,
            merchant_id: squeezeCashAjax.merchantId,
            access_token: squeezeCashAjax.accessToken
        },
        success: function (response) {
            console.log(response)
            if ('success' === response.type){
                console.log(response.data)
            }else{
                //Something went wrong
            }
        },
        error:function (request, textStatus, errorThrown) {

        }
    });

})(jQuery);


