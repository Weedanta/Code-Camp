<?php
$base_url = '';
include_once $base_url . 'views/includes/header.php';

// Debug: Check if CV data exists
$has_cv_data = !empty($cv_data);

// Parse existing CV data if available with better error handling
$personal_info = [];
$experience = [];
$education = [];
$skills = [];
$projects = [];
$certifications = [];

if ($has_cv_data) {
    $personal_info = json_decode($cv_data['personal_info'], true) ?: [];
    $experience = json_decode($cv_data['experience'], true) ?: [];
    $education = json_decode($cv_data['education'], true) ?: [];
    $skills = json_decode($cv_data['skills'], true) ?: [];
    $projects = json_decode($cv_data['projects'], true) ?: [];
    $certifications = json_decode($cv_data['certifications'], true) ?: [];
}
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">CV Builder</h1>
                    <p class="text-gray-600 mt-2">Create an ATS-friendly resume that gets you noticed</p>
                    <?php if ($has_cv_data): ?>
                        <p class="text-sm text-green-600 mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            CV data found - Last updated: <?php echo date('M d, Y', strtotime($cv_data['updated_at'] ?: $cv_data['created_at'])); ?>
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-blue-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            No CV data found - Create your first CV
                        </p>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <?php if ($has_cv_data): ?>
                        <a href="index.php?action=cv_preview" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </a>
                        <a href="index.php?action=cv_pdf" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-center">
                            <i class="fas fa-download mr-2"></i>Download PDF
                        </a>
                        <button onclick="deleteCv()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete CV
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Success!</strong> 
                    <span class="ml-1">
                        <?php 
                        switch($_GET['success']) {
                            case 'created': echo 'Your CV has been created successfully!'; break;
                            case 'updated': echo 'Your CV has been updated successfully!'; break;
                            case 'deleted': echo 'Your CV has been deleted successfully!'; break;
                            default: echo 'Operation completed successfully!';
                        }
                        ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Error!</strong> 
                    <span class="ml-1">
                        <?php 
                        switch($_GET['error']) {
                            case 'create_failed': echo 'Failed to create CV. Please try again.'; break;
                            case 'update_failed': echo 'Failed to update CV. Please try again.'; break;
                            case 'delete_failed': echo 'Failed to delete CV. Please try again.'; break;
                            case 'no_data': echo 'No CV data found. Please create your CV first.'; break;
                            default: echo 'There was a problem saving your CV. Please try again.';
                        }
                        ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <form action="index.php?action=cv_save" method="POST" class="space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user text-blue-600 mr-2"></i>Personal Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="personal_info[full_name]" value="<?php echo $personal_info['full_name'] ?? ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="personal_info[email]" value="<?php echo $personal_info['email'] ?? ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="personal_info[phone]" value="<?php echo $personal_info['phone'] ?? ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="personal_info[location]" value="<?php echo $personal_info['location'] ?? ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn Profile</label>
                        <input type="url" name="personal_info[linkedin]" value="<?php echo $personal_info['linkedin'] ?? ''; ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Professional Summary</label>
                        <textarea name="personal_info[summary]" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Brief professional summary highlighting your key achievements and career goals"><?php echo $personal_info['summary'] ?? ''; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Experience Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-briefcase text-blue-600 mr-2"></i>Work Experience
                    </h2>
                    <button type="button" onclick="addExperience()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i>Add Experience
                    </button>
                </div>
                <div id="experience-container">
                    <?php if (!empty($experience)): ?>
                        <?php foreach ($experience as $index => $exp): ?>
                            <div class="experience-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-medium text-gray-900">Experience <?php echo $index + 1; ?></h3>
                                    <button type="button" onclick="removeExperience(this)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                                        <input type="text" name="experience[<?php echo $index; ?>][title]" value="<?php echo $exp['title'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                        <input type="text" name="experience[<?php echo $index; ?>][company]" value="<?php echo $exp['company'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="month" name="experience[<?php echo $index; ?>][start_date]" value="<?php echo $exp['start_date'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                        <input type="month" name="experience[<?php echo $index; ?>][end_date]" value="<?php echo $exp['end_date'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <div class="flex items-center mt-1">
                                            <input type="checkbox" name="experience[<?php echo $index; ?>][current]" value="1" 
                                                   <?php echo isset($exp['current']) && $exp['current'] ? 'checked' : ''; ?> 
                                                   class="mr-1">
                                            <span class="text-sm text-gray-600">I currently work here</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="experience[<?php echo $index; ?>][description]" rows="3" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Describe your responsibilities and achievements"><?php echo $exp['description'] ?? ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="experience-item border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-medium text-gray-900">Experience 1</h3>
                                <button type="button" onclick="removeExperience(this)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                                    <input type="text" name="experience[0][title]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                    <input type="text" name="experience[0][company]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="month" name="experience[0][start_date]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="month" name="experience[0][end_date]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <div class="flex items-center mt-1">
                                        <input type="checkbox" name="experience[0][current]" value="1" class="mr-1">
                                        <span class="text-sm text-gray-600">I currently work here</span>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="experience[0][description]" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Describe your responsibilities and achievements"></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Education Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>Education
                    </h2>
                    <button type="button" onclick="addEducation()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i>Add Education
                    </button>
                </div>
                <div id="education-container">
                    <?php if (!empty($education)): ?>
                        <?php foreach ($education as $index => $edu): ?>
                            <div class="education-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-medium text-gray-900">Education <?php echo $index + 1; ?></h3>
                                    <button type="button" onclick="removeEducation(this)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Degree</label>
                                        <input type="text" name="education[<?php echo $index; ?>][degree]" value="<?php echo $edu['degree'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                                        <input type="text" name="education[<?php echo $index; ?>][institution]" value="<?php echo $edu['institution'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                                        <input type="number" name="education[<?php echo $index; ?>][start_year]" value="<?php echo $edu['start_year'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1900" max="2030">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                                        <input type="number" name="education[<?php echo $index; ?>][end_year]" value="<?php echo $edu['end_year'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1900" max="2030">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">GPA (Optional)</label>
                                        <input type="text" name="education[<?php echo $index; ?>][gpa]" value="<?php echo $edu['gpa'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
                                        <input type="text" name="education[<?php echo $index; ?>][field]" value="<?php echo $edu['field'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="education-item border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-medium text-gray-900">Education 1</h3>
                                <button type="button" onclick="removeEducation(this)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Degree</label>
                                    <input type="text" name="education[0][degree]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                                    <input type="text" name="education[0][institution]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                                    <input type="number" name="education[0][start_year]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1900" max="2030">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                                    <input type="number" name="education[0][end_year]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1900" max="2030">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">GPA (Optional)</label>
                                    <input type="text" name="education[0][gpa]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
                                    <input type="text" name="education[0][field]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Skills Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-code text-blue-600 mr-2"></i>Skills
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Technical Skills</label>
                        <textarea name="skills[technical]" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="e.g., JavaScript, Python, React, Node.js (separate with commas)"><?php echo $skills['technical'] ?? ''; ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Soft Skills</label>
                        <textarea name="skills[soft]" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="e.g., Leadership, Communication, Problem Solving (separate with commas)"><?php echo $skills['soft'] ?? ''; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Projects Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-project-diagram text-blue-600 mr-2"></i>Projects
                    </h2>
                    <button type="button" onclick="addProject()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i>Add Project
                    </button>
                </div>
                <div id="projects-container">
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $index => $project): ?>
                            <div class="project-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-medium text-gray-900">Project <?php echo $index + 1; ?></h3>
                                    <button type="button" onclick="removeProject(this)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                                        <input type="text" name="projects[<?php echo $index; ?>][name]" value="<?php echo $project['name'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Technologies Used</label>
                                        <input type="text" name="projects[<?php echo $index; ?>][technologies]" value="<?php echo $project['technologies'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="e.g., React, Node.js, MongoDB">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Project URL (Optional)</label>
                                        <input type="url" name="projects[<?php echo $index; ?>][url]" value="<?php echo $project['url'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="projects[<?php echo $index; ?>][description]" rows="3" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Describe the project and your role"><?php echo $project['description'] ?? ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Certifications Section -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-certificate text-blue-600 mr-2"></i>Certifications
                    </h2>
                    <button type="button" onclick="addCertification()" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-plus mr-1"></i>Add Certification
                    </button>
                </div>
                <div id="certifications-container">
                    <?php if (!empty($certifications)): ?>
                        <?php foreach ($certifications as $index => $cert): ?>
                            <div class="certification-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-medium text-gray-900">Certification <?php echo $index + 1; ?></h3>
                                    <button type="button" onclick="removeCertification(this)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Certification Name</label>
                                        <input type="text" name="certifications[<?php echo $index; ?>][name]" value="<?php echo $cert['name'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Issuing Organization</label>
                                        <input type="text" name="certifications[<?php echo $index; ?>][issuer]" value="<?php echo $cert['issuer'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Obtained</label>
                                        <input type="month" name="certifications[<?php echo $index; ?>][date]" value="<?php echo $cert['date'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Credential URL (Optional)</label>
                                        <input type="url" name="certifications[<?php echo $index; ?>][url]" value="<?php echo $cert['url'] ?? ''; ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Save Button -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-end space-x-3">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>Save CV
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let experienceIndex = <?php echo !empty($experience) ? count($experience) : 1; ?>;
let educationIndex = <?php echo !empty($education) ? count($education) : 1; ?>;
let projectIndex = <?php echo !empty($projects) ? count($projects) : 0; ?>;
let certificationIndex = <?php echo !empty($certifications) ? count($certifications) : 0; ?>;

// Delete CV function
function deleteCv() {
    if (confirm('Are you sure you want to delete your CV? This action cannot be undone.')) {
        window.location.href = 'index.php?action=cv_delete';
    }
}

// Auto-save function (optional)
function autoSave() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    fetch('index.php?action=cv_save', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            console.log('Auto-saved successfully');
            // Show a subtle indicator
            showAutoSaveIndicator();
        }
    })
    .catch(error => {
        console.error('Auto-save failed:', error);
    });
}

function showAutoSaveIndicator() {
    const indicator = document.createElement('div');
    indicator.className = 'fixed top-4 right-4 bg-green-500 text-white px-3 py-2 rounded shadow-lg z-50';
    indicator.innerHTML = '<i class="fas fa-check mr-2"></i>Auto-saved';
    document.body.appendChild(indicator);
    
    setTimeout(() => {
        indicator.remove();
    }, 2000);
}

// Add auto-save every 30 seconds
setInterval(autoSave, 30000);

function addExperience() {
    const container = document.getElementById('experience-container');
    const html = `
        <div class="experience-item border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-medium text-gray-900">Experience ${experienceIndex + 1}</h3>
                <button type="button" onclick="removeExperience(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                    <input type="text" name="experience[${experienceIndex}][title]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                    <input type="text" name="experience[${experienceIndex}][company]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="month" name="experience[${experienceIndex}][start_date]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="month" name="experience[${experienceIndex}][end_date]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="flex items-center mt-1">
                        <input type="checkbox" name="experience[${experienceIndex}][current]" value="1" class="mr-1">
                        <span class="text-sm text-gray-600">I currently work here</span>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="experience[${experienceIndex}][description]" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe your responsibilities and achievements"></textarea>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    experienceIndex++;
}

function removeExperience(button) {
    if (document.querySelectorAll('.experience-item').length > 1) {
        button.closest('.experience-item').remove();
    } else {
        alert('You must have at least one experience entry.');
    }
}

function addEducation() {
    const container = document.getElementById('education-container');
    const html = `
        <div class="education-item border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-medium text-gray-900">Education ${educationIndex + 1}</h3>
                <button type="button" onclick="removeEducation(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Degree</label>
                    <input type="text" name="education[${educationIndex}][degree]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                    <input type="text" name="education[${educationIndex}][institution]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                    <input type="number" name="education[${educationIndex}][start_year]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1900" max="2030">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                    <input type="number" name="education[${educationIndex}][end_year]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1900" max="2030">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GPA (Optional)</label>
                    <input type="text" name="education[${educationIndex}][gpa]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Field of Study</label>
                    <input type="text" name="education[${educationIndex}][field]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    educationIndex++;
}

function removeEducation(button) {
    if (document.querySelectorAll('.education-item').length > 1) {
        button.closest('.education-item').remove();
    } else {
        alert('You must have at least one education entry.');
    }
}

function addProject() {
    const container = document.getElementById('projects-container');
    const html = `
        <div class="project-item border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-medium text-gray-900">Project ${projectIndex + 1}</h3>
                <button type="button" onclick="removeProject(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                    <input type="text" name="projects[${projectIndex}][name]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Technologies Used</label>
                    <input type="text" name="projects[${projectIndex}][technologies]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., React, Node.js, MongoDB">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Project URL (Optional)</label>
                    <input type="url" name="projects[${projectIndex}][url]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="projects[${projectIndex}][description]" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe the project and your role"></textarea>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    projectIndex++;
}

function removeProject(button) {
    button.closest('.project-item').remove();
}

function addCertification() {
    const container = document.getElementById('certifications-container');
    const html = `
        <div class="certification-item border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-medium text-gray-900">Certification ${certificationIndex + 1}</h3>
                <button type="button" onclick="removeCertification(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Certification Name</label>
                    <input type="text" name="certifications[${certificationIndex}][name]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issuing Organization</label>
                    <input type="text" name="certifications[${certificationIndex}][issuer]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Obtained</label>
                    <input type="month" name="certifications[${certificationIndex}][date]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Credential URL (Optional)</label>
                    <input type="url" name="certifications[${certificationIndex}][url]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    certificationIndex++;
}

function removeCertification(button) {
    button.closest('.certification-item').remove();
}

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const fullName = document.querySelector('input[name="personal_info[full_name]"]').value.trim();
    const email = document.querySelector('input[name="personal_info[email]"]').value.trim();
    
    if (!fullName || !email) {
        e.preventDefault();
        alert('Please fill in at least your full name and email address.');
        return false;
    }
    
    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    submitBtn.disabled = true;
});
</script>

<?php include_once $base_url . 'views/includes/footer.php'; ?>