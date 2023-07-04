let addressForm = $('#invCountrySelect').closest('form');
document.getElementsByName("userform").forEach(function (submit) {
    submit.setAttribute('type', 'button');
    submit.setAttribute('onclick', 'validateAdress()')
});


function validateAddress() {
    if (isAddressValid()) {
        addressForm.submit();
    } else {
        //error
    }
}

function isAddressValid() {
    let isBillingAddressValid = addressValidation('#invadr_oxuser__oxzip', '#invadr_oxuser__oxcity', '#invCountrySelect');
    let isShippingAddressValid = isBillingAddressValid;

    if (isShippingAddressDiffrent()) {
        isShippingAddressValid = addressValidation('#deladr_oxaddress__oxzip', '#deladr_oxaddress__oxcity', '#delCountrySelect');
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
