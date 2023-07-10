function errorMessage() {
    const parent = document.getElementById('invadr_oxuser__oxfname').parentElement.parentElement
    const errorElement = document.createElement('<div class="alert alert-danger"></div>');
    errorElement.innerText = document.getElementById('errorText').value;
    parent.insertBefore(errorElement, parent.firstChild);
}

function isShippingAddressDifferent() {
    return $('#shippingAddress').css('display') !== 'none';
}

async function addressValidation(zip, city, country) {
    return new Promise(function (resolve) {
        $.ajax({
            url: $('#baseUrl').val(),
            type: "POST",
            data: {
                'cl': 'addressValidator',
                'fnc': 'validateAddress',
                'zip': zip,
                'city': city,
                'countryId': country
            },
            success: function(data){
                resolve(JSON.parse(data));
            }
        });
    })
}

async function isAddressValid() {
    const zip = document.getElementsByName('invadr[oxuser__oxzip]')[0].value;
    const city = document.getElementsByName('invadr[oxuser__oxcity]')[0].value;
    const country = document.getElementsByName('invadr[oxuser__oxcountryid]')[0].value;

    const isBillingAddressValid = await addressValidation(zip, city, country).then(function (data) {
        return data;
    });
    let isShippingAddressValid = isBillingAddressValid;

    if (isShippingAddressDifferent()) {
        const delZip = document.getElementsByName('deladr[oxaddress__oxzip]')[0].value;
        const delCity = document.getElementsByName('deladr[oxaddress__oxcity]')[0].value;
        const delCountry = document.getElementsByName('deladr[oxaddress__oxcountryid]')[0].value;

        isShippingAddressValid = await addressValidation(delZip, delCity, delCountry).then(function (data) {
            return data;
        });
    }

    return isBillingAddressValid && isShippingAddressValid;
}

const addressForm = $('form[name=order]');
document.getElementsByName("userform").forEach( function (submit) {
    submit.setAttribute('type', 'button');
    submit.onclick = async function () {
        if (await isAddressValid()) {
            addressForm.submit();
        } else {
            errorMessage();
        }
    }
});
