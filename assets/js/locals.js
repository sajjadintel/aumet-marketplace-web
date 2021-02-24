'use strict';

// Class Definition
var WebAppLocals = (function () {
	var lang = 'ar';

	var _arrLocals = {
		appName: {
			en: 'Aumet Marketplace',
			ar: 'Aumet Marketplace', // TODO: add translation
			fr: 'Aumet Marketplace', // TODO: add translation
		},
		success: {
			en: 'Your request was executed successfully',
			ar: 'لقد تم تنفيذ طلبك بنجاح',
			fr: '',
		},
		success_confirmButtonText: {
			en: 'Ok',
			ar: 'موافق',
			fr: '',
		},
		loading: {
			en: 'Please Wait...',
			ar: 'الرجاء الانتظار...',
			fr: '',
		},
		view: {
			en: 'View',
			ar: 'شاهد',
			fr: '',
		},
		feedback: {
			en: 'Feedback',
			ar: '', // TODO: add translation
			fr: '',
		},
		edit: {
			en: 'Edit',
			ar: 'تعديل',
			fr: '',
		},
		editQuantity: {
			en: 'Edit Stock',
			ar: 'تعديل الكمية',
			fr: '',
		},
		add: {
			en: 'Add',
			ar: 'أضف',
			fr: '',
		},
		delete: {
			en: 'Delete',
			ar: 'حذف',
			fr: '',
		},
		print: {
			en: 'Download PDF',
			ar: 'طباعة', //TODO: Translation
			fr: '',
		},
		options: {
			en: 'Options',
			ar: 'خيارات',
			fr: '',
		},
		viewOrder: {
			en: 'View Order',
			ar: 'عرض الطلب',
			fr: '',
		},
		viewOrders: {
			en: 'View Orders',
			ar: 'عرض الطلبات',
			fr: '',
		},
		actions: {
			en: 'Actions',
			ar: 'خيارات',
			fr: '',
		},
		name: {
			en: 'Name',
			ar: 'الاسم',
			fr: '',
		},
		product: {
			en: 'Product',
			ar: 'المنتج',
			fr: '',
		},
		productName: {
			en: 'Brand Name',
			ar: 'الاسم التجاري',
			fr: '',
		},
		id: {
			en: 'ID',
			ar: 'ID', // TODO: add translation
			fr: '',
		},
		productCode: {
			en: 'Product Code',
			ar: 'رمز المنتج',
			fr: '',
		},
		entityBuyer: {
			en: 'Customer',
			ar: 'الزبون',
			fr: '',
		},
		entitySeller: {
			en: 'Distributor',
			ar: 'الموزع',
			fr: '',
		},
		branchSeller: {
			en: 'Branch',
			ar: '', // TODO: add translation
			fr: '',
		},
		userBuyer: {
			en: 'Reference',
			ar: 'المرجع',
			fr: '',
		},
		userSeller: {
			en: 'Reference',
			ar: 'المرجع',
			fr: '',
		},
		order: {
			en: 'Order',
			ar: 'طلب',
			fr: '',
		},
		orderId: {
			en: 'Order ID',
			ar: '', // TODO: add translation
			fr: '',
		},
		productId: {
			en: 'Product ID',
			ar: '', // TODO: add translation
			fr: '',
		},
		orderCount: {
			en: 'Order Count',
			ar: 'عدد الطلبات',
			fr: '',
		},
		orderDetails: {
			en: 'Order Details',
			ar: 'تفاصيل الطلب',
			fr: '',
		},
		orderMissingProduct: {
			en: 'Missing Product',
			ar: '', // TODO: add translation
			fr: '',
		},
		orderReportMissing: {
			en: 'Report Missing',
			ar: '', // TODO: add translation
			fr: '',
		},
		orderFeedback: {
			en: 'Order Feedback',
			ar: '', // TODO: add translation
			fr: '',
		},
		missingProducts_deleteConfirmation: {
			en: 'Are you sure you want to delete this element?',
			ar: 'Are you sure you want to delete this element?', // TODO: add translation
			fr: '',
		},
		missingProducts_filterByProduct: {
			en: 'Filter by Product',
			ar: 'Filter by Product', // TODO: add translation
			fr: '',
		},
		orderLogs: {
			en: 'Order Logs',
			ar: 'سجلات الطلبات',
			fr: '',
		},
		orderStatus: {
			en: 'Order Status',
			ar: 'حالة الطلب',
			fr: '',
		},
		status: {
			en: 'Status',
			ar: 'حالة',
			fr: '',
		},
		orderStatusMove: {
			en: 'Set ',
			ar: '',
			fr: '',
		},
		orderStatus_New: {
			en: 'New',
			ar: 'جديد',
			fr: '',
		},
		orderStatus_OnHold: {
			en: 'On Hold',
			ar: 'في الانتظار',
			fr: '',
		},
		orderStatus_Processing: {
			en: 'Processing',
			ar: 'معالجة',
			fr: '',
		},
		orderStatus_Completed: {
			en: 'Completed',
			ar: 'منجز',
			fr: '',
		},
		order_modifyQuantity: {
			en: 'Modify Quantity',
			ar: 'Modify Quantity', // TODO: Translate
			fr: '',
		},
		orderStatus_Canceled: {
			en: 'Canceled',
			ar: 'ألغيت',
			fr: '',
		},
		orderStatus_Canceled_Pharmacy: {
			en: 'Canceled Pharmacy',
			ar: '', // TODO: Translate
			fr: '',
		},
		orderStatus_Received: {
			en: 'Received',
			ar: 'تم الاستلام',
			fr: '',
		},
		orderStatus_Paid: {
			en: 'Paid',
			ar: 'مدفوع',
			fr: '',
		},
		orderStatus_MissingProducts: {
			en: 'Missing Products',
			ar: '', // TODO: add translation
			fr: '',
		},
		orderStatus_PayOrder: {
			en: 'Pay Order',
			ar: '',
			fr: '',
		},
		orderStatus_Pending: {
			en: 'Pending',
			ar: 'معلق',
			fr: '',
		},
		orderSubtotal: {
			en: 'Subtotal',
			ar: 'Subtotal',
			fr: '',
		},
		orderTotal: {
			en: 'Total',
			ar: 'مجموع',
			fr: '',
		},
		orderShippedQuantity: {
			en: 'Shipped Quantity',
			ar: 'Shipped Quantity',
			fr: '',
		},
		orderOrderedQuantity: {
			en: 'Ordered Quantity',
			ar: 'Ordered Quantity',
			fr: '',
		},
		branch: {
			en: 'Branch',
			ar: 'فرع',
			fr: '',
		},
		address: {
			en: 'Address',
			ar: 'عنوان',
			fr: '',
		},
		price: {
			en: 'Price',
			ar: 'السعر',
			fr: '',
		},
		review: {
			en: 'Review',
			ar: 'Review', // TODO: Add translation
			fr: '',
		},
		ignore: {
			en: 'Ignore',
			ar: 'Ignore', // TODO: Add translation
			fr: '',
		},
		newOrderMessage: {
			en: 'You have received ( {0} ) orders',
			ar: 'You have received ( {0} ) orders', // TODO: Add translation
			fr: '',
		},
		unitPrice: {
			en: 'Unit Price',
			ar: 'سعر الوحدة',
			fr: '',
		},
		unit: {
			en: 'Unit',
			ar: 'الوحدة',
			fr: '',
		},
		tax: {
			en: 'VAT',
			ar: 'ضريبة',
			fr: '',
		},
		orderTotalWithVAT: {
			en: 'Total (+ VAT)',
			ar: 'مجموع',
			fr: '',
		},
		quantity: {
			en: 'Quantity',
			ar: 'الكمية المطلوبة',
			fr: '',
		},
		quantityFree: {
			en: 'Quantity Free',
			ar: 'Quantity Free', // TODO: Add translation
			fr: '',
		},
		note: {
			en: 'Note',
			ar: 'Note', // TODO: Add translation
			fr: '',
		},
		minOrder: {
			en: 'Minimum Order',
			ar: 'أقل كمية',
			fr: '',
		},
		date: {
			en: 'Date',
			ar: 'تاريخ',
			fr: '',
		},
		expiryDate: {
			en: 'Expiry Date',
			ar: 'تاريخ انتهاء الصلاحية',
			fr: '',
		},
		insertDate: {
			en: 'Insert Date',
			ar: 'تاريخ الإدخال',
			fr: '',
		},
		orderDate: {
			en: 'Ordered at',
			ar: 'Ordered at', // TODO: add translation
			fr: '',
		},
		productsOrdered: {
			en: 'Products',
			ar: 'Products', // TODO: add translation
			fr: '',
		},
		orderQuantity: {
			en: 'Quantity',
			ar: 'Quantity', // TODO: add translation
			fr: '',
		},
		bonus: {
			en: 'Bonus',
			ar: 'البونص',
			fr: '',
		},
		madeInCountry: {
			en: 'Made In',
			ar: 'صنع في',
			fr: '',
		},
		quantityOrdered: {
			en: 'Quantity Ordered',
			ar: 'الكمية المطلوبة',
			fr: '',
		},
		productScientificName: {
			en: 'Scientific Name',
			ar: 'الاسم العلمي',
			fr: '',
		},
		sellingEntityName: {
			en: 'Distributor Name',
			ar: 'اسم الموزع',
			fr: '',
		},
		quantityAvailable: {
			en: 'Available Quality',
			ar: 'الكمية المتوفرة',
			fr: '',
		},
		availability: {
			en: 'Availability',
			ar: 'التوفر',
			fr: '',
		},
		stockQuantity: {
			en: 'Stock Quantity',
			ar: 'كمية المخزون',
			fr: '',
		},
		stockAvailability: {
			en: 'Stock Availability',
			ar: 'توفر المنتج',
			fr: '',
		},
		stockAvailability_available: {
			en: 'Available',
			ar: 'متوفر',
			fr: '',
		},
		stockAvailability_notAvailable: {
			en: 'Out of Stock',
			ar: 'غير متوفر',
			fr: '',
		},
		stockAvailability_availableSoon: {
			en: 'Available Soon',
			ar: 'متوفر قريبا',
			fr: '',
		},
		stockUpdateDateTime: {
			en: 'Stock Last Updated',
			ar: 'آخر تحديث لتوفر المنتج',
			fr: '',
		},
		customerStatus: {
			en: 'Status',
			ar: 'الحالة',
			fr: '',
		},
		relationAvailable: {
			en: 'Active',
			ar: 'فعال',
			fr: '',
		},
		relationBlacklisted: {
			en: 'Blocked',
			ar: 'محظور',
			fr: '',
		},
		userFullname: {
			en: 'User Full Name',
			ar: 'إسم المستخدم',
			fr: '',
		},
		orderRating: {
			en: 'Rating',
			ar: 'التصنيف',
			fr: '',
		},
		missingProduct: {
			en: 'Missing Product',
			ar: '', // TODO: add translation
			fr: '',
		},
		orderComment: {
			en: 'Feedback',
			ar: '', // TODO: add translation
			fr: '',
		},
		stockUpdateProcessing: {
			en: 'Stock file under processing, Please wait...',
			ar: 'جاري العمل على تحديث ملف الأصناف, الرجاء الانتظار',
			fr: '',
		},
		orderTotalPaid: {
			en: 'Total Paid',
			ar: 'مجموع المدفوع',
			fr: '',
		},
		orderTotalUnPaid: {
			en: 'Total Un-Paid',
			ar: 'مجموع الغير مدفوع',
			fr: '',
		},
		error: {
			en: 'Something went wrong while processing your request, Please contact support',
			ar: 'لقد حصل خطأ أثناء محاولة تنفيذ طلبك، يرجى التواصل مع الدعم الفني',
			fr: '',
		},
		error_confirmButtonText: {
			en: 'Close',
			ar: 'إغلاق',
			fr: '',
		},
		error_emailNotEmpty: {
			en: 'Please enter your email address',
			ar: 'عنوان البريد الإلكتروني إلزامي',
			fr: '',
		},
		error_emailFormat: {
			en: 'Email address is invalid',
			ar: 'عنوان البريد الإلكتروني غير صالح',
			fr: '',
		},
		error_passwordNotEmpty: {
			en: 'Please enter your password',
			ar: 'يجب ادخال كلمة السر',
			fr: '',
		},
		addBonusTitle: {
			en: 'Add Bonus',
			ar: 'إضافة مكافأة',
			fr: '',
		},
		done: {
			en: 'Done',
			ar: 'منجز',
			fr: '',
		},
		addImageTitle: {
			en: 'Add Images',
			ar: 'إضافة الصور',
			fr: '',
		},
		maximumOrderQuantity: {
			en: 'Maximum Order Quantity',
			ar: 'كمية الطلب القصوى',
			fr: '',
		},
		city: {
			en: 'City',
			ar: 'مدينة',
			fr: '',
		},
		subcategory: {
			en: 'Subcategory',
			ar: 'تصنيف فرعي',
			fr: '',
		},
		required: {
			en: 'Required',
			ar: 'مطلوب',
			fr: '',
		},
		invalid: {
			en: 'Invalid',
			ar: 'غير صالحة',
			fr: '',
		},
		wrongPasswordConfirmation: {
			en: "Password Confirmation doesn't match",
			ar: 'تأكيد كلمة المرور غير مطابق',
			fr: '',
		},
		validationError: {
			en: 'Sorry, looks like there are some errors detected, please try again.',
			ar: 'معذرة ، يبدو أنه تم اكتشاف بعض الأخطاء ، يرجى المحاولة مرة أخرى.',
			fr: '',
		},
		validationErrorOk: {
			en: 'Ok, got it!',
			ar: 'حسنا، حصلت عليه!',
			fr: '',
		},
		relationGroup: {
			en: 'Customer Group',
			ar: 'مجموعة العملاء',
			fr: '',
		},
		changeRelationGroup: {
			en: 'Change Customer Group',
			ar: 'تغيير مجموعة العملاء',
			fr: '',
		},
		minimumValueOrderCityError: {
			en: 'The following cities are selected more than once',
			ar: 'تم تحديد المدن التالية أكثر من مرة',
			fr: '',
		},
		lengthError: {
			en: 'Should be between',
			ar: 'يجب أن يكون بين',
			fr: '',
		},
		and: {
			en: 'and',
			ar: 'و',
			fr: '',
		},
		characters: {
			en: 'characters',
			ar: 'الأحرف',
			fr: '',
		},
		fileTypeError: {
			en: "You can't upload files of this type",
			ar: 'لا يمكنك تحميل ملفات من هذا النوع',
			fr: '',
		},
		uploadSuccess: {
			en: 'Upload Completed Successfully',
			ar: 'اكتمل التحميل بنجاح',
			fr: '',
		},
		uploadError: {
			en: 'Upload failed',
			ar: 'التحميل فشل',
			fr: '',
		},
		bulkAddUploadError: {
			en: 'Error processing the file. Please ensure its format is .xlsm, .xlsx and matches the structure of the sample file',
			ar: 'Error processing the file. Please ensure its format is .xlsm, .xlsx and matches the structure of the sample file', // TODO: translate
			fr: '',
		},
		bonusUploadError: {
			en: 'Error processing the file. Please ensure its format is .xlsx and matches the structure of the sample file',
			ar: 'Error processing the file. Please ensure its format is .xlsx and matches the structure of the sample file', // TODO: translate
			fr: '',
		},
		goToProfile: {
			en: 'Go to Profile',
			ar: 'Go to Profile', // TODO: translate
			fr: '',
		},
		distributorNameRequired: {
			en: 'Enter Distributor Name',
			ar: 'أدخل اسم الموزع',
			fr: '',
		},
		distributorNameTooShort: {
			en: 'Sorry, the Distributor Name entered is too short',
			ar: 'عذرًا ، اسم الموزع الذي تم إدخاله قصير جدًا',
			fr: '',
		},
		pharmacyNameRequired: {
			en: 'Enter Pharmacy Name',
			ar: 'أدخل اسم الصيدلية',
			fr: '',
		},
		pharmacyNameTooShort: {
			en: 'Sorry, the Pharmacy Name entered is too short',
			ar: 'عذرًا ، اسم الصيدلية الذي تم إدخاله قصير جدًا',
			fr: '',
		},
		tradeLicenseNumberTooShort: {
			en: 'Sorry, the Trade License Number entered is too short',
			ar: 'عذرًا ، رقم الترخيص التجاري الذي تم إدخاله قصير جدًا',
			fr: '',
		},
		addressRequired: {
			en: 'Enter Address',
			ar: 'أدخل العنوان',
			fr: '',
		},
		addressTooShort: {
			en: 'Sorry, the Address entered is too short',
			ar: 'عذرا ، العنوان الذي تم إدخاله قصير جدا',
			fr: '',
		},
		minimumValueOrderRequired: {
			en: 'Enter Minimum Value Order',
			ar: 'أدخل أمر الحد الأدنى للقيمة',
			fr: '',
		},
		cityRequired: {
			en: 'Select City from dropdown menu',
			ar: 'حدد المدينة من القائمة المنسدلة',
			fr: '',
		},
		oldPasswordRequired: {
			en: 'Enter Current Password',
			ar: 'إدخل كلمة السر الحالية',
			fr: '',
		},
		newPasswordRequired: {
			en: 'Enter New Password',
			ar: 'أدخل كلمة مرور جديدة',
			fr: '',
		},
		supportEmailRequired: {
			en: 'Enter Email',
			ar: 'أدخل البريد الإلكتروني',
			fr: '',
		},
		supportEmailInvalid: {
			en: 'Invalid Email',
			ar: 'بريد إلكتروني خاطئ',
			fr: '',
		},
		supportPhoneRequired: {
			en: 'Enter Telephone no',
			ar: 'أدخل رقم الهاتف',
			fr: '',
		},
		supportReasonRequired: {
			en: 'Select Reason from dropdown menu',
			ar: 'حدد السبب من القائمة المنسدلة',
			fr: '',
		},
		subimagesExceeded: {
			en: 'Max 6 images are allowed',
			ar: 'Max 6 images are allowed', // TODO: translate
			fr: '',
		},
		subimagesMaximumSize: {
			en: 'The image maximum size is 2MB',
			ar: 'The image maximum size is 2MB', // TODO: translate
			fr: '',
		},
		subimagesWrongFormat: {
			en: 'Allowed types are jpeg, jpg, png',
			ar: 'Allowed types are jpeg, jpg, png', // TODO: translate
			fr: '',
		},
		cartError: {
			en: 'Payment methods missing',
			ar: 'طرق الدفع مفقودة',
			fr: ''
		},
		groupMembers: {
			en: 'Group Members',
			ar: 'عدد أعضاء المجموعة',
			fr: ''
		},
		revenue: {
			en: 'Revenue',
			ar: 'ربح',
			fr: ''
		},
		totalOrders: {
			en: 'Total Orders',
			ar: 'إجمالي الطلبات',
			fr: ''
		},
		recentOrdersWeekly: {
			en: 'Recent Orders (Week)',
			ar: 'الطلبات الأخيرة (أسبوع)',
			fr: ''
		},
		recentOrdersMonthly: {
			en: 'Recent Orders (Month)',
			ar: 'الطلبات الأخيرة (شهر)',
			fr: ''
		},
		addToGroup: {
			en: 'Add to group',
			ar: 'إضافة إلى المجموعة',
			fr: ''
		},
		cancel: {
			en: 'Cancel',
			ar: 'إلغاء',
			fr: ''
		},
		remove: {
			en: 'Remove',
			ar: 'يزيل',
			fr: ''
		},
		assign: {
			en: 'Assign',
			ar: 'تعيين',
			fr: ''
		},
		assignMemberConfirmationFirstPart: {
			en: 'Are you sure you want to assign',
			ar: 'هل أنت متأكد أنك تريد التخصيص',
			fr: ''
		},
		assignMemberConfirmationSecondPart: {
			en: 'to this group?',
			ar: 'لهذه المجموعة؟',
			fr: ''
		},
		removeMemberConfirmationFirstPart: {
			en: 'Are you sure you want to remove',
			ar: 'هل أنت متأكد من أنك تريد إزالة',
			fr: ''
		},
		removeMemberConfirmationSecondPart: {
			en: 'from the group?',
			ar: 'من المجموعة؟',
			fr: ''
		},
		paymentOption: {
			en: 'Payment Option',
			ar: 'خيار الدفع',
			fr: ''	
		},
		customerName: {
			en: 'Customer Name',
			ar: 'اسم الزبون',
			fr: ''	
		},
		ordersCount: {
			en: 'Orders Count',
			ar: 'عدد الطلبات',
			fr: ''	
		},
		ordersCompleted: {
			en: 'Orders Completed',
			ar: 'الطلبات المنجزة',
			fr: ''	
		},
		country: {
			en: 'Country',
			ar: 'دولة',
			fr: ''	
		},
		city: {
			en: 'City',
			ar: 'مدينة',
			fr: ''	
		},
		addToGroup: {
			en: 'Add to group',
			ar: 'إضافة إلى المجموعة',
			fr: ''
		},
		activeIngredients: {
			en: 'Active Ingredients',
			ar: 'مكونات نشطة',
			fr: ''
		},
		pleaseWait: {
			en: 'Please wait',
			ar: 'انتظر من فضلك',
			fr: ''
		},
	};

	var _symbolsLocals = {
		svgProceed: '',
		svgCancel:
			'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/> <path d="M 10,10 l 90,90 M 100,10 l -90,90" fill="#000000" /></g></svg>',
	};

	var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';

	// Public Functions
	return {
		init: function () {
			// add support for string format
			if (!String.prototype.format) {
				String.prototype.format = function () {
					var args = arguments;
					return this.replace(/{(\d+)}/g, function (match, number) {
						return typeof args[number] != 'undefined'
							? args[number]
							: match
							;
					});
				};
			}
		},
		getMessage(key) {
			return _arrLocals[key][docLang];
		},
		getSymbol(key) {
			return _symbolsLocals[key];
		},
	};
})();
