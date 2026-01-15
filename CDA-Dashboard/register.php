<?php
session_start();
include "config.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Insert into users table
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CDA Registration</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f9f7;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background: #fff;
        padding: 30px;
        width: 380px;
        border-radius: 12px;
        box-shadow: 0px 6px 15px rgba(0,0,0,0.1);
        text-align: center;
        position: relative;
    }
    .logo {
        position: absolute;
        top: 15px;
        left: 15px;
        width: 60px;
        height: auto;
    }
    h2 {
        color: #67C090;
        margin-top: 0;
        margin-bottom: 25px;
    }
    input {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }
    input:focus {
        outline: none;
        border: 1px solid #67C090;
    }
    button {
        width: 100%;
        background: #67C090;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        margin-top: 10px;
    }
    button:hover {
        background: #56a87a;
    }
    p {
        margin-top: 15px;
        font-size: 14px;
    }
    p a {
        color: #67C090;
        text-decoration: none;
        font-weight: bold;
    }
    .error {
        color: red;
        font-size: 13px;
        margin-bottom: 10px;
    }
    .success {
        color: green;
        font-size: 13px;
        margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="cda.webp" alt="CDA Logo" class="logo">
    <h2>Welcome to CDA</h2>

    <?php 
    if (!empty($error)) echo "<p class='error'>$error</p>"; 
    if (!empty($success)) echo "<p class='success'>$success</p>"; 
    ?>

    <form method="POST" autocomplete="off">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">Register</button>
    </form>

    <p>Already registered? <a href="login.php">Login here</a></p>
  </div>
</body>
</html>
