<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <img src="logo.png" alt="">
            <section class="welcome">
                <H1>Welcome to the School Management System</H1>
                <p>
                    Your private space An integrated information system that allows the implementation of new school management methods.
                </p>
            </section>

            <section class="login">
                <form method="POST">
                    
                    <input type="text" id="username" name="username" placeholder="Username" required>

                    <input type="password" id="password" name="password" placeholder="Password" required>

                    <input type="submit" name="login" value="Login">
                </form>
                <p class="forgot-password">
                   <a href="reset_password.php"> Forgot your password?</a>
                </p>
            </section>
    </div>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'root', '', 'school_app');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id']   = $user['id'];
        switch ($user['role']) {
            case 'admin':
                $_SESSION['role'] = 'admin';
                $_SESSION['linked_id'] = $user['linked_id'];
                header('Location: ../admin/admin_dashboard.php');
                break;
            case 'teacher':
                $_SESSION['role'] = 'teacher';
                $_SESSION['linked_id'] = $user['linked_id'];
                header('Location: ../teacher/teacher_dashboard.php');
                break;
            case 'student':
                $_SESSION['role'] = 'student';
                $_SESSION['linked_id'] = $user['linked_id'];
                header('Location: ../student/student_dashboard.php');
                break;
            default:
                echo"<script>alert('Invalid role')</script>";
        }


    }else {
        echo "<script>alert('Invalid username or password')</script>";
        header('Location: login.php');
    }
    $conn->close();
}
?>