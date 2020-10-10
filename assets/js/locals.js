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
        entityCustomer: {
            en: "Customer",
            ar: "الزبون",
            fr: ""
        },
        entityDistributor: {
            en: "Distributor",
            ar: "الموزع",
            fr: ""
        },
        userCustomer: {
            en: "Reference",
            ar: "المرجع",
            fr: ""
        },
        userDistributor: {
            en: "Reference",
            ar: "المرجع",
            fr: ""
        },
        orderStatus: {
            en: "Order Status",
            ar: "حالة الطلب",
            fr: ""
        },
        orderStatus_Pending: {
            en: "Pending",
            ar: "قيد الانتظار",
            fr: ""
        },
        orderStatus_OnHold: {
            en: "On Hold",
            ar: "في الانتظار",
            fr: ""
        },
        orderStatus_Processing: {
            en: "Processing",
            ar: "معالجة",
            fr: ""
        },
        orderStatus_Completed: {
            en: "Completed",
            ar: "منجز",
            fr: ""
        },
        orderStatus_Canceled: {
            en: "Canceled",
            ar: "ألغيت",
            fr: ""
        },
        orderStatus_Received: {
            en: "Received",
            ar: "تم الاستلام",
            fr: ""
        },
        orderTotal: {
            en: "Total",
            ar: "مجموع",
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
        insertDate: {
            en: "Insert Date",
            ar: "تاريخ الإدخال",
            fr: ""
        },
        bonus: {
            en: "Bonus",
            ar: "البونص",
            fr: ""
        },
        productScintificName: {
            en: "Scientific Name",
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

    var _symbolsLocals = {
        svgProceed: "",
        svgCancel: '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/> <path d="M 10,10 l 90,90 M 100,10 l -90,90" fill="#000000" /></g></svg>',
    }

    var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';

    // Public Functions
    return {
        init: function () {

        },
        getMessage(key) {
            return _arrLocals[key][docLang];
        },
        getSymbol(key){
            return _symbolsLocals[key];
        }
    };
}();