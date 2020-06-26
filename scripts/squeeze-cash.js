//
//
// (function ($) {
//     $.ajax({
//         type: 'POST',
//         dataType: 'json',
//         url: squeezeCashAjax.ajaxUrl,
//         data:{
//             merchant_id: squeezeCashAjax.merchantId,
//             access_token: squeezeCashAjax.accessToken
//         },
//         success: function (response) {
//             console.log(response)
//             if ('success' === response.type){
//                 console.log(response.data)
//             }else{
//                 //Something went wrong
//             }
//         },
//         error:function (request, textStatus, errorThrown) {
//
//         }
//     });
//     let userSqueeze;
//     let userPin;
//     const successCallback = () =>{
//         let wcCheckoutForm = $('form.woocommerce-checkout');
//
//         // let sqCheckoutForm = $('form.squeeze-checkout-form');
//         //
//         // userSqueeze = sqCheckoutForm.find('#squeeze-user-id').val();
//         // console.log(userSqueeze);
//         //
//         // userPin = sqCheckoutForm.find('#squeeze-user-pin').val();
//         // console.log(userSqueeze);
//
//         wcCheckoutForm.off('checkout_place_order', makePayment);
//
//         wcCheckoutForm.submit();
//     }
//
//     const makePayment = () => {
//         fetch("https://us-central1-squeeze-a69e9.cloudfunctions.net/makePayment", {
//             method: "POST",
//             mode: 'no-cors',
//             body: JSON.stringify({
//                 userSqueeze: "SQBBTST",
//                 merchantSqueeze: "SQ00006",
//                 userPin: "1234",
//                 accessToken: "gvo2tr216cqfm",
//                 amount: "1",
//                 reason: "",
//             }),
//         })
//             .then((res) => res.json())
//             .then((parsedRes) => {
//                 console.log(parsedRes);
//                 successCallback();
//             })
//             .catch((e) => {
//                 console.log(e);
//             });
//         return false;
//     };
//     let checkoutForm = $("form.woocommerce-checkout");
//     checkoutForm.on("checkout_place_order", makePayment);
//
// })(jQuery);


(function ($) {

    //
    // const successCallback = () => {
    //     let checkoutForm = $("form.woocommerce-checkout");
    //     checkoutForm.off("checkout_place_order", makePayment);
    //     checkoutForm.submit();
    // };
    //
    //
    // const makePayment = () => {
    //     fetch("https://us-central1-squeeze-a69e9.cloudfunctions.net/makePayment", {
    //         method: "POST",
    //         mode: "no-cors",
    //         body: JSON.stringify({
    //             userSqueeze: "SQBBTST",
    //             merchantSqueeze: "SQ00006",
    //             userPin: "1234",
    //             accessToken: "gvo2tr216cqfm",
    //             amount: "100",
    //             reason: "",
    //         }),
    //     })
    //         .then((res) => res.json())
    //         .then((parsedRes) => {
    //             console.log(parsedRes);
    //             successCallback();
    //         })
    //         .catch((e) => {
    //             console.log(e);
    //         });
    //     return false;
    // };
    // let checkoutForm = $("form.woocommerce-checkout");
    // checkoutForm.on("checkout_place_order", makePayment);
})(jQuery);