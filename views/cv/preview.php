<?php
$base_url = '';
include_once $base_url . 'views/includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <a href="index.php?action=cv_builder" class="flex items-center text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Editor
            </a>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <a href="index.php?action=cv_pdf" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
            </div>
        </div>

        <!-- CV Preview -->
        <div class="bg-white shadow-lg max-w-4xl mx-auto" id="cv-preview">
            <div class="p-8">
                <!-- Header Section -->
                <div class="border-b-2 border-gray-200 pb-6 mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($personal_info['full_name'] ?? 'Your Name'); ?>
                    </h1>
                    
                    <div class="flex flex-wrap gap-4 text-gray-600 text-sm">
                        <?php if (!empty($personal_info['email'])): ?>
                            <div class="flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                <span><?php echo htmlspecialchars($personal_info['email']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($personal_info['phone'])): ?>
                            <div class="flex items-center">
                                <i class="fas fa-phone mr-2"></i>
                                <span><?php echo htmlspecialchars($personal_info['phone']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($personal_info['location'])): ?>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span><?php echo htmlspecialchars($personal_info['location']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($personal_info['linkedin'])): ?>
                            <div class="flex items-center">
                                <i class="fab fa-linkedin mr-2"></i>
                                <a href="<?php echo htmlspecialchars($personal_info['linkedin']); ?>" class="text-blue-600 hover:underline">
                                    LinkedIn Profile
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Professional Summary -->
                <?php if (!empty($personal_info['summary'])): ?>
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b border-gray-300 pb-1">
                            PROFESSIONAL SUMMARY
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($personal_info['summary'])); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Work Experience -->
                <?php if (!empty($experience)): ?>
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b border-gray-300 pb-1">
                            WORK EXPERIENCE
                        </h2>
                        
                        <?php foreach ($experience as $exp): ?>
                            <?php if (!empty($exp['title']) || !empty($exp['company'])): ?>
                                <div class="mb-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($exp['title'] ?? ''); ?>
                                            </h3>
                                            <p class="text-gray-700">
                                                <?php echo htmlspecialchars($exp['company'] ?? ''); ?>
                                            </p>
                                        </div>
                                        <div class="text-gray-600 text-sm">
                                            <?php 
                                            $start_date = !empty($exp['start_date']) ? date('M Y', strtotime($exp['start_date'] . '-01')) : '';
                                            $end_date = !empty($exp['end_date']) ? date('M Y', strtotime($exp['end_date'] . '-01')) : '';
                                            if (isset($exp['current']) && $exp['current']) {
                                                $end_date = 'Present';
                                            }
                                            
                                            if ($start_date || $end_date) {
                                                echo $start_date;
                                                if ($start_date && $end_date) echo ' - ';
                                                echo $end_date;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($exp['description'])): ?>
                                        <div class="text-gray-700 text-sm leading-relaxed ml-0">
                                            <?php echo nl2br(htmlspecialchars($exp['description'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Education -->
                <?php if (!empty($education)): ?>
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b border-gray-300 pb-1">
                            EDUCATION
                        </h2>
                        
                        <?php foreach ($education as $edu): ?>
                            <?php if (!empty($edu['degree']) || !empty($edu['institution'])): ?>
                                <div class="mb-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($edu['degree'] ?? ''); ?>
                                                <?php if (!empty($edu['field'])): ?>
                                                    in <?php echo htmlspecialchars($edu['field']); ?>
                                                <?php endif; ?>
                                            </h3>
                                            <p class="text-gray-700">
                                                <?php echo htmlspecialchars($edu['institution'] ?? ''); ?>
                                                <?php if (!empty($edu['gpa'])): ?>
                                                    | GPA: <?php echo htmlspecialchars($edu['gpa']); ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="text-gray-600 text-sm">
                                            <?php 
                                            if (!empty($edu['start_year']) || !empty($edu['end_year'])) {
                                                echo $edu['start_year'] ?? '';
                                                if (!empty($edu['start_year']) && !empty($edu['end_year'])) echo ' - ';
                                                echo $edu['end_year'] ?? '';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Skills -->
                <?php if (!empty($skills['technical']) || !empty($skills['soft'])): ?>
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b border-gray-300 pb-1">
                            SKILLS
                        </h2>
                        
                        <?php if (!empty($skills['technical'])): ?>
                            <div class="mb-3">
                                <h3 class="font-semibold text-gray-900 mb-2">Technical Skills</h3>
                                <p class="text-gray-700">
                                    <?php echo htmlspecialchars($skills['technical']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($skills['soft'])): ?>
                            <div class="mb-3">
                                <h3 class="font-semibold text-gray-900 mb-2">Soft Skills</h3>
                                <p class="text-gray-700">
                                    <?php echo htmlspecialchars($skills['soft']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Projects -->
                <?php if (!empty($projects)): ?>
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b border-gray-300 pb-1">
                            PROJECTS
                        </h2>
                        
                        <?php foreach ($projects as $project): ?>
                            <?php if (!empty($project['name'])): ?>
                                <div class="mb-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-gray-900">
                                            <?php if (!empty($project['url'])): ?>
                                                <a href="<?php echo htmlspecialchars($project['url']); ?>" class="text-blue-600 hover:underline">
                                                    <?php echo htmlspecialchars($project['name']); ?>
                                                </a>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($project['name']); ?>
                                            <?php endif; ?>
                                        </h3>
                                        <?php if (!empty($project['technologies'])): ?>
                                            <span class="text-gray-600 text-sm">
                                                <?php echo htmlspecialchars($project['technologies']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (!empty($project['description'])): ?>
                                        <p class="text-gray-700 text-sm leading-relaxed">
                                            <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Certifications -->
                <?php if (!empty($certifications)): ?>
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-3 border-b border-gray-300 pb-1">
                            CERTIFICATIONS
                        </h2>
                        
                        <?php foreach ($certifications as $cert): ?>
                            <?php if (!empty($cert['name'])): ?>
                                <div class="mb-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                <?php if (!empty($cert['url'])): ?>
                                                    <a href="<?php echo htmlspecialchars($cert['url']); ?>" class="text-blue-600 hover:underline">
                                                        <?php echo htmlspecialchars($cert['name']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($cert['name']); ?>
                                                <?php endif; ?>
                                            </h3>
                                            <?php if (!empty($cert['issuer'])): ?>
                                                <p class="text-gray-700">
                                                    <?php echo htmlspecialchars($cert['issuer']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($cert['date'])): ?>
                                            <div class="text-gray-600 text-sm">
                                                <?php echo date('M Y', strtotime($cert['date'] . '-01')); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    #cv-preview, #cv-preview * {
        visibility: visible;
    }
    
    #cv-preview {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
    }
    
    .container {
        max-width: none !important;
        padding: 0 !important;
    }
    
    /* Hide non-essential elements during print */
    .no-print {
        display: none !important;
    }
    
    /* Optimize font sizes for print */
    h1 {
        font-size: 24px !important;
    }
    
    h2 {
        font-size: 18px !important;
    }
    
    h3 {
        font-size: 16px !important;
    }
    
    p, div {
        font-size: 12px !important;
        line-height: 1.4 !important;
    }
    
    /* Page break handling */
    .mb-6 {
        page-break-inside: avoid;
    }
}
</style>

<?php include_once $base_url . 'views/includes/footer.php'; ?>