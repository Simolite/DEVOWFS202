<!DOCTYPE html>
<html lang="ar" dir="rtl" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madrasa Al-Noor - Escuela Islámica</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap');
        
        .arabic-font { font-family: 'Amiri', serif; }
        .main-font { font-family: 'Inter', sans-serif; }
        
        .hero-pattern {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(34, 197, 94, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
        }
        
        .islamic-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23059669' fill-opacity='0.05'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm0 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }
        
        .mobile-menu.active {
            transform: translateX(0);
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Language transition */
        .lang-transition {
            transition: opacity 0.3s ease-in-out;
        }

        /* Spanish layout adjustments */
        [dir="ltr"] .space-x-reverse > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 0;
            margin-right: calc(2rem * var(--tw-space-x-reverse));
            margin-left: calc(2rem * (1 - var(--tw-space-x-reverse)));
        }
    </style>
</head>
<body class="main-font bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center bg-white shadow-sm">
                        <img src="https://drive.google.com/uc?export=view&id=12ACIyNQZvVtC3l-8lE9X_ePb-hM-ixM5" alt="شعار مدرسة مسجد الهجرة" class="w-full h-full object-contain" onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'text-green-600 font-bold text-lg\'><img src=logo.png></span>'; this.parentElement.classList.add('bg-gradient-to-br', 'from-green-600', 'to-blue-600'); this.parentElement.classList.remove('bg-white', 'shadow-sm');">
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800 arabic-font" data-ar="مدرسة مسجد الهجرة" data-es="Madrasa Mezquita Al-Hijra">مدرسة مسجد الهجرة</h1>
                        <p class="text-xs text-gray-600">Madrasa Masjid Al-Hijra</p>
                        
                    </div>
                </div>
                
                <div class="hidden md:flex items-center space-x-8 space-x-reverse">
                    <a href="#inicio" class="text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font nav-link" data-ar="الرئيسية" data-es="Inicio">الرئيسية</a>
                    <a href="#programas" class="text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font nav-link" data-ar="البرامج" data-es="Programas">البرامج</a>
                    <a href="#nosotros" class="text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font nav-link" data-ar="من نحن" data-es="Nosotros">من نحن</a>
                    <a href="#horarios" class="text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font nav-link" data-ar="المواعيد" data-es="Horarios">المواعيد</a>
                    <a href="#contacto" class="text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font nav-link" data-ar="اتصل بنا" data-es="Contacto">اتصل بنا</a>
                    <button id="lang-toggle" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-sm font-medium transition-colors">
                        ES
                    </button>
                </div>
                
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu md:hidden fixed top-16 right-0 w-full h-screen bg-white shadow-lg z-40">
            <div class="px-4 py-6 space-y-4">
                <a href="#inicio" class="block text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font text-lg mobile-nav-link" data-ar="الرئيسية" data-es="Inicio">الرئيسية</a>
                <a href="#programas" class="block text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font text-lg mobile-nav-link" data-ar="البرامج" data-es="Programas">البرامج</a>
                <a href="#nosotros" class="block text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font text-lg mobile-nav-link" data-ar="من نحن" data-es="Nosotros">من نحن</a>
                <a href="#horarios" class="block text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font text-lg mobile-nav-link" data-ar="المواعيد" data-es="Horarios">المواعيد</a>
                <a href="#contacto" class="block text-gray-700 hover:text-green-600 transition-colors font-medium arabic-font text-lg mobile-nav-link" data-ar="اتصل بنا" data-es="Contacto">اتصل بنا</a>
                <button id="mobile-lang-toggle" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition-colors" data-ar="Cambiar a Español" data-es="تغيير إلى العربية">
                    Cambiar a Español
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero-pattern bg-gradient-to-br from-green-50 to-blue-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center fade-in">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-800 mb-6 arabic-font" data-ar="أهلاً وسهلاً بكم في مدرسة مسجد الهجرة" data-es="Bienvenidos a la Madrasa Mezquita Al-Hijra">
                    أهلاً وسهلاً بكم في <span class="text-green-600">مدرسة مسجد الهجرة</span>
                </h1>
                <p class="text-2xl arabic-font text-gray-600 mb-4" data-ar="بسم الله الرحمن الرحيم" data-es="En el nombre de Alá, el Compasivo, el Misericordioso">بسم الله الرحمن الرحيم</p>
                <p class="text-lg text-gray-600 mb-8 max-w-4xl mx-auto leading-relaxed arabic-font" data-ar="مدرسة الهجرة مدرسة إسلامية تهدف إلى تربية الناشئة على تعاليم الدين الحنيف، حيث تقدم دروساً في تحفيظ القرآن وتعليم التلاوة والتجويد والتفسير، إلى جانب دراسة السنة النبوية والفقه والعبادات والأخلاق والتاريخ الإسلامي واللغة العربية والنحو. كما تولي المدرسة اهتماماً خاصاً بالأطفال من خلال تنظيم أنشطة ترفيهية وثقافية تجمع بين التعليم والمتعة لبناء جيل متوازن في العلم والعمل والأخلاق." data-es="La Madrasa Al-Hijra es una escuela islámica que tiene como objetivo educar a los jóvenes en las enseñanzas de la religión verdadera. Ofrece clases de memorización del Corán, recitación, tajwid e interpretación, además del estudio de la Sunnah profética, jurisprudencia, adoración, ética, historia islámica, lengua árabe y gramática. La escuela también presta especial atención a los niños organizando actividades recreativas y culturales que combinan educación y diversión para construir una generación equilibrada en conocimiento, trabajo y moral.">
                    مدرسة الهجرة مدرسة إسلامية تهدف إلى تربية الناشئة على تعاليم الدين الحنيف، حيث تقدم دروساً في تحفيظ القرآن وتعليم التلاوة والتجويد والتفسير، إلى جانب دراسة السنة النبوية والفقه والعبادات والأخلاق والتاريخ الإسلامي واللغة العربية والنحو. كما تولي المدرسة اهتماماً خاصاً بالأطفال من خلال تنظيم أنشطة ترفيهية وثقافية تجمع بين التعليم والمتعة لبناء جيل متوازن في العلم والعمل والأخلاق.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="login/login.php"><button class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors arabic-font" data-ar="تسجيل الدخول" data-es="Iniciar Sesión">
                        تسجيل الدخول
                    </button></a>

                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programas" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4 arabic-font" data-ar="برامجنا التعليمية" data-es="Nuestros Programas Educativos">برامجنا التعليمية</h2>
                <p class="text-xl text-gray-600 arabic-font" data-ar="برامج متنوعة لجميع الأعمار" data-es="Programas diversos para todas las edades">برامج متنوعة لجميع الأعمار</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-hover bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-xl border border-green-200">
                    <div class="text-4xl mb-4 text-center">📖</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 arabic-font text-center" data-ar="القرآن الكريم" data-es="El Sagrado Corán">القرآن الكريم</h3>
                    <p class="text-gray-600 arabic-font text-center leading-relaxed mb-6" data-ar="برنامج شامل لتعليم القرآن الكريم وعلومه" data-es="Programa integral para la enseñanza del Sagrado Corán y sus ciencias">
                        برنامج شامل لتعليم القرآن الكريم وعلومه
                    </p>
                    <ul class="space-y-2 arabic-font">
                        <li class="flex items-center text-gray-700">
                            <span class="text-green-600 ml-2">✓</span>
                            <span data-ar="الحفظ" data-es="Memorización">الحفظ</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <span class="text-green-600 ml-2">✓</span>
                            <span data-ar="التلاوة والتجويد" data-es="Recitación y Tajwid">التلاوة والتجويد</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <span class="text-green-600 ml-2">✓</span>
                            <span data-ar="التفسير" data-es="Interpretación">التفسير</span>
                        </li>
                    </ul>
                </div>
                
                <div class="card-hover bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-xl border border-blue-200">
                    <div class="text-4xl mb-4 text-center">🕌</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 arabic-font text-center" data-ar="العلوم الشرعية" data-es="Ciencias Islámicas">العلوم الشرعية</h3>
                    <p class="text-gray-600 arabic-font text-center leading-relaxed mb-6" data-ar="دراسة شاملة للعلوم الإسلامية" data-es="Estudio integral de las ciencias islámicas">
                        دراسة شاملة للعلوم الإسلامية
                    </p>
                    <ul class="space-y-2 arabic-font">
                        <li class="flex items-center text-gray-700">
                            <span class="text-blue-600 ml-2">✓</span>
                            <span data-ar="السيرة النبوية" data-es="Biografía del Profeta">السيرة النبوية</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <span class="text-blue-600 ml-2">✓</span>
                            <span data-ar="الأحاديث" data-es="Hadices">الأحاديث</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <span class="text-blue-600 ml-2">✓</span>
                            <span data-ar="الفقه والعقيدة" data-es="Jurisprudencia y Creencia">الفقه والعقيدة</span>
                        </li>
                    </ul>
                </div>
                
                <div class="card-hover bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-xl border border-purple-200">
                    <div class="text-4xl mb-4 text-center">✍️</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 arabic-font text-center" data-ar="اللغة العربية" data-es="Lengua Árabe">اللغة العربية</h3>
                    <p class="text-gray-600 arabic-font text-center leading-relaxed mb-6" data-ar="تعليم شامل للغة العربية والثقافة الإسلامية" data-es="Enseñanza integral del idioma árabe y la cultura islámica">
                        تعليم شامل للغة العربية والثقافة الإسلامية
                    </p>
                    <ul class="space-y-2 arabic-font">
                        <li class="flex items-center text-gray-700">
                            <span class="text-purple-600 ml-2">✓</span>
                            <span data-ar="اللغة العربية" data-es="Lengua Árabe">اللغة العربية</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <span class="text-purple-600 ml-2">✓</span>
                            <span data-ar="الأخلاق الإسلامية" data-es="Ética Islámica">الأخلاق الإسلامية</span>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <span class="text-purple-600 ml-2">✓</span>
                            <span data-ar="التاريخ الإسلامي" data-es="Historia Islámica">التاريخ الإسلامي</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="nosotros" class="py-20 islamic-pattern bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6 arabic-font" data-ar="من نحن" data-es="Quiénes Somos">من نحن</h2>
                    <p class="text-lg text-gray-600 mb-6 arabic-font leading-relaxed" data-ar="مدرسة مسجد الهجرة تأسست سنة 2000 في بلدة سوماراقا (Zumarraga) وتضم هيئة تعليمية متخصصة في تعليم القرآن الكريم وعلومه للذكور وأستاذة لتعليم القرآن للإناث، إضافة إلى أساتذة اللغة العربية والتاريخ والعبادات بدعم من هيئة إدارية وعدد من المتطوعين من أبناء الجالية المقيمة." data-es="La Madrasa Mezquita Al-Hijra fue fundada en el año 2000 en la localidad de Zumarraga e incluye un cuerpo docente especializado en la enseñanza del Sagrado Corán y sus ciencias para hombres y una profesora para enseñar el Corán a mujeres, además de profesores de lengua árabe, historia y adoración con el apoyo de un cuerpo administrativo y varios voluntarios de los miembros de la comunidad residente.">
                        مدرسة مسجد الهجرة تأسست سنة 2000 في بلدة سوماراقا (Zumarraga) وتضم هيئة تعليمية متخصصة في تعليم القرآن الكريم وعلومه للذكور وأستاذة لتعليم القرآن للإناث، إضافة إلى أساتذة اللغة العربية والتاريخ والعبادات بدعم من هيئة إدارية وعدد من المتطوعين من أبناء الجالية المقيمة.
                    </p>
                    <p class="text-lg text-gray-600 mb-8 arabic-font leading-relaxed" data-ar="نؤمن بأهمية التعليم الديني المتوازن الذي يجمع بين الأصالة والمعاصرة، ونسعى لإعداد جيل مؤمن متعلم قادر على المساهمة الإيجابية في المجتمع." data-es="Creemos en la importancia de la educación religiosa equilibrada que combina autenticidad y modernidad, y buscamos preparar una generación creyente y educada capaz de contribuir positivamente a la sociedad.">
                        نؤمن بأهمية التعليم الديني المتوازن الذي يجمع بين الأصالة والمعاصرة، ونسعى لإعداد جيل مؤمن متعلم قادر على المساهمة الإيجابية في المجتمع.
                    </p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 arabic-font">+100</div>
                            <div class="text-gray-600 arabic-font" data-ar="طالب وطالبة" data-es="Estudiantes">طالب وطالبة</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 arabic-font">+3</div>
                            <div class="text-gray-600 arabic-font" data-ar="معلم ومعلمة" data-es="Profesores">معلم ومعلمة</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 arabic-font text-center" data-ar="رؤيتنا ورسالتنا" data-es="Nuestra Visión y Misión">رؤيتنا ورسالتنا</h3>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4 space-x-reverse">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-green-600 font-bold">👁️</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 arabic-font" data-ar="الرؤية" data-es="Visión">الرؤية</h4>
                                <p class="text-gray-600 arabic-font" data-ar="أن نكون المرجع التعليمي الإسلامي الرائد في إسبانيا" data-es="Ser la referencia educativa islámica líder en España">أن نكون المرجع التعليمي الإسلامي الرائد في إسبانيا</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4 space-x-reverse">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold">🎯</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 arabic-font" data-ar="الرسالة" data-es="Misión">الرسالة</h4>
                                <p class="text-gray-600 arabic-font" data-ar="تربية جيل مؤمن متعلم على القيم الإسلامية الأصيلة" data-es="Educar una generación creyente y educada en los valores islámicos auténticos">تربية جيل مؤمن متعلم على القيم الإسلامية الأصيلة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="horarios" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4 arabic-font" data-ar="مواعيد الدروس" data-es="Horarios de Clases">مواعيد الدروس</h2>
                <p class="text-xl text-gray-600 arabic-font" data-ar="جدول الحصص الأسبوعي" data-es="Horario semanal de clases">جدول الحصص الأسبوعي</p>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl p-8 shadow-lg">
                <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 arabic-font text-center" data-ar="السبت" data-es="Sábado">السبت</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 arabic-font" data-ar="تحفيظ القرآن" data-es="Memorización del Corán">تحفيظ القرآن</span>
                                <span class="text-green-600 font-semibold">10:30 - 12:00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 arabic-font" data-ar="اللغة العربية" data-es="Lengua Árabe">اللغة العربية</span>
                                <span class="text-blue-600 font-semibold">10:00 - 14:00</span>
                            </div>
                        </div>
                    </div>
                    

                    
                    <div class="bg-white p-6 rounded-lg shadow-md md:col-span-2 lg:col-span-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 arabic-font text-center" data-ar="أيام الأسبوع" data-es="Días de Semana">أيام الأسبوع</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 arabic-font" data-ar="دروس مسائية" data-es="Clases vespertinas">دروس مسائية</span>
                                <span class="text-orange-600 font-semibold">17:30 - 19:30</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contacto" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4 arabic-font" data-ar="اتصل بنا" data-es="Contáctanos">اتصل بنا</h2>
                <p class="text-xl text-gray-600 arabic-font" data-ar="نحن هنا للإجابة على استفساراتكم" data-es="Estamos aquí para responder a sus consultas">نحن هنا للإجابة على استفساراتكم</p>
            </div>
            
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="space-y-8">
                    <div class="flex items-start space-x-4 space-x-reverse">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 arabic-font" data-ar="العنوان" data-es="Dirección">العنوان</h3>
                            <p class="text-gray-600 arabic-font" data-ar="سوماراقا، إسبانيا" data-es="">سوماراقا، إسبانيا</p>
                            <p class="text-gray-500">Calle Antonino Oraa N 13 bajo Zumarraga 20700, España</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4 space-x-reverse">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 arabic-font" data-ar="الهاتف" data-es="Teléfono">الهاتف</h3>
                            <p class="text-gray-600">+34 123 456 789</p>
                            <p class="text-gray-600">+34 987 654 321</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4 space-x-reverse">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 arabic-font" data-ar="البريد الإلكتروني" data-es="Correo Electrónico">البريد الإلكتروني</h3>
                            <p class="text-gray-600">hijaramadrasa@gmail.com</p>
                            <p class="text-gray-600">hijaramasjid@gmail.com</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 arabic-font text-center" data-ar="أرسل لنا رسالة" data-es="Envíanos un mensaje">أرسل لنا رسالة</h3>
                    <form class="space-y-6" id="contact-form">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2 arabic-font" data-ar="الاسم الكامل" data-es="Nombre completo">الاسم الكامل</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" data-ar-placeholder="أدخل اسمك الكامل" data-es-placeholder="Ingresa tu nombre completo" placeholder="أدخل اسمك الكامل" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2 arabic-font" data-ar="البريد الإلكتروني" data-es="Correo electrónico">البريد الإلكتروني</label>
                            <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="example@email.com" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2 arabic-font" data-ar="الموضوع" data-es="Asunto">الموضوع</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required id="subject-select">
                                <option value="" data-ar="اختر الموضوع" data-es="Selecciona el asunto">اختر الموضوع</option>
                                <option value="registration" data-ar="التسجيل في المدرسة" data-es="Inscripción en la escuela">التسجيل في المدرسة</option>
                                <option value="programs" data-ar="الاستفسار عن البرامج" data-es="Consulta sobre programas">الاستفسار عن البرامج</option>
                                <option value="schedule" data-ar="مواعيد الدروس" data-es="Horarios de clases">مواعيد الدروس</option>
                                <option value="other" data-ar="أخرى" data-es="Otros">أخرى</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2 arabic-font" data-ar="الرسالة" data-es="Mensaje">الرسالة</label>
                            <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" data-ar-placeholder="اكتب رسالتك هنا..." data-es-placeholder="Escribe tu mensaje aquí..." placeholder="اكتب رسالتك هنا..." required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition-colors arabic-font" data-ar="إرسال الرسالة" data-es="Enviar mensaje">
                            إرسال الرسالة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->


    <script>
        // Language switching functionality
        let currentLang = 'ar';
        
        function switchLanguage() {
            const htmlRoot = document.getElementById('html-root');
            const langToggle = document.getElementById('lang-toggle');
            const mobileLangToggle = document.getElementById('mobile-lang-toggle');
            
            if (currentLang === 'ar') {
                currentLang = 'es';
                htmlRoot.setAttribute('lang', 'es');
                htmlRoot.setAttribute('dir', 'ltr');
                langToggle.textContent = 'AR';
                mobileLangToggle.textContent = 'تغيير إلى العربية';
                
                // Update all elements with data attributes
                document.querySelectorAll('[data-es]').forEach(element => {
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                        element.placeholder = element.getAttribute('data-es-placeholder') || element.getAttribute('data-es');
                    } else if (element.tagName === 'OPTION') {
                        element.textContent = element.getAttribute('data-es');
                    } else {
                        element.textContent = element.getAttribute('data-es');
                    }
                    
                    // Remove Arabic font for Spanish
                    if (element.classList.contains('arabic-font')) {
                        element.style.fontFamily = "'Inter', sans-serif";
                    }
                });
                
                // Update hero title specifically
                const heroTitle = document.querySelector('h1[data-ar]');
                if (heroTitle) {
                    heroTitle.innerHTML = 'Bienvenidos a la <span class="text-green-600">Madrasa Mezquita Al-Hijra</span>';
                }
                
            } else {
                currentLang = 'ar';
                htmlRoot.setAttribute('lang', 'ar');
                htmlRoot.setAttribute('dir', 'rtl');
                langToggle.textContent = 'ES';
                mobileLangToggle.textContent = 'Cambiar a Español';
                
                // Update all elements with data attributes
                document.querySelectorAll('[data-ar]').forEach(element => {
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                        element.placeholder = element.getAttribute('data-ar-placeholder') || element.getAttribute('data-ar');
                    } else if (element.tagName === 'OPTION') {
                        element.textContent = element.getAttribute('data-ar');
                    } else {
                        element.textContent = element.getAttribute('data-ar');
                    }
                    
                    // Restore Arabic font
                    if (element.classList.contains('arabic-font')) {
                        element.style.fontFamily = "'Amiri', serif";
                    }
                });
                
                // Update hero title specifically
                const heroTitle = document.querySelector('h1[data-ar]');
                if (heroTitle) {
                    heroTitle.innerHTML = 'أهلاً وسهلاً بكم في <span class="text-green-600">مدرسة مسجد الهجرة</span>';
                }
            }
        }

        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });

        // Close mobile menu when clicking on links
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
            });
        });

        // Language toggle event listeners
        document.getElementById('lang-toggle').addEventListener('click', switchLanguage);
        document.getElementById('mobile-lang-toggle').addEventListener('click', () => {
            switchLanguage();
            mobileMenu.classList.remove('active');
        });

        // Smooth scrolling for navigation links
        const navLinks = document.querySelectorAll('.nav-link, .mobile-nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Contact form submission
        const contactForm = document.getElementById('contact-form');
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Show success message
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            const successText = currentLang === 'ar' ? 'تم الإرسال بنجاح!' : '¡Mensaje enviado con éxito!';
            submitBtn.textContent = successText;
            submitBtn.style.backgroundColor = '#10b981';
            
            // Reset form after 2 seconds
            setTimeout(() => {
                contactForm.reset();
                submitBtn.textContent = originalText;
                submitBtn.style.backgroundColor = '';
            }, 2000);
        });

        // Add scroll effect to navigation
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('shadow-xl');
            } else {
                nav.classList.remove('shadow-xl');
            }
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = '0.2s';
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        // Observe all sections
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'97eab1b455442147',t:'MTc1Nzc5ODQ4NS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
