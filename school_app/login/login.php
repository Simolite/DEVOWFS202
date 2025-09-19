<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') header("Location: ../admin/admin_dashboard.php");
    if ($_SESSION['role'] === 'teacher') header("Location: ../teacher/teacher_dashboard.php");
    if ($_SESSION['role'] === 'student') header("Location: ../student/student_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: "Tahoma", sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-8 space-y-6">
        <div class="text-center">
        <a href="../index.php"><img src="logo.png" alt="شعار المدرسة" class="mx-auto w-20 h-20 mb-4"></a>
        <h1 class="text-2xl font-bold text-blue-600">تسجيل الدخول</h1>
            <p class="text-gray-600 mt-2">أهلاً بك في نظام إدارة المدرسة</p>
        </div>

        <form method="POST" class="space-y-4">
            <input type="text" id="username" name="username" 
                placeholder="اسم المستخدم"
                required
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none 
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

            <input type="password" id="password" name="password" 
                placeholder="كلمة المرور"
                required
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none 
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

            <input type="submit" name="login" value="تسجيل الدخول"
                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold 
                       hover:bg-blue-700 transition">
        </form>

        <p class="text-center text-sm text-gray-600">
            <a href="reset_password.php" class="text-blue-600 hover:underline">هل نسيت كلمة المرور؟</a>
        </p>
    </div>

</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

require_once '../api/functions.php';

    if ($conn->connect_error) {
        die("فشل الاتصال: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['linked_id'] = $user['linked_id'];
        $_SESSION['role'] = $user['role'];
        
        switch ($user['role']) {
            case 'admin':
                header('Location: ../admin/admin_dashboard.php');
                break;
            case 'teacher':
                header('Location: ../teacher/teacher_dashboard.php');
                break;
            case 'student':
                header('Location: ../student/student_dashboard.php');
                break;
            default:
                echo "<script>alert('دور غير صالح')</script>";
        }
    } else {
        echo "<script>alert('اسم المستخدم أو كلمة المرور غير صحيحة')</script>";
    }
    $conn->close();
}
?>
