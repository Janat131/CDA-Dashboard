<?php
session_start();
include "users_db.php";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Corrected query to match your table columns
    $sql = "SELECT * FROM users WHERE admin='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['superadmin'] = $username;
        header("Location: plots.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CDA Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
    height: 100vh;
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    background: url('Background.jpg') no-repeat center center fixed;
    background-size: cover;
}

/* Glassmorphism form container */
.login-form {
    width: 380px;
    padding: 45px 35px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.35);
    text-align: center;
    border: 1px solid rgba(255,255,255,0.3);
    animation: fadeIn 1s ease-in-out;
}

/* Logo */
.login-form img {
    width: 90px;
    margin-bottom: 20px;
    filter: drop-shadow(0 0 5px #016B61);
}

/* Heading */
.login-form h2 {
    margin-bottom: 30px;
    color: #016B61;
    font-weight: 600;
    font-size: 26px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* Inputs */
input[type=text], input[type=password] {
    width: 100%;
    padding: 14px 18px;
    margin: 12px 0;
    border: 2px solid rgba(176, 206, 136,0.7);
    border-radius: 12px;
    font-size: 16px;
    outline: none;
    background: rgba(255,255,255,0.7);
    transition: all 0.3s ease;
}

input[type=text]:focus, input[type=password]:focus {
    border-color: #016B61;
    box-shadow: 0 0 12px rgba(1,107,97,0.6);
    background: rgba(255,255,255,0.9);
}

/* Submit button */
input[type=submit] {
    width: 100%;
    padding: 15px;
    margin-top: 20px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #4C763B, #016B61);
    color: white;
    font-size: 17px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(1,107,97,0.4);
    transition: all 0.3s ease;
}

input[type=submit]:hover {
    background: linear-gradient(135deg, #016B61, #4C763B);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(1,107,97,0.6);
}

/* Error message */
.error {
    color: #B00020;
    margin-top: 12px;
    font-size: 14px;
    font-weight: 500;
    text-shadow: 0 1px 1px rgba(0,0,0,0.2);
}

/* Animations */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-20px);}
    to {opacity: 1; transform: translateY(0);}
}

/* Responsive */
@media (max-width: 420px) {
    .login-form {
        width: 90%;
        padding: 35px 20px;
    }
    .login-form img {
        width: 70px;
    }
}
</style>
</head>
<body>
<div class="login-form">
    <img src="CDALOGO.png" alt="CDA Logo">
    <h2>CDA Login</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
    <p class="error"><?php echo $error; ?></p>
</div>
</body>
</html>