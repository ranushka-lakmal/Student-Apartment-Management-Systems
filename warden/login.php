<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .login-container {
            background-color: white;
            padding: 40px 60px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        
        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #667eea;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .form-group input[type="submit"]:hover {
            background-color: #5a67d8;
        }

        .link-container {
            text-align: center;
            margin-top: 20px;
        }
        
        .link-container a {
            text-decoration: none;
            color: #667eea;
            transition: color 0.3s;
        }
        
        .link-container a:hover {
            color: #5a67d8;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login to worden Account</h2>
    <form id="loginForm" action="login_action.php" method="post">
        <div class="form-group">
            <input type="email" id="email" name="email" placeholder="Email address..." required>
        </div>
        <div class="form-group">
            <input type="password" id="password" name="password" placeholder="Password..." required>
        </div>
        <div class="form-group">
            <input type="submit" value="Login">
        </div>
    </form>
    <div class="link-container">
        <a href="register.php">Need an account? Sign up!</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    document.getElementById('loginForm').onsubmit = function(event) {
        event.preventDefault();
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;

        // Perform your AJAX request to the backend here
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "login_action.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                // Handle response here
                var response = JSON.parse(this.responseText);
                if (response.success) {
                    window.location.href = 'index.php';
                } else {
                    // Show error using SweetAlert
                    swal("Error", response.message, "error");
                }
            }
        }
        xhr.send("email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password));
    };
</script>

</body>
</html>
