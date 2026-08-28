<?php
session_start();

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include "include/function.php";
include "commonFunction.php";

$s_id=$_REQUEST['sid'];
$subis_id = $_REQUEST['subis'];
$dbconn->reconnectToMainDatabase();
$query=$dbconn->single_rec("SELECT * FROM `gentral_setting` where `cweburl`='$subis_id'") ;
$partrner_id=$query['partner_id'];
$website_email=$query['email'];
$website_contact=$query['contact_number'];
$website_company_name=$query['company_name'];
// print_r($website_contact);exit;
$partner_details=$dbconn->single_rec("SELECT * FROM `partnerlists` where `id`='$partrner_id'");
$dbconn->switchDatabase(
'localhost',
$partner_details['database_name'],
$partner_details['database_user'],
$partner_details['database_password']
); 

$booking_seting=$dbconn->single_rec("SELECT distance_unit,country,currency FROM `bookingsetting` where `partner_id`='$partrner_id'");

$booking_distance_unit = ($booking_seting['distance_unit'] == 'kms') ? '1000' : '1609.34' ; // for km or miles value get for distance price get 
$kmSymbol = ($booking_seting['distance_unit'] == 'kms') ? 'Kms' : 'Miles' ; 
$country = $booking_seting['country'] ?? null  ;
$currency = $booking_seting['currency'] ?? null  ;

    $_SESSION[$subis]=['country' => $country,'kmSymbol' => $kmSymbol,'booking_distance_unit' => $booking_distance_unit,'currency' => $currency];
    $currency = $_SESSION[$subis_id]['currency'] ;
    $currencySymbol =  getCurrencySymbol ($currency) ;
    $boked_info="select * from `bookinfo` where `sid`='$s_id'";
    $bookinfo1=$dbconn->single_rec($boked_info);



if(!$bookinfo1){
    $ur_l = $mainurl.'$subis_id' ;
    header("Location:  $ur_l") ;
}
$carType = $bookinfo1['car_type'] ? $bookinfo1['car_type'] : null;
$pickup = $bookinfo1['from'] ? "area_from ='$bookinfo1[from]'" : null;
$dropoff = $bookinfo1['to'] ? "AND area_to='$bookinfo1[to]'" : null;
$viapoint1 = $bookinfo1['viapoint1'] ? "AND viapoint1 = '$bookinfo1[viapoint1]'" : null;
$viapoint2 = $bookinfo1['viapoint2'] ? "AND viapoint2 = '$bookinfo1[viapoint2]'" : null;
$viapoint3 = $bookinfo1['viapoint3'] ? "AND viapoint3 = '$bookinfo1[viapoint3]'" : null;

$query="select $carType from `admin_form`where $pickup $dropoff $viapoint1 $viapoint2 $viapoint3" ;
$adminFromData=$dbconn->single_rec($query);

$pickupTime = $bookinfo1['pickup_time'] ?? null ;
$pickupDate = $bookinfo1['pickup_date'] ?? null ;
$returnTime = null;
$returnDate = null ;
$car_price_only = $adminFromData[$carType] ?? $bookinfo1['total'] ;
$pickupTimeCharge = $returnTimeCharge = $pickupDateCharge = $returnDateCharge =  0;
$pickupTimeContent = $returnTimeContent = $pickupDateContent = $returnDateContent = $restructionDuration = $returnRestructionDuration = '' ;

include "timeDatePriceGet.php" ;


?>
<!DOCTYPE html>
<html lang="en-US">

<head>
<?php 
include("include/header.php");
?>
<style>
    table td {
        color: black !important;
    }
</style>

</head>

<body>

	<!-- preloader -->
	<div id="preloader">
		<div class="book">
		</div>
	</div>

	<!-- site wrapper -->
	<div class="site-wrapper">

		<div class="main-overlay"></div>

		<!-- header -->
        <?php 
        
        // include("include/nav.php");
        
        ?>
        
        
		<!-- section main content -->
		

        <section class="bp-section">
		    
	<div class="container">
		
		<div class="text-center my-3 bottom-line mv-div">
			<h3 style="background-color:#535048; text-align:center;"><?php echo $website_company_name;?> Booking Information</h3>
			<h6 style=" margin-bottom:0;">Email: <a href="mailto:<?php echo $website_email;?>"><?php echo $website_email;?></a> Cell: <a href="<?php echo $website_contact?>"><?php echo $website_contact?></a> Phone: <a href="tel:07557026312">07557026312</a></h6>
			<h6 style=" margin:0">Thank You for using <a href="<?php echo $mainurl; ?>">customer.goride.run</a> to complete your journey. Please check your journey details.</h6>
		</div>
		
		<table class="table table-bordered" style="color: #000000;">
			<tr>
				<th colspan="2" style="background-color:#535048; text-align:center;">Booking Information</th>
			</tr>
			<tr>
				<td style="width:50%">Booking ID</td>
				<td><?php echo $bookinfo1['job_no']?></td>
			</tr>
			<tr>
				<td>Booked On</td>
				<!--<td><?php echo $bookinfo1['booking_date'] ?></td>-->
				<td><?php $originalDate = $bookinfo1['booking_date']; $formattedDate = date('d-m-Y H:i:s', strtotime($originalDate)); echo $formattedDate;?></td>
			</tr>
			<tr>
				<td>Booking Status</td>
				<td><?php echo $bookinfo1['order_status']?></td>
			</tr>
		</table>

		<table class="table table-bordered mt-3" style="color: #000000;">
			<tr>
				<th colspan="2" style="background-color:#535048; text-align:center;">Your Route</th>
			</tr>
			<tr>
				<td style="width:50%">Journey Type</td>
				<td><?php echo $bookinfo1['way']?></td>
			</tr>
			<tr>
				<td>Date of Journey</td>
				<td>
					<?php
					    $date = new DateTime($bookinfo1['pickup_date']);
					    $formattedDate = $date->format('d-m-Y');
					    echo $formattedDate;
					?>
				</td>
			</tr>
			<tr>
				<td>Pick Up Time</td>
				<td><?php echo substr($bookinfo1['pickup_time'], 0, -3); ?></td>
			</tr>
			<tr>
				<td>Preferred Vehicle</td>
				<td><?php echo $bookinfo1['car_type']?></td>
			</tr>
			<tr>
				<td>Message</td>
				<td><?php echo $bookinfo1['message']?></td>
			</tr>
		</table>
		
		<table class="table table-bordered mt-3" style="color: #000000;">
			<tr>
				<th colspan="2" style="background-color:#535048; text-align:center;">Passengers & Luggage</th>
			</tr>
			<tr>
				<td style="width:50%">Passengers</td>
				<td><?php echo $bookinfo1['passengers']?></td>
			</tr>
			<tr>
				<td>Baby seat</td>
				<td><?php echo $bookinfo1['child_seat']?></td>
			</tr>
				<tr>
				<td>Luggage</td>
				<td><?php echo $bookinfo1['baggages']?></td>
			</tr>
				<tr>
				<td>Hand Luggage</td>
				<td><?php echo $bookinfo1['hand_luggages']?></td>
			</tr>
			
		</table>
		
		<table class="table table-bordered mt-3" style="color: #000000;">
			<tr>
				<th colspan="2" style="background-color:#535048; text-align:center;">Your Details</th>
			</tr>
			<tr>
				<td style="width:50%">Your Name</td>
				<td><?php echo $bookinfo1['fname']?></td>
			</tr>
			<tr>
				<td>Contact Number</td>
				<td><?php echo $bookinfo1['mobile']?></td>
			</tr>
			<tr>
				<td>E-mail Address</td>
				<td><?php echo $bookinfo1['email']?></td>
			</tr>
		</table>
		
		<table class="table table-bordered mt-3" style="color: #000000;">
			<tr>
				<th colspan="2" style="background-color:#535048; text-align:center;">Journey Details</th>
			</tr>
			<tr>
				<td style="width:50%" style="width:50%">From</td>
				<td><?php echo $bookinfo1['from']?></td>
			</tr>
<?php if (!empty($bookinfo1['way']) && $bookinfo1['way'] == 'One way'): ?>
<?php if (!empty($bookinfo1['viapoint1'])): ?>
    <tr>
        <td>Via Point:</td>
        <td><?php echo htmlspecialchars($bookinfo1['viapoint1']); ?></td>
    </tr>
    <?php endif; ?>
    
    <?php if (!empty($bookinfo1['viapoint2'])): ?>
    <tr>
        <td>Via Point:</td>
        <td><?php echo htmlspecialchars($bookinfo1['viapoint2']); ?></td>
    </tr>
    <?php endif; ?>
    
    <?php if (!empty($bookinfo1['viapoint3'])): ?>
    <tr>
        <td>Via Point:</td>
        <td><?php echo htmlspecialchars($bookinfo1['viapoint3']); ?></td>
    </tr>
    <?php endif; ?>
     <?php endif; ?>
    
    
     <?php if (!empty($bookinfo1['way']) && $bookinfo1['way'] == 'Return'): ?>
    <?php if (!empty($bookinfo1['viapoint1'])): ?>
    <tr>
        <td>Return Via Point:</td>
        <td><?php echo htmlspecialchars($bookinfo1['viapoint1']); ?></td>
    </tr>
    <?php endif; ?>
    
    <?php if (!empty($bookinfo1['viapoint2'])): ?>
    <tr>
        <td>Return Via Point:</td>
        <td><?php echo htmlspecialchars($bookinfo1['viapoint2']); ?></td>
    </tr>
    <?php endif; ?>
    
    <?php if (!empty($bookinfo1['viapoint3'])): ?>
    <tr>
        <td>Return Via Point:</td>
        <td><?php echo htmlspecialchars($bookinfo1['viapoint3']); ?></td>
    </tr>
    <?php endif; ?>
<?php endif; ?>

    
			<?php if($bookinfo1['place_from'] == 1){ ?> 
			<tr>
				<td>Flight Number</td>
				<td><?php echo $bookinfo1['pickup_flight_num']; ?></td>
			</tr>
			<tr>
				<td>Flight From</td>
				<td><?php echo $bookinfo1['pickup_flight_from']; ?></td>
			</tr>
			
			<tr>
				<td>Meet and Greet Service</td>
				<td><?php echo $bookinfo1['meet_greet']; ?></td>
			</tr>
			<?php } else if($bookinfo1['place_from'] == 2){ ?> 
			<tr>
				<td>Ship Name</td>
				<td><?php echo $bookinfo1['pick_shipname']; ?></td>
			</tr>
			<tr>
				<td>Ship From</td>
				<td><?php echo $bookinfo1['pick_shipname']; ?></td>
			</tr>
			<tr>
				<td>Meet and Greet Service</td>
				<td><?php echo $bookinfo1['meet_greet']; ?></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td>Pickup Address</td>
				<td><?php echo $bookinfo1['pickup_address']; ?></td>
			</tr>
			<tr>
				<td>Pickup Postcode</td>
				<td><?php echo $bookinfo1['pickup_postcode']; ?></td>
			</tr>
			<?php } ?>
			
			<tr>
				<td>To</td>
				<td><?php echo $bookinfo1['to']?></td>
			</tr>
			
			<?php 

			if($bookinfo1['place_to'] == 4){ ?>
			<tr>
				<td>Dropoff Address</td>
				<td><?php echo $bookinfo1['dest_address']; ?></td>
			</tr>
			<tr>
				<td>Dropoff Postcode</td>
				<td><?php echo $bookinfo1['dest_postcode']; ?></td>
			</tr>
			<?php } ?>
		</table>
		
		<table class="table table-bordered mt-3" style="color: #000000;">
			<tr>
				<th colspan="2" style="background-color:#535048; text-align:center;">Payment Details</th>
			</tr>
			<tr>
				<td style="width:50%">Payment Status</td>
				<td><?php echo $bookinfo1['payment_status']?></td>
			</tr>
			<?php if($pickupTimeContent!= ''):?>
				<tr>
				<td>Time Charges Message</td>
				<td><?= $pickupTimeContent;?></td>
			</tr>
			<?php endif; ?>
				<?php if($pickupDateContent!= ''):?>
				<tr>
				<td>Date Charges Message</td>
				<td><?= $pickupDateContent;?></td>
			</tr>
			<?php endif; ?>
				<tr>
				<td>Journey Cost</td>
				<td><?=$currencySymbol?><?php echo $bookinfo1['total']?></td>
			</tr>
		
			
			<tr>
				<td>Payment</td>
				<td><?php echo $bookinfo1['type']?></td>
			</tr>
			 
		</table>
		
		<div class="col-lg-12 mt-4 col-sm-12 mv-div">
			<p style="color: #000000;text-align:center;font-size:large">If you have a difficulty to find a driver please call us on <?php echo $website_contact ?></p>
			<p style="color: #000000;text-align:center;font-size:large">Hope to see you again in your future airport transport requirements. Have a nice journey.</p>
			<p style="color: #000000;text-align:center;font-size:large">Best Regards</p>
			<p style="color: #000000;text-align:center;font-size:large"><?php echo $website_company_name ;?></p>
			<!--<p style="color: #000000;text-align:center;font-size:large"><a href="terms">Terms & Conditions</a></p>-->
		</div>
		
	</div>
	
</section>


		

		<!-- footer -->
		<?php 
		
// 		include("include/footer.php");
		
		?>

	</div><!-- end site wrapper -->

	<!-- search popup area -->
	<div class="search-popup">
		<!-- close button -->
		<button type="button" class="btn-close" aria-label="Close"></button>
		<!-- content -->
		<div class="search-content">
			<div class="text-center">
				<h3 class="mb-4 mt-0">Press ESC to close</h3>
			</div>
			<!-- form -->
			<form class="d-flex search-form">
				<input class="form-control me-2" type="search" placeholder="Search and press enter ..."
					aria-label="Search">
				<button class="btn btn-default btn-lg" type="submit"><i class="icon-magnifier"></i></button>
			</form>
		</div>
	</div>

	<!-- canvas menu -->
	<div class="canvas-menu d-flex align-items-end flex-column">
		<!-- close button -->
		<button type="button" class="btn-close" aria-label="Close"></button>

		<!-- logo -->
		<div class="logo">
			<img src="images/logo.png" alt="Katen" />
		</div>

		<!-- menu -->
		<nav>
			<ul class="vertical-menu">
				<li class=""><a href="#">Home</a></li>
				<li><a href="#">About Us</a></li>
				<li><a href="#">Fleets</a></li>
				<li><a href="#">Service</a></li>
				<li><a href="#">Contact</a></li>
			</ul>
		</nav>

		<!-- social icons -->
		<ul class="social-icons list-unstyled list-inline mb-0 mt-auto w-100">
			<li class="list-inline-item"><a href="#"><i class="fab fa-facebook-f"></i></a></li>
			<li class="list-inline-item"><a href="#"><i class="fab fa-twitter"></i></a></li>
			<li class="list-inline-item"><a href="#"><i class="fab fa-instagram"></i></a></li>
            <li class="list-inline-item"><a href="#"><i class="fab fa-linkedin"></i></a></li>
		</ul>
	</div>

	<!-- JAVA SCRIPTS -->
	
	<script src="js/jquery.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/slick.min.js"></script>
	<script src="js/jquery.sticky-sidebar.min.js"></script>
	<script src="js/custom.js"></script>

</body>

</html>