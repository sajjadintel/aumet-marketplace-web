"use strict";

// Class Definition
var WebAppLocals = function () {

    var lang = 'ar';

    var _arrLocals = {
        success: {
            en: "Your request was executed successfuly",
            ar: "لقد تم تنفيذ طلبك بنجاح",
            fr: ""
        },
        success_confirmButtonText: {
            en: "Ok",
            ar: "موافق",
            fr: ""
        },
        loading: {
            en: "Please Wait...",
            ar: "الرجاء الانتظار...",
            fr: ""
        },
        productName: {
            en: "Trade Name",
            ar: "الاسم التجاري",
            fr: ""
        },
        unitPrice: {
            en: "Unit Price",
            ar: "سعر الوحدة",
            fr: ""
        },
        quantity: {
            en: "Quantity",
            ar: "الكمية المطلوبة",
            fr: ""
        },
        expiryDate: {
            en: "Expiry Date",
            ar: "تاريخ انتهاء الصلاحية",
            fr: ""
        },
        bonus: {
            en: "Bonus",
            ar: "البونص",
            fr: ""
        },
        productScintificName: {
            en: "Scintific Name",
            ar: "الاسم العلمي",
            fr: ""
        },
        sellingEntityName: {
            en: "Ditributor Name",
            ar: "اسم الموزع",
            fr: ""
        },
        stockAvailability: {
            en: "Stock Availability",
            ar: "توفر المنتج",
            fr: ""
        },
        stockAvailability_available: {
            en: "Available",
            ar: "متوفر",
            fr: ""
        },
        stockAvailability_notAvailable: {
            en: "Out of Stock",
            ar: "غير متوفر",
            fr: ""
        },
        stockAvailability_availableSoon: {
            en: "Available Soon",
            ar: "متوفر قريبا",
            fr: ""
        },
        stockUpdateDateTime: {
            en: "Stock Update Date Time",
            ar: "آخر تحديث لتوفر المنتج",
            fr: ""
        },
        error: {
            en: "Something went wrong while processing your request, Please contact support",
            ar: "لقد حصل خطأ أثناء محاولة تنفيذ طلبك، يرجى التواصل مع الدعم الفني",
            fr: ""
        },
        error_confirmButtonText: {
            en: "Close",
            ar: "إغلاق",
            fr: ""
        },
        error_emailNotEmpty: {
            en: "Please enter your email address",
            ar: "عنوان البريد الإلكتروني إلزامي",
            fr: ""
        },
        error_emailFormat: {
            en: "Email address is invalid",
            ar: "عنوان البريد الإلكتروني غير صالح",
            fr: ""
        },
        error_passwordNotEmpty: {
            en: "Please enter your password",
            ar: "يجب ادخال كلمة السر",
            fr: ""
        }
    };

    var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';

    // Public Functions
    return {
        init: function () {

        },
        getMessage(key) {
            return _arrLocals[key][docLang];
        }
    };
}();