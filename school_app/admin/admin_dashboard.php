<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$linked_id = $_SESSION['linked_id'];

$conn = new mysqli('localhost', 'root', '', 'school_app');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT fname FROM admins WHERE id = ?");
$stmt->bind_param("i", $linked_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $fname = $row['fname'];
} else {
    $fname = "Unknown";
}

$sql = "SELECT
    (SELECT COUNT(*) FROM students) AS total_students,
    (SELECT COUNT(*) FROM classes) AS total_classes,
    (SELECT COUNT(*) FROM teachers) AS total_teachers;";
$result = $conn->query($sql);
$total = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الإدارة - <?php echo $fname; ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="tailwindcss.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
        body { font-family: 'Cairo', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-shadow { box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .selected { background: linear-gradient(45deg, #667eea, #764ba2); color: white; border-radius: 0.5rem 0.5rem 0 0; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="gradient-bg text-white py-6 shadow-lg">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                    <span class="text-2xl">🛠️</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">مدرسة الهجرة</h1>
                    <p class="text-blue-100">لوحة الإدارة</p>
                </div>
            </div>
            <a href="../login/logout.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all">
                تسجيل الخروج
            </a>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl card-shadow p-6 text-center">
                <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden border-4 border-purple-600 flex items-center justify-center text-4xl font-bold bg-gradient-to-br from-blue-500 to-purple-600 text-white">
                    <span><?php echo $fname[0]; ?></span>
                </div>
                <h2 class="text-2xl font-bold text-gray-800"><?php echo $fname; ?></h2>
                <p class="text-blue-600 font-semibold">مسؤول</p>
                <div class="space-y-2 mt-6 text-right text-sm divide-y divide-gray-100">
                    <div class="flex justify-between"><span>مجموع التلاميذ :</span><?php echo $total['total_students']; ?></div>
                    <div class="flex justify-between"><span>مجموع الاساتذة :</span><?php echo $total['total_teachers']; ?></div>
                    <div class="flex justify-between"><span>مجموع الاقسام :</span><?php echo $total['total_classes']; ?></div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="bg-white rounded-xl card-shadow mt-6">
                <div class="flex flex-col divide-y divide-gray-200">
                    <button id="notifaction" class="selected px-6 py-3 text-right font-semibold">الإشعارات</button>
                    <button id="messages" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الرسائل</button>
                    <button id="account" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الحسابات</button>
                    <button id="announcement" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الإعلانات</button>
                    <button id="attendance" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الحضور</button>
                    <button id="class" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الفصول</button>
                    <button id="mark" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الدرجات</button>
                    <button id="student" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الطلاب</button>
                    <button id="teacher" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الأساتذة</button>
                    <button id="term" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الدورات</button>
                    <button id="parents" class="px-6 py-3 text-right font-semibold hover:text-blue-600">اولياء الامور</button>
                    <button id="classes" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الاقسام</button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">

            <!-- Notifications Section -->
            <main id="notifaction_section"class="bg-white rounded-xl card-shadow p-6 ">
                <form id="notifForm" class="space-y-4 p-4 max-w-md">
                    <input type="text" id="notifTitle" placeholder="عنوان الإشعار"
                        class="w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium
                            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                            hover:border-blue-400 transition-colors">

                    <textarea id="notifBody" placeholder="محتوى الإشعار"
                        class="w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium
                            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                            hover:border-blue-400 transition-colors"></textarea>

                    <!-- Target Select -->
                    <select name="target" id="target"
                        class="w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium
                            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                            hover:border-blue-400 transition-colors">
                        <option value="0" selected disabled>الهدف</option>
                        <option value="all">الكل</option>
                        <option value="admins">الإدارة</option>
                        <option value="teachers">الأساتذة</option>
                        <option value="classes">الأقسام</option>
                        <option value="students">الطلاب</option>
                    </select>

                    <!-- Dynamic selects will appear here -->
                    <div id="dynamicContainer"></div>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700
                            focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        إضافة إشعار
                    </button>
                </form>
            </main>
            <!-- Accounts Section -->
            <main id="account_section" class="bg-white rounded-xl card-shadow p-6 hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-6">الحسابات</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <select id="accRole" class="border p-2 rounded w-full">
                        <option value="0" selected disabled>اختر الدور</option>
                        <option value="student">طالب</option>
                        <option value="teacher">أستاذ</option>
                        <option value="admin">مسؤول</option>
                    </select>
                    <select id="account" class="border p-2 rounded w-full">
                        <option value="0" selected disabled>اختر الحساب</option>
                    </select>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">عرض الحساب</button>
                </div>
                <div class="flex space-x-4 space-x-reverse mb-4">
                    <input id="userName" type="text" disabled class="border p-2 rounded w-full" placeholder="اسم المستخدم">
                    <input id="password" type="text" class="border p-2 rounded w-full" placeholder="كلمة المرور">
                </div>
                <div class="flex justify-between">
                    <button id="applyBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">تطبيق التغييرات</button>
                    <button id="cancelBtn" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">إلغاء</button>
                </div>
            </main>

            <!-- Messages Section -->
            <main id="messages_section" class="bg-white rounded-xl card-shadow p-6 hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-6">الرسائل</h3>
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">📩 الرسائل الواردة</h4>
                    <table class="w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2">المرسل</th>
                                <th class="p-2">الموضوع</th>
                                <th class="p-2">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody id="messagesList"></tbody>
                    </table>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">✉️ إرسال رسالة جديدة</h4>
                    <form id="messageForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">المرسل إليه</label>
                            <select id="recipient" class="w-full p-3 border border-gray-300 rounded-lg">
                                <option value="0" selected disabled>اختر المرسل إليه</option>
                                <option value="admin">مسؤول</option>
                                <option value="teacher">أستاذ</option>
                                <option value="student">طالب</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">موضوع الرسالة</label>
                            <input type="text" id="message_subject" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="اكتب الموضوع هنا...">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">محتوى الرسالة</label>
                            <textarea id="messageContent" rows="5" class="w-full p-3 border border-gray-300 rounded-lg resize-none" placeholder="اكتب رسالتك هنا..."></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="reset" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600">مسح</button>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700">إرسال</button>
                        </div>
                    </form>
                    <div id="messageStatus" class="mt-4 hidden"></div>
                </div>
            </main>

        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>
