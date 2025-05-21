<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Camp - Temukan Bootcamp IT Terbaik</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-gray-50">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center">
                        <span class="text-blue-600 font-bold text-xl">Campus</span>
                        <span class="bg-blue-600 text-white px-2 py-1 rounded font-bold text-xl">Hub</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-blue-600 font-medium">Home</a>
                    <a href="views/bootcamp/index.php" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">Bootcamps</a>
                    <a href="views/about/index.php" class="text-gray-700 hover:text-blue-600 transition-colors duration-300">About Us</a>
                </nav>
                
                <div class="flex space-x-3">
                    <a href="views/auth/login.php" class="px-4 py-2 rounded-md border border-blue-600 text-blue-600 hover:bg-blue-50 transition-colors duration-300">Login</a>
                    <a href="views/auth/register.php" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-300">Sign Up</a>
                    
                    <!-- Mobile menu button -->
                    <button class="md:hidden text-gray-700 focus:outline-none" id="mobile-menu-button">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="index.php" class="block px-3 py-2 rounded-md text-blue-600 font-medium">Home</a>
                    <a href="views/bootcamp/index.php" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">Bootcamps</a>
                    <a href="views/about/index.php" class="block px-3 py-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-blue-50">About Us</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-blue-900 text-white">
        <div class="container mx-auto px-4 py-16 md:py-20">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 animate__animated animate__fadeInLeft">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4">Wujudkan Potensimu Melalui Pengalaman yang Tak Terbatas!</h1>
                    <p class="mb-6">Kembangkan dirimu sekarang juga melalui program terbaik dari bootcamp terpercaya.</p>
                    <a href="views/bootcamp/index.php" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-300">Mulai Sekarang</a>
                    
                    <div class="flex mt-8 space-x-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold">20+</div>
                            <div class="text-sm text-blue-200">Bootcamp Tersedia</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">10+</div>
                            <div class="text-sm text-blue-200">Kategori</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">5+</div>
                            <div class="text-sm text-blue-200">Partner Bootcamp</div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 mt-8 md:mt-0 animate__animated animate__fadeInRight">
                    <img src="assets/images/hero-image.jpg" alt="Coding Bootcamp" class="rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">KATEGORI</h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 text-center">
                <a href="views/bootcamp/category.php?id=1" class="p-4 hover:shadow-md rounded-lg transition-shadow duration-300 group">
                    <div class="w-16 h-16 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-300">
                        <img src="assets/images/icons/webdev.png" alt="Web Dev" class="w-8 h-8">
                    </div>
                    <h3 class="font-medium">Web Dev</h3>
                </a>
                
                <a href="views/bootcamp/category.php?id=2" class="p-4 hover:shadow-md rounded-lg transition-shadow duration-300 group">
                    <div class="w-16 h-16 mx-auto mb-3 bg-pink-100 rounded-full flex items-center justify-center group-hover:bg-pink-200 transition-colors duration-300">
                        <img src="assets/images/icons/datascience.png" alt="Data Sci" class="w-8 h-8">
                    </div>
                    <h3 class="font-medium">Data Sci</h3>
                </a>
                
                <a href="views/bootcamp/category.php?id=3" class="p-4 hover:shadow-md rounded-lg transition-shadow duration-300 group">
                    <div class="w-16 h-16 mx-auto mb-3 bg-red-100 rounded-full flex items-center justify-center group-hover:bg-red-200 transition-colors duration-300">
                        <img src="assets/images/icons/uiux.png" alt="UI/UX" class="w-8 h-8">
                    </div>
                    <h3 class="font-medium">UI/UX Design</h3>
                </a>
                
                <a href="views/bootcamp/category.php?id=4" class="p-4 hover:shadow-md rounded-lg transition-shadow duration-300 group">
                    <div class="w-16 h-16 mx-auto mb-3 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-300">
                        <img src="assets/images/icons/mobiledev.png" alt="Mobile Dev" class="w-8 h-8">
                    </div>
                    <h3 class="font-medium">Mobile Dev</h3>
                </a>
                
                <a href="views/bootcamp/category.php?id=5" class="p-4 hover:shadow-md rounded-lg transition-shadow duration-300 group">
                    <div class="w-16 h-16 mx-auto mb-3 bg-yellow-100 rounded-full flex items-center justify-center group-hover:bg-yellow-200 transition-colors duration-300">
                        <img src="assets/images/icons/ai.png" alt="AI" class="w-8 h-8">
                    </div>
                    <h3 class="font-medium">Artificial Intelligence</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Bootcamps Section -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-bold mb-8">Jelajahi Acara Unggulan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Bootcamp Card 1 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img src="assets/images/bootcamps/uiux-design.jpg" alt="UI/UX Design" class="w-full h-48 object-cover">
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Figma UI UX Design</h3>
                        <p class="text-gray-600 mb-4">Belajar cara buat UI UX design pakai figma di bootcamp ini bareng mentor expert!</p>
                        
                        <div class="flex items-center mb-4">
                            <img src="assets/images/instructors/john.jpg" alt="Instructor" class="w-8 h-8 rounded-full mr-2">
                            <span class="text-sm text-gray-700">Instructor: John Doe</span>
                        </div>
                        
                        <div class="flex justify-between text-sm text-gray-500 mb-4">
                            <div>
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                10 Maret 2025
                            </div>
                            <div>
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                3 bulan
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-blue-600 font-bold">Rp 2.500.000</span>
                            <a href="views/bootcamp/detail.php?id=1" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-300">Detail</a>
                        </div>
                    </div>
                </div>
                
                <!-- Bootcamp Card 2 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img src="assets/images/bootcamps/language.jpg" alt="Foreign Language" class="w-full h-48 object-cover">
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Maximize Our Language Skills</h3>
                        <p class="text-gray-600 mb-4">Tingkatkan kemampuan bahasa asing Anda dengan bootcamp intensif yang praktis dan interaktif!</p>
                        
                        <div class="flex items-center mb-4">
                            <img src="assets/images/instructors/sarah.jpg" alt="Instructor" class="w-8 h-8 rounded-full mr-2">
                            <span class="text-sm text-gray-700">Instructor: Sarah Johnson</span>
                        </div>
                        
                        <div class="flex justify-between text-sm text-gray-500 mb-4">
                            <div>
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                15 April 2025
                            </div>
                            <div>
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                2 bulan
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-blue-600 font-bold">Rp 1.800.000</span>
                            <a href="views/bootcamp/detail.php?id=2" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-300">Detail</a>
                        </div>
                    </div>
                </div>
                
                <!-- Bootcamp Card 3 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img src="assets/images/bootcamps/iot.jpg" alt="IoT" class="w-full h-48 object-cover">
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Pengembangan IoT</h3>
                        <p class="text-gray-600 mb-4">Pelajari cara mengembangkan solusi Internet of Things dari awal hingga implementasi!</p>
                        
                        <div class="flex items-center mb-4">
                            <img src="assets/images/instructors/michael.jpg" alt="Instructor" class="w-8 h-8 rounded-full mr-2">
                            <span class="text-sm text-gray-700">Instructor: Michael Lee</span>
                        </div>
                        
                        <div class="flex justify-between text-sm text-gray-500 mb-4">
                            <div>
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                20 Mei 2025
                            </div>
                            <div>
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                4 bulan
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-blue-600 font-bold">Rp 3.000.000</span>
                            <a href="views/bootcamp/detail.php?id=3" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-300">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-center mt-8">
                <button class="w-8 h-8 mx-1 flex items-center justify-center rounded-full border border-blue-600 text-blue-600 hover:bg-blue-50 transition-colors duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <button class="w-8 h-8 mx-1 flex items-center justify-center rounded-full bg-blue-600 text-white">1</button>
                <button class="w-8 h-8 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50 transition-colors duration-300">2</button>
                <button class="w-8 h-8 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50 transition-colors duration-300">3</button>
                <button class="w-8 h-8 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50 transition-colors duration-300">4</button>
                <button class="w-8 h-8 mx-1 flex items-center justify-center rounded-full text-gray-700 hover:bg-blue-50 transition-colors duration-300">5</button>
                
                <button class="w-8 h-8 mx-1 flex items-center justify-center rounded-full border border-blue-600 text-blue-600 hover:bg-blue-50 transition-colors duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <span class="text-white font-bold text-xl">Campus</span>
                        <span class="bg-white text-blue-600 px-2 py-1 rounded font-bold text-xl">Hub</span>
                    </div>
                    <p class="text-blue-200 mb-4">Temukan bootcamp IT terbaik untuk mengembangkan keterampilan dan mempercepat karier Anda dalam dunia teknologi.</p>
                    <p class="text-blue-200">Jl. Pendidikan No. 123, Malang<br>Jawa Timur, Indonesia, 65145</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Kategori</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-200 hover:text-white">Web Dev</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Data Science</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">UI/UX Design</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Mobile Development</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Artificial Intelligence</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Informasi</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Bootcamp</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-blue-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-blue-200 text-sm">&copy; 2025 CodeCamp. All Rights Reserved.</p>
                
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-blue-200 hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"></path>
                        </svg>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.457 0 1.705.867 3.214 2.19 4.097-.807-.025-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.828-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63.961-.689 1.8-1.56 2.46-2.548l-.047-.02z"></path>
                        </svg>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"></path>
                        </svg>
                    </a>
                    <a href="#" class="text-blue-200 hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.7 3H4.3A1.3 1.3 0 003 4.3v15.4A1.3 1.3 0 004.3 21h15.4a1.3 1.3 0 001.3-1.3V4.3A1.3 1.3 0 0019.7 3zM8.339 18.338H5.667v-8.59h2.672v8.59zM7.004 8.574a1.548 1.548 0 11-.002-3.096 1.548 1.548 0 01.002 3.096zm11.335 9.764H15.67v-4.177c0-.996-.017-2.278-1.387-2.278-1.389 0-1.601 1.086-1.601 2.206v4.249h-2.667v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.779 3.203 4.092v4.711z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>