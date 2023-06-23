let addressForm;
console.log("1");

$(document).ready(function () {
    addressForm = document.getElementById('invCountrySelect').form;
    addressForm.on('submit', function(e) {
        e.preventDefault();
        if (isAddressValid()) {
            addressForm.submit();
        } else {
            //error
        }
    });
});

function isAddressValid() {
    let isBillingAddressValid = addressValidation('#invadr_oxuser__oxzip', '#invadr_oxuser__oxcity', '#invCountrySelect');

    let isShippingAddressValid;
    if (isShippingAddressDiffrent()) {
        isShippingAddressValid = addressValidation('#deladr_oxaddress__oxzip', '#deladr_oxaddress__oxcity', '#delCountrySelect');
    } else {
        isShippingAddressValid = isBillingAddressValid;
    }

    return isBillingAddressValid && isShippingAddressValid;
}

function isShippingAddressDiffrent() {
    return $('#shippingAddress').css('display') !== 'none';
}

function addressValidation(zipSelector, citySelector, countrySelector) {
    let response;
    $.ajax({
        url: $('#baseUrl').val(),
        type: "POST",
        data: {
            'cl': 'addressValidator',
            'fnc': 'validateAddress',
            'zip': $(zipSelector).val(),
            'city':  $(citySelector).val(),
            'countryId':  $(countrySelector).val()
        },
        success: function(data){
            response = JSON.parse(data);
        }
    });

    if (!response) {
        errorMessage(response);
    }

    return response;
}

function errorMessage() {

}
