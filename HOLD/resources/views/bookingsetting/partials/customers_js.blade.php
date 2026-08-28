<script>
    
    const countries = @json($allCountries);
    
    $(document).ready(function () {
        $('#country').select2({
            width: '100%',
            placeholder: "Select a country",
            allowClear: true,
            dropdownParent: $('#country').parent()
        });
    
        $('#country').on('select2:open', function () {
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 100);
        });

        showlist();

        $('#staticBackdrop').modal('show')

        $('#advance_booking_minium_type').val('hours').change();

        $('#advance_booking_maximum_type').val('months').change(); 

        $('#advance_booking_minium').val(2); 

        $('#advance_booking_maximum').val(1); 

        getMinuteOrHoueRange('advance_booking_minium', 2, 'hours'); 

        getMinuteOrHoueRange('advance_booking_maximum', 1, 'months'); 
        
        getMinuteOrHoueRange('cancel_booking', 1, 'hours'); 


    });
    
    $('#country').on('change', function () {
        const selected = $(this).find(':selected');
        const currency = selected.data('currency');
        const currencySymbol = selected.data('currency-symbol');
        const timezone = selected.data('timezone');

        $('#currency').val(`${currencySymbol}`);
        $('#timezone').val(timezone);
    });



    function showlist() {

        var formDataObject = {

            token: getCookie('d_token'),

            device_id: 0

        };



        // Make an AJAX request

        $.ajax({

            url: '{{env('API_URL')}}bookingsetting',

            method: 'POST',

            data: formDataObject,

            success: function (response) {

                //  console.log(response.data);

                var data = response.data;

                AssignValues(data);

                //console.log(response);

            },

            error: function (error) {

                //console.error('Error fetching data:', error);

            }

        });

    }





    function AssignValues(data) {



        $(`#countryCode`).val(data.countryCode);

        $('#bokingsettingid').val(data.id);

        // $('#advance_booking_minium').val(data.advance_booking_minium);

        $('#advance_booking_maximum').val(data.advance_booking_maximum);

        $('#additional_drop_offs').val(data.additional_drop_offs);

        $('#country').val(data.country);
        
        $('#company_name').val(data.company_name);

        // $('#auto_booking_accept').val(data.auto_booking_accept);

        // $('#auto_customer_registration').val(data.auto_customer_registration);

        $('#avoid_route').val(data.avoid_route);

        // $('#cancel_booking').val(data.cancel_booking);

        $('#country').val(data.country).select2();

        $('#currency').val(data.currency);

        $('#distance_unit').val(data.distance_unit);

        $('#google_map_api_key_browser').val(data.google_map_api_key_browser);

        $('#google_map_api_key_server').val(data.google_map_api_key_server);

        // $('#hourl_package').val(data.hourl_package);

        $('#order_prefix').val(data.order_prefix);

        $('#route').val(data.route);

        $('#timezone').val(data.timezone);

        $('#txtCancelBookingRestrictType').val(data.txtCancelBookingRestrictType);

        $('#cancel_booking_terms').val(data.cancel_booking_terms);

        // $('#advance_booking_maximum_type').val(data.advance_booking_maximum_type);

        getMinuteOrHoueRange('advance_booking_minium', data.advance_booking_minium, data.advance_booking_minium_type);

        getMinuteOrHoueRange('advance_booking_maximum', data.advance_booking_maximum, data.advance_booking_maximum_type)

        getMinuteOrHoueRange('cancel_booking', data.cancel_booking, data.cancel_booking_type);



        $('#cancel_booking_type').val(data.cancel_booking_type);

        $('#advance_booking_minium_type').val(data.advance_booking_minium_type);

        $('#advance_booking_maximum_type').val(data.advance_booking_maximum_type);







    }



    $('#saveBtn').click(function (e) {
        
        e.preventDefault();

        const url = 'bookingstore';
        
        const selectedCountry = $('#country').val();
        const countryData = countries.find(c => c.name == selectedCountry);
        
        // console.log(countries, selectedCountry)
    
        // Set values
        $('#timezone').val(countryData.zone_name || '');
        $('#countryCode').val(countryData.phonecode || '');
        $('#currency').val(`${countryData.currency_symbol}`);


        
        // $('#timezone').val(countryToTimezone[$('#country').val()].timezone);

        // $(`#countryCode`).val(countryToTimezone[$('#country').val()].countryCode);

        // $('#currency').val(countryToTimezone[$('#country').val()].currency);
        // if(!Object.keys(countryToTimezoneCurrency).filter(country => country.includes($('#country').val()))){
        //     return false;
        // }

        var formdata = new FormData($('#formSettingsSocialMedia')[0]);

        // console.log(formdata)

        // Append additional fields

        formdata.append('token', getCookie('d_token'));

        formdata.append('device_id', 0);



        $.ajax({

            data: formdata,

            url: "{{env('API_URL')}}" + url,

            type: "POST",

            processData: false,

            contentType: false,

            dataType: 'json',
            
            beforeSend: function () {
                $('#saveBtn').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
            },

            success: function (response) {
                $('#saveBtn').html('Save and Next').prop('disabled', false);
                if (response.status == 400) {

                    errornotify(response);

                } else if (response.status == 500) {

                    warningClick('Error', response['error'], "danger");

                } else if (response.status == 401) {

                    unauth();

                } else if (response.status == 200) {
                    
                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Success',

                        text: response.message,

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        let curr_url = window.location.pathname;



                        if (curr_url == '/bookingSetting') {

                            window.location.href = '/create-fleet';

                        } else {

                            window.location.reload();

                        }



                    });
                    // if (response.message == "Data has been updated successfully") {

                    //     Swal.fire({

                    //         position: 'center',

                    //         icon: 'success',

                    //         title: 'Updated',

                    //         text: 'Data has been updated successfully',

                    //         showConfirmButton: false,

                    //         timer: 2000,

                    //     }).then(function () {

                    //         let curr_url = window.location.pathname;



                    //         if (curr_url == '/bookingSetting') {

                    //             window.location.href = '/create-fleet';

                    //         } else {

                    //             window.location.reload();

                    //         }



                    //     });

                    // } else if (response.message == "Data has been inserted successfully") {

                    //     Swal.fire({

                    //         position: 'center',

                    //         icon: 'success',

                    //         title: 'Added',

                    //         text: 'Data has been inserted successfully',

                    //         showConfirmButton: false,

                    //         timer: 2000,

                    //     }).then(function () {

                    //         let curr_url = window.location.pathname;



                    //         if (curr_url == '/bookingSetting') {

                    //             window.location.href = '/create-fleet';

                    //         } else {

                    //             window.location.reload();

                    //         }



                    //     });

                    // }

                }

            },

            error: function (data) {

                // console.log('Error:', data);

            }

        });

    });





    $(document).ready(function () {

        // Mapping for country -> timezone and currency

        // const countryToTimezoneCurrency = {

        //     'United States': {

        //         timezone: 'America/New_York',

        //         currency: 'USD (United States Dollar)'

        //     },

        //     'Canada': {

        //         timezone: 'America/Toronto',

        //         currency: 'CAD (Canadian Dollar)'

        //     },

        //     'Mexico': {

        //         timezone: 'America/Mexico_City',

        //         currency: 'MXN (Mexican Peso)'

        //     },

        //     'India': {

        //         timezone: 'Asia/Kolkata',

        //         currency: 'INR (Indian Rupee)'

        //     },

        //     'Australia': {

        //         timezone: 'Australia/Sydney',

        //         currency: 'AUD (Australian Dollar)'

        //     },

        //     'United Kingdom': {

        //         timezone: 'Europe/London',

        //         currency: 'GBP (Pound Sterling)'

        //     },

        //     'Germany': {

        //         timezone: 'Europe/Berlin',

        //         currency: 'EUR (Euro)'

        //     },

        //     'Japan': {

        //         timezone: 'Asia/Tokyo',

        //         currency: 'JPY (Japanese Yen)'

        //     },

        //     'Brazil': {

        //         timezone: 'America/Sao_Paulo',

        //         currency: 'BRL (Brazilian Real)'

        //     },

        //     'South Africa': {

        //         timezone: 'Africa/Johannesburg',

        //         currency: 'ZAR (South African Rand)'

        //     },

        //     'Russia': {

        //         timezone: 'Europe/Moscow',

        //         currency: 'RUB (Russian Ruble)'

        //     },

        //     'China': {

        //         timezone: 'Asia/Shanghai',

        //         currency: 'CNY (Chinese Yuan)'

        //     },

        //     'France': {

        //         timezone: 'Europe/Paris',

        //         currency: 'EUR (Euro)'

        //     },

        //     'Italy': {

        //         timezone: 'Europe/Rome',

        //         currency: 'EUR (Euro)'

        //     },

        //     'Spain': {

        //         timezone: 'Europe/Madrid',

        //         currency: 'EUR (Euro)'

        //     },

        //     'Argentina': {

        //         timezone: 'America/Argentina/Buenos_Aires',

        //         currency: 'ARS (Argentine Peso)'

        //     },

        //     'Nigeria': {

        //         timezone: 'Africa/Lagos',

        //         currency: 'NGN (Nigerian Naira)'

        //     },

        //     'UAE': {

        //     currency: "AED", 

        //     timezone: "Asia/Dubai"

        //      },

        //     // Add more countries as needed

        // };





        const countryToTimezoneCurrency = {

            'United States': {

                countryCode: 'US',

                timezone: 'America/New_York',

                currency: 'USD'

            },

            'Canada': {

                countryCode: 'CA',

                timezone: 'America/Toronto',

                currency: 'CAD'

            },

            // 'Mexico': {

            //     countryCode: 'MX',

            //     timezone: 'America/Mexico_City',

            //     currency: 'MXN (Mexican Peso)'

            // },

            'India': {

                countryCode: 'IN',

                timezone: 'Asia/Kolkata',

                currency: 'INR'

            },

            // 'Australia': {

            //     countryCode: 'AU',

            //     timezone: 'Australia/Sydney',

            //     currency: 'AUD (Australian Dollar)'

            // },

            'United Kingdom': {

                countryCode: 'GB',

                timezone: 'Europe/London',

                currency: 'GBP'

            },
            
            'Kuwait': {
                countryCode: 'KW',
                
                timezone: 'Asia/Kuwait',
                
                currency: 'KWD'
            },
            
            'Iraq': {
                countryCode: 'IQ',
                timezone: 'Asia/Baghdad',
                currency: 'IQD'
            },


            // 'Germany': {

            //     countryCode: 'DE',

            //     timezone: 'Europe/Berlin',

            //     currency: 'EUR (Euro)'

            // },

            // 'Japan': {

            //     countryCode: 'JP',

            //     timezone: 'Asia/Tokyo',

            //     currency: 'JPY (Japanese Yen)'

            // },

            // 'Brazil': {

            //     countryCode: 'BR',

            //     timezone: 'America/Sao_Paulo',

            //     currency: 'BRL (Brazilian Real)'

            // },

            // 'South Africa': {

            //     countryCode: 'ZA',

            //     timezone: 'Africa/Johannesburg',

            //     currency: 'ZAR (South African Rand)'

            // },

            // 'Russia': {

            //     countryCode: 'RU',

            //     timezone: 'Europe/Moscow',

            //     currency: 'RUB (Russian Ruble)'

            // },

            // 'China': {

            //     countryCode: 'CN',

            //     timezone: 'Asia/Shanghai',

            //     currency: 'CNY (Chinese Yuan)'

            // },

            // 'France': {

            //     countryCode: 'FR',

            //     timezone: 'Europe/Paris',

            //     currency: 'EUR (Euro)'

            // },

            // 'Italy': {

            //     countryCode: 'IT',

            //     timezone: 'Europe/Rome',

            //     currency: 'EUR (Euro)'

            // },

            // 'Spain': {

            //     countryCode: 'ES',

            //     timezone: 'Europe/Madrid',

            //     currency: 'EUR (Euro)'

            // },

            // 'Argentina': {

            //     countryCode: 'AR',

            //     timezone: 'America/Argentina/Buenos_Aires',

            //     currency: 'ARS (Argentine Peso)'

            // },

            // 'Nigeria': {

            //     countryCode: 'NG',

            //     timezone: 'Africa/Lagos',

            //     currency: 'NGN (Nigerian Naira)'

            // },

            // 'UAE': {

            //     countryCode: 'AE',

            //     currency: "AED",

            //     timezone: "Asia/Dubai"

            // },

            // Add more countries as needed

        };





        // Country search logic

        $('#country').on('input', function () {

            // let searchValue = $(this).val().toLowerCase();

            // let filteredCountries = Object.keys(countryToTimezoneCurrency).filter(country => country.toLowerCase().includes(searchValue));



            // $('#dropdown').empty();



            // if (searchValue && filteredCountries.length) {
                
                
            //     $('#dropdown').addClass('active');

            //     filteredCountries.forEach(function (country) {

            //         // let countryCode = countryToTimezoneCurrency[country].countryCode;

            //         $('#dropdown').append('<div class="dropdown-item" >' + country + '</div>');

            //     });

            // } else {

            //     $('#dropdown').removeClass('active');

            // }



            // If country input is empty, reset timezone and currency inputs

            // if (!searchValue) {

            //     $('#timezone').val('');

            //     $('#currency').val('');

            // }

        });



        // When a country is selected

        // $(document).on('click', '.dropdown-item', function () {

        //     let selectedCountry = $(this).text();



        //     $('#country').val(selectedCountry);

        //     $('#dropdown').removeClass('active');



        //     // Set the default timezone and currency based on the selected country

        //     if (countryToTimezoneCurrency[selectedCountry]) {
        //         if(selectedCountry == 'United Kingdom'){
        //             $('#distance_unit').val('kms').prop('selected', false);
        //             $('#distance_unit').val('miles').prop('selected', true);
        //         }else{
                    
        //             $('#distance_unit').val('miles').prop('selected', false);
        //             $('#distance_unit').val('kms').prop('selected', true);
        //         }
        //         $('#timezone').val(countryToTimezoneCurrency[selectedCountry].timezone);



        //         $(`#countryCode`).val(countryToTimezoneCurrency[selectedCountry].countryCode);

        //         $('#currency').val(countryToTimezoneCurrency[selectedCountry].currency);

        //     }

        // });



        // Validate input on blur for timezone and currency

        // $('#country').on('blur', function () {

        //     let inputValue = $(this).val().trim(); // Ensure no leading/trailing spaces



        //     if (inputValue && !$('#dropdown').hasClass('active')) {

        //         const isValidCountry = Object.keys(countryToTimezoneCurrency).map(country => country.toLowerCase()).includes(inputValue.toLowerCase());

        //         if (!isValidCountry) {

        //             $(this).val('');

        //             warningClick('Alert', 'Please select a valid country from the list', 'warning');

        //             // Reset the timezone and currency inputs if country is invalid

        //             $('#timezone').val('');

        //             $('#currency').val('');

        //         }

        //     }

        // });



        $(document).on('change', '#advance_booking_minium_type,#advance_booking_maximum_type,#cancel_booking_type', function () {

            var elementId = $(this).attr('id');

            var hourOrMinute = $(this).val();

            var child = elementId.replace(/_type/gi, "");

            var value = $('#' + child).val();

            getMinuteOrHoueRange(child, value, hourOrMinute)

        });



        // Handle item selection

        $(document).on('change', '#advance_booking_minimum_type,#advance_booking_maximum_type,#cancel_booking_type', function () {

            var elementId = $(this).attr('id');

            var hourOrMinute = $(this).val();

            var child = elementId.replace(/_type/gi, "");

            var value = $('#' + child).val();

            getMinuteOrHoueRange(child, value, hourOrMinute)

        })



        // $(document).click(function (e) {

        //     if (!$(e.target).closest('#country').length && !$(e.target).closest('#dropdown').length) {

        //         $('#dropdown').removeClass('active');

        //     }

        // });

    });





    function getMinuteOrHoueRange(id, idValue, value) {



        var id = $('#' + id);

        let end = 24;

        if (value == 'minutes' || value == 'days') {

            end = 60;

        } else if (value == 'months') {

            end = 12



        } else if (value == 'years') {

            end = 2

        }



        let text = "";

        let i = 1;

        do {

            text += i + `<option value ='${i}'>${i} </option>`;

            i++;

        }

        while (i <= end);



        id.empty().append(text).val(idValue)



        // console.log(text);

    }





</script>

<script type="text/javascript">

    // $("#selOperatingCountry,#selTimezone,#selCurrencyCode").select2();

    // $(document).on('select2:open', () => {

    //     document.querySelector('.select2-search__field').focus();

    // });
    
    

    $("#selAdvanceBookingFilterMinType").val('hours');

    $("#selAdvanceBookingFilterMaxType").val('years');

    $("#txtCancelBookingRestrictType").val('hours');

</script>