define(
    [
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/totals'
    ],
    function (ko, Component, totals) {
        "use strict";


        return Component.extend({
            defaults: {
                template: 'Mageplaza_GiftCard/checkout/summary/customdiscount'
            },
            price: ko.observable(-10),
            isDisplayedCustomdiscount : function(){
                   if(totals.getSegment('customer_discount').value > 0){
                       return true;
                   }else{
                       return false
                   }
            },
            getCustomDiscount : function(){
                var discount = totals.getSegment('customer_discount').value;
                return '-' + this.getFormattedPrice(discount);
            }
            ,
            gettitleaaa : function(){
                var title = totals.getSegment('customer_discount').title;
                return title;
            }
        });
    }
);




// Use strict là từ khóa khai báo sử dụng chế độ Strict Mode,