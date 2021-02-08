'use strict';
// Class definition

var SearchDataTable = (function () {
	// Private functions

	var datatable;
	var _readParams;

	var _init = function (objQuery) {
		_readParams = objQuery;
		datatable = $('#kt_datatable').KTDatatable({
			// datasource definition

			data: {
				type: 'remote',
				source: {
					read: {
						url: '/web/product/search',
						params: _readParams,
					},
				},
				serverPaging: true,
				serverFiltering: true,
				serverSorting: true,
			},

			// layout definition
			layout: {
				scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
				footer: false, // display/hide footer
			},

			// column sorting
			sortable: true,

			pagination: true,

			// Order settings
			order: [[2, 'asc']],

			// columns definition
			columns: [
				{
					field: 'id',
					title: '#',
					sortable: 'asc',
					width: 40,
					type: 'number',
					selector: false,
					textAlign: 'left',
					autoHide: false,
				},
				{
					field: 'productName_en', // + docLang,
					title: WebAppLocals.getMessage('productName'),
					autoHide: false,
				},
				{
					field: 'image',
					title: '',
					autoHide: true,
					sortable: false,
					template: function (row) {
						return (
							'<div class="symbol symbol-60 flex-shrink-0 mr-4 bg-light"> <div class="symbol-label" style="background-image: url(\'' +
							row.image +
							'\')" ></div></div>'
						);
					},
				},
				{
					field: 'scientificName',
					title: WebAppLocals.getMessage('productScientificName'),
					autoHide: true,
				},
				{
					field: 'entityName_ar', // + docLang,
					title: WebAppLocals.getMessage('sellingEntityName'),
					autoHide: false,
				},
				{
					field: 'expiryDate',
					title: WebAppLocals.getMessage('expiryDate'),
					autoHide: true,
					template: function (row) {
						if (row.expiryDate) {
							return (
								'<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.expiryDate).format('DD / MM / YYYY') + '</span>'
							);
						} else {
							return '';
						}
					},
				},
				{
					field: 'stockStatusId',
					sortable: false,
					title: WebAppLocals.getMessage('stockAvailability'),
					autoHide: true,
					// callback function support for column rendering
					template: function (row) {
						var status = {
							1: {
								title: WebAppLocals.getMessage('stockAvailability_available'),
								class: ' label-primary',
							},
							2: {
								title: WebAppLocals.getMessage('stockAvailability_notAvailable'),
								class: ' label-danger',
							},
							3: {
								title: WebAppLocals.getMessage('stockAvailability_availableSoon'),
								class: ' label-warning',
							},
						};

						var output = '';

						output +=
							'<div><span class="label label-lg font-weight-bold ' +
							status[row.stockStatusId].class +
							' label-inline">' +
							status[row.stockStatusId].title +
							'</span></div>';
						// output += '<div class="text-muted">' + (row.stockUpdateDateTime != null ? jQuery.timeago(row.stockUpdateDateTime) : 'NA') + '</div>';

						return output;
					},
				},
				{
					field: 'stockUpdateDateTime',
					title: WebAppLocals.getMessage('stockUpdateDateTime'),
					autoHide: false,
					template: function (row) {
						if (row.stockUpdateDateTime) {
							return '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment.utc(row.stockUpdateDateTime).fromNow() + '</span>';
						} else {
							return '';
						}
					},
				},
				{
					field: 'unitPrice', // + docLang,
					title: WebAppLocals.getMessage('unitPrice'),
					autoHide: false,
					template: function (row) {
						return  WebApp.formatMoney(row.unitPrice)  + ' ' + row.currency;
					},
				},
				{
					field: 'bonusOptions', // + docLang,
					title: WebAppLocals.getMessage('bonus'),
					autoHide: false,
					sortable: false,
					template: function (row) {
						if (row.stockStatusId == 1) {
							var tdText = '';
							if(row.bonusOptions) {
								row.bonusOptions.sort((a, b) => parseInt(a.minOrder) - parseInt(b.minOrder));
								row.bonusOptions.forEach((element) => {
									tdText +=
										'<a href="javascript:;" onclick=\'SearchDataTable.onBonusOptionCallback(' +
										JSON.stringify(row) +
										', ' +
										JSON.stringify(element) +
										' )\'><span id="bonusOption-' +
										row.id +
										'-' +
										element.id +
										'" class="label label-xl label-light label-square label-inline mr-2 bonus-option-label-' +
										row.id +
										'">' +
										element.name +
										' </span></a>';
								});
							}
							//var bonus = math.evaluate('floor(quantity / 6) * 2', row);
							//return '<span id="bonus-' + row.id + '" class="label label-xl label-rounded label-primary" style="width: 50px">' + bonus + ' </span>';
							return tdText;
						} else {
							return '';
						}
					},
				},
				{
					field: 'quantity',
					title: WebAppLocals.getMessage('quantity'),
					autoHide: false,
					sortable: false,
					template: function (row) {
						var vQuantity = '';
						var vBonus = '';
						if (row.stockStatusId == 1) {
							vQuantity =
								'<input id="quantity-' +
								row.id +
								'" type="text" style="width: 70px; direction: ltr" value="' +
								row.quantity +
								'" oninput=\'SearchDataTable.changeProductQuantityCallback(' +
								JSON.stringify(row) +
								" )' >";
							if (row.bonusTypeId == 2) {
								vBonus = '<span id="bonus-' + row.id + '" class="label label-xl label-rounded label-primary ml-1" style = "width: 50px" > </span>';
							}

							return '<div>' + vQuantity + vBonus + '</div>';
						} else {
							return '';
						}
					},
				},
				{
					field: 'Actions',
					title: '',
					sortable: false,
					width: 130,
					overflow: 'visible',
					autoHide: false,
					template: function (row) {
						var btnAddMoreToCart =
							'<a href="javascript:;" onclick=\'SearchDataTable.onClickAddMoreToCart(' +
							JSON.stringify(row) +
							' )\' class="btn btn-sm btn-primary btn-text-primary btn-hover-primary  mr-2" title="Add to cart">\
                    <span class="svg-icon svg-icon-md">\
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                        <rect x="0" y="0" width="24" height="24"/>\
                        <path d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z" fill="#000000" opacity="0.3"/>\
                        <path d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z" fill="#000000"/>\
                    </g></svg></span>\
                    <span class="label label-danger ml-2">' +
							row.cart +
							'</span></a>';

						var btnAddToCart =
							'<a href="javascript:;" onclick=\'SearchDataTable.onClickAddToCart(' +
							JSON.stringify(row) +
							' )\' class="btn btn-sm btn-default btn-text-primary btn-hover-primary  mr-2" title="Add to cart">\
                    <span class="svg-icon svg-icon-md">\
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                        <rect x="0" y="0" width="24" height="24"/>\
                        <path d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z" fill="#000000" opacity="0.3"/>\
                        <path d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z" fill="#000000"/>\
                    </g></svg></span></a>';

						var btnNotifyMe =
							'<a href="javascript:;" class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2" title="Add to cart">\
                    <span class="svg-icon svg-icon-md">\
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                        <rect x="0" y="0" width="24" height="24"/>\
                        <path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"/>\
                        <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5"/>\
                        </g></svg></span></a>';

						var btnViewProduct =
							'<a href="javascript:;" onclick="WebApp.loadSubPage(\'/web/entity/' +
							row.entityId +
							'/product/' +
							row.id +
							'\')" class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2" title="View">\
                    <span class="svg-icon svg-icon-md">\
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                        <rect x="0" y="0" width="24" height="24"/>\
                        <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>\
                        <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"/>\
                        <path d="M10.5,10.5 L10.5,9.5 C10.5,9.22385763 10.7238576,9 11,9 C11.2761424,9 11.5,9.22385763 11.5,9.5 L11.5,10.5 L12.5,10.5 C12.7761424,10.5 13,10.7238576 13,11 C13,11.2761424 12.7761424,11.5 12.5,11.5 L11.5,11.5 L11.5,12.5 C11.5,12.7761424 11.2761424,13 11,13 C10.7238576,13 10.5,12.7761424 10.5,12.5 L10.5,11.5 L9.5,11.5 C9.22385763,11.5 9,11.2761424 9,11 C9,10.7238576 9.22385763,10.5 9.5,10.5 L10.5,10.5 Z" fill="#000000" opacity="0.3"/>\
                        </g></svg></span></a>';

						var outActions = '';

						switch (row.stockStatusId) {
							case 1:
								outActions += btnViewProduct;
								if (row.cart > 0) {
									outActions += btnAddMoreToCart;
								} else {
									outActions += btnAddToCart;
								}
								SearchDataTable.changeProductQuantityCallback(row);
								break;
							case 2:
								outActions += btnViewProduct;
								outActions += btnNotifyMe;
								break;
							case 3:
								outActions += btnViewProduct;
								outActions += btnNotifyMe;
								break;
						}

						return outActions;
					},
				},
			],
		});
	};

	var _productAddBonusModal = function (productId, entityId, bonuses) {
		$('#addBonusProductId').val(productId);
		$('#addBonusEntityId').val(entityId);

		$('#addBonusModalTitle').html(WebAppLocals.getMessage('addBonusTitle'));

		$("label[for='addBonusMinOrder']").text(WebAppLocals.getMessage('minOrder'));
		$("label[for='addBonusQuantity']").text(WebAppLocals.getMessage('bonus'));
		$("label[for='addBonusAdd']").text(WebAppLocals.getMessage('add'));

		var repeater = $('#addBonusListRepeater').repeater({
			show: function() {
				$(this).slideDown();
			},
		});
		repeater.setList(bonuses);

		$('#addBonusDone').html(WebAppLocals.getMessage('done'));
		$('#addBonusModal').appendTo('body').modal('show');
	};

	var _productAddBonus = function(addButtonName) {
		let bonusRepeaterIndex = addButtonName.charAt(14);

		let bonusMinOrderName = "bonusRepeater[" + bonusRepeaterIndex + "][minOrder]";
		let bonusMinOrder = $($('[name ="' + bonusMinOrderName + '"]')[0]).val();

		let bonusBonusName = "bonusRepeater[" + bonusRepeaterIndex + "][bonus]";
		let bonusBonus = $($('[name ="' + bonusBonusName + '"]')[0]).val();

		let productId = $('#addBonusProductId').val();

		let quantityId = "#quantity-" + productId;
		$(quantityId).val(bonusMinOrder);

		let quantityFreeInputId = "#quantityFreeInput-" + productId;
		$(quantityFreeInputId).val(bonusBonus);

		let quantityFreeHolderId = "#quantityFreeHolder-" + productId;
		$(quantityFreeHolderId).html(bonusBonus);

		$('#addBonusModal').appendTo('body').modal('hide');
	}

	var _initSearchFilter = function () {
		/*
		$('#searchProductScientificNameInput').autocomplete({
			nameProperty: 'name',
			valueField: '#hidden-field',
			dataSource: myData,
			// value property
			valueProperty: 'value',
			// item filter
			filter: function (input, data) {
				return data.filter(x => ~x[this.options.nameProperty].toLowerCase().indexOf(input.toLowerCase()));
			},
			// trigger event
			filterOn: 'input',
			// called when the input is clicked
			openOnInput: true,
			// function(li, item)
			preAppendDataItem: null,
			// function(input, data) { ... }
			validation: null,
			// auto select the first matched item
			selectFirstMatch: false,
			// trigger element
			validateOn: 'blur',
			// called when selected
			onSelected: null,
			// class for invalid
			invalidClass: 'invalid',
			// triggered as soon as the initial value is selected
			initialValueSelectedEvent: 'initial-value-selected.autocomplete',
			// append to the body element
			appendToBody: false,

			// if true the dropdown will only show unique values.
			distinct: false
		});*/
	};

	return {
		// public functions
		init: function (objQuery) {
			_init(objQuery);
		},
		onClickAddToCart: function (row) {
			Cart.addItem(row.entityId, row.id, '#quantity-' + row.id, '#quantityFreeInput-' + row.id);
			//WebApp.reloadDatatable();
		},
		onClickAddMoreToCart: function (row) {
			Cart.addItem(row.entityId, row.id, '#quantity-' + row.id, '#quantityFreeInput-' + row.id);
			WebApp.reloadDatatable();
		},
		onBonusOptionCallback: function (row, bonusOption) {
			$('#quantity-' + row.id).val(bonusOption.minOrder);
			SearchDataTable.changeProductQuantityCallback(row);
		},
		changeProductQuantityCallback: function (row) {
			if (row.bonusTypeId == 2) {
				$('.bonus-option-label-' + row.id).removeClass('label-primary');
				$('.bonus-option-label-' + row.id).addClass('label-light');

				var newQuantity = $('#quantity-' + row.id).val();
				var formulaConfig = {
					quantity: newQuantity,
					minOrder: 0,
					bonus: 0,
					formula: '',
				};

				var bonusOptionLabelId = '';

				if(row.bonusOptions) {
					row.bonusOptions.forEach((bonusOption) => {
						//var bonusOptionLabelId = '#bonusOption-' + row.id + '-' + element.id;
						if (newQuantity >= bonusOption.minOrder) {
							bonusOptionLabelId = '#bonusOption-' + row.id + '-' + bonusOption.id;
							formulaConfig.minOrder = bonusOption.minOrder;
							formulaConfig.formula = bonusOption.formula;
							formulaConfig.bonus = bonusOption.bonus;
						}
					});
				}
				var bonus = 0;
				if (formulaConfig.bonus > 0) {
					bonus = math.evaluate(formulaConfig.formula, formulaConfig);
					$(bonusOptionLabelId).removeClass('label-light').addClass('label-primary');
				}
				$('#bonus-' + row.id).html(bonus);


				let bonusVal = '';
				let bestBonus = 0;
				row.bonuses && row.bonuses.forEach((bonusOption) => {
					if (newQuantity >= bonusOption.minOrder && bonusOption.minOrder > bestBonus) {
						bonusVal = Math.floor(newQuantity / bonusOption.minOrder) * bonusOption.bonus;
						bestBonus = bonusOption.minOrder;
					}
				});
				let activeBonus = row.activeBonus?.minOrder || 0;
				if(activeBonus >= bestBonus) {
					bonusVal = '';
					bestBonus = 0;
				}

				$("#quantityFreeHolder-" + row.id).html(bonusVal);
				$('#quantityFreeInput-' + row.id).val(bonusVal);
			}
		},
		setReadParams: function (objQuery) {
			_readParams = objQuery;
			datatable.setDataSourceParam('query', _readParams);
			datatable.reload();
		},
		showColumn: function (columnName) {
			datatable.showColumn(columnName);
		},
		hideColumn: function (columnName) {
			datatable.hideColumn(columnName);
		},
		productAddBonusModal: function(productId, entityId, bonuses) {
			_productAddBonusModal(productId, entityId, bonuses)
		},
		productAddBonus: function(addButtonName) {
			_productAddBonus(addButtonName)
		},
		updateQty(row, oldValue = null){

			if(row.cart == 0){
				Cart.addItem(row.entityId, row.id, '#quantity-' + row.id, '#quantityFreeInput-' + row.id);
			}else{
				CartCheckout.updateQuantity(row.productId, 0, row.stock, row.cartDetailId, row.entityId,0, oldValue)
			}
			WebApp.reloadDatatable();
		}
	};
})();
