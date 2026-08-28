<script>
$(document).ready(function(){
    
  showlist();  
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
        success: function(response) {
             console.log(response.data);
         var data= response.data;  
         AssignValues(data);
          //console.log(response);
        },
        error: function(error) {
            //console.error('Error fetching data:', error);
        }
    });
}

    
    function AssignValues(data){

        $('#bokingsettingid').val(data.id);
        $('#advance_booking_minium').val(data.advance_booking_minium);
        $('#advance_booking_maximum').val(data.advance_booking_maximum);
        $('#additional_drop_offs').val(data.additional_drop_offs);
        $('#country').val(data.country);
        $('#auto_booking_accept').val(data.auto_booking_accept);
        $('#auto_customer_registration').val(data.auto_customer_registration);
        $('#avoid_route').val(data.avoid_route);
        $('#cancel_booking').val(data.cancel_booking);
        $('#country').val(data.country);
        $('#currency').val(data.currency);
        $('#distance_unit').val(data.distance_unit);
        $('#google_map_api_key_browser').val(data.google_map_api_key_browser);
        $('#google_map_api_key_server').val(data.google_map_api_key_server);
        $('#hourl_package').val(data.hourl_package);
        $('#order_prefix').val(data.order_prefix);
        $('#route').val(data.route);
        $('#timezone').val(data.timezone);
        $('#txtCancelBookingRestrictType').val(data.txtCancelBookingRestrictType);
        $('#cancel_booking_terms').val(data.cancel_booking_terms);
        $('#advance_booking_maximum_type').val(data.advance_booking_maximum_type);
        $('#cancel_booking_type').val(data.cancel_booking_type);
        $('#advance_booking_minium_type').val(data.advance_booking_minium_type);
        
        
        
    }
    
  		$('#saveBtn').click(function (e) {
    e.preventDefault();

    const url = 'bookingstore';
    var formdata = new FormData($('#formSettingsSocialMedia')[0]); 

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
        success: function (response) {
            if (response.status == 400) {
                errornotify(response);
            } else if (response.status == 500) {
                warningClick('Error', response['error'], "danger");
            } else if (response.status == 401) {
                unauth();
            } else if (response.status == 200) {

                if (response.message =="Data has been updated successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Data has been updated successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                } else if(response.message =="Data has been inserted successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Added',
                        text: 'Data has been inserted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                }
            }
        },
        error: function (data) {
            // console.log('Error:', data);
        }
    });
});



const countries = [
            'Afghanistan', 'Aland Islands', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 
            'Anguilla', 'Antarctica', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia',
            'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium',
            'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana',
            'Bouvet Island', 'Brazil', 'British Indian Ocean Territory', 'British Virgin Islands', 'Brunei',
            'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde',
            'Caribbean Netherlands', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China',
            'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Cook Islands', 'Costa Rica',
            'Croatia', 'Cuba', 'Curacao', 'Cyprus', 'Czech Republic', 'Democratic Republic of the Congo',
            'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'East Timor', 'Ecuador', 'Egypt', 
            'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands (Islas Malvinas)',
            'Faroe Islands', 'Federated States of Micronesia', 'Fiji', 'Finland', 'France', 'French Guiana',
            'French Polynesia', 'French Southern and Antarctic Lands', 'Gabon', 'Gambia', 'Georgia', 'Germany',
            'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe','Guam','Guatemala','Guernsey','Guinea','Guinea-Bissau','Guyana','Haiti','Heard Island and McDonald Islands','Honduras','HongKong','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Isle of Man','Israel','Italy','Ivory Coast','Jamaica','Japan','Jersey','MongoliaJordan','Kazakhstan','Kenya','Kiribati','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Macau','Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Martinique','MauritaniNew Caledonia','Mauritius','Mayotte','Mexico','Monaco','Mongolia','Montenegro','Montserrat','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','New Caledonia','New Zealand','Nicaragua','Niger','Nigeria','Niue','Norfolk Island','North Korea','Northern Mariana Islands','Norway','Oman','Pakistan','Palau','PalestinianTerritory,Occupied','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Pitcairn','Poland','Portugal','Puerto Rico','Qatar','Republic of Moldova','Republic of the Congo','Reunion','Romania','Russia','Rwanda','Saint Barthelemy','Saint Helena','Saint Kitts and Nevis','Saint Lucia','Saint Martin(Frenchpart)','Saint Pierreand Miquelon','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Sint Maarten','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Georgia and the South Sandwich Islands','South Korea','Spain','SriLanka','Sudan','Suriname','Svalbard and Jan Mayen','Swaziland','Sweden','Switzerland','Syrian Arab Republic','Taiwan','Tajikistan','Tanzania','Thailand','Togo','Tokelau','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Turksand Caicos Islands','Tuvalu','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','United States Minor Outlying Islands','United States Virgin Islands','Uruguay','Uzbekistan','Vanuatu','VaticanCity','Venezuela','Vietnam','WallisandFutuna','WesternSahara','Yemen','Zambia','Zimbabwe'
        ];
$(document).ready(function () {
            $('#country').on('input', function () {
                let searchValue = $(this).val().toLowerCase();
                let filteredCountries = countries.filter(country => country.toLowerCase().includes(searchValue));

                $('#dropdown').empty(); // Clear previous results

                if (searchValue && filteredCountries.length) {
                    $('#dropdown').addClass('active');
                    filteredCountries.forEach(function (country) {
                        $('#dropdown').append('<div class="dropdown-item">' + country + '</div>');
                    });
                } else {
                    $('#dropdown').removeClass('active');
                }
            });

            // Handle item selection
            $(document).on('click', '.dropdown-item', function () {
                $('#country').val($(this).text());
                $('#dropdown').removeClass('active');
            });

            // Close dropdown if clicked outside
            $(document).click(function (e) {
                if (!$(e.target).closest('#country').length && !$(e.target).closest('#dropdown').length) {
                    $('#dropdown').removeClass('active');
                }
            });
        });
        
 $(document).ready(function () {       
const timezone = [
            'Africa/Abidjan', 'Africa/Accra', 'Africa/Addis_Ababa', 'Africa/Algiers', 'Africa/Asmara', 'Africa/Bamako', 'Africa/Bangui', 
            'Africa/Banjul', 'Africa/Bissau', 'Africa/Blantyre', 'Africa/Brazzaville', 'Africa/Bujumbura', 'Africa/Cairo', 'Africa/Casablanca',
            'Africa/Ceuta', 'Africa/Conakry', 'Africa/Dakar', 'Africa/Dar_es_Salaam', 'Africa/Djibouti', 'Africa/Douala', 'Africa/El_Aaiun', 'Africa/Freetown',
            'Africa/Gaborone', 'Africa/Harare', 'Africa/Johannesburg', 'Africa/Juba', 'Africa/Kampala', 'Africa/Khartoum', 'Africa/Kigali',
            'Africa/Kinshasa', 'Africa/Lagos', 'Africa/Libreville', 'Africa/Lome ', 'Africa/Luanda',
            'Africa/Lubumbashi', '>Africa/Lusaka', 'Africa/Malabo', 'Africa/Maputo', 'Africa/Maseru', 'Africa/Mbabane', 'Africa/Mogadishu',
            'Africa/Monrovia', 'Africa/Nairobi', 'Africa/Ndjamena', 'Africa/Niamey', 'Africa/Nouakchott', 'Africa/Ouagadougou',
            'Africa/Porto-Novo', 'Africa/Sao_Tome', 'Africa/Tripoli', 'Africa/Tunis', 'Africa/Windhoek', 'America/Adak',
            'America/Anchorage', 'America/Anguilla', 'America/Antigua', 'America/Araguaina', 'America/Argentina/Buenos_Aires', 'America/Argentina/Catamarca',
            'America/Argentina/Cordoba', 'America/Argentina/Jujuy', 'America/Argentina/La_Rioja', 'America/Argentina/Mendoza', 'America/Argentina/Rio_Gallegos', 'America/Argentina/Salta', 'America/Argentina/San_Juan', 
            'America/Argentina/San_Luis', 'America/Argentina/Tucuman', 'America/Argentina/Ushuaia', 'America/Aruba', 'America/Asuncion', 'America/Atikokan',
            'America/Bahia', 'America/Bahia_Banderas', 'America/Barbados', 'America/Belem', 'America/Belize', 'America/Blanc-Sablon',
            'America/Boa_Vista', 'America/Bogota', 'America/Boise', 'America/Cambridge_Bay', 'America/Campo_Grande', 'America/Cancun',
            'America/Caracas', 'America/Cayenne', 'America/Cayman', 'America/Chicago', 'America/Chihuahua', 'America/Costa_Rica','America/Creston','America/Cuiaba','America/Curacao','America/Danmarkshavn','America/Dawson','America/Dawson_Creek','America/Denver','America/Detroit','America/Dominica','America/Edmonton','America/Eirunepe','America/El_Salvador','America/Fortaleza','merica/Fort_Nelson','America/Glace_Bay','America/Godthab','America/Goose_Bay','America/Grand_Turk','America/Grenada','America/Guadeloupe','America/Guatemala','America/Guayaquil','America/Guyana','America/Halifax','America/Havana','America/Hermosillo','America/Indiana/Indianapolis','America/Indiana/Knox','America/Indiana/Marengo','America/Indiana/Petersburg','America/Indiana/Tell_City','America/Indiana/Vevay','America/Indiana/Vincennes','America/Indiana/Winamac','America/Inuvik','America/Iqaluit','America/Jamaica','America/Juneau','America/Kentucky/Louisville','America/Kentucky/Monticello','America/Kralendijk','America/La_Paz','America/Lima','America/Los_Angeles','America/Lower_Princes','America/Maceio','America/Managua','America/Manaus','America/Marigot','America/Martinique America/Mazatlan','America/Menominee','America/Merida','America/Metlakatla','America/Mexico_City','America/Miquelon','America/Moncton','America/Monterrey','America/Montevideo','America/Montserrat','America/Nassau','America/New_York','America/Nipigon','America/Nome','America/Noronha','America/North_Dakota/Beulah','America/North_Dakota/Center','America/North_Dakota/New_Salem','America/Ojinaga','America/Panama','America/Pangnirtung','America/Paramaribo','America/Phoenix','America/Port-au-Prince','America/Porto_Velho','America/Port_of_Spain','America/Puerto_Rico','America/Punta_Arenas','America/Rainy_River','America/Rankin_Inlet','America/Recife','America/Regina','America/Resolute','America/Rio_Branco','America/Santarem','America/Santiago','America/Santo_Domingo','America/Sao_Paulo','America/Scoresbysund','America/Sitka','America/St_Barthelemy','America/St_Johns','America/St_Kitts','America/St_Lucia','America/St_Thomas','America/St_Vincent','America/Swift_Current','America/Tegucigalpa','America/Thule','America/Thunder_Bay','America/Tijuana','America/Toronto','America/Tortola','America/Vancouver','America/Whitehorse','America/Winnipeg','America/Yakutat','America/Yellowknife','Antarctica/Casey','Antarctica/Davis','Antarctica/DumontDUrville','Antarctica/Macquarie','Antarctica/Mawson','Antarctica/McMurdo','Antarctica/Palmer','Antarctica/Rothera','Antarctica/Syowa','Antarctica/Troll','Antarctica/Vostok','Arctic/Longyearbyen','Asia/Aden','Asia/Almaty','Asia/Amman','Asia/Anadyr','Asia/Aqtau','Asia/Aqtobe','Asia/Ashgabat','Asia/Atyrau','Asia/Baghdad','Asia/Bahrain','Asia/Baku','Asia/Bangkok','Asia/Barnaul','Asia/Beirut','Asia/Bishkek','Asia/Brunei','Asia/Chita','Asia/Choibalsa','Asia/Colombo','Asia/Damascus','Asia/Dhaka','Asia/Dili','Asia/Dubai','Asia/Dushanbe','Asia/Famagusta','Asia/Gaza','Asia/Hebron','Asia/Hong_Kong','Asia/Hovd','Asia/Ho_Chi_Minh','Asia/Irkutsk','Asia/Jakarta','Asia/Jayapura','Asia/Jerusalem','Asia/Kabul','Asia/Kamchatka','Asia/Karachi','Asia/Kathmandu','Asia/Khandyga','Asia/Kolkata','Asia/Krasnoyarsk','Asia/Kuala_Lumpur','Asia/Kuching','Asia/Kuwait','Asia/Macau','Asia/Magadan','Asia/Makassar','Asia/Manila','Asia/Muscat','Asia/Nicosia','Asia/Novokuznetsk','Asia/Novosibirsk','Asia/Omsk','Asia/Oral','Asia/Phnom_Penh','Asia/Pontianak','Asia/Pyongyang','Asia/Qatar','Asia/Qyzylorda','Asia/Riyadh','Asia/Sakhalin','Asia/Samarkand','Asia/Seoul','Asia/Shanghai','Asia/Singapore','Asia/Srednekolymsk','Asia/Taipei','Asia/Tashkent','Asia/Tbilisi','Asia/Tehran','Asia/Thimphu','Asia/Tokyo','Asia/Tomsk','Asia/Ulaanbaatar','Asia/Urumqi','Asia/Ust-Nera','Asia/Vientiane','Asia/Vladivostok','Asia/Yakutsk','Asia/Yangon','Asia/Yekaterinburg','Asia/Yerevan','Atlantic/Azores','Atlantic/Bermuda','Atlantic/Canary','Atlantic/Cape_Verde','Atlantic/Faroe','Atlantic/Madeira','Atlantic/Reykjavik','Atlantic/South_Georgia','Atlantic/Stanley','Atlantic/St_Helena','Australia/Adelaide','Australia/Brisbane','Australia/Broken_Hill','Australia/Currie','Australia/Darwin','Australia/Eucla','Australia/Hobart','Australia/Lindeman','Australia/Lord_Howe','Australia/Melbourne','Australia/Perth','Australia/Sydney','Europe/Amsterdam','Europe/Andorra','Europe/Astrakhan','Europe/Athens','Europe/Belgrade','Europe/Berlin','Europe/Bratislava','Europe/Brussels','Europe/Bucharest','Europe/Budapest','Europe/Busingen','Europe/Chisinau','Europe/Copenhagen','Europe/Dublin','Europe/Gibraltar','Europe/Guernsey','Europe/Helsinki','Europe/Isle_of_Man','Europe/Istanbul','Europe/Jersey','Europe/Kaliningrad','Europe/Kiev','Europe/Kirov','Europe/Lisbon','Europe/Ljubljana','Europe/London','Europe/Luxembourg','Europe/Madrid','Europe/Malta','Europe/Mariehamn','Europe/Minsk','Europe/Monaco','Europe/Moscow','Europe/Oslo','Europe/Paris','Europe/Podgorica','Europe/Prague','Europe/Riga','Europe/Rome','Europe/Samara','Europe/San_Marino','Europe/Sarajevo','Europe/Saratov','Europe/Simferopol','Europe/Skopje','Europe/Sofia','Europe/Stockholm','Europe/Tallinn','Europe/Tirane','Europe/Ulyanovsk','Europe/Uzhgorod','Europe/Vaduz','Europe/Vatican','Europe/Vienna','Europe/Vilnius','Europe/Volgograd','Europe/Warsaw','Europe/Zagreb','Europe/Zaporozhye','Europe/Zurich','Indian/Antananarivo','Indian/Chagos','Indian/Christmas','Indian/Cocos','Indian/Comoro','Indian/Kerguelen','Indian/Mahe','Indian/Maldives','Indian/Mauritius','Indian/Mayotte','Indian/Reunion','Pacific/Apia','Pacific/Auckland','Pacific/Bougainville','Pacific/Chatham','Pacific/Chuuk','Pacific/Easter','Pacific/Efate','Pacific/Enderbury','Pacific/Fakaofo','Pacific/Fiji','Pacific/Funafuti','Pacific/Galapagos','>Pacific/Gambier','Pacific/Guadalcanal','Pacific/Guam','Pacific/Honolulu','Pacific/Kiritimati','Pacific/Kosrae','Pacific/Kwajalein','Pacific/Majuro','Pacific/Marquesas','Pacific/Midway','Pacific/Nauru','Pacific/Niue','Pacific/Norfolk','Pacific/Noumea','Pacific/Pago_Pago','Pacific/Palau','Pacific/Pitcairn','Pacific/Pohnpei','Pacific/Port_Moresby','Pacific/Rarotonga','Pacific/Saipan','Pacific/Tahiti','Pacific/Tarawa','Pacific/Tongatapu','Pacific/Wake','Pacific/Wallis'
        ];

    $('#timezone').on('input', function () {
        let searchValue = $(this).val().toLowerCase();
        let filteredTimezones = timezone.filter(zone => zone.toLowerCase().includes(searchValue));

        $('#dropdowntimezone').empty(); // Clear previous results

        if (searchValue && filteredTimezones.length) {
            $('#dropdowntimezone').addClass('active');
            filteredTimezones.forEach(function (zone) {
                $('#dropdowntimezone').append('<div class="dropdown">' + zone + '</div>');
            });
        } else {
            $('#dropdowntimezone').removeClass('active');
        }
    });

    // Handle item selection
    $(document).on('click', '.dropdown', function () {
        $('#timezone').val($(this).text());
        $('#dropdowntimezone').removeClass('active');
    });

    // Close dropdown if clicked outside
    $(document).click(function (e) {
        if (!$(e.target).closest('#timezone').length && !$(e.target).closest('#dropdowntimezone').length) {
            $('#dropdowntimezone').removeClass('active');
        }
    });
});




 $(document).ready(function () {       
const currency = [
            'Afghan afghani (AFN)', 'Albanian lek (ALL)', 'Algerian dinar (DZD)', 'Angolan kwanza (AOA)', 'Argentine peso (ARS)', 'Armenian dram (AMD)', 'Aruban florin (AWG)', 
            'Australian dollar (AUD)', 'Azerbaijani manat (AZN)', 'Bahamian dollar (BSD)', 'Bahraini dinar (BHD)', 'Bangladeshi taka (BDT)', 'Barbadian dollar (BBD)', 'Belarusian ruble (BYR)',
            'Belize dollar (BZD)', 'Bermudian dollar (BMD)', 'Bhutanese ngultrum (BTN)', 'Bolivian boliviano (BOB)', 'Bosnia and Herzegovina convertible mark (BAM)', 'Botswana pula (BWP)', 'Brazilian real (BRL)', 'British pound (GBP)',
            'British Virgin Islands dollar (None)', 'Brunei dollar (BND)', 'Bulgarian lev (BGN)', 'Burundian franc (BIF)', 'Cambodian riel (KHR)', 'Canadian dollar (CAD)', 'Cape Verdean escudo (CVE)',
            'Cayman Islands dollar (KYD)', 'Central African CFA franc (XAF)', 'CFP franc (XPF)', 'Chilean peso (CLP) ', 'Chinese yuan (CNY)',
            'Colombian peso (COP)', 'Comorian franc (KMF)', 'Congolese franc (CDF)', 'Costa Rican colon (CRC)', 'Croatian kuna (HRK)', 'Cuban convertible peso (CUC)', 'Czech koruna (CZK)',
            'Danish krone (DKK)', 'Djiboutian franc (DJF)', 'Dominican peso (DOP)', 'East Caribbean dollar (XCD)', 'Egyptian pound (EGP)', 'Eritrean nakfa (ERN)',
            'Ethiopian birr (ETB)', 'Euro (EUR)', 'Falkland Islands pound (FKP)', 'Fijian dollar (FJD)', 'Gambian dalasi (GMD)', 'Georgian lari (GEL)',
            'Ghanaian cedi (GHS)', 'Gibraltar pound (GIP)', 'Guatemalan quetzal (GTQ)', 'Guernsey pound (?GGP)', 'Guinean franc (GNF)', 'Guyanese dollar (GYD)',
            'Haitian gourde (HTG)', 'Honduran lempira (HNL)', 'Hong Kong dollar (HKD)', 'Hungarian forint (HUF)', 'Icelandic krona (ISK)', 'Indian rupee (INR)', 'AIndonesian rupiah (IDR)', 
            'Iranian rial (IRR)', 'Iraqi dinar (IQD)', 'Israeli new shekel (ILS)', 'Jamaican dollar (JMD)', 'Japanese yen (JPY)', 'Jersey pound (?JEP)',
            'Jordanian dinar (JOD)', 'Kazakhstani tenge (KZT)', 'Kenyan shilling (KES)', 'Kuwaiti dinar (KWD)', 'Kyrgyzstani som (KGS)', 'Lao kip (LAK)',
            'Latvian lats (LVL)', 'Lebanese pound (LBP)', 'Lesotho loti (LSL)', 'Liberian dollar (LRD)', 'Libyan dinar (LYD)',
            'Lithuanian litas (LTL)', 'Macanese pataca (MOP)', 'Macedonian denar (MKD)', 'Malagasy ariary (MGA)', 'Malawian kwacha (MWK)', 'Malaysian ringgit (MYR)','Maldivian rufiyaa (MVR)','Manx pound (IMP)','Mauritanian ouguiya (MRO)','Mauritian rupee (MUR)','Mexican peso (MXN)','Micronesian dollar (None)','Moldovan leu (MDL)','Mongolian togrog (MNT)','Moroccan dirham (MAD)','Mozambican metical (MZN)','Myanma kyat (MMK)','Namibian dollar (NAD)','Nepalese rupee (NPR)','Netherlands Antillean guilder (ANG)','New Taiwan dollar (TWD)','New Zealand dollar (NZD)','Nicaraguan crodoba (NIO)','Nigerian naira (NGN)','North Korean won (KPW)','Norwegian krone (NOK)','Omani rial (OMR)','Pakistani rupee (PKR)','Palauan dollar (None)','Panamanian balboa (PAB)','Papua New Guinean kina (PGK)','Paraguayan guarani (PYG)','Peruvian nuevo sol (PEN)','Philippine peso (PHP)','Polish zloty (PLN)','Pound sterling (GBP)','Qatari riyal (QAR)','Romanian leu (RON)','Russian ruble (RUB)','Rwandan franc (RWF)','Saint Helena pound (SHP)','Salvadoran colon (SVC)','Samoan tala (WST)','Sao Tome and Principe dobra (STD)','Saudi riyal (SAR)','Serbian dinar (RSD)','Seychellois rupee (SCR)','Sierra Leonean leone (SLL)','Solomon Islands dollar (SBD)','Somali shilling (SOS)','South African rand (ZAR)','South Korean won (KRW)','Sri Lankan rupee (LKR)','Sudanese pound (SDG)','Surinamese dollar (SRD)','Swazi lilangeni (SZL)','Swedish krona (SEK)','Swiss franc (CHF)','Syrian pound (SYP)','Tajikistani somoni (TJS)','Tanzanian shilling (TZS)','Thai baht (THB)','Tongan paoanga (TOP)','Trinidad and Tobago dollar (TTD)','Tunisian dinar (TND)','Turkish lira (TRY)','Turkmenistani manat (TMT)','Ugandan shilling (UGX)','Ukrainian hryvnia (UAH)','United Arab Emirates dirham (AED)','United States dollar (USD)','Uruguayan peso (UYU)','Uzbekistani som (UZS)','Vanuatu vatu (VUV)','Venezuelan bolivar (VEF)','Vietnamese dong (VND)','West African CFA franc (XOF)','Yemeni rial (YER)','Zambian kwacha (ZMK)'
        ];

    $('#currency').on('input', function () {
        let searchValue = $(this).val().toLowerCase();
        let filteredTimezones = currency.filter(zone => zone.toLowerCase().includes(searchValue));

        $('#dropdowncurrency').empty(); // Clear previous results

        if (searchValue && filteredTimezones.length) {
            $('#dropdowncurrency').addClass('active');
            filteredTimezones.forEach(function (zone) {
                $('#dropdowncurrency').append('<div class="dropdown-currency">' + zone + '</div>');
            });
        } else {
            $('#dropdowncurrency').removeClass('active');
        }
    });

    // Handle item selection
    $(document).on('click', '.dropdown-currency', function () {
        $('#currency').val($(this).text());
        $('#dropdowncurrency').removeClass('active');
    });

    // Close dropdown if clicked outside
    $(document).click(function (e) {
        if (!$(e.target).closest('#currency').length && !$(e.target).closest('#dropdowncurrency').length) {
            $('#dropdowncurrency').removeClass('active');
        }
    });
});
</script> 
<script type="text/javascript">
		$("#selOperatingCountry,#selTimezone,#selCurrencyCode").select2();
		$(document).on('select2:open', () => {
			document.querySelector('.select2-search__field').focus();
		});
		$("#selAdvanceBookingFilterMinType").val('hours');
		$("#selAdvanceBookingFilterMaxType").val('years');
		$("#txtCancelBookingRestrictType").val('hours');
			</script> 
 
        
