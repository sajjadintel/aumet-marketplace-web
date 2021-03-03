function initializeBonusPopover() {
    $('.bonusLabel').popover('dispose');
    $('.bonusLabel').each(function(index, element) {
        var arrBonusStr = $(element).attr('data-arrBonus') || "[]";
        var arrBonus = JSON.parse(arrBonusStr);
        if (arrBonus.length > 0) {
            $(element).popover({
                html: true,
                sanitize: false,
                trigger: "manual",
                placement: "bottom",
                content: getBonusPopoverContent(element),
            }).on("mouseenter", function() {
                var _this = this;
                $(this).popover("show");
                $(".popover").on("mouseleave", function() {
                    $(_this).popover('hide');
                });
            }).on("mouseleave", function() {
                var _this = this;
                setTimeout(function() {
                    if (!$(".popover:hover").length) {
                        $(_this).popover("hide");
                    }
                }, 300);
            });
        } else {
            $(element).hide();
        }
    });
}

function getBonusPopoverContent(element) {
    var arrBonusStr = $(element).attr('data-arrBonus') || "[]";
    var arrBonus = JSON.parse(arrBonusStr);
    var activeBonusStr = $(element).attr('data-activeBonus') || "{}";
    var activeBonus = JSON.parse(activeBonusStr);

    var tableElement = document.createElement("table");

    var tableHead = [
        "BONUSES TYPE",
        "MIN QTY",
        "BONUSES"
    ];
    var allTableData = [
        tableHead,
        ...arrBonus
    ];
    for (var i = 0; i < allTableData.length; i++) {
        var row = allTableData[i];

        if (i == 0) {
            /* Add table head*/
            var trElement = document.createElement('tr');
            for (var j = 0; j < row.length; j++) {
                var item = row[j];
                var thElement = document.createElement('th');
                thElement.className = "cart-checkout-bonus-th text-center p-1 pb-3";
                thElement.innerHTML = item;
                trElement.append(thElement);
            }
            tableElement.append(trElement);
        } else {
            var arrMinQty = row.arrMinQty || [];
            var arrBonuses = row.arrBonuses || [];
            if (arrMinQty.length > 0 && arrMinQty.length === arrBonuses.length) {
                /* Add bonus type column*/
                var trElement = document.createElement('tr');

                var bonusType = row.bonusType;
                var tdBonusTypeElement = document.createElement('td');
                tdBonusTypeElement.className = "cart-checkout-bonus-td text-center p-1";
                if (i != allTableData.length - 1) tdBonusTypeElement.className += " border-bottom";
                if (arrMinQty.length > 1) tdBonusTypeElement.setAttribute('rowspan', arrMinQty.length);
                tdBonusTypeElement.innerHTML = bonusType;
                trElement.append(tdBonusTypeElement);

                /* Add minQty and bonuses columns*/
                for (var j = 0; j < arrMinQty.length; j++) {
                    if (j != 0) {
                        trElement = document.createElement('tr');
                    }

                    var minQty = arrMinQty[j];
                    var tdMinQtyElement = document.createElement('td');
                    tdMinQtyElement.className = "cart-checkout-bonus-td text-center p-1 border-left";
                    if (i != allTableData.length - 1 || j != arrMinQty.length - 1) {
                        tdMinQtyElement.className += " border-bottom";
                    }
                    tdMinQtyElement.innerHTML = minQty;
                    trElement.append(tdMinQtyElement);

                    var bonuses = arrBonuses[j];
                    var tdBonusesElement = document.createElement('td');
                    tdBonusesElement.className = "cart-checkout-bonus-td text-center p-1 border-left";
                    if (i != allTableData.length - 1 || j != arrMinQty.length - 1) {
                        tdBonusesElement.className += " border-bottom";
                    }
                    tdBonusesElement.innerHTML = bonuses;
                    trElement.append(tdBonusesElement);

                    if (activeBonus) {
                        if (bonusType == activeBonus.bonusType && minQty == activeBonus.minQty && bonuses == activeBonus.bonuses) {
                            var tdCheckElement = document.createElement('td');
                            tdCheckElement.className = "cart-checkout-bonus-td text-center p-1";
                            tdCheckElement.innerHTML = "<i class='las la-check check'></i>";
                            trElement.append(tdCheckElement);
                        }
                    }

                    tableElement.append(trElement);
                }
            }
        }
    }
    if (activeBonus && activeBonus.totalBonus) {
        $(element).find('.bonus').text("(+" + activeBonus.totalBonus + ")");
    } else {
        $(element).find('.bonus').text("");
    }
    return tableElement.outerHTML;
}