<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تطبيق المدرسة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: "Tahoma", sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Navigation -->
    <header class="bg-blue-600 text-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">تطبيق المدرسة</h1>
            <nav class="hidden md:flex space-x-6 rtl:space-x-reverse">
                <a href="#features" class="hover:underline">المميزات</a>
                <a href="#about" class="hover:underline">عن التطبيق</a>
                <a href="#contact" class="hover:underline">اتصل بنا</a>
            </nav>
            <a href="login/login.php" class="px-4 py-2 bg-white text-blue-600 font-semibold rounded-md hover:bg-gray-100">تسجيل الدخول</a>
        </div>
    </header>

    <!-- Hero -->
    <section class="pt-28 pb-20 bg-blue-600 text-white text-center">
        <div class="max-w-3xl mx-auto px-6">
            <h2 class="text-4xl font-bold mb-4">مرحبا بكم في تطبيق المدرسة</h2>
            <p class="text-lg mb-6">إدارة الطلاب، الأساتذة، الأقسام والإعلانات في مكان واحد.</p>
            <a href="login/login.php" class="px-6 py-3 bg-white text-blue-600 font-medium rounded-md shadow hover:bg-gray-100">
                تسجيل الدخول الآن
            </a>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-8 text-center">
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">إدارة الحضور</h3>
                <p class="text-gray-600">تسجيل وتتبع حضور الطلاب بسهولة.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">العلامات والتقارير</h3>
                <p class="text-gray-600">متابعة مستوى الطلاب عبر تقارير مفصلة.</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-md transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">الإعلانات</h3>
                <p class="text-gray-600">إرسال إشعارات للإدارة، الأساتذة أو الطلاب بسرعة.</p>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="py-16">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-blue-600 mb-4">عن التطبيق</h2>
            <p class="text-lg text-gray-700 leading-relaxed">
                تم تطوير تطبيق المدرسة لتسهيل الإدارة اليومية بين الطلاب، المعلمين والإدارة.
                يتميز بواجهة بسيطة وسهلة الاستخدام متوافقة مع جميع الأجهزة.
            </p>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="py-16 bg-gray-50">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-blue-600 mb-4">اتصل بنا</h2>
            <p class="text-gray-700 mb-4">للاستفسارات والدعم: </p>
            <a href="mailto:support@schoolapp.com" class="text-blue-600 font-semibold hover:underline">support@schoolapp.com</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white py-6 text-center">
        <p>&copy; <?php echo date("Y"); ?> تطبيق المدرسة - جميع الحقوق محفوظة</p>
    </footer>

</body>
</html>
