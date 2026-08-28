<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired - Goride</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F6F6F6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        .logo {
            width: 350px;
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            color: #002d72;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 18px;
            color: #ffffff;
            background-color: #002d72;
            border: 2px solid #002d72;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #001f4d;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="" target="_blank">
            <img src="https://www.goride.run/goride/img/go_ride_logo.png" alt="Goride Logo" class="logo">
        </a>
        <div class="message">
            Sorry, the link has expired. Please try again later.
        </div>
        <a href="#" id="loginButton" class="button">Back to Login</a>
    </div>

    <script>
        const loginButton = document.getElementById('loginButton');
        const loginUrl = `${window.location.protocol}//${window.location.host}/login`;
        loginButton.href = loginUrl;
    </script>
</body>
</html>