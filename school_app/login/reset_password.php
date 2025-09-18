<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">إعادة تعيين كلمة المرور</h1>

        <form method="POST" action="reset_password.php" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">البريد الإلكتروني</label>
                <input type="email" name="email" placeholder="أدخل بريدك الإلكتروني" required
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <input type="submit" value="إرسال رابط إعادة التعيين"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 cursor-pointer transition-all">
            </div>
        </form>
    </div>

</body>
</html>
