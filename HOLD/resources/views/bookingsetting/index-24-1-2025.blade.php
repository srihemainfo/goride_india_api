@extends('dashboard-layout.index')

@section('content')

  <style>
.x_panel{
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 1px 12px rgba(0, 0, 0, .15);

}
  .nav-tabs{

      border:none;

  }

        .dropdown-list {

            max-height: 200px;

            overflow-y: auto;

            border: 1px solid #ccc;

            display: none;

            position: absolute;

            background-color: white;

            width: 93%;

            z-index: 1000;

        }

        .dropdown-list.active {

            display: block;

        }

        .dropdown-item {

            padding: 8px;

            cursor: pointer;

        }

        .dropdown-item:hover {

            background-color: #f0f0f0;

        }

        [id="dropdown"] {

            width: 94% !important;

        }

        .form-select{

          

            color:#000;

        }

        .arrow-none{

              background-image:none !important;

        }

        .form-control{

            color:#000;

        }

    </style>

<!-- @include('customers.partials.filter') -->

<div class="col-sm-9 mx-4">

<div class="right_col" role="main">

                    <div class="x_panel">

                        <div class="x_title">

                            <h2>Booking Settings</h2>

                        </div>

                        <div class="x_content">

                            <div class="x_content-container">

                                <form id="formSettingsSocialMedia" class="form-horizontal" method="post" data-parsley-validate>

                                    <div class="row mb-3">

                                        <label for="" class="col-form-label col-md-5 col-sm-5">Operating country:</label>

                                        <div class="col-md-7 col-sm-7">

                                            <input class="form-control " type="text" name="country" id="country" placeholder="Enter Country (India)" autocomplete="off">

                                            <div id="dropdown" class="dropdown-list form-select arrow-none"></div>

                                        </div>

                                    </div>

                                    



                                    <!--<div class="row mb-3">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Operating country</label>-->

                                    <!--    <div class="col-md-7 col-sm-7">-->

                                            

                                    <!--        <select name="country" id="country" class="form-select">-->

                                    <!--            <option value>Select Country</option>-->

                                    <!--            <option value="Afghanistan">Afghanistan</option>-->

                                    <!--            <option value="Aland Islands">Aland Islands</option>-->

                                    <!--            <option value="Albania">Albania</option>-->

                                    <!--            <option value="Algeria">Algeria</option>-->

                                    <!--            <option value="American Samoa">American Samoa</option>-->

                                    <!--            <option value="Andorra">Andorra</option>-->

                                    <!--            <option value="Angola">Angola</option>-->

                                    <!--            <option value="Anguilla">Anguilla</option>-->

                                    <!--            <option value="Antarctica">Antarctica</option>-->

                                    <!--            <option value="Antigua and Barbuda">Antigua and Barbuda</option>-->

                                    <!--            <option value="Argentina">Argentina</option>-->

                                    <!--            <option value="Armenia">Armenia</option>-->

                                    <!--            <option value="Aruba">Aruba</option>-->

                                    <!--            <option value="Australia">Australia</option>-->

                                    <!--            <option value="Austria">Austria</option>-->

                                    <!--            <option value="Azerbaijan">Azerbaijan</option>-->

                                    <!--            <option value="Bahamas">Bahamas</option>-->

                                    <!--            <option value="Bahrain">Bahrain</option>-->

                                    <!--            <option value="Bangladesh">Bangladesh</option>-->

                                    <!--            <option value="Barbados">Barbados</option>-->

                                    <!--            <option value="Belarus">Belarus</option>-->

                                    <!--            <option value="Belgium">Belgium</option>-->

                                    <!--            <option value="Belize">Belize</option>-->

                                    <!--            <option value="Benin">Benin</option>-->

                                    <!--            <option value="Bermuda">Bermuda</option>-->

                                    <!--            <option value="Bhutan">Bhutan</option>-->

                                    <!--            <option value="Bolivia">Bolivia</option>-->

                                    <!--            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>-->

                                    <!--            <option value="Botswana">Botswana</option>-->

                                    <!--            <option value="Bouvet Island">Bouvet Island</option>-->

                                    <!--            <option value="Brazil">Brazil</option>-->

                                    <!--            <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>-->

                                    <!--            <option value="British Virgin Islands">British Virgin Islands</option>-->

                                    <!--            <option value="Brunei">Brunei</option>-->

                                    <!--            <option value="Bulgaria">Bulgaria</option>-->

                                    <!--            <option value="Burkina Faso">Burkina Faso</option>-->

                                    <!--            <option value="Burundi">Burundi</option>-->

                                    <!--            <option value="Cambodia">Cambodia</option>-->

                                    <!--            <option value="Cameroon">Cameroon</option>-->

                                    <!--            <option value="Canada">Canada</option>-->

                                    <!--            <option value="CapeVerde">CapeVerde</option>-->

                                    <!--            <option value="Caribbean Netherlands">Caribbean Netherlands</option>-->

                                    <!--            <option value="Cayman Islands">Cayman Islands</option>-->

                                    <!--            <option value="Central African Republic">Central African Republic</option>-->

                                    <!--            <option value="Chad">Chad</option>-->

                                    <!--            <option value="Chile">Chile</option>-->

                                    <!--            <option value="China">China</option>-->

                                    <!--            <option value="ChristmasIsland">ChristmasIsland</option>-->

                                    <!--            <option value="Cocos(Keeling)Islands">Cocos(Keeling)Islands</option>-->

                                    <!--            <option value="Colombia">Colombia</option>-->

                                    <!--            <option value="Comoros">Comoros</option>-->

                                    <!--            <option value="Cook Islands">Cook Islands</option>-->

                                    <!--            <option value="CostaRica">CostaRica</option>-->

                                    <!--            <option value="Croatia">Croatia</option>-->

                                    <!--            <option value="Cuba">Cuba</option>-->

                                    <!--            <option value="Curacao">Curacao</option>-->

                                    <!--            <option value="Cyprus">Cyprus</option>-->

                                    <!--            <option value="Czech Republic">Czech Republic</option>-->

                                    <!--            <option value="Democratic Republic of the Congo">Democratic Republic of the Congo</option>-->

                                    <!--            <option value="Denmark">Denmark</option>-->

                                    <!--            <option value="Djibouti">Djibouti</option>-->

                                    <!--            <option value="Dominica">Dominica</option>-->

                                    <!--            <option value="Dominican Republic">Dominican Republic</option>-->

                                    <!--            <option value="East Timor">East Timor</option>-->

                                    <!--            <option value="Ecuador">Ecuador</option>-->

                                    <!--            <option value="Egypt">Egypt</option>-->

                                    <!--            <option value="El Salvador">El Salvador</option>-->

                                    <!--            <option value="Equatorial Guinea">Equatorial Guinea</option>-->

                                    <!--            <option value="Eritrea">Eritrea</option>-->

                                    <!--            <option value="Estonia">Estonia</option>-->

                                    <!--            <option value="Ethiopia">Ethiopia</option>-->

                                    <!--            <option value="Falkland Islands (Islas Malvinas)">Falkland Islands (Islas Malvinas)</option>-->

                                    <!--            <option value="Faroe Islands">Faroe Islands</option>-->

                                    <!--            <option value="Federated States of Micronesia">Federated States of Micronesia</option>-->

                                    <!--            <option value="Fiji">Fiji</option>-->

                                    <!--            <option value="Finland">Finland</option>-->

                                    <!--            <option value="France">France</option>-->

                                    <!--            <option value="French Guiana">French Guiana</option>-->

                                    <!--            <option value="French Polynesia">French Polynesia</option>-->

                                    <!--            <option value="French Southern and Antarctic Lands">French Southern and Antarctic Lands</option>-->

                                    <!--            <option value="Gabon">Gabon</option>-->

                                    <!--            <option value="Gambia">Gambia</option>-->

                                    <!--            <option value="Georgia">Georgia</option>-->

                                    <!--            <option value="Germany">Germany</option>-->

                                    <!--            <option value="Ghana">Ghana</option>-->

                                    <!--            <option value="Gibraltar">Gibraltar</option>-->

                                    <!--            <option value="Greece">Greece</option>-->

                                    <!--            <option value="Greenland">Greenland</option>-->

                                    <!--            <option value="Grenada">Grenada</option>-->

                                    <!--            <option value="Guadeloupe">Guadeloupe</option>-->

                                    <!--            <option value="Guam">Guam</option>-->

                                    <!--            <option value="Guatemala">Guatemala</option>-->

                                    <!--            <option value="Guernsey">Guernsey</option>-->

                                    <!--            <option value="Guinea">Guinea</option>-->

                                    <!--            <option value="Guinea-Bissau">Guinea-Bissau</option>-->

                                    <!--            <option value="Guyana">Guyana</option>-->

                                    <!--            <option value="Haiti">Haiti</option>-->

                                    <!--            <option value="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>-->

                                    <!--            <option value="Honduras">Honduras</option>-->

                                    <!--            <option value="HongKong">HongKong</option>-->

                                    <!--            <option value="Hungary">Hungary</option>-->

                                    <!--            <option value="Iceland">Iceland</option>-->

                                    <!--            <option value="India">India</option>-->

                                    <!--            <option value="Indonesia">Indonesia</option>-->

                                    <!--            <option value="Iran">Iran</option>-->

                                    <!--            <option value="Iraq">Iraq</option>-->

                                    <!--            <option value="Ireland">Ireland</option>-->

                                    <!--            <option value="Isle of Man">Isle of Man</option>-->

                                    <!--            <option value="Israel">Israel</option>-->

                                    <!--            <option value="Italy">Italy</option>-->

                                    <!--            <option value="Ivory Coast">Ivory Coast</option>-->

                                    <!--            <option value="Jamaica">Jamaica</option>-->

                                    <!--            <option value="Japan">Japan</option>-->

                                    <!--            <option value="Jersey">Jersey</option>-->

                                    <!--            <option value="Jordan">Jordan</option>-->

                                    <!--            <option value="Kazakhstan">Kazakhstan</option>-->

                                    <!--            <option value="Kenya">Kenya</option>-->

                                    <!--            <option value="Kiribati">Kiribati</option>-->

                                    <!--            <option value="Kuwait">Kuwait</option>-->

                                    <!--            <option value="Kyrgyzstan">Kyrgyzstan</option>-->

                                    <!--            <option value="Laos">Laos</option>-->

                                    <!--            <option value="Latvia">Latvia</option>-->

                                    <!--            <option value="Lebanon">Lebanon</option>-->

                                    <!--            <option value="Lesotho">Lesotho</option>-->

                                    <!--            <option value="Liberia">Liberia</option>-->

                                    <!--            <option value="Libya">Libya</option>-->

                                    <!--            <option value="Liechtenstein">Liechtenstein</option>-->

                                    <!--            <option value="Lithuania">Lithuania</option>-->

                                    <!--            <option value="Luxembourg">Luxembourg</option>-->

                                    <!--            <option value="Macau">Macau</option>-->

                                    <!--            <option value="Macedonia">Macedonia</option>-->

                                    <!--            <option value="Madagascar">Madagascar</option>-->

                                    <!--            <option value="Malawi">Malawi</option>-->

                                    <!--            <option value="Malaysia">Malaysia</option>-->

                                    <!--            <option value="Maldives">Maldives</option>-->

                                    <!--            <option value="Mali">Mali</option>-->

                                    <!--            <option value="Malta">Malta</option>-->

                                    <!--            <option value="Marshall Islands">Marshall Islands</option>-->

                                    <!--            <option value="Martinique">Martinique</option>-->

                                    <!--            <option value="Mauritania">Mauritania</option>-->

                                    <!--            <option value="Mauritius">Mauritius</option>-->

                                    <!--            <option value="Mayotte">Mayotte</option>-->

                                    <!--            <option value="Mexico">Mexico</option>-->

                                    <!--            <option value="Monaco">Monaco</option>-->

                                    <!--            <option value="Mongolia">Mongolia</option>-->

                                    <!--            <option value="Montenegro">Montenegro</option>-->

                                    <!--            <option value="Montserrat">Montserrat</option>-->

                                    <!--            <option value="Morocco">Morocco</option>-->

                                    <!--            <option value="Mozambique">Mozambique</option>-->

                                    <!--            <option value="Myanmar">Myanmar</option>-->

                                    <!--            <option value="Namibia">Namibia</option>-->

                                    <!--            <option value="Nauru">Nauru</option>-->

                                    <!--            <option value="Nepal">Nepal</option>-->

                                    <!--            <option value="Netherlands">Netherlands</option>-->

                                    <!--            <option value="New Caledonia">New Caledonia</option>-->

                                    <!--            <option value="New Zealand">New Zealand</option>-->

                                    <!--            <option value="Nicaragua">Nicaragua</option>-->

                                    <!--            <option value="Niger">Niger</option>-->

                                    <!--            <option value="Nigeria">Nigeria</option>-->

                                    <!--            <option value="Niue">Niue</option>-->

                                    <!--            <option value="Norfolk Island">Norfolk Island</option>-->

                                    <!--            <option value="North Korea">North Korea</option>-->

                                    <!--            <option value="Northern Mariana Islands">Northern Mariana Islands</option>-->

                                    <!--            <option value="Norway">Norway</option>-->

                                    <!--            <option value="Oman">Oman</option>-->

                                    <!--            <option value="Pakistan">Pakistan</option>-->

                                    <!--            <option value="Palau">Palau</option>-->

                                    <!--            <option value="PalestinianTerritory,Occupied">PalestinianTerritory,Occupied</option>-->

                                    <!--            <option value="Panama">Panama</option>-->

                                    <!--            <option value="Papua New Guinea">Papua New Guinea</option>-->

                                    <!--            <option value="Paraguay">Paraguay</option>-->

                                    <!--            <option value="Peru">Peru</option>-->

                                    <!--            <option value="Philippines">Philippines</option>-->

                                    <!--            <option value="Pitcairn">Pitcairn</option>-->

                                    <!--            <option value="Poland">Poland</option>-->

                                    <!--            <option value="Portugal">Portugal</option>-->

                                    <!--            <option value="Puerto Rico">Puerto Rico</option>-->

                                    <!--            <option value="Qatar">Qatar</option>-->

                                    <!--            <option value="Republic of Moldova">Republic of Moldova</option>-->

                                    <!--            <option value="Republic of the Congo">Republic of the Congo</option>-->

                                    <!--            <option value="Reunion">Reunion</option>-->

                                    <!--            <option value="Romania">Romania</option>-->

                                    <!--            <option value="Russia">Russia</option>-->

                                    <!--            <option value="Rwanda">Rwanda</option>-->

                                    <!--            <option value="Saint Barthelemy">Saint Barthelemy</option>-->

                                    <!--            <option value="Saint Helena">Saint Helena</option>-->

                                    <!--            <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>-->

                                    <!--            <option value="Saint Lucia">Saint Lucia</option>-->

                                    <!--            <option value="Saint Martin(Frenchpart)">Saint Martin(Frenchpart)</option>-->

                                    <!--            <option value="Saint Pierreand Miquelon">Saint Pierreand Miquelon</option>-->

                                    <!--            <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>-->

                                    <!--            <option value="Samoa">Samoa</option>-->

                                    <!--            <option value="San Marino">San Marino</option>-->

                                    <!--            <option value="Sao Tome and Principe">Sao Tome and Principe</option>-->

                                    <!--            <option value="Saudi Arabia">Saudi Arabia</option>-->

                                    <!--            <option value="Senegal">Senegal</option>-->

                                    <!--            <option value="Serbia">Serbia</option>-->

                                    <!--            <option value="Seychelles">Seychelles</option>-->

                                    <!--            <option value="Sierra Leone">Sierra Leone</option>-->

                                    <!--            <option value="Singapore">Singapore</option>-->

                                    <!--            <option value="Sint Maarten">Sint Maarten</option>-->

                                    <!--            <option value="Slovakia">Slovakia</option>-->

                                    <!--            <option value="Slovenia">Slovenia</option>-->

                                    <!--            <option value="Solomon Islands">Solomon Islands</option>-->

                                    <!--            <option value="Somalia">Somalia</option>-->

                                    <!--            <option value="South Africa">South Africa</option>-->

                                    <!--            <option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>-->

                                    <!--            <option value="South Korea">South Korea</option>-->

                                    <!--            <option value="Spain">Spain</option>-->

                                    <!--            <option value="SriLanka">SriLanka</option>-->

                                    <!--            <option value="Sudan">Sudan</option>-->

                                    <!--            <option value="Suriname">Suriname</option>-->

                                    <!--            <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>-->

                                    <!--            <option value="Swaziland">Swaziland</option>-->

                                    <!--            <option value="Sweden">Sweden</option>-->

                                    <!--            <option value="Switzerland">Switzerland</option>-->

                                    <!--            <option value="Syrian Arab Republic">Syrian Arab Republic</option>-->

                                    <!--            <option value="Taiwan">Taiwan</option>-->

                                    <!--            <option value="Tajikistan">Tajikistan</option>-->

                                    <!--            <option value="Tanzania">Tanzania</option>-->

                                    <!--            <option value="Thailand">Thailand</option>-->

                                    <!--            <option value="Togo">Togo</option>-->

                                    <!--            <option value="Tokelau">Tokelau</option>-->

                                    <!--            <option value="Tonga">Tonga</option>-->

                                    <!--            <option value="Trinidad and Tobago">Trinidad and Tobago</option>-->

                                    <!--            <option value="Tunisia">Tunisia</option>-->

                                    <!--            <option value="Turkey">Turkey</option>-->

                                    <!--            <option value="Turkmenistan">Turkmenistan</option>-->

                                    <!--            <option value="Turksand Caicos Islands">Turksand Caicos Islands</option>-->

                                    <!--            <option value="Tuvalu">Tuvalu</option>-->

                                    <!--            <option value="Uganda">Uganda</option>-->

                                    <!--            <option value="Ukraine">Ukraine</option>-->

                                    <!--            <option value="United Arab Emirates">United Arab Emirates</option>-->

                                    <!--            <option value="United Kingdom" selected>United Kingdom</option>-->

                                    <!--            <option value="United States">United States</option>-->

                                    <!--            <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>-->

                                    <!--            <option value="United States Virgin Islands">United States Virgin Islands</option>-->

                                    <!--            <option value="Uruguay">Uruguay</option>-->

                                    <!--            <option value="Uzbekistan">Uzbekistan</option>-->

                                    <!--            <option value="Vanuatu">Vanuatu</option>-->

                                    <!--            <option value="VaticanCity">VaticanCity</option>-->

                                    <!--            <option value="Venezuela">Venezuela</option>-->

                                    <!--            <option value="Vietnam">Vietnam</option>-->

                                    <!--            <option value="WallisandFutuna">WallisandFutuna</option>-->

                                    <!--            <option value="WesternSahara">WesternSahara</option>-->

                                    <!--            <option value="Yemen">Yemen</option>-->

                                    <!--            <option value="Zambia">Zambia</option>-->

                                    <!--            <option value="Zimbabwe">Zimbabwe</option>-->

                                    <!--        </select>-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    

                                    <div class="row mb-3">

    <label for="" class="col-form-label col-md-5 col-sm-5">Timezone</label>

    <div class="col-md-7 col-sm-7">

        <input class="form-control " type="text" id="timezone" name="timezone" placeholder="Enter TimeZone Africa/Abidjan" autocomplete="off">

        <div id="dropdowntimezone" class="dropdown-list form-select arrow-none"></div>

    </div>

</div>



                                        <!--<label class="col-form-label col-md-5 col-sm-5">Timezone</label>-->

                                        <!--<div class="col-md-7 col-sm-7">-->

                                        <!--    <select name="timezone" id="timezone" class="form-select">-->

                                        <!--        <option value>Select Timezone</option>-->

                                        <!--        <option value="Africa/Abidjan">Africa/Abidjan</option>-->

                                        <!--        <option value="Africa/Accra">Africa/Accra</option>-->

                                        <!--        <option value="Africa/Addis_Ababa">Africa/Addis_Ababa</option>-->

                                        <!--        <option value="Africa/Algiers">Africa/Algiers</option>-->

                                        <!--        <option value="Africa/Asmara">Africa/Asmara</option>-->

                                        <!--        <option value="Africa/Bamako">Africa/Bamako</option>-->

                                        <!--        <option value="Africa/Bangui">Africa/Bangui</option>-->

                                        <!--        <option value="Africa/Banjul">Africa/Banjul</option>-->

                                        <!--        <option value="Africa/Bissau">Africa/Bissau</option>-->

                                        <!--        <option value="Africa/Blantyre">Africa/Blantyre</option>-->

                                        <!--        <option value="Africa/Brazzaville">Africa/Brazzaville</option>-->

                                        <!--        <option value="Africa/Bujumbura">Africa/Bujumbura</option>-->

                                        <!--        <option value="Africa/Cairo">Africa/Cairo</option>-->

                                        <!--        <option value="Africa/Casablanca">Africa/Casablanca</option>-->

                                        <!--        <option value="Africa/Ceuta">Africa/Ceuta</option>-->

                                        <!--        <option value="Africa/Conakry">Africa/Conakry</option>-->

                                        <!--        <option value="Africa/Dakar">Africa/Dakar</option>-->

                                        <!--        <option value="Africa/Dar_es_Salaam">Africa/Dar_es_Salaam</option>-->

                                        <!--        <option value="Africa/Djibouti">Africa/Djibouti</option>-->

                                        <!--        <option value="Africa/Douala">Africa/Douala</option>-->

                                        <!--        <option value="Africa/El_Aaiun">Africa/El_Aaiun</option>-->

                                        <!--        <option value="Africa/Freetown">Africa/Freetown</option>-->

                                        <!--        <option value="Africa/Gaborone">Africa/Gaborone</option>-->

                                        <!--        <option value="Africa/Harare">Africa/Harare</option>-->

                                        <!--        <option value="Africa/Johannesburg">Africa/Johannesburg</option>-->

                                        <!--        <option value="Africa/Juba">Africa/Juba</option>-->

                                        <!--        <option value="Africa/Kampala">Africa/Kampala</option>-->

                                        <!--        <option value="Africa/Khartoum">Africa/Khartoum</option>-->

                                        <!--        <option value="Africa/Kigali">Africa/Kigali</option>-->

                                        <!--        <option value="Africa/Kinshasa">Africa/Kinshasa</option>-->

                                        <!--        <option value="Africa/Lagos">Africa/Lagos</option>-->

                                        <!--        <option value="Africa/Libreville">Africa/Libreville</option>-->

                                        <!--        <option value="Africa/Lome">Africa/Lome</option>-->

                                        <!--        <option value="Africa/Luanda">Africa/Luanda</option>-->

                                        <!--        <option value="Africa/Lubumbashi">Africa/Lubumbashi</option>-->

                                        <!--        <option value="Africa/Lusaka">Africa/Lusaka</option>-->

                                        <!--        <option value="Africa/Malabo">Africa/Malabo</option>-->

                                        <!--        <option value="Africa/Maputo">Africa/Maputo</option>-->

                                        <!--        <option value="Africa/Maseru">Africa/Maseru</option>-->

                                        <!--        <option value="Africa/Mbabane">Africa/Mbabane</option>-->

                                        <!--        <option value="Africa/Mogadishu">Africa/Mogadishu</option>-->

                                        <!--        <option value="Africa/Monrovia">Africa/Monrovia</option>-->

                                        <!--        <option value="Africa/Nairobi">Africa/Nairobi</option>-->

                                        <!--        <option value="Africa/Ndjamena">Africa/Ndjamena</option>-->

                                        <!--        <option value="Africa/Niamey">Africa/Niamey</option>-->

                                        <!--        <option value="Africa/Nouakchott">Africa/Nouakchott</option>-->

                                        <!--        <option value="Africa/Ouagadougou">Africa/Ouagadougou</option>-->

                                        <!--        <option value="Africa/Porto-Novo">Africa/Porto-Novo</option>-->

                                        <!--        <option value="Africa/Sao_Tome">Africa/Sao_Tome</option>-->

                                        <!--        <option value="Africa/Tripoli">Africa/Tripoli</option>-->

                                        <!--        <option value="Africa/Tunis">Africa/Tunis</option>-->

                                        <!--        <option value="Africa/Windhoek">Africa/Windhoek</option>-->

                                        <!--        <option value="America/Adak">America/Adak</option>-->

                                        <!--        <option value="America/Anchorage">America/Anchorage</option>-->

                                        <!--        <option value="America/Anguilla">America/Anguilla</option>-->

                                        <!--        <option value="America/Antigua">America/Antigua</option>-->

                                        <!--        <option value="America/Araguaina">America/Araguaina</option>-->

                                        <!--        <option value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires</option>-->

                                        <!--        <option value="America/Argentina/Catamarca">America/Argentina/Catamarca</option>-->

                                        <!--        <option value="America/Argentina/Cordoba">America/Argentina/Cordoba</option>-->

                                        <!--        <option value="America/Argentina/Jujuy">America/Argentina/Jujuy</option>-->

                                        <!--        <option value="America/Argentina/La_Rioja">America/Argentina/La_Rioja</option>-->

                                        <!--        <option value="America/Argentina/Mendoza">America/Argentina/Mendoza</option>-->

                                        <!--        <option value="America/Argentina/Rio_Gallegos">America/Argentina/Rio_Gallegos</option>-->

                                        <!--        <option value="America/Argentina/Salta">America/Argentina/Salta</option>-->

                                        <!--        <option value="America/Argentina/San_Juan">America/Argentina/San_Juan</option>-->

                                        <!--        <option value="America/Argentina/San_Luis">America/Argentina/San_Luis</option>-->

                                        <!--        <option value="America/Argentina/Tucuman">America/Argentina/Tucuman</option>-->

                                        <!--        <option value="America/Argentina/Ushuaia">America/Argentina/Ushuaia</option>-->

                                        <!--        <option value="America/Aruba">America/Aruba</option>-->

                                        <!--        <option value="America/Asuncion">America/Asuncion</option>-->

                                        <!--        <option value="America/Atikokan">America/Atikokan</option>-->

                                        <!--        <option value="America/Bahia">America/Bahia</option>-->

                                        <!--        <option value="America/Bahia_Banderas">America/Bahia_Banderas</option>-->

                                        <!--        <option value="America/Barbados">America/Barbados</option>-->

                                        <!--        <option value="America/Belem">America/Belem</option>-->

                                        <!--        <option value="America/Belize">America/Belize</option>-->

                                        <!--        <option value="America/Blanc-Sablon">America/Blanc-Sablon</option>-->

                                        <!--        <option value="America/Boa_Vista">America/Boa_Vista</option>-->

                                        <!--        <option value="America/Bogota">America/Bogota</option>-->

                                        <!--        <option value="America/Boise">America/Boise</option>-->

                                        <!--        <option value="America/Cambridge_Bay">America/Cambridge_Bay</option>-->

                                        <!--        <option value="America/Campo_Grande">America/Campo_Grande</option>-->

                                        <!--        <option value="America/Cancun">America/Cancun</option>-->

                                        <!--        <option value="America/Caracas">America/Caracas</option>-->

                                        <!--        <option value="America/Cayenne">America/Cayenne</option>-->

                                        <!--        <option value="America/Cayman">America/Cayman</option>-->

                                        <!--        <option value="America/Chicago">America/Chicago</option>-->

                                        <!--        <option value="America/Chihuahua">America/Chihuahua</option>-->

                                        <!--        <option value="America/Costa_Rica">America/Costa_Rica</option>-->

                                        <!--        <option value="America/Creston">America/Creston</option>-->

                                        <!--        <option value="America/Cuiaba">America/Cuiaba</option>-->

                                        <!--        <option value="America/Curacao">America/Curacao</option>-->

                                        <!--        <option value="America/Danmarkshavn">America/Danmarkshavn</option>-->

                                        <!--        <option value="America/Dawson">America/Dawson</option>-->

                                        <!--        <option value="America/Dawson_Creek">America/Dawson_Creek</option>-->

                                        <!--        <option value="America/Denver">America/Denver</option>-->

                                        <!--        <option value="America/Detroit">America/Detroit</option>-->

                                        <!--        <option value="America/Dominica">America/Dominica</option>-->

                                        <!--        <option value="America/Edmonton">America/Edmonton</option>-->

                                        <!--        <option value="America/Eirunepe">America/Eirunepe</option>-->

                                        <!--        <option value="America/El_Salvador">America/El_Salvador</option>-->

                                        <!--        <option value="America/Fortaleza">America/Fortaleza</option>-->

                                        <!--        <option value="America/Fort_Nelson">America/Fort_Nelson</option>-->

                                        <!--        <option value="America/Glace_Bay">America/Glace_Bay</option>-->

                                        <!--        <option value="America/Godthab">America/Godthab</option>-->

                                        <!--        <option value="America/Goose_Bay">America/Goose_Bay</option>-->

                                        <!--        <option value="America/Grand_Turk">America/Grand_Turk</option>-->

                                        <!--        <option value="America/Grenada">America/Grenada</option>-->

                                        <!--        <option value="America/Guadeloupe">America/Guadeloupe</option>-->

                                        <!--        <option value="America/Guatemala">America/Guatemala</option>-->

                                        <!--        <option value="America/Guayaquil">America/Guayaquil</option>-->

                                        <!--        <option value="America/Guyana">America/Guyana</option>-->

                                        <!--        <option value="America/Halifax">America/Halifax</option>-->

                                        <!--        <option value="America/Havana">America/Havana</option>-->

                                        <!--        <option value="America/Hermosillo">America/Hermosillo</option>-->

                                        <!--        <option value="America/Indiana/Indianapolis">America/Indiana/Indianapolis</option>-->

                                        <!--        <option value="America/Indiana/Knox">America/Indiana/Knox</option>-->

                                        <!--        <option value="America/Indiana/Marengo">America/Indiana/Marengo</option>-->

                                        <!--        <option value="America/Indiana/Petersburg">America/Indiana/Petersburg</option>-->

                                        <!--        <option value="America/Indiana/Tell_City">America/Indiana/Tell_City</option>-->

                                        <!--        <option value="America/Indiana/Vevay">America/Indiana/Vevay</option>-->

                                        <!--        <option value="America/Indiana/Vincennes">America/Indiana/Vincennes</option>-->

                                        <!--        <option value="America/Indiana/Winamac">America/Indiana/Winamac</option>-->

                                        <!--        <option value="America/Inuvik">America/Inuvik</option>-->

                                        <!--        <option value="America/Iqaluit">America/Iqaluit</option>-->

                                        <!--        <option value="America/Jamaica">America/Jamaica</option>-->

                                        <!--        <option value="America/Juneau">America/Juneau</option>-->

                                        <!--        <option value="America/Kentucky/Louisville">America/Kentucky/Louisville</option>-->

                                        <!--        <option value="America/Kentucky/Monticello">America/Kentucky/Monticello</option>-->

                                        <!--        <option value="America/Kralendijk">America/Kralendijk</option>-->

                                        <!--        <option value="America/La_Paz">America/La_Paz</option>-->

                                        <!--        <option value="America/Lima">America/Lima</option>-->

                                        <!--        <option value="America/Los_Angeles">America/Los_Angeles</option>-->

                                        <!--        <option value="America/Lower_Princes">America/Lower_Princes</option>-->

                                        <!--        <option value="America/Maceio">America/Maceio</option>-->

                                        <!--        <option value="America/Managua">America/Managua</option>-->

                                        <!--        <option value="America/Manaus">America/Manaus</option>-->

                                        <!--        <option value="America/Marigot">America/Marigot</option>-->

                                        <!--        <option value="America/Martinique">America/Martinique</option>-->

                                        <!--        <option value="America/Matamoros">America/Matamoros</option>-->

                                        <!--        <option value="America/Mazatlan">America/Mazatlan</option>-->

                                        <!--        <option value="America/Menominee">America/Menominee</option>-->

                                        <!--        <option value="America/Merida">America/Merida</option>-->

                                        <!--        <option value="America/Metlakatla">America/Metlakatla</option>-->

                                        <!--        <option value="America/Mexico_City">America/Mexico_City</option>-->

                                        <!--        <option value="America/Miquelon">America/Miquelon</option>-->

                                        <!--        <option value="America/Moncton">America/Moncton</option>-->

                                        <!--        <option value="America/Monterrey">America/Monterrey</option>-->

                                        <!--        <option value="America/Montevideo">America/Montevideo</option>-->

                                        <!--        <option value="America/Montserrat">America/Montserrat</option>-->

                                        <!--        <option value="America/Nassau">America/Nassau</option>-->

                                        <!--        <option value="America/New_York">America/New_York</option>-->

                                        <!--        <option value="America/Nipigon">America/Nipigon</option>-->

                                        <!--        <option value="America/Nome">America/Nome</option>-->

                                        <!--        <option value="America/Noronha">America/Noronha</option>-->

                                        <!--        <option value="America/North_Dakota/Beulah">America/North_Dakota/Beulah</option>-->

                                        <!--        <option value="America/North_Dakota/Center">America/North_Dakota/Center</option>-->

                                        <!--        <option value="America/North_Dakota/New_Salem">America/North_Dakota/New_Salem</option>-->

                                        <!--        <option value="America/Ojinaga">America/Ojinaga</option>-->

                                        <!--        <option value="America/Panama">America/Panama</option>-->

                                        <!--        <option value="America/Pangnirtung">America/Pangnirtung</option>-->

                                        <!--        <option value="America/Paramaribo">America/Paramaribo</option>-->

                                        <!--        <option value="America/Phoenix">America/Phoenix</option>-->

                                        <!--        <option value="America/Port-au-Prince">America/Port-au-Prince</option>-->

                                        <!--        <option value="America/Porto_Velho">America/Porto_Velho</option>-->

                                        <!--        <option value="America/Port_of_Spain">America/Port_of_Spain</option>-->

                                        <!--        <option value="America/Puerto_Rico">America/Puerto_Rico</option>-->

                                        <!--        <option value="America/Punta_Arenas">America/Punta_Arenas</option>-->

                                        <!--        <option value="America/Rainy_River">America/Rainy_River</option>-->

                                        <!--        <option value="America/Rankin_Inlet">America/Rankin_Inlet</option>-->

                                        <!--        <option value="America/Recife">America/Recife</option>-->

                                        <!--        <option value="America/Regina">America/Regina</option>-->

                                        <!--        <option value="America/Resolute">America/Resolute</option>-->

                                        <!--        <option value="America/Rio_Branco">America/Rio_Branco</option>-->

                                        <!--        <option value="America/Santarem">America/Santarem</option>-->

                                        <!--        <option value="America/Santiago">America/Santiago</option>-->

                                        <!--        <option value="America/Santo_Domingo">America/Santo_Domingo</option>-->

                                        <!--        <option value="America/Sao_Paulo">America/Sao_Paulo</option>-->

                                        <!--        <option value="America/Scoresbysund">America/Scoresbysund</option>-->

                                        <!--        <option value="America/Sitka">America/Sitka</option>-->

                                        <!--        <option value="America/St_Barthelemy">America/St_Barthelemy</option>-->

                                        <!--        <option value="America/St_Johns">America/St_Johns</option>-->

                                        <!--        <option value="America/St_Kitts">America/St_Kitts</option>-->

                                        <!--        <option value="America/St_Lucia">America/St_Lucia</option>-->

                                        <!--        <option value="America/St_Thomas">America/St_Thomas</option>-->

                                        <!--        <option value="America/St_Vincent">America/St_Vincent</option>-->

                                        <!--        <option value="America/Swift_Current">America/Swift_Current</option>-->

                                        <!--        <option value="America/Tegucigalpa">America/Tegucigalpa</option>-->

                                        <!--        <option value="America/Thule">America/Thule</option>-->

                                        <!--        <option value="America/Thunder_Bay">America/Thunder_Bay</option>-->

                                        <!--        <option value="America/Tijuana">America/Tijuana</option>-->

                                        <!--        <option value="America/Toronto">America/Toronto</option>-->

                                        <!--        <option value="America/Tortola">America/Tortola</option>-->

                                        <!--        <option value="America/Vancouver">America/Vancouver</option>-->

                                        <!--        <option value="America/Whitehorse">America/Whitehorse</option>-->

                                        <!--        <option value="America/Winnipeg">America/Winnipeg</option>-->

                                        <!--        <option value="America/Yakutat">America/Yakutat</option>-->

                                        <!--        <option value="America/Yellowknife">America/Yellowknife</option>-->

                                        <!--        <option value="Antarctica/Casey">Antarctica/Casey</option>-->

                                        <!--        <option value="Antarctica/Davis">Antarctica/Davis</option>-->

                                        <!--        <option value="Antarctica/DumontDUrville">Antarctica/DumontDUrville</option>-->

                                        <!--        <option value="Antarctica/Macquarie">Antarctica/Macquarie</option>-->

                                        <!--        <option value="Antarctica/Mawson">Antarctica/Mawson</option>-->

                                        <!--        <option value="Antarctica/McMurdo">Antarctica/McMurdo</option>-->

                                        <!--        <option value="Antarctica/Palmer">Antarctica/Palmer</option>-->

                                        <!--        <option value="Antarctica/Rothera">Antarctica/Rothera</option>-->

                                        <!--        <option value="Antarctica/Syowa">Antarctica/Syowa</option>-->

                                        <!--        <option value="Antarctica/Troll">Antarctica/Troll</option>-->

                                        <!--        <option value="Antarctica/Vostok">Antarctica/Vostok</option>-->

                                        <!--        <option value="Arctic/Longyearbyen">Arctic/Longyearbyen</option>-->

                                        <!--        <option value="Asia/Aden">Asia/Aden</option>-->

                                        <!--        <option value="Asia/Almaty">Asia/Almaty</option>-->

                                        <!--        <option value="Asia/Amman">Asia/Amman</option>-->

                                        <!--        <option value="Asia/Anadyr">Asia/Anadyr</option>-->

                                        <!--        <option value="Asia/Aqtau">Asia/Aqtau</option>-->

                                        <!--        <option value="Asia/Aqtobe">Asia/Aqtobe</option>-->

                                        <!--        <option value="Asia/Ashgabat">Asia/Ashgabat</option>-->

                                        <!--        <option value="Asia/Atyrau">Asia/Atyrau</option>-->

                                        <!--        <option value="Asia/Baghdad">Asia/Baghdad</option>-->

                                        <!--        <option value="Asia/Bahrain">Asia/Bahrain</option>-->

                                        <!--        <option value="Asia/Baku">Asia/Baku</option>-->

                                        <!--        <option value="Asia/Bangkok">Asia/Bangkok</option>-->

                                        <!--        <option value="Asia/Barnaul">Asia/Barnaul</option>-->

                                        <!--        <option value="Asia/Beirut">Asia/Beirut</option>-->

                                        <!--        <option value="Asia/Bishkek">Asia/Bishkek</option>-->

                                        <!--        <option value="Asia/Brunei">Asia/Brunei</option>-->

                                        <!--        <option value="Asia/Chita">Asia/Chita</option>-->

                                        <!--        <option value="Asia/Choibalsan">Asia/Choibalsan</option>-->

                                        <!--        <option value="Asia/Colombo">Asia/Colombo</option>-->

                                        <!--        <option value="Asia/Damascus">Asia/Damascus</option>-->

                                        <!--        <option value="Asia/Dhaka">Asia/Dhaka</option>-->

                                        <!--        <option value="Asia/Dili">Asia/Dili</option>-->

                                        <!--        <option value="Asia/Dubai">Asia/Dubai</option>-->

                                        <!--        <option value="Asia/Dushanbe">Asia/Dushanbe</option>-->

                                        <!--        <option value="Asia/Famagusta">Asia/Famagusta</option>-->

                                        <!--        <option value="Asia/Gaza">Asia/Gaza</option>-->

                                        <!--        <option value="Asia/Hebron">Asia/Hebron</option>-->

                                        <!--        <option value="Asia/Hong_Kong">Asia/Hong_Kong</option>-->

                                        <!--        <option value="Asia/Hovd">Asia/Hovd</option>-->

                                        <!--        <option value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh</option>-->

                                        <!--        <option value="Asia/Irkutsk">Asia/Irkutsk</option>-->

                                        <!--        <option value="Asia/Jakarta">Asia/Jakarta</option>-->

                                        <!--        <option value="Asia/Jayapura">Asia/Jayapura</option>-->

                                        <!--        <option value="Asia/Jerusalem">Asia/Jerusalem</option>-->

                                        <!--        <option value="Asia/Kabul">Asia/Kabul</option>-->

                                        <!--        <option value="Asia/Kamchatka">Asia/Kamchatka</option>-->

                                        <!--        <option value="Asia/Karachi">Asia/Karachi</option>-->

                                        <!--        <option value="Asia/Kathmandu">Asia/Kathmandu</option>-->

                                        <!--        <option value="Asia/Khandyga">Asia/Khandyga</option>-->

                                        <!--        <option value="Asia/Kolkata">Asia/Kolkata</option>-->

                                        <!--        <option value="Asia/Krasnoyarsk">Asia/Krasnoyarsk</option>-->

                                        <!--        <option value="Asia/Kuala_Lumpur">Asia/Kuala_Lumpur</option>-->

                                        <!--        <option value="Asia/Kuching">Asia/Kuching</option>-->

                                        <!--        <option value="Asia/Kuwait">Asia/Kuwait</option>-->

                                        <!--        <option value="Asia/Macau">Asia/Macau</option>-->

                                        <!--        <option value="Asia/Magadan">Asia/Magadan</option>-->

                                        <!--        <option value="Asia/Makassar">Asia/Makassar</option>-->

                                        <!--        <option value="Asia/Manila">Asia/Manila</option>-->

                                        <!--        <option value="Asia/Muscat">Asia/Muscat</option>-->

                                        <!--        <option value="Asia/Nicosia">Asia/Nicosia</option>-->

                                        <!--        <option value="Asia/Novokuznetsk">Asia/Novokuznetsk</option>-->

                                        <!--        <option value="Asia/Novosibirsk">Asia/Novosibirsk</option>-->

                                        <!--        <option value="Asia/Omsk">Asia/Omsk</option>-->

                                        <!--        <option value="Asia/Oral">Asia/Oral</option>-->

                                        <!--        <option value="Asia/Phnom_Penh">Asia/Phnom_Penh</option>-->

                                        <!--        <option value="Asia/Pontianak">Asia/Pontianak</option>-->

                                        <!--        <option value="Asia/Pyongyang">Asia/Pyongyang</option>-->

                                        <!--        <option value="Asia/Qatar">Asia/Qatar</option>-->

                                        <!--        <option value="Asia/Qyzylorda">Asia/Qyzylorda</option>-->

                                        <!--        <option value="Asia/Riyadh">Asia/Riyadh</option>-->

                                        <!--        <option value="Asia/Sakhalin">Asia/Sakhalin</option>-->

                                        <!--        <option value="Asia/Samarkand">Asia/Samarkand</option>-->

                                        <!--        <option value="Asia/Seoul">Asia/Seoul</option>-->

                                        <!--        <option value="Asia/Shanghai">Asia/Shanghai</option>-->

                                        <!--        <option value="Asia/Singapore">Asia/Singapore</option>-->

                                        <!--        <option value="Asia/Srednekolymsk">Asia/Srednekolymsk</option>-->

                                        <!--        <option value="Asia/Taipei">Asia/Taipei</option>-->

                                        <!--        <option value="Asia/Tashkent">Asia/Tashkent</option>-->

                                        <!--        <option value="Asia/Tbilisi">Asia/Tbilisi</option>-->

                                        <!--        <option value="Asia/Tehran">Asia/Tehran</option>-->

                                        <!--        <option value="Asia/Thimphu">Asia/Thimphu</option>-->

                                        <!--        <option value="Asia/Tokyo">Asia/Tokyo</option>-->

                                        <!--        <option value="Asia/Tomsk">Asia/Tomsk</option>-->

                                        <!--        <option value="Asia/Ulaanbaatar">Asia/Ulaanbaatar</option>-->

                                        <!--        <option value="Asia/Urumqi">Asia/Urumqi</option>-->

                                        <!--        <option value="Asia/Ust-Nera">Asia/Ust-Nera</option>-->

                                        <!--        <option value="Asia/Vientiane">Asia/Vientiane</option>-->

                                        <!--        <option value="Asia/Vladivostok">Asia/Vladivostok</option>-->

                                        <!--        <option value="Asia/Yakutsk">Asia/Yakutsk</option>-->

                                        <!--        <option value="Asia/Yangon">Asia/Yangon</option>-->

                                        <!--        <option value="Asia/Yekaterinburg">Asia/Yekaterinburg</option>-->

                                        <!--        <option value="Asia/Yerevan">Asia/Yerevan</option>-->

                                        <!--        <option value="Atlantic/Azores">Atlantic/Azores</option>-->

                                        <!--        <option value="Atlantic/Bermuda">Atlantic/Bermuda</option>-->

                                        <!--        <option value="Atlantic/Canary">Atlantic/Canary</option>-->

                                        <!--        <option value="Atlantic/Cape_Verde">Atlantic/Cape_Verde</option>-->

                                        <!--        <option value="Atlantic/Faroe">Atlantic/Faroe</option>-->

                                        <!--        <option value="Atlantic/Madeira">Atlantic/Madeira</option>-->

                                            <!--        <option value="Atlantic/Reykjavik">Atlantic/Reykjavik</option>-->

                                        <!--        <option value="Atlantic/South_Georgia">Atlantic/South_Georgia</option>-->

                                        <!--        <option value="Atlantic/Stanley">Atlantic/Stanley</option>-->

                                        <!--        <option value="Atlantic/St_Helena">Atlantic/St_Helena</option>-->

                                        <!--        <option value="Australia/Adelaide">Australia/Adelaide</option>-->

                                        <!--        <option value="Australia/Brisbane">Australia/Brisbane</option>-->

                                        <!--        <option value="Australia/Broken_Hill">Australia/Broken_Hill</option>-->

                                        <!--        <option value="Australia/Currie">Australia/Currie</option>-->

                                        <!--        <option value="Australia/Darwin">Australia/Darwin</option>-->

                                        <!--        <option value="Australia/Eucla">Australia/Eucla</option>-->

                                        <!--        <option value="Australia/Hobart">Australia/Hobart</option>-->

                                        <!--        <option value="Australia/Lindeman">Australia/Lindeman</option>-->

                                        <!--        <option value="Australia/Lord_Howe">Australia/Lord_Howe</option>-->

                                        <!--        <option value="Australia/Melbourne">Australia/Melbourne</option>-->

                                        <!--        <option value="Australia/Perth">Australia/Perth</option>-->

                                        <!--        <option value="Australia/Sydney">Australia/Sydney</option>-->

                                        <!--        <option value="Europe/Amsterdam">Europe/Amsterdam</option>-->

                                        <!--        <option value="Europe/Andorra">Europe/Andorra</option>-->

                                        <!--        <option value="Europe/Astrakhan">Europe/Astrakhan</option>-->

                                        <!--        <option value="Europe/Athens">Europe/Athens</option>-->

                                        <!--        <option value="Europe/Belgrade">Europe/Belgrade</option>-->

                                        <!--        <option value="Europe/Berlin">Europe/Berlin</option>-->

                                        <!--        <option value="Europe/Bratislava">Europe/Bratislava</option>-->

                                        <!--        <option value="Europe/Brussels">Europe/Brussels</option>-->

                                        <!--        <option value="Europe/Bucharest">Europe/Bucharest</option>-->

                                        <!--        <option value="Europe/Budapest">Europe/Budapest</option>-->

                                        <!--        <option value="Europe/Busingen">Europe/Busingen</option>-->

                                        <!--        <option value="Europe/Chisinau">Europe/Chisinau</option>-->

                                        <!--        <option value="Europe/Copenhagen">Europe/Copenhagen</option>-->

                                        <!--        <option value="Europe/Dublin">Europe/Dublin</option>-->

                                        <!--        <option value="Europe/Gibraltar">Europe/Gibraltar</option>-->

                                        <!--        <option value="Europe/Guernsey">Europe/Guernsey</option>-->

                                        <!--        <option value="Europe/Helsinki">Europe/Helsinki</option>-->

                                        <!--        <option value="Europe/Isle_of_Man">Europe/Isle_of_Man</option>-->

                                        <!--        <option value="Europe/Istanbul">Europe/Istanbul</option>-->

                                        <!--        <option value="Europe/Jersey">Europe/Jersey</option>-->

                                        <!--        <option value="Europe/Kaliningrad">Europe/Kaliningrad</option>-->

                                        <!--        <option value="Europe/Kiev">Europe/Kiev</option>-->

                                        <!--        <option value="Europe/Kirov">Europe/Kirov</option>-->

                                        <!--        <option value="Europe/Lisbon">Europe/Lisbon</option>-->

                                        <!--        <option value="Europe/Ljubljana">Europe/Ljubljana</option>-->

                                        <!--        <option value="Europe/London" selected>Europe/London</option>-->

                                        <!--        <option value="Europe/Luxembourg">Europe/Luxembourg</option>-->

                                        <!--        <option value="Europe/Madrid">Europe/Madrid</option>-->

                                        <!--        <option value="Europe/Malta">Europe/Malta</option>-->

                                        <!--        <option value="Europe/Mariehamn">Europe/Mariehamn</option>-->

                                        <!--        <option value="Europe/Minsk">Europe/Minsk</option>-->

                                        <!--        <option value="Europe/Monaco">Europe/Monaco</option>-->

                                        <!--        <option value="Europe/Moscow">Europe/Moscow</option>-->

                                        <!--        <option value="Europe/Oslo">Europe/Oslo</option>-->

                                        <!--        <option value="Europe/Paris">Europe/Paris</option>-->

                                        <!--        <option value="Europe/Podgorica">Europe/Podgorica</option>-->

                                        <!--        <option value="Europe/Prague">Europe/Prague</option>-->

                                        <!--        <option value="Europe/Riga">Europe/Riga</option>-->

                                        <!--        <option value="Europe/Rome">Europe/Rome</option>-->

                                        <!--        <option value="Europe/Samara">Europe/Samara</option>-->

                                        <!--        <option value="Europe/San_Marino">Europe/San_Marino</option>-->

                                        <!--        <option value="Europe/Sarajevo">Europe/Sarajevo</option>-->

                                        <!--        <option value="Europe/Saratov">Europe/Saratov</option>-->

                                        <!--        <option value="Europe/Simferopol">Europe/Simferopol</option>-->

                                        <!--        <option value="Europe/Skopje">Europe/Skopje</option>-->

                                        <!--        <option value="Europe/Sofia">Europe/Sofia</option>-->

                                        <!--        <option value="Europe/Stockholm">Europe/Stockholm</option>-->

                                        <!--        <option value="Europe/Tallinn">Europe/Tallinn</option>-->

                                        <!--        <option value="Europe/Tirane">Europe/Tirane</option>-->

                                        <!--        <option value="Europe/Ulyanovsk">Europe/Ulyanovsk</option>-->

                                        <!--        <option value="Europe/Uzhgorod">Europe/Uzhgorod</option>-->

                                        <!--        <option value="Europe/Vaduz">Europe/Vaduz</option>-->

                                        <!--        <option value="Europe/Vatican">Europe/Vatican</option>-->

                                        <!--        <option value="Europe/Vienna">Europe/Vienna</option>-->

                                        <!--        <option value="Europe/Vilnius">Europe/Vilnius</option>-->

                                        <!--        <option value="Europe/Volgograd">Europe/Volgograd</option>-->

                                        <!--        <option value="Europe/Warsaw">Europe/Warsaw</option>-->

                                        <!--        <option value="Europe/Zagreb">Europe/Zagreb</option>-->

                                        <!--        <option value="Europe/Zaporozhye">Europe/Zaporozhye</option>-->

                                        <!--        <option value="Europe/Zurich">Europe/Zurich</option>-->

                                        <!--        <option value="Indian/Antananarivo">Indian/Antananarivo</option>-->

                                        <!--        <option value="Indian/Chagos">Indian/Chagos</option>-->

                                        <!--        <option value="Indian/Christmas">Indian/Christmas</option>-->

                                        <!--        <option value="Indian/Cocos">Indian/Cocos</option>-->

                                        <!--        <option value="Indian/Comoro">Indian/Comoro</option>-->

                                        <!--        <option value="Indian/Kerguelen">Indian/Kerguelen</option>-->

                                        <!--        <option value="Indian/Mahe">Indian/Mahe</option>-->

                                        <!--        <option value="Indian/Maldives">Indian/Maldives</option>-->

                                        <!--        <option value="Indian/Mauritius">Indian/Mauritius</option>-->

                                        <!--        <option value="Indian/Mayotte">Indian/Mayotte</option>-->

                                        <!--        <option value="Indian/Reunion">Indian/Reunion</option>-->

                                        <!--        <option value="Pacific/Apia">Pacific/Apia</option>-->

                                        <!--        <option value="Pacific/Auckland">Pacific/Auckland</option>-->

                                        <!--        <option value="Pacific/Bougainville">Pacific/Bougainville</option>-->

                                        <!--        <option value="Pacific/Chatham">Pacific/Chatham</option>-->

                                        <!--        <option value="Pacific/Chuuk">Pacific/Chuuk</option>-->

                                        <!--        <option value="Pacific/Easter">Pacific/Easter</option>-->

                                        <!--        <option value="Pacific/Efate">Pacific/Efate</option>-->

                                        <!--        <option value="Pacific/Enderbury">Pacific/Enderbury</option>-->

                                        <!--        <option value="Pacific/Fakaofo">Pacific/Fakaofo</option>-->

                                        <!--        <option value="Pacific/Fiji">Pacific/Fiji</option>-->

                                        <!--        <option value="Pacific/Funafuti">Pacific/Funafuti</option>-->

                                        <!--        <option value="Pacific/Galapagos">Pacific/Galapagos</option>-->

                                        <!--        <option value="Pacific/Gambier">Pacific/Gambier</option>-->

                                        <!--        <option value="Pacific/Guadalcanal">Pacific/Guadalcanal</option>-->

                                        <!--        <option value="Pacific/Guam">Pacific/Guam</option>-->

                                        <!--        <option value="Pacific/Honolulu">Pacific/Honolulu</option>-->

                                        <!--        <option value="Pacific/Kiritimati">Pacific/Kiritimati</option>-->

                                        <!--        <option value="Pacific/Kosrae">Pacific/Kosrae</option>-->

                                        <!--        <option value="Pacific/Kwajalein">Pacific/Kwajalein</option>-->

                                        <!--        <option value="Pacific/Majuro">Pacific/Majuro</option>-->

                                        <!--        <option value="Pacific/Marquesas">Pacific/Marquesas</option>-->

                                        <!--        <option value="Pacific/Midway">Pacific/Midway</option>-->

                                        <!--        <option value="Pacific/Nauru">Pacific/Nauru</option>-->

                                        <!--        <option value="Pacific/Niue">Pacific/Niue</option>-->

                                        <!--        <option value="Pacific/Norfolk">Pacific/Norfolk</option>-->

                                        <!--        <option value="Pacific/Noumea">Pacific/Noumea</option>-->

                                        <!--        <option value="Pacific/Pago_Pago">Pacific/Pago_Pago</option>-->

                                        <!--        <option value="Pacific/Palau">Pacific/Palau</option>-->

                                        <!--        <option value="Pacific/Pitcairn">Pacific/Pitcairn</option>-->

                                        <!--        <option value="Pacific/Pohnpei">Pacific/Pohnpei</option>-->

                                        <!--        <option value="Pacific/Port_Moresby">Pacific/Port_Moresby</option>-->

                                        <!--        <option value="Pacific/Rarotonga">Pacific/Rarotonga</option>-->

                                        <!--        <option value="Pacific/Saipan">Pacific/Saipan</option>-->

                                        <!--        <option value="Pacific/Tahiti">Pacific/Tahiti</option>-->

                                        <!--        <option value="Pacific/Tarawa">Pacific/Tarawa</option>-->

                                        <!--        <option value="Pacific/Tongatapu">Pacific/Tongatapu</option>-->

                                        <!--        <option value="Pacific/Wake">Pacific/Wake</option>-->

                                        <!--        <option value="Pacific/Wallis">Pacific/Wallis</option>-->

                                        <!--    </select>-->

                                        <!--</div>-->

                                    <!--</div>-->

                                        <div class="row mb-3">

                                        <label for="" class="col-form-label col-md-5 col-sm-5">Currency</label>

                                        <div class="col-md-7 col-sm-7">

                                            <input class="form-control " type="text" name="currency" id="currency" placeholder="Enter Currency Afghan afghani (AFN)" autocomplete="off">

                                            <div id="dropdowncurrency" class="dropdown-list form-select arrow-none"></div>

                                        </div>

                                    </div>

                                    <!--<div class="row mb-3">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Currency</label>-->

                                    <!--    <div class="col-md-7 col-sm-7">-->

                                    <!--        <select name="currency" id="currency" class="form-select">-->

                                    <!--            <option value>Select Currency</option>-->

                                    <!--            <option value="AFN">Afghan afghani (AFN)</option>-->

                                    <!--            <option value="ALL">Albanian lek (ALL)</option>-->

                                    <!--            <option value="DZD">Algerian dinar (DZD)</option>-->

                                    <!--            <option value="AOA">Angolan kwanza (AOA)</option>-->

                                    <!--            <option value="ARS">Argentine peso (ARS)</option>-->

                                    <!--            <option value="AMD">Armenian dram (AMD)</option>-->

                                    <!--            <option value="AWG">Aruban florin (AWG)</option>-->

                                    <!--            <option value="AUD">Australian dollar (AUD)</option>-->

                                    <!--            <option value="AZN">Azerbaijani manat (AZN)</option>-->

                                    <!--            <option value="BSD">Bahamian dollar (BSD)</option>-->

                                    <!--            <option value="BHD">Bahraini dinar (BHD)</option>-->

                                    <!--            <option value="BDT">Bangladeshi taka (BDT)</option>-->

                                    <!--            <option value="BBD">Barbadian dollar (BBD)</option>-->

                                    <!--            <option value="BYR">Belarusian ruble (BYR)</option>-->

                                    <!--            <option value="BZD">Belize dollar (BZD)</option>-->

                                    <!--            <option value="BMD">Bermudian dollar (BMD)</option>-->

                                    <!--            <option value="BTN">Bhutanese ngultrum (BTN)</option>-->

                                    <!--            <option value="BOB">Bolivian boliviano (BOB)</option>-->

                                    <!--            <option value="BAM">Bosnia and Herzegovina convertible mark (BAM)</option>-->

                                    <!--            <option value="BWP">Botswana pula (BWP)</option>-->

                                    <!--            <option value="BRL">Brazilian real (BRL)</option>-->

                                    <!--            <option value="GBP" selected>British pound (GBP)</option>-->

                                    <!--            <option value="None">British Virgin Islands dollar (None)</option>-->

                                    <!--            <option value="BND">Brunei dollar (BND)</option>-->

                                    <!--            <option value="BGN">Bulgarian lev (BGN)</option>-->

                                    <!--            <option value="BIF">Burundian franc (BIF)</option>-->

                                    <!--            <option value="KHR">Cambodian riel (KHR)</option>-->

                                    <!--            <option value="CAD">Canadian dollar (CAD)</option>-->

                                    <!--            <option value="CVE">Cape Verdean escudo (CVE)</option>-->

                                    <!--            <option value="KYD">Cayman Islands dollar (KYD)</option>-->

                                    <!--            <option value="XAF">Central African CFA franc (XAF)</option>-->

                                    <!--            <option value="XPF">CFP franc (XPF)</option>-->

                                    <!--            <option value="CLP">Chilean peso (CLP)</option>-->

                                    <!--            <option value="CNY">Chinese yuan (CNY)</option>-->

                                    <!--            <option value="COP">Colombian peso (COP)</option>-->

                                    <!--            <option value="KMF">Comorian franc (KMF)</option>-->

                                    <!--            <option value="CDF">Congolese franc (CDF)</option>-->

                                    <!--            <option value="CRC">Costa Rican colon (CRC)</option>-->

                                    <!--            <option value="HRK">Croatian kuna (HRK)</option>-->

                                    <!--            <option value="CUC">Cuban convertible peso (CUC)</option>-->

                                    <!--            <option value="CZK">Czech koruna (CZK)</option>-->

                                    <!--            <option value="DKK">Danish krone (DKK)</option>-->

                                    <!--            <option value="DJF">Djiboutian franc (DJF)</option>-->

                                    <!--            <option value="DOP">Dominican peso (DOP)</option>-->

                                    <!--            <option value="XCD">East Caribbean dollar (XCD)</option>-->

                                    <!--            <option value="EGP">Egyptian pound (EGP)</option>-->

                                    <!--            <option value="ERN">Eritrean nakfa (ERN)</option>-->

                                    <!--            <option value="ETB">Ethiopian birr (ETB)</option>-->

                                    <!--            <option value="EUR">Euro (EUR)</option>-->

                                    <!--            <option value="FKP">Falkland Islands pound (FKP)</option>-->

                                    <!--            <option value="FJD">Fijian dollar (FJD)</option>-->

                                    <!--            <option value="GMD">Gambian dalasi (GMD)</option>-->

                                    <!--            <option value="GEL">Georgian lari (GEL)</option>-->

                                    <!--            <option value="GHS">Ghanaian cedi (GHS)</option>-->

                                    <!--            <option value="GIP">Gibraltar pound (GIP)</option>-->

                                    <!--            <option value="GTQ">Guatemalan quetzal (GTQ)</option>-->

                                    <!--            <option value="?GGP">Guernsey pound (?GGP)</option>-->

                                    <!--            <option value="GNF">Guinean franc (GNF)</option>-->

                                    <!--            <option value="GYD">Guyanese dollar (GYD)</option>-->

                                    <!--            <option value="HTG">Haitian gourde (HTG)</option>-->

                                    <!--            <option value="HNL">Honduran lempira (HNL)</option>-->

                                    <!--            <option value="HKD">Hong Kong dollar (HKD)</option>-->

                                    <!--            <option value="HUF">Hungarian forint (HUF)</option>-->

                                    <!--            <option value="ISK">Icelandic krona (ISK)</option>-->

                                    <!--            <option value="INR">Indian rupee (INR)</option>-->

                                    <!--            <option value="IDR">Indonesian rupiah (IDR)</option>-->

                                    <!--            <option value="IRR">Iranian rial (IRR)</option>-->

                                    <!--            <option value="IQD">Iraqi dinar (IQD)</option>-->

                                    <!--            <option value="ILS">Israeli new shekel (ILS)</option>-->

                                    <!--            <option value="JMD">Jamaican dollar (JMD)</option>-->

                                    <!--            <option value="JPY">Japanese yen (JPY)</option>-->

                                    <!--            <option value="?JEP">Jersey pound (?JEP)</option>-->

                                    <!--            <option value="JOD">Jordanian dinar (JOD)</option>-->

                                    <!--            <option value="KZT">Kazakhstani tenge (KZT)</option>-->

                                    <!--            <option value="KES">Kenyan shilling (KES)</option>-->

                                    <!--            <option value="KWD">Kuwaiti dinar (KWD)</option>-->

                                    <!--            <option value="KGS">Kyrgyzstani som (KGS)</option>-->

                                    <!--            <option value="LAK">Lao kip (LAK)</option>-->

                                    <!--            <option value="LVL">Latvian lats (LVL)</option>-->

                                    <!--            <option value="LBP">Lebanese pound (LBP)</option>-->

                                    <!--            <option value="LSL">Lesotho loti (LSL)</option>-->

                                    <!--            <option value="LRD">Liberian dollar (LRD)</option>-->

                                    <!--            <option value="LYD">Libyan dinar (LYD)</option>-->

                                    <!--            <option value="LTL">Lithuanian litas (LTL)</option>-->

                                    <!--            <option value="MOP">Macanese pataca (MOP)</option>-->

                                    <!--            <option value="MKD">Macedonian denar (MKD)</option>-->

                                    <!--            <option value="MGA">Malagasy ariary (MGA)</option>-->

                                    <!--            <option value="MWK">Malawian kwacha (MWK)</option>-->

                                    <!--            <option value="MYR">Malaysian ringgit (MYR)</option>-->

                                    <!--            <option value="MVR">Maldivian rufiyaa (MVR)</option>-->

                                    <!--            <option value="IMP">Manx pound (IMP)</option>-->

                                    <!--            <option value="MRO">Mauritanian ouguiya (MRO)</option>-->

                                    <!--            <option value="MUR">Mauritian rupee (MUR)</option>-->

                                    <!--            <option value="MXN">Mexican peso (MXN)</option>-->

                                    <!--            <option value="None">Micronesian dollar (None)</option>-->

                                    <!--            <option value="MDL">Moldovan leu (MDL)</option>-->

                                    <!--            <option value="MNT">Mongolian togrog (MNT)</option>-->

                                    <!--            <option value="MAD">Moroccan dirham (MAD)</option>-->

                                    <!--            <option value="MZN">Mozambican metical (MZN)</option>-->

                                    <!--            <option value="MMK">Myanma kyat (MMK)</option>-->

                                    <!--            <option value="NAD">Namibian dollar (NAD)</option>-->

                                    <!--            <option value="NPR">Nepalese rupee (NPR)</option>-->

                                    <!--            <option value="ANG">Netherlands Antillean guilder (ANG)</option>-->

                                    <!--            <option value="TWD">New Taiwan dollar (TWD)</option>-->

                                    <!--            <option value="NZD">New Zealand dollar (NZD)</option>-->

                                    <!--            <option value="NIO">Nicaraguan crodoba (NIO)</option>-->

                                    <!--            <option value="NGN">Nigerian naira (NGN)</option>-->

                                    <!--            <option value="KPW">North Korean won (KPW)</option>-->

                                    <!--            <option value="NOK">Norwegian krone (NOK)</option>-->

                                    <!--            <option value="OMR">Omani rial (OMR)</option>-->

                                    <!--            <option value="PKR">Pakistani rupee (PKR)</option>-->

                                    <!--            <option value="None">Palauan dollar (None)</option>-->

                                    <!--            <option value="PAB">Panamanian balboa (PAB)</option>-->

                                    <!--            <option value="PGK">Papua New Guinean kina (PGK)</option>-->

                                    <!--            <option value="PYG">Paraguayan guarani (PYG)</option>-->

                                    <!--            <option value="PEN">Peruvian nuevo sol (PEN)</option>-->

                                    <!--            <option value="PHP">Philippine peso (PHP)</option>-->

                                    <!--            <option value="PLN">Polish zloty (PLN)</option>-->

                                    <!--            <option value="GBP" selected>Pound sterling (GBP)</option>-->

                                    <!--            <option value="QAR">Qatari riyal (QAR)</option>-->

                                    <!--            <option value="RON">Romanian leu (RON)</option>-->

                                    <!--            <option value="RUB">Russian ruble (RUB)</option>-->

                                    <!--            <option value="RWF">Rwandan franc (RWF)</option>-->

                                    <!--            <option value="SHP">Saint Helena pound (SHP)</option>-->

                                    <!--            <option value="SVC">Salvadoran colon (SVC)</option>-->

                                    <!--            <option value="WST">Samoan tala (WST)</option>-->

                                    <!--            <option value="STD">Sao Tome and Principe dobra (STD)</option>-->

                                    <!--            <option value="SAR">Saudi riyal (SAR)</option>-->

                                    <!--            <option value="RSD">Serbian dinar (RSD)</option>-->

                                    <!--            <option value="SCR">Seychellois rupee (SCR)</option>-->

                                    <!--            <option value="SLL">Sierra Leonean leone (SLL)</option>-->

                                    <!--            <option value="SBD">Solomon Islands dollar (SBD)</option>-->

                                    <!--            <option value="SOS">Somali shilling (SOS)</option>-->

                                    <!--            <option value="ZAR">South African rand (ZAR)</option>-->

                                    <!--            <option value="KRW">South Korean won (KRW)</option>-->

                                    <!--            <option value="LKR">Sri Lankan rupee (LKR)</option>-->

                                    <!--            <option value="SDG">Sudanese pound (SDG)</option>-->

                                    <!--            <option value="SRD">Surinamese dollar (SRD)</option>-->

                                    <!--            <option value="SZL">Swazi lilangeni (SZL)</option>-->

                                    <!--            <option value="SEK">Swedish krona (SEK)</option>-->

                                    <!--            <option value="CHF">Swiss franc (CHF)</option>-->

                                    <!--            <option value="SYP">Syrian pound (SYP)</option>-->

                                    <!--            <option value="TJS">Tajikistani somoni (TJS)</option>-->

                                    <!--            <option value="TZS">Tanzanian shilling (TZS)</option>-->

                                    <!--            <option value="THB">Thai baht (THB)</option>-->

                                    <!--            <option value="TOP">Tongan paoanga (TOP)</option>-->

                                    <!--            <option value="TTD">Trinidad and Tobago dollar (TTD)</option>-->

                                    <!--            <option value="TND">Tunisian dinar (TND)</option>-->

                                    <!--            <option value="TRY">Turkish lira (TRY)</option>-->

                                    <!--            <option value="TMT">Turkmenistani manat (TMT)</option>-->

                                    <!--            <option value="UGX">Ugandan shilling (UGX)</option>-->

                                    <!--            <option value="UAH">Ukrainian hryvnia (UAH)</option>-->

                                    <!--            <option value="AED">United Arab Emirates dirham (AED)</option>-->

                                    <!--            <option value="USD">United States dollar (USD)</option>-->

                                    <!--            <option value="UYU">Uruguayan peso (UYU)</option>-->

                                    <!--            <option value="UZS">Uzbekistani som (UZS)</option>-->

                                    <!--            <option value="VUV">Vanuatu vatu (VUV)</option>-->

                                    <!--            <option value="VEF">Venezuelan bolivar (VEF)</option>-->

                                    <!--            <option value="VND">Vietnamese dong (VND)</option>-->

                                    <!--            <option value="XOF">West African CFA franc (XOF)</option>-->

                                    <!--            <option value="YER">Yemeni rial (YER)</option>-->

                                    <!--            <option value="ZMK">Zambian kwacha (ZMK)</option>-->

                                    <!--        </select>-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    <?php /*

                                    <div class="row mb-3">

                                        <label class="col-form-label col-md-5 col-sm-5">Maximum additional drop offs</label>

                                        <div class="col-md-7 col-sm-7">

                                             <select name="additional_drop_offs" id="additional_drop_offs" class="form-select">

                                                    <option value>Select Drop off</option>

                                                    <option value="1">1</option>

                                                    <option value="2">2</option>

                                                    <option value="3">3</option>

                                            </select>

                                            <!--<input type="text" id="additional_drop_offs" placeholder=" Enter the maximum additional drop offs"name="additional_drop_offs"  class="form-control" maxlength="2" data-parsley-maxlength="2" required data-parsley-type="digits">-->

                                        </div>

                                    </div> */ ?>

                                    <!--<div class="row mb-3">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Google Maps API key (Browser)</label>-->

                                    <!--    <div class="col-md-7 col-sm-7">-->

                                    <!--        <input type="text" id="google_map_api_key_browser" name="google_map_api_key_browser"placeholder=" Enter the Browser API key"  class="form-control" maxlength="100" data-parsley-maxlength="100" required>-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    <!--<div class="row mb-3">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Google Maps API key (Server)</label>-->

                                    <!--    <div class="col-md-7 col-sm-7">-->

                                    <!--        <input type="text" id="google_map_api_key_server" name="google_map_api_key_server" placeholder=" Enter the Server API key"  class="form-control" maxlength="100" data-parsley-maxlength="100" required>-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    <!--<div class="row mb-3">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Route</label>-->

                                    <!--    <div class="col-md-7 col-sm-7">-->

                                    <!--        <select name="route" id="route" class="form-select" required>-->

                                    <!--            <option value="shortest duration">Shortest Duration</option>-->

                                    <!--            <option value="shortest distance">Shortest Distance</option>-->

                                    <!--            <option value="longest duration">Longest Duration</option>-->

                                    <!--            <option value="longest distance">Longest Distance</option>-->

                                    <!--        </select>-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    <!--<div class="row mb-3">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Avoid routes</label>-->

                                    <!--    <div class="col-md-7 col-sm-7 align-self-center">-->

                                    <!--        <div class="form-check form-check-inline">-->

                                    <!--            <input class="form-check-input" type="checkbox" name="avoid_route[]" id="avoid_route" value="highways">-->

                                    <!--            <label class="form-check-label" for="distanceRouteAvoidHighways">Highways</label>-->

                                    <!--        </div>-->

                                    <!--        <div class="form-check form-check-inline">-->

                                    <!--            <input class="form-check-input" type="checkbox" name="distanceRouteAvoid[]" id="distanceRouteAvoidTolls" value="tolls">-->

                                    <!--            <label class="form-check-label" for="distanceRouteAvoidTolls">Tolls</label>-->

                                    <!--        </div>-->

                                    <!--        <div class="form-check form-check-inline">-->

                                    <!--            <input class="form-check-input" type="checkbox" name="distanceRouteAvoid[]" id="distanceRouteAvoidFerries" value="ferries">-->

                                    <!--            <label class="form-check-label" for="distanceRouteAvoidFerries">Ferries</label>-->

                                    <!--        </div>-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    <div class="row mb-3">

                                        <label class="col-form-label col-md-5 col-sm-5">Distance unit</label>

                                        <div class="col-md-7 col-sm-7">

                                            <select name="distance_unit" id="distance_unit" class="form-select" required>

                                                <option value="miles">Miles</option>

                                                <option value="kms">Kms</option>

                                            </select>

                                        </div>

                                    </div>

                                    <!--<div class="row mb-3">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Order prefix</label>-->

                                    <!--    <div class="col-md-7 col-sm-7">-->

                                    <!--        <input type="text" id="order_prefix" name="order_prefix" placeholder="Enter the order prefix" class="form-control" maxlength="5" data-parsley-maxlength="5">-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    <?php /*

                                    <div class="row mb-3">

                                        <label class="col-form-label col-md-5 col-sm-5">Auto Customer Registration</label>

                                        <div class="col-md-7 col-sm-7">

                                            <select name="auto_customer_registration" id="auto_customer_registration" class="form-select" required>

                                                <option value="Yes">Yes</option>

                                                <option value="No">No</option>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="row mb-3">

                                        <label class="col-form-label col-md-5 col-sm-5">Auto Bookings Accept</label>

                                        <div class="col-md-7 col-sm-7">

                                            <select name="auto_booking_accept" id="auto_booking_accept" class="form-select" required>

                                                <option value="Yes">Yes</option>

                                                <option value="No">No</option>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="row mb-3">

                                        <label class="col-form-label col-md-5 col-sm-5">Hourly Package</label>

                                        <div class="col-md-7 col-sm-7">

                                            <select name="hourl_package" id="hourl_package" class="form-select" required>

                                                <option value="Yes">Yes</option>

                                                <option value="No">No</option>

                                            </select>

                                        </div>

                                    </div>

                                     */

                                    ?>

                                    

                                    <div class="row">

                                        <label class="col-form-label col-md-5 col-sm-5">Advance booking minimum</label>

                                        <div class="col-md-4 col-sm-4 col-6 mb-3">

                                            <select name="advance_booking_minium_type" id="advance_booking_minium_type" class="form-select" required>

                                                <option value="minutes">Minutes</option>

                                                <option value="hours">Hours</option>

                                            </select>

                                        </div>

                                        <div class="col-md-3 col-sm-3 col-6 mb-3">

                                             <select name="advance_booking_minium" id="advance_booking_minium" class="form-select" required>

                                            </select>

                                            <!--<input type="text" id="advance_booking_minium" name="advance_booking_minium"  placeholder="Enter Number"class="form-control" maxlength="2" data-parsley-maxlength="2" required data-parsley-type="digits">-->

                                        </div>

                                    </div>

                                    <div class="row ">

                                        <label class="col-form-label col-md-5 col-sm-5">Advance booking maximum</label>

                                        <div class="col-md-4 col-sm-4 col-6 mb-3">

                                            <select name="advance_booking_maximum_type" id="advance_booking_maximum_type" class="form-select" required>

                                                <option value="days">Days</option>

                                                <option value="months">Months</option>

                                                <option value="years">Years</option>

                                            </select>

                                        </div>

                                        <div class="col-md-3 col-sm-3 col-6 mb-3">

                                             <select name="advance_booking_maximum" id="advance_booking_maximum" class="form-select" required>

                                            </select>

                                            <!--<input type="text" id="advance_booking_maximum" name="advance_booking_maximum"  placeholder="Enter Number" class="form-control" maxlength="2" data-parsley-maxlength="2" required data-parsley-type="digits">-->

                                            

                                        <input type="hidden" id="bokingsettingid" name="bokingsettingid" >   

                                        </div>

                                    </div>

                                      <input type="hidden" id="countryCode" name="countryCode" > 

                                    

                                    

                                    <!--<div class="row ">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Cancel booking</label>-->

                                    <!--    <div class="col-md-4 col-sm-4 col-6 mb-3">-->

                                    <!--        <select name="cancel_booking_type" id="cancel_booking_type" class="form-select" required>-->

                                    <!--            <option value="hours">Hours</option>-->

                                    <!--            <option value="days">Days</option>-->

                                    <!--        </select>-->

                                    <!--    </div>-->

                                    <!--    <div class="col-md-3 col-sm-3 col-6 mb-3">-->

                                    <!--          <select name="cancel_booking" id="cancel_booking" class="form-select" required>-->

                                    <!--        </select>-->

                                            <!--<input type="text" id="cancel_booking" name="cancel_booking" value="6" placeholder="Enter Number" class="form-control" maxlength="2" data-parsley-maxlength="2" required data-parsley-type="digits">-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    <!--<div class="row mb-3">-->

                                    <!--    <label class="col-form-label col-md-5 col-sm-5">Cancel Booking Terms</label>-->

                                    <!--    <div class="col-md-7 col-sm-7">-->

                                    <!--        <textarea type="text" id="cancel_booking_terms" name="cancel_booking_terms" class="form-control"></textarea>-->

                                    <!--    </div>-->

                                    <!--</div>-->

                                    <div class="text-center">

                                        <button type="button" name="sbtUpdate" class="btn btn-primary" id="saveBtn">UPDATE</button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

                </div>

                <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position" >

  <div class="nav flex-column nav-tabs nav-tabs-right" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">

    

    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->

    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->

    <!--</a>-->

    

    <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/bookingsetting" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i>Booking

    </a>

    

    <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/emailsetting" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-envelope" style="margin-right: 8px;"></i> Email

    </a>

    

    <a class="nav-link text-light" id="vert-tabs-right-promo-code-tab" href="/EmailTemplate" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

    <i class="fas fa-plus"style="margin-right: 8px;"></i> Email Template

    </a>

    

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options

    </a>

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date 

    </a>

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar

    </a>

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-star" style="margin-right: 8px;"></i> Review

    </a>

  </div>

</div>



<style>

.nav-tabs .nav-link:hover  {

    background-color: #747474 !important;

    color: white !important; 

}

.nav-link.active {

  background-color: #fff !important;

  color:#343a40 !important;

}



.nav-link:hover {

  background-color: #6c757d !important; 

}

   



</style>

@include('bookingsetting.partials.add_customer_modal')

@endsection

@section('custom_scripts')

@include('bookingsetting.partials.customers_js')

@endsection