$(document).ready(function() {
    $(".valid_phone").each(function() {
        var e = document.querySelector(".valid_phone"),
            n = window.intlTelInput(e, {
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.0/js/utils.js",
                initialCountry: "auto",
                separateDialCode: false,
                preferredCountries:["ng"],
                hiddenInput: "full",
                nationalMode: !1,
                autoHideDialCode: !1,
                dropdownContainer: null
            });
        e.addEventListener("countrychange", function() {
            var o = n.getSelectedCountryData();
            $(e).val(o.dialCode)
        })
    })
});