<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cổng Thông Tin Sinh Viên Fithou</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen font-sans">
        <!-- Main Container -->
        <div class="min-h-screen flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('hou-logo.png') }}" alt="HOU Logo" class="h-12 w-auto">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">Cổng Thông Tin Sinh Viên Fithou</h1>
                                <p class="text-sm text-gray-600">Hệ thống quản lý thông tin sinh viên</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 mt-5">
                <div class="max-w-4xl w-full">
                    <!-- Welcome Section -->
                    <div class="text-center mb-12">
                        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                            Chào mừng đến với
                        </h2>
                        <h3 class="text-3xl md:text-4xl font-semibold text-blue-600 mb-6">
                            Cổng Thông Tin Sinh Viên Fithou
                        </h3>
                        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                            Vui lòng chọn phương thức đăng nhập phù hợp với vai trò của bạn trong hệ thống
                        </p>
                    </div>

                    <!-- Login Options -->
                    <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                        <!-- Student Login -->
                        <div class="group">
                            <a href="{{ url('/student/login') }}"
                               class="block bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-200 hover:border-blue-300">
                                <div class="p-8 text-center">
                                    <!-- Student Icon -->
                                    <div class="w-20 h-20 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-300">
                                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                        </svg>
                                    </div>

                                    <h4 class="text-2xl font-bold text-gray-900 mb-3">Sinh viên</h4>
                                    <p class="text-gray-600 mb-6">
                                        Truy cập thông tin học tập, đăng ký môn học, xem điểm số và các dịch vụ dành cho sinh viên
                                    </p>

                                    <div class="inline-flex items-center text-blue-600 font-semibold group-hover:text-blue-700">
                                        Đăng nhập dành cho Sinh viên
                                        <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Admin Login -->
                        <div class="group">
                            <a href="{{ url('/admin/login') }}"
                               class="block bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-200 hover:border-indigo-300">
                                <div class="p-8 text-center">
                                    <!-- Admin Icon -->
                                    <div class="w-20 h-20 mx-auto mb-6 bg-indigo-100 rounded-full flex items-center justify-center group-hover:bg-indigo-200 transition-colors duration-300">
                                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>

                                    <h4 class="text-2xl font-bold text-gray-900 mb-3">Quản trị viên</h4>
                                    <p class="text-gray-600 mb-6">
                                        Quản lý hệ thống, quản lý sinh viên, chương trình đào tạo và các chức năng quản trị
                                    </p>

                                    <div class="inline-flex items-center text-indigo-600 font-semibold group-hover:text-indigo-700">
                                        Đăng nhập dành cho Quản trị viên
                                        <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="my-16 text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-8">Tính năng nổi bật</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl p-6 shadow-md">
                                <div class="w-12 h-12 mx-auto mb-4 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-2">Điều chỉnh tín chỉ</h4>
                                <p class="text-sm text-gray-600">Tạo yêu cầu mở lớp, đơn điều chỉnh tín chỉ</p>
                            </div>

                            <div class="bg-white rounded-xl p-6 shadow-md">
                                <div class="w-12 h-12 mx-auto mb-4 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-2">Theo dõi tính trạng học phần/tín chỉ</h4>
                                <p class="text-sm text-gray-600">Theo dõi học phần có thể đăng kí, tình trạng học tập</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="text-center text-gray-600">
                        <p class="mt-1 text-sm">Phát triển bởi Đội ngũ IT - Khoa CNTT Trường Đại Học Mở Hà Nội</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
