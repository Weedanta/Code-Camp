<?php
// views/admin/view_cv.php - New file to create
$page_title = 'View CV - Code Camp Admin';

// Get CV data from controller
$cvData = $cvData ?? [];
$personalInfo = !empty($cvData['personal_info']) ? json_decode($cvData['personal_info'], true) : [];
$experience = !empty($cvData['experience']) ? json_decode($cvData['experience'], true) : [];
$education = !empty($cvData['education']) ? json_decode($cvData['education'], true) : [];
$skills = !empty($cvData['skills']) ? json_decode($cvData['skills'], true) : [];
$projects = !empty($cvData['projects']) ? json_decode($cvData['projects'], true) : [];
$certifications = !empty($cvData['certifications']) ? json_decode($cvData['certifications'], true) : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
        }
        
        .cv-section {
            transition: all 0.3s ease;
        }
        
        .cv-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="admin.php?action=manage_features" class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Features</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print CV
                    </button>
                    <span class="text-sm text-gray-600">
                        Admin: <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Unknown'); ?></strong>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- CV Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <?php echo htmlspecialchars($personalInfo['fullName'] ?? $cvData['user_name'] ?? 'Nama Tidak Tersedia'); ?>
                    </h1>
                    <p class="text-lg text-gray-600 mt-2">
                        <?php echo htmlspecialchars($personalInfo['title'] ?? 'Professional'); ?>
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        User: <?php echo htmlspecialchars($cvData['user_name'] ?? 'Unknown'); ?> 
                        (<?php echo htmlspecialchars($cvData['user_email'] ?? 'Unknown'); ?>)
                    </p>
                </div>
                <div class="text-right text-sm text-gray-500 no-print">
                    <p>Created: <?php echo date('d M Y H:i', strtotime($cvData['created_at'] ?? 'now')); ?></p>
                    <?php if (!empty($cvData['updated_at'])): ?>
                        <p>Updated: <?php echo date('d M Y H:i', strtotime($cvData['updated_at'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Information -->
            <?php if (!empty($personalInfo)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-6 border-t border-gray-200">
                    <?php if (!empty($personalInfo['email'])): ?>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-700"><?php echo htmlspecialchars($personalInfo['email']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($personalInfo['phone'])): ?>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-gray-700"><?php echo htmlspecialchars($personalInfo['phone']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($personalInfo['location'])): ?>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-700"><?php echo htmlspecialchars($personalInfo['location']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($personalInfo['linkedin'])): ?>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                            <a href="<?php echo htmlspecialchars($personalInfo['linkedin']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800"><?php echo htmlspecialchars($personalInfo['linkedin']); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Professional Summary -->
        <?php if (!empty($personalInfo['summary'])): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 cv-section">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Professional Summary</h2>
                <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($personalInfo['summary'])); ?></p>
            </div>
        <?php endif; ?>

        <!-- Experience Section -->
        <?php if (!empty($experience)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 cv-section">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Work Experience</h2>
                <div class="space-y-6">
                    <?php foreach ($experience as $exp): ?>
                        <div class="border-l-4 border-blue-500 pl-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($exp['position'] ?? ''); ?></h3>
                                <span class="text-sm text-gray-500"><?php echo htmlspecialchars($exp['duration'] ?? ''); ?></span>
                            </div>
                            <p class="text-blue-600 font-medium mb-2"><?php echo htmlspecialchars($exp['company'] ?? ''); ?></p>
                            <?php if (!empty($exp['description'])): ?>
                                <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Education Section -->
        <?php if (!empty($education)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 cv-section">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Education</h2>
                <div class="space-y-4">
                    <?php foreach ($education as $edu): ?>
                        <div class="border-l-4 border-green-500 pl-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($edu['degree'] ?? ''); ?></h3>
                                <span class="text-sm text-gray-500"><?php echo htmlspecialchars($edu['year'] ?? ''); ?></span>
                            </div>
                            <p class="text-green-600 font-medium"><?php echo htmlspecialchars($edu['school'] ?? ''); ?></p>
                            <?php if (!empty($edu['gpa'])): ?>
                                <p class="text-gray-600 text-sm">GPA: <?php echo htmlspecialchars($edu['gpa']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Skills Section -->
        <?php if (!empty($skills)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 cv-section">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Skills</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($skills as $skillCategory): ?>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-3"><?php echo htmlspecialchars($skillCategory['category'] ?? 'Skills'); ?></h3>
                            <div class="flex flex-wrap gap-2">
                                <?php if (!empty($skillCategory['items'])): ?>
                                    <?php foreach ($skillCategory['items'] as $skill): ?>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                            <?php echo htmlspecialchars($skill); ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Projects Section -->
        <?php if (!empty($projects)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 cv-section">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Projects</h2>
                <div class="space-y-6">
                    <?php foreach ($projects as $project): ?>
                        <div class="border-l-4 border-purple-500 pl-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($project['name'] ?? ''); ?></h3>
                            <?php if (!empty($project['technologies'])): ?>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <?php foreach ($project['technologies'] as $tech): ?>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                            <?php echo htmlspecialchars($tech); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($project['description'])): ?>
                                <p class="text-gray-700 leading-relaxed mb-2"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($project['url'])): ?>
                                <a href="<?php echo htmlspecialchars($project['url']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View Project →
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Certifications Section -->
        <?php if (!empty($certifications)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 cv-section">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Certifications</h2>
                <div class="space-y-4">
                    <?php foreach ($certifications as $cert): ?>
                        <div class="border-l-4 border-yellow-500 pl-6">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($cert['name'] ?? ''); ?></h3>
                                <span class="text-sm text-gray-500"><?php echo htmlspecialchars($cert['year'] ?? ''); ?></span>
                            </div>
                            <p class="text-yellow-600 font-medium"><?php echo htmlspecialchars($cert['issuer'] ?? ''); ?></p>
                            <?php if (!empty($cert['credential'])): ?>
                                <p class="text-gray-600 text-sm">Credential ID: <?php echo htmlspecialchars($cert['credential']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Admin Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 no-print">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Actions</h2>
            <div class="flex space-x-4">
                <button onclick="downloadCV()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </button>
                <button onclick="if(confirm('Delete this CV? This action cannot be undone.')) { window.location.href='admin.php?action=delete_cv&user_id=<?php echo $cvData['user_id'] ?? 0; ?>'; }" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete CV
                </button>
            </div>
        </div>
    </div>

    <script>
        function downloadCV() {
            // Simple PDF download using browser print
            window.print();
        }
    </script>
</body>
</html>