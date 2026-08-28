<!DOCTYPE html>
<html>
<head>
  <title>Razorpay Test Payment</title>
</head>
<body>

<h2>Enter Razorpay Order Details</h2>

<label>Razorpay Key:</label><br>
<input id="key" type="text" value="rzp_test_S84w7ERQq5c7L0"><br><br>

<label>Order ID:</label><br>
<input id="order" type="text" placeholder="order_RhGn9YR8MH02W2"><br><br>

<label>Amount (₹):</label><br>
<input id="amount" type="number" placeholder="100"><br><br>

<button onclick="pay()">Pay Now</button>

<div id="result" style="margin-top:20px; color:green; font-size:16px;"></div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function pay() {

  var key = document.getElementById('key').value;
  var order_id = document.getElementById('order').value;
  var amount = document.getElementById('amount').value;

  var options = {
    "key": key,
    "amount": amount * 100,
    "currency": "INR",
    "name": "Wallet Topup Test",
    "description": "Razorpay Test Payment",
    "order_id": order_id,

    "handler": function (response){
        document.getElementById('result').innerHTML =
          "Payment Success:<br>" +
          "payment_id: " + response.razorpay_payment_id + "<br>" +
          "order_id: " + response.razorpay_order_id + "<br>" +
          "signature: " + response.razorpay_signature;
    }
  };

  var rzp = new Razorpay(options);
  rzp.open();
}
</script>

</body>
</html>