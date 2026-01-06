<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login/login.php');
    exit;
}

$linked_id = $_SESSION['linked_id'];

require_once '../api/functions.php';

$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $linked_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $fname = $row['fname'];
    $lname = $row['lname'];
} else {
    $fname = "Unknown";
}

$sql = "SELECT
    (SELECT COUNT(*) FROM students) AS total_students,
    (SELECT COUNT(*) FROM classes) AS total_classes,
    (SELECT COUNT(*) FROM teachers) AS total_teachers;";
$result = $conn->query($sql);
$total = $result->fetch_assoc();




$sql = "SELECT * FROM admins WHERE id = '$linked_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

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
                    <span><?php echo mb_substr($user['fname'], 0, 1, "UTF-8") .'.'.mb_substr($user['lname'], 0, 1, "UTF-8"); ?></span>
                </div>
                <h2 class="text-2xl font-bold text-gray-800"><?php echo $fname ." ". $lname; ?></h2>
                <p class="text-blue-600 font-semibold">مسؤول</p>
                <div class="space-y-2 mt-6 text-right text-sm divide-y divide-gray-100">
                    <div class="flex justify-between"><span>إجمالي التلاميذ :</span><?php echo $total['total_students']; ?></div>
                    <div class="flex justify-between"><span>إجمالي الأساتذة :</span><?php echo $total['total_teachers']; ?></div>
                    <div class="flex justify-between"><span>إجمالي الأقسام :</span><?php echo $total['total_classes']; ?></div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="bg-white rounded-xl card-shadow mt-6">
                <div class="flex flex-col divide-y divide-gray-200">
                    <button id="notifaction" class="selected px-6 py-3 text-right font-semibold hover:text-blue-600">الإشعارات</button>
                    <button id="messages" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الرسائل</button>
                    <button id="account" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الحسابات</button>
                    <button id="attendance" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الحضور</button>
                    <button id="marks" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الدرجات</button>
                    <button id="class" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الأقسام</button>
                    <button id="student" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الطلاب</button>
                    <button id="parents" class="px-6 py-3 text-right font-semibold hover:text-blue-600">أولياء الأمور</button>
                    <!-- <button id="teacher" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الأساتذة</button>
                    <button id="term" class="px-6 py-3 text-right font-semibold hover:text-blue-600">الدورات</button> -->
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">

            <!-- Notifications Section -->
            <main id="notifaction_section" class="bg-white rounded-xl card-shadow p-6">
                <form id="notifForm" class="space-y-4 p-4 max-w-md">
                    <input type="text" id="notifTitle" placeholder="عنوان الإشعار"
                        class="w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium
                            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                            hover:border-blue-400 transition-colors">

                    <textarea id="notifBody" placeholder="محتوى الإشعار"
                        class="w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium
                            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                            hover:border-blue-400 transition-colors"></textarea>

                    <select name="target" id="target"
                        class="w-full p-2 border border-gray-300 rounded-md bg-white text-gray-700 font-medium
                            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                            hover:border-blue-400 transition-colors">
                        <option value="0" selected disabled>الهدف</option>
                        <option value="all">الجميع</option>
                        <option value="admins">الإدارة</option>
                        <option value="teachers">الأساتذة</option>
                        <option value="classes">الأقسام</option>
                    </select>

                    <div id="dynamicContainer"></div>

                    <button id="add_ann" type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700
                            focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        إضافة إشعار
                    </button>
                </form>

                <table class="w-full mt-6 border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2">العنوان</th>
                            <th class="border p-2">النص</th>
                            <th class="border p-2">التاريخ</th>
                            <th class="border p-2">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </main>

            <!-- Accounts Section -->
            <main id="account_section" class="bg-white rounded-xl card-shadow p-6 hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-6">الحسابات</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <select id="accRole" class="border p-2 rounded w-full">
                        <option value="0" selected disabled>اختر الدور</option>
                        <option value="student">طالب</option>
                        <option value="teacher">أستاذ</option>
                        <option value="admin">الإدارة</option>
                    </select>
                    <select id="accountSelect" class="border p-2 rounded w-full">
                        <option value="0" selected disabled>اختر الحساب</option>
                    </select>
                </div>
                <div class="flex space-x-4 space-x-reverse mb-4">
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
                                <th class="p-2">العنوان</th>
                                <th class="p-2">الموضوع</th>
                                <th class="p-2">النوع</th>
                                <th class="p-2">التاريخ</th>
                                <th class="p-2">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody id="messagesList"></tbody>
                    </table>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">✉️ إرسال رسالة جديدة</h4>
                    <form id="messageForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">المستلم</label>
                            <select id="recipient" class="w-full p-3 border border-gray-300 rounded-lg">
                                <option value="0" selected disabled>اختر المستلم</option>
                                <option value="admin">الإدارة</option>
                                <option value="teacher">أستاذ</option>
                                <option value="student">طالب</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">نوع الرسالة</label>
                            <select id="messageType" class="w-full p-3 border border-gray-300 rounded-lg">
                                <option value="0" selected disabled>اختر نوع الرسالة</option>
                                <option value="inquiry">استفسار</option>
                                <option value="complaint">شكوى</option>
                                <option value="suggestion">اقتراح</option>
                                <option value="absence">اعتذار عن غياب</option>
                                <option value="meeting">طلب موعد</option>
                                <option value="other">أخرى</option>
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
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700" id="message_send_btn">إرسال</button>
                        </div>
                    </form>
                    <div id="messageStatus" class="mt-4 hidden"></div>
                </div>
            </main>

            <!-- Attendance Section -->
            <main id="attendance_section" class="bg-white rounded-xl card-shadow p-6 hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-6">تسجيل الحضور</h3>
                <div class="flex space-x-4 space-x-reverse mb-4">
                    <select id="Attclass" class="border p-2 rounded">
                        <option value="0" selected disabled>اختر القسم</option>
                    </select>
                    <select id="Attsub" class="border p-2 rounded">
                        <option value="0" selected disabled>اختر المادة</option>
                    </select>
                    <button id="getAttList" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">عرض القائمة</button>
                </div>
                <table class="w-full border mb-4">
                    <thead class="bg-gray-100">
                        <tr><th class="p-2">اسم الطالب</th><th class="p-2">غائب</th></tr>
                    </thead>
                    <tbody id="studentsAttList"></tbody>
                </table>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <input id="Attdate" type="date" class="border p-2 rounded">
                    <button id="submitAtt" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">حفظ</button>
                </div>
                <br>
                <h3 class="text-xl font-bold text-gray-800 mb-6">حذف الحضور</h3>
                <div class="flex space-x-4 space-x-reverse mb-4">
                    <select id="AttclassDell" class="border p-2 rounded">
                        <option value="0" selected disabled>اختر القسم</option>
                    </select>
                    <select id="AttsubDell" class="border p-2 rounded">
                        <option value="0" selected disabled>اختر الطالب</option>
                    </select>
                    <button id="getAttListDell" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">عرض القائمة</button>
                </div>
                <table class="w-full border mb-4">
                    <thead class="bg-gray-100">
                        <tr><th class="p-2">المادة</th><th class="p-2">التاريخ</th><th class="p-2">حذف</th></tr>
                    </thead>
                    <tbody id="attDellTbody"></tbody>
                </table>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <button id="submitAttDell" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">حذف</button>
                </div>
            </main>

            
            <main id="class_section" class="bg-white rounded-xl card-shadow p-6 relative hidden">

                <!-- Top-right Tabs -->
                <div class="absolute top-0 right-0 flex">
                <button id="showCreate"
                    class="tab-active px-6 py-3 font-semibold text-gray-800 bg-gray-50 border-b-2 border-blue-600 rounded-t-lg shadow-sm">
                    إنشاء قسم
                </button>
                <button id="showEdit"
                    class="tab-inactive px-6 py-3 font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 border-b-2 border-transparent rounded-t-lg">
                    تعديل قسم
                </button>
                <button id="showDelete"
                    class="tab-inactive px-6 py-3 font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 border-b-2 border-transparent rounded-t-lg">
                    حذف قسم
                </button>
                </div>


                <!-- Create Section -->
                <div id="createSection" class="pt-16">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input id="add_class_input" type="text" class="border p-2 rounded" placeholder="اسم القسم">
                    </div>
                    <button id="add_class" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">حفظ</button>
                </div>

                <!-- Edit Section -->
                <div id="editSection" class="hidden pt-16">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <h4 class="text-xl font-bold text-gray-800 mb-6">اختر القسم</h4>
                        <select name="class" id="editClassSelect" class="border p-2 rounded">
                            <option value="0" selected disabled>اختر القسم</option>
                        </select>
                        <input id="classNameEdit" type="text" class="border p-2 rounded" placeholder="اسم القسم">
                        <input id="classTimeEdit" type="text" class="border p-2 rounded" placeholder="استعمال الزمن">
                        <button id="classSubmit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">حفظ</button>
                    </div>
                </div>
                <!-- Delete Section -->
                <div id="deleteSection" class="hidden pt-16">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <h4 class="text-xl font-bold text-gray-800 mb-6">اختر القسم</h4>
                        <select name="class" id="deleteClassSelect" class="border p-2 rounded">
                            <option value="0" selected disabled>اختر القسم</option>
                        </select>
                        <button id="classDelete" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">حذف</button>
                    </div>
                </div>

            </main>

            <!-- Marks Section -->
            <main id="marks_section" class="bg-white rounded-xl card-shadow p-6 hidden">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <select id="Markclass" class="border p-2 rounded">
                        <option value="0" selected disabled>اختر القسم</option>
                    </select>
                    <select id="Marksubject" class="border p-2 rounded">
                        <option value="0" selected disabled>اختر المادة</option>
                    </select>
                    <select id="student_mark_select" class="border p-2 rounded">
                        <option value="0" selected disabled>اختر الطالب</option>
                    </select>
                    <select id="term_mark_select" class="border p-2 rounded">
                        <option value="0" selected disabled>اختر الدورة</option>
                    </select>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-6">إدخال الدرجات</h3>
                <div class="flex items-center space-x-4 space-x-reverse">
                <select name="markToSubmit" id="markToSubmit" class="border p-2 rounded pr-4">
                    <option value="0" selected disabled>الدرجة</option>
                    <option value="ضعيف"> ضعيف</option>
                    <option value="متوسط"> متوسط</option>
                    <option value="لاباس">لاباس</option>
                    <option value="جيد">جيد</option>
                    <option value="جيد جدا">جيد جدا</option>
                    <option value="ممتاز">ممتاز</option>
                </select>
                    <input id="Markdate" type="date" class="border p-2 rounded">
                    <button id="SubmitMark" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">حفظ</button>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-6">حذف الدرجات</h3>
                <button id="Markshow" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">عرض درجات الطالب</button>
                <table class="w-full border mt-4">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">المادة</th>
                            <th class="p-2">النقطة</th>
                            <th class="p-2">التاريخ</th>
                            <th class="p-2">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody id="marksList"></tbody>
                </table>
            </main>

            <!-- Students Section-->
            <main id="student_section" class="bg-white rounded-xl card-shadow p-6 relative hidden">

                <div class="absolute top-0 right-0 flex">
                <button id="addStudentTab"
                    class="tab-active px-6 py-3 font-semibold text-gray-800 bg-gray-50 border-b-2 border-blue-600 rounded-t-lg shadow-sm">
                   إضافة طالب
                </button>
                <button id="studentInfoTab"
                    class="tab-inactive px-6 py-3 font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 border-b-2 border-transparent rounded-t-lg">
                    معلومات طالب
                </button>
                <button id="deleteStudentTab"
                    class="tab-inactive px-6 py-3 font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 border-b-2 border-transparent rounded-t-lg">
                    حذف طالب
                </button>
                </div>
                <!-- Add Student Section -->
                <div id="studentAddSection" class="">

                    <h4 class="text-lg font-semibold text-gray-700 mb-4 mt-12">معلومات الطالب :</h4>
                    <select id="studentClassSelect" class="border p-2 rounded mt-2 mb-4">
                        <option value="0" selected disabled>اختر القسم</option>
                    </select>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input id="studentFname" type="text" class="border p-2 rounded" placeholder="الاسم">
                        <input id="studentLname" type="text" class="border p-2 rounded" placeholder="اللقب">
                        <input id="studentDOB" type="date" class="border p-2 rounded" placeholder="تاريخ الميلاد">
                        <select id="studentSex" class="border p-2 rounded">
                            <option value="0" selected disabled>اختر الجنس</option>
                            <option value="ذكر">ذكر</option>
                            <option value="أنثى">أنثى</option>
                        </select>

                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">معلومات ولي الأمر</h4>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input id="parentFname" type="text" class="border p-2 rounded" placeholder="اسم ولي الأمر الأول">
                        <input id="parentLname" type="text" class="border p-2 rounded" placeholder="اسم ولي الأمر الأخير">
                        <input id="parentPhone" type="text" class="border p-2 rounded" placeholder="هاتف ولي الأمر">
                        <input id="parentEmail" type="email" class="border p-2 rounded" placeholder="بريد ولي الأمر الإلكتروني">
                    </div>
                    <button id="add_student" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">إضافة طالب</button>
                </div>
                <!-- Show Student Info Section -->
                <div id="studentInfoSection" class="hidden">

                    <h4 class="text-lg font-semibold text-gray-700 mb-4 mt-12">معلومات الطالب :</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <select id="studentInfoSelectClass" class="border p-2 rounded mt-2 mb-4">
                            <option value="0" selected disabled>اختر القسم</option>
                        </select>
                        <select class="border p-2 rounded mt-2 mb-4" id="studentInfoSelectStudent">
                            <option value="0" selected disabled>اختر الطالب</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input id="showFname" type="text" class="border p-2 rounded" placeholder="الاسم" >
                        <input id="showLname" type="text" class="border p-2 rounded" placeholder="اللقب" >
                        <input id="showDOB" type="date" class="border p-2 rounded" placeholder="تاريخ الميلاد" >
                        <select name="showSex" id="showSex" class="border p-2 rounded" >
                            <option id="showSexOption" value="0" selected disabled> الجنس</option>
                            <option id="showSexMale" value="ذكر">ذكر</option>
                            <option id="showSexFemale" value="أنثى">أنثى</option>
                        </select>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">معلومات ولي الأمر</h4>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <input id="showParentFname" type="text" class="border p-2 rounded" placeholder="اسم ولي الأمر الأول" disabled>
                        <input id="showParentLname" type="text" class="border p-2 rounded" placeholder="اسم ولي الأمر الأخير" disabled>
                        <input id="showParentPhone" type="text" class="border p-2 rounded" placeholder="هاتف ولي الأمر" disabled>
                        <input id="showParentEmail" type="email" class="border p-2 rounded" placeholder="بريد ولي الأمر الإلكتروني" disabled>
                    </div>
                    <button id="saveStudentInfoBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">حفظ</button>
                </div>
                <!-- Delete Student Section -->
                <div id="studentDeleteSection" class="hidden">
                    <div class="mb-4">
                        <h4 class="text-xl font-bold text-gray-800 mb-6 mt-12">اختر الطالب</h4>
                        <div class="text-red-600 mb-2">
                            <p>>تنبيه: عند حذف الطالب، سيتم حذف جميع بياناته بما في ذلك الحضور والدرجات والمعلومات الشخصية. الرجاء التأكد قبل المتابعة.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4"> 
                            <select name="" id="ClassSelectStudentDelete" class="border p-2 rounded mt-2 mb-4">
                                <option value="0" selected disabled>اختر القسم</option>
                            </select>                           
                            <select id="StudentSelectStudentDelete" class="border p-2 rounded mt-2 mb-4">
                                <option value="0" selected disabled>اختر الطالب</option>
                            </select>
                            <button id="deleteStudentBtn" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">حذف الطالب</button>
                        </div>
                    </div>
                </div>
            </main>
            <!-- teachers Section-->
            <main id="teacher_section" class="bg-white rounded-xl card-shadow p-6 hidden">
                <h3 class="text-xl font-bold text-gray-800 mb-6">إدارة الأساتذة</h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <input id="teacherFname" type="text" class="border p-2 rounded" placeholder="الاسم">
                    <input id="teacherLname" type="text" class="border p-2 rounded" placeholder="اللقب">
                    <input id="teacherEmail" type="email" class="border p-2 rounded" placeholder="البريد الإلكتروني">
                    <input id="teacherPhone" type="tel" class="border p-2 rounded" placeholder="الهاتف">
                </div>
                <button id="add_teacher" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">إضافة أستاذ</button>
            </main>

            <!-- parents Section-->
            <main id="parents_section" class="bg-white rounded-xl card-shadow p-6 hidden grid grid-rows-2 gap-4">
                <h3 class="text-xl font-bold text-gray-800 mb-6">إدارة أولياء الأمور</h3>
                <select name="parentsSelect" id="parentsSelect" class="border p-2 rounded w-full">
                    <option value="0" selected disabled>اختر ولي الامر</option>
                </select>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <input id="parentFnameAdmin" type="text" class="border p-2 rounded" placeholder="الاسم">
                    <input id="parentLnameAdmin" type="text" class="border p-2 rounded" placeholder="اللقب">
                    <input id="parentEmailAdmin" type="email" class="border p-2 rounded" placeholder="البريد الإلكتروني">
                    <input id="parentPhoneAdmin" type="tel" class="border p-2 rounded" placeholder="الهاتف">
                </div>
                <button id="add_parent" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">حفض التغييرات</button>
            </main>

    </div>

    <script src="app.js"></script>
</body>
</html>