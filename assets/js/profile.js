'use strict';

// Class Definition
var Profile = (function () {

    var _init = function () {
        if($('select[name=country]').val()) {
            _handleCountrySelectChange();
        }
        $('#myProfileForm select[name=country]').on('change', _handleCountrySelectChange);
    }

    var _handleCountrySelectChange = function () {
        var countryId = $('#myProfileForm select[name=country]').val();
        if (countryId) {
            WebApp.get('/web/city/list/' + countryId, function (webResponse) {
                $('#myProfileForm select[name=city]')
                    .empty()
                    .append('<option value="">' + WebAppLocals.getMessage('city') + '</option>');
                var allCities = webResponse.data;
                allCities.forEach((city) => {
                    $('#myProfileForm select[name=city]').append(new Option(city.name, city.id));
                });
                $('#myProfileForm select[name=city]').prop('disabled', false);
            });
        } else {
            $('#myProfileForm select[name=city]').prop('disabled', true);
            $('#myProfileForm select[name=city]')
                .empty()
                .append('<option value="">' + WebAppLocals.getMessage('city') + '</option>');
        }
    }

	// Public Functions
	return {
        init: function () {
            _init();
        }
	};
})();
