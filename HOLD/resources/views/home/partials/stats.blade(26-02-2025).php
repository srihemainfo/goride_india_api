<style>
.home-demo h2 {
    color: #FFF;
    text-align: center;
    padding: 5rem 0;
    margin: 0;
    font-style: italic;
    font-weight: 300;
}
.img-cd {
    padding: 6px 6px 6px 6px;
}
.owl-dots {
    margin: 8px 0 0 0;
}
.owl-theme .owl-nav {
    margin-top: 10px;
    display: none;
}
.car-name{
    font-size: 14px;
    font-weight: 700;
    color: #424141;
}
.ed-icon{
    list-style-type: none;
    margin: 5px;
    background: #f2bd10;
    padding: 6px 6px 5px 8px;
    border-radius: 6px;
    border: none;
}
.del-icon{
    list-style-type: none;
    margin: 5px;
    background: #003757;
    padding: 6px 6px 5px 8px;
    border-radius: 6px;
    border: none;
}
.ed-ul{
    display: flex;
    float: right;
    margin: 4px 0 0 0;
}
.hd-name{
    color: #595555;
    font-size: 19px;
    font-weight: 600;
}
.item.card:hover{
    box-shadow: 3px 3px 13px #cccccc;
}
#myModal1 {
    background: #000;
    opacity: 0.9;
}
@media (max-width: 768px){
.item {
    padding: 30px;
}
}

.wallet-section{
        font-size: 13px;
    font-weight: 500;
}

.screen {
      margin-top: 32px;
}
    </style>

<div class="row">   
    <div class="col-lg-7 col-md-6">
        <div class="row">
    <div class="col-xl-4 col-sm-6 mb-2">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fa fa-car"></i>
                    <span class="ml-2"><b>Total bookings</b></span>
                </div>
                <h1 class="mt-2 text-right" id="ttl_book">0</h1>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-sm-6 mb-2">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fa fa-car"></i>
                    <span class="ml-2"><b>Pending bookings</b></span>
                </div>
                <h1 class="mt-2 text-right" id="pending_book">0</h1>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-sm-6 mb-2">
        <div class="card text-white bg-info">
            <div class="card-body" style="height: 106px;">
                <div class="card-body-icon">
                    <i class="fa fa-car"></i>
                    <span class="ml-2"><b>Confirmed bookings</b></span>
                </div>
                <h1 class="mt-2 text-right" id="confirmed_book">0</h1>
            </div>
        </div>
    </div>
    </div>
    <div class="row">
    <div class="col-xl-4 col-sm-6 mb-2">
    <div class="card text-white bg-success">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fa fa-car"></i>
                    <span class="ml-2"><b>Assigned bookings</b></span>
                </div>
                <h1 class="mt-2 text-right" id="assigned_book">0</h1>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-sm-6 mb-2">
    <div class="card text-white bg-dark">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fa fa-car"></i>
                    <span class="ml-2"><b>Dispatched bookings</b></span>
                </div>
                <h1 class="mt-2 text-right" id="dispatched_book">0</h1>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-sm-6 mb-2">
    <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fa fa-car"></i>
                    <span class="ml-2"><b>Cancelled bookings</b></span>
                </div>
                <h1 class="mt-2 text-right" id="cancelled_book">0</h1>
            </div>
        </div>
    </div>
    </div>
    </div>

    <div class="col-lg-5 col-md-6  align-self-center">
                                <div class="wallet-section">
                                <div class="row d-flex">
                                    <div class="col-md-12 align-self-center">
                                    <div class="wallet-sec  text-center mb-2">
                                        <h4>Payment Details</h4>
                                    </div>
                                    </div>
                            <!--        <div class="col md-5 text-center mb-2">-->
                            <!--            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><span><i class="fa-solid fa-plus" style="-->
                            <!--    margin: 0 9px 0px 0px;-->
                            <!--" ></i></span>Add Bank Account</button>-->
                            <!--        </div>-->
                                    <!--<div class="col-md-4 align-self-cente mb-2 text-center">-->
                                    <!--    <button type="button" class="btn btn-danger">Show Wallet</button>-->
                                    <!--</div>-->
                                </div>
                                <div class="row d-flex mt-3">
                                    <div class="col-md-4 amt-btn mb-2">
                                        <p>Booking Amount</p>
                                        <div class="style_euroWrapper__B12gK">
                                            <div class="style_euroIcon___erBA"><span id="site_currency">$</span></div>
                                            <div class="style_value__ByCOb"><span id="totalbookingValue">0</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 amt-btn mb-2">
                                        <p>Settlement Amount</p>
                                        <div class="style_dollarWrapper__omcqb">
                                            <div class="style_dollarIcon__4j46y"><span id="site_currency1">$</span></div>
                                            <div class="style_value__ByCOb"><span id="totalValue">0</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 amt-btn mb-2">
                                        <p>Driver Amount</p>
                                        <div class="style_poundWrapper__U4AWC"id="doller">
                                            <div class="style_poundIcon__WqVhH"><span id="site_currency2">$</span></div>
                                            <div class="style_value__ByCOb"><span id="totaldrivervalue">0</span></div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            

                        </div>
</div>
<div class="row mt-4">
    <div class="container">
        <div class="row">
        <div class="col-lg-7 col-md-6">
<div class="home-demo" id="fleet_container"></div>
</div> 
<div class="col-lg-5 col-md-6" id='chart_container' style='displa:none;'>
    <div class="name">
    <h3 class="hd-name">All bookings</h3>
    </div>
    <div class="row airpottdrw">
        {{-- <div class="col-md-12 airpottd bg-white"> --}}
        <div class="col-md-12 bg-white">
              <canvas id="bookingChart" width="400" height="250"></canvas>
    {{--
         <div class="airport">
         <div class="tent">
                <!--<ul class="ed-ul">-->
                <!--    <button class="ed-icon" >-->
                <!--       <i class="fa-solid fa-pen-to-square" style="color: #fff;"></i> -->
                <!--    </button>-->
                <!--    <button class="del-icon"><i class="fa-solid fa-trash" style="color: #fff;"></i></button>-->
                <!--</ul>-->
</div>
<div class="style_airportCountry__brgRx"><span id="country1">CANADA</span></div>
<div class="style_airportName__svE4s"><span id="totalairportvalue">Pearson International Airpor</span></div>
         </div>
        </div>
        <div class="col-md-6 airpottd">
        <div class="airport">
        <div class="tent">
    <!--   <ul class="ed-ul">-->
        <!--<button class="ed-icon" >-->
        <!--   <i class="fa-solid fa-pen-to-square" style="color: #fff;"></i> -->
        <!--</button>-->
        <!--<button class="del-icon"><i class="fa-solid fa-trash" style="color: #fff;"></i></button>-->
    <!--</ul>-->
</div>
<div class="style_airportCountry__brgRx"><span id="country2">CANADA</span></div>
<div class="style_airportName__svE4s"><span id="totalairportvalue1">Pearson International Airpor</span></div>
         </div>
        </div>
    --}}
    </div>
</div>
    </div> 
</div>
</div>
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog newmodal">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">        
          <h4 class="modal-title">Create Account</h4>
          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
         <div class="row">
            <div class="col-md-6">
            <div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Name:</label>
  <input type="name" class="form-control" id="exampleFormControlInput1" placeholder="Name">
</div>
            </div>
         </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

  <!-- Modal1 -->
  <div class="modal fade" id="myModal1" role="dialog">
    <div class="modal-dialog newmodal">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">        
          <h4 class="modal-title mymodal-tiltle">Edit Car Details</h4>
          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
         <div class="row">
            <div class="col-md-6">
            <div class="mb-3">
  <label for="exampleFormControlInput1" class="form-label">Name:</label>
  <input type="name" class="form-control" id="exampleFormControlInput1" placeholder="Name">
</div>
            </div>
         </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>


  <script>
    $(document).on("click","#cust_btn",function(){
  
  $("#myModal").modal("toggle");
  
})

    $(document).on("click","#cust_btn",function(){
  
  $("#myModal1").modal("toggle");
  
})

    
$(document).ready(function(){
    $('.close-sidebar-btn').click(function(){
        $('#doller').toggleClass('screen');
    });
});
    
</script>


