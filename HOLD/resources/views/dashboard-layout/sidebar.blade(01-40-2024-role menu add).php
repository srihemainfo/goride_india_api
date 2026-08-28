@php


    // For menu with sub-menus
    $customer_urls = ['customer'];
    $driver_urls = ['driver'];
     $carfares = ['carfares', 'locationrange'];
    $driver_request_urls = ['driver-request'];
    $geographical_urls = ['place', 'area', 'fixed-price'];
    $tools_urls = ['fleet',
                    'currency',
                    'offertimes',
                    'offerdays',
                    'promocode',
                    
                    'notifications',
                    'livetracking'
                ];
                    $pricing_urls = ['generalpricing',
                    'vehiclepricing',
                    'distanceslab',
                    'hourlypackage',
                    'locationcategory',
                    'FixedPrice',
                   
                ];
                 
    $booking_urls = ['booking'];
    $payment_urls = ['invoice', 'settlement'];
    $report_urls = ['admin-report', 'driver-report'];
    $employee_urls = ['employee', 'module-permissions'];
    $settings=[  'bookingsetting',
                 'emailsetting',
                 'EmailTemplate',
                 'paymentoption',
                 'bookingrestriction',
                 'googlecallender',
                 'review'
    
    ];
@endphp
<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu" >
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" style="background-color:#3f6ad8;"class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu metismenu">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="{{ Request::segment(1) === 'dashboard' ? 'mm-active' : '' }}">
                        <i class="fa-solid fa-rocket metismenu-icon"></i> Dashboard
                    </a>
                </li>
                <!--<li>-->
                <!--    <a href="{{ route('audit-logs.index') }}"-->
                <!--        class="{{ Request::segment(1) === 'audit-logs' ? 'mm-active' : '' }}">-->
                <!--        <i class="fa-solid fa-rocket metismenu-icon"></i> Audit Logs-->
                <!--    </a>-->
                <!--</li> -->
       {{--  @if(session('permissions')['BOOKING_MODULE_read'] ?? false)  --}}
                    <li class="{{ Request::segment(1) === 'booking' ? 'mm-active' : '' }}">
                        <a href="{{ route('booking.create') }}"
                            class="{{ Request::segment(1) === 'booking' ? 'mm-active' : '' }}">
                            <i class="fa-solid fa-book  metismenu-icon"></i> Bookings
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                            <li>
                                <a href="{{ url('booking/list/All') }}"
                                    class="{{ strtolower(Request::segment(3)) === 'all' ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-user"></i> All Jobs
                                </a>
                            </li>
                        
                            		
                            <!--<li>-->
                            <!--    <a href="{{ url('booking/list/Pending') }}"-->
                            <!--        class="{{ Request::segment(3) === 'Pending' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-user"></i> Pending Jobs-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <a href="{{ url('booking/list/Confirmed') }}"-->
                            <!--        class="{{ Request::segment(3) === 'Confirmed' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-users"></i> Confirmed Jobs-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <a href="{{ url('booking/list/Assigned') }}"-->
                            <!--        class="{{ Request::segment(3) === 'Assigned' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-add-user"></i> Assigned Jobs-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <a href="{{ url('booking/list/Dispatched') }}"-->
                            <!--        class="{{ Request::segment(3) === 'Dispatched' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-user"></i> Dispatched Jobs-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <a href="{{ url('booking/list/Completed') }}"-->
                            <!--        class="{{ Request::segment(3) === 'Completed' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-users"></i> Completed Jobs-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <a href="{{ url('booking/list/settled') }}"-->
                            <!--        class="{{ Request::segment(3) === 'settled' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-add-user"></i> Settled Jobs-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <a href="{{ url('booking/list/Canceled') }}"-->
                            <!--        class="{{ Request::segment(3) === 'Canceled' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-user"></i> Cancelled Jobs-->
                            <!--    </a>-->
                            <!--</li>-->
                        </ul>
                    </li>
            {{--         @endif --}}
                
                    <li>
    <a href="{{ route('carfare.index') }}"
        class="{{ in_array(Request::segment(1), $carfares) ? 'mm-active' : '' }}">
        <i class="fa-solid fa-car metismenu-icon"></i> Car Fares
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul class="mm-collapse">
        <li>
            <a href="{{ url('/carfares') }}"
                class="{{ Request::segment(1) === 'carfares' ? 'mm-active' : '' }}">
                <i class="metismenu-icon pe-7s-users"></i> Fare
            </a>
        </li>
        <li>
            <a href="{{ route('locationrange.index') }}"
                class="{{ Request::segment(1) === 'locationrange' ? 'mm-active' : '' }}">
                <i class="metismenu-icon pe-7s-users"></i> Zones
            </a>
        </li>
        <li>
            <a href="{{ route('area.index') }}"
                class="{{ Request::segment(1) === 'area' ? 'mm-active' : '' }}">
                <i class="metismenu-icon pe-7s-users"></i> Area
            </a>
        </li>
    </ul>
</li>
                
                    <li class="{{ in_array(Request::segment(1), $employee_urls) ? 'mm-active' : '' }}">
                        <a href="javascript:void(0)"
                            class="{{ in_array(Request::segment(1), $employee_urls) ? 'mm-active' : '' }}">
                            <i class="fa-solid fa-users metismenu-icon "></i> Employee
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                            <li>
                                <a href="{{ route('employee.index') }}"
                                    class="{{ Request::segment(1) === 'employee' ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-user"></i> Employees
                                </a>
                            </li>
                            <li>
                                <a href="{{  route('module-permissions.index') }}"
                                class="{{ Request::segment(1) === 'module-permissions' ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-users"></i> Module Permissions
                                </a>
                            </li>
                        </ul>
                    </li>
               
                    <li class="{{ in_array(Request::segment(1), $customer_urls) ? 'mm-active' : '' }}">
                        <a class="{{ in_array(Request::segment(1), $customer_urls) ? 'mm-active' : '' }}">                      
                            <i class="fa-solid fa-circle-user metismenu-icon "></i> Customer
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                            <li>
                                <a href="{{ route('customer.index') }}"
                                    class="{{ Request::segment(1) === 'customer' ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-user"></i> List Customers
                                </a>
                            </li>
                        </ul>
                    </li>
               
                    <li class="{{ in_array(Request::segment(1), $tools_urls) ? 'mm-active' : '' }}">
                        <a href="javascript:void(0)"
                            class="{{ in_array(Request::segment(1), $tools_urls) ? 'mm-active' : '' }}">
                            <i class="fa-solid fa-screwdriver-wrench metismenu-icon"></i> Tools
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                           
                                <li>
                                    <a href="{{ route('fleet.index') }}"
                                        class="{{ Request::segment(1) === 'fleet' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-user"></i> List Fleets
                                    </a>
                                </li>
                            
                                <!--<li>-->
                                <!--    <a href="{{ route('currency.index') }}"-->
                                <!--        class="{{ Request::segment(1) === 'currency' ? 'mm-active' : '' }}">-->
                                <!--        <i class="metismenu-icon pe-7s-user"></i> Currency-->
                                <!--    </a>-->
                                <!--</li>-->
                            
                                <li>
                                    <a href="{{ route('offertimes.index') }}"
                                        class="{{ Request::segment(1) === 'offertimes' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-user"></i> Offer Times
                                    </a>
                                </li>
                            
                                <li>
                                    <a href="{{ route('offerdays.index') }}"
                                        class="{{ Request::segment(1) === 'offerdays' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-user"></i> Offer Days
                                    </a>
                                </li>
                                @if(isset($aa))
                                <li>
                                    <a href="{{ route('promocode.index') }}"
                                        class="{{ Request::segment(1) === 'promocode' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-user"></i> Promo Code
                                    </a>
                                </li>
                                @endif
                            
                                <!--<li>-->
                                <!--    <a href="{{ route('locationrange.index') }}"-->
                                <!--        class="{{ Request::segment(1) === 'locationrange' ? 'mm-active' : '' }}">-->
                                <!--        <i class="metismenu-icon pe-7s-user"></i> Location Range-->
                                <!--    </a>-->
                                <!--</li>-->
                             @if(isset($aa))
                                <li>
                                    <a href="{{ route('notifications.index') }}"
                                        class="{{ Request::segment(1) === 'notifications' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-user"></i> Notifications
                                    </a>
                                </li>
                            @endif
                            
                                <!--<li>-->
                                <!--    <a href="{{ route('driver-live-tracking.index') }}"-->
                                <!--        class="{{ Request::segment(1) === 'livetracking' ? 'mm-active' : '' }}">-->
                                <!--        <i class="metismenu-icon pe-7s-user"></i> Live Tracking-->
                                <!--    </a>-->
                                <!--</li>-->
                            
                        </ul>
                    </li>
               
                    <!--payments dashboard-->

                    <!-- <li class="{{ in_array(Request::segment(1), $pricing_urls) ? 'mm-active' : '' }}">-->
                    <!--    <a href="javascript:void(0)"-->
                    <!--        class="{{ in_array(Request::segment(1),$pricing_urls) ? 'mm-active' : '' }}">                         -->
                    <!--        <i class="fa-solid fa-dollar-sign metismenu-icon "></i> Pricing-->
                    <!--        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>-->
                    <!--    </a>-->
                    <!--    <ul class="mm-collapse">-->
                    <!--        <li>-->
                    <!--            <a href=" {{ route('generalpricing')}}"-->
                    <!--                class="{{ Request::segment(1) === 'generalpricing' ? 'mm-active' : '' }}">-->
                    <!--                <i class="metismenu-icon pe-7s-users"></i> General-->
                    <!--            </a>-->
                    <!--        </li>-->
                    <!--        <li>-->
                    <!--             <a href=" {{ route('vehiclepricing')}}"-->
                    <!--                class="{{ Request::segment(1) === 'vehiclepricing' ? 'mm-active' : '' }}">-->
                    <!--                <i class="metismenu-icon pe-7s-users"></i>Vehicle Pricing-->
                    <!--            </a>-->
                    <!--        </li>-->
                    <!--        <li>-->
                    <!--            <a href="{{url('distanceslab')}} "-->
                    <!--                class="{{ Request::segment(1) === 'distanceslab' ? 'mm-active' : '' }}">-->
                    <!--                <i class="metismenu-icon pe-7s-users"></i>Distance Slab-->
                    <!--            </a>-->
                    <!--        </li>-->
                    <!--         <li>-->
                    <!--            <a href="{{url('/hourlypackage')}} "-->
                    <!--                class="{{ Request::segment(1) === 'hourlypackage' ? 'mm-active' : '' }}">-->
                    <!--                <i class="metismenu-icon pe-7s-users"></i>Hourly Package-->
                    <!--            </a>-->
                    <!--        </li>-->
                    <!--         <li>-->
                    <!--            <a href="{{url('/locationcategory')}}"-->
                    <!--                class="{{ Request::segment(1) === 'locationcategory' ? 'mm-active' : '' }}">-->
                    <!--                <i class="metismenu-icon pe-7s-users"></i>Location Category-->
                    <!--            </a>-->
                    <!--        </li>-->
                    <!--         <li>-->
                    <!--            <a href="{{url('FixedPrice')}}"-->
                    <!--                class="{{ Request::segment(1) === 'FixedPrice' ? 'mm-active' : '' }}">-->
                    <!--                <i class="metismenu-icon pe-7s-users"></i>Fixed Pricing-->
                    <!--            </a>-->
                    <!--        </li>-->
                            <!-- <li>-->
                            <!--    <a href=""-->
                            <!--        class="{{ Request::segment(1) === 'driver' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-users"></i>Fixed Pricing-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <a href=""-->
                            <!--        class="{{ Request::segment(1) === 'driver' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-users"></i>Distance Slab-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!-- <li>-->
                            <!--    <a href=""-->
                            <!--        class="{{ Request::segment(1) === 'driver' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-users"></i>Congestion Charges-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!-- <li>-->
                            <!--    <a href=""-->
                            <!--        class="{{ Request::segment(1) === 'driver' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-users"></i>Discount / Surcharge Pricing-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!-- <li>-->
                            <!--    <a href=""-->
                            <!--        class="{{ Request::segment(1) === 'driver' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-users"></i>Location - Discount / Surcharge Pricing-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <a href=""-->
                            <!--        class="{{ Request::segment(1) === 'driver' ? 'mm-active' : '' }}">-->
                            <!--        <i class="metismenu-icon pe-7s-users"></i>Voucher-->
                            <!--    </a>-->
                            <!--</li>-->
                    <!--    </ul>-->
                    <!--</li>-->
                    
                    <!--end-->
                
                    <li class="{{ in_array(Request::segment(1), $driver_urls) ? 'mm-active' : '' }}">
                        <a href="javascript:void(0)"
                            class="{{ in_array(Request::segment(1), $driver_urls) ? 'mm-active' : '' }}">                          
                            <i class="fa-solid fa-user metismenu-icon "></i> Drivers
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                            <li>
                                <a href="{{ route('driver.index') }}"
                                    class="{{ Request::segment(1) === 'driver' ? 'mm-active' : '' }}">
                                    <i class="metismenu-icon pe-7s-users"></i> List Drivers
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ in_array(Request::segment(1), $payment_urls) ? 'mm-active' : '' }}">
                        <a href="javascript:void(0)"
                            class="{{ in_array(Request::segment(1), $payment_urls) ? 'mm-active' : '' }}">                       
                            <i class="fa-solid fa-money-bill metismenu-icon "></i> Payments
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                           
                                <li>
                                    <a href="{{ route('invoice.index') }}"
                                        class="{{ Request::segment(1) === 'invoice' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-user"></i> Generate Invoice
                                    </a>
                                </li>
                            
                                <li>
                                    <a href="{{  route('settlement.index') }}"
                                    class="{{ Request::segment(1) === 'settlement' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i> Settlement
                                    </a>
                                </li>
                            
                        </ul>
                    </li>
                
                    <li class="{{ in_array(Request::segment(1), $report_urls) ? 'mm-active' : '' }}">
                        <a href="javascript:void(0)"
                            class="{{ in_array(Request::segment(1), $report_urls) ? 'mm-active' : '' }}">                         
                            <i class="fa-solid fa-chart-bar metismenu-icon "></i> Reports
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                           
                                <li>
                                    <a href="{{ route('ManageAdminReport') }}"
                                        class="{{ Request::segment(1) === 'admin-report' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-user"></i> Admin Report
                                    </a>
                                </li>
                           
                                <li>
                                    <a href="{{ route('ManageDriverReport') }}"
                                        class="{{ Request::segment(1) === 'driver-report' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i> Driver Report
                                    </a>
                                </li>
                           
                        </ul>
                    </li>
                    <!--settings-->
                    
                    <li class="{{ in_array(Request::segment(1), $settings) ? 'mm-active' : '' }}">
                        <a href="javascript:void(0)"
                            class="{{ in_array(Request::segment(1), $settings) ? 'mm-active' : '' }}">                         
                            <i class="fa fa-cog metismenu-icon"></i> Settings
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                           
                                <!--<li>-->
                                <!--    <a href="{{ route('general') }}"-->
                                <!--        class="{{ Request::segment(1) === '$settings' ? 'mm-active' : '' }}">-->
                                <!--        <i class="metismenu-icon pe-7s-user"></i>General                                    </a>-->
                                <!--</li>-->
                           
                                <li>
                                    <a href="{{ route('bookingsetting') }}"
                                        class="{{ Request::segment(1) ===  'bookingsetting' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i>Booking
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('emailsetting') }}"
                                        class="{{ Request::segment(1) === 'emailsetting' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i>Email
                                    </a>
                                </li>
                                 <li>
                                    <a href="{{ route('EmailTemplate') }}"
                                        class="{{ Request::segment(1) === 'EmailTemplate' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i>Email Template Settings
                                    </a>
                                </li>
                                <!--<li>-->
                                <!--    <a href="{{ route('locationrange.index') }}"-->
                                <!--        class="{{ Request::segment(1) === 'Zones' ? 'mm-active' : '' }}">-->
                                <!--        <i class="metismenu-icon pe-7s-users"></i>Zones-->
                                <!--    </a>-->
                                <!--</li>-->
                                <li>
                                    <a href="{{ route('paymentoption') }}"
                                        class="{{ Request::segment(1) ===  'paymentoption' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i>Payment Options
                                    </a>
                                </li>
                                
                                <li>
                                    <a href="{{ route('bookingrestriction') }}"
                                        class="{{ Request::segment(1) === 'bookingrestriction' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i>Booking Restriction Date
                                    </a>
                                </li>
                              
                            @if(isset($aa))
                              <li>
                                    <a href="{{ route('googlecallender') }}"
                                        class="{{ Request::segment(1) ===  'googlecallender' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i>Google Calendar
                                    </a>
                                </li>
                           
                                <li>
                                    <a href="{{ route('review') }}"
                                        class="{{ Request::segment(1) === 'review' ? 'mm-active' : '' }}">
                                        <i class="metismenu-icon pe-7s-users"></i>Review
                                    </a>
                                </li>  
                         @endif
                              
                        </ul>
                    </li>
                     @if(isset($aa))
                   <li class="{{ in_array(Request::segment(1), $driver_request_urls) ? 'mm-active' : '' }}">
    <a href="javascript:void(0)" class="{{ in_array(Request::segment(1), $driver_request_urls) ? 'mm-active' : '' }}">                         
        <i class="fa fa-address-card-o metismenu-icon"></i> Driver Request
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul class="mm-collapse">
        <li>
            <a href="{{ route('driver-request') }}"
               class="{{ in_array('driver-request', $driver_request_urls) ? 'mm-active' : '' }}">
                <i class="metismenu-icon pe-7s-users"></i> Driver Request List
            </a>
        </li>
    </ul>
</li>
                    @endif

                    
            </ul>
        </div>
    </div>
</div>
