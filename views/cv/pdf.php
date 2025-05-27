<?php
// Set headers for PDF download
header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="CV_' . ($personal_info['full_name'] ?? 'Resume') . '_' . date('Y-m-d') . '.html"');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - <?php echo htmlspecialchars($personal_info['full_name'] ?? 'Resume'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .name {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .contact-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            color: #6b7280;
            font-size: 14px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .job-item, .education-item, .project-item, .cert-item {
            margin-bottom: 15px;
        }
        
        .job-header, .edu-header, .project-header, .cert-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
        }
        
        .job-title, .degree, .project-name, .cert-name {
            font-weight: bold;
            color: #1f2937;
            font-size: 16px;
        }
        
        .company, .institution {
            color: #4b5563;
            font-size: 14px;
        }
        
        .date {
            color: #6b7280;
            font-size: 14px;
            white-space: nowrap;
        }
        
        .description {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.5;
            margin-top: 5px;
        }
        
        .skills-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .skill-category {
            margin-bottom: 10px;
        }
        
        .skill-title {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .skill-list {
            color: #4b5563;
            font-size: 14px;
        }
        
        .summary {
            font-size: 14px;
            line-height: 1.6;
            color: #4b5563;
        }
        
        .project-tech {
            color: #6b7280;
            font-size: 13px;
            font-style: italic;
        }
        
        a {
            color: #2563eb;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .header {
                border-bottom: 2px solid #000;
            }
            
            .section-title {
                border-bottom: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="name">
            <?php echo htmlspecialchars($personal_info['full_name'] ?? 'Your Name'); ?>
        </div>
        
        <div class="contact-info">
            <?php if (!empty($personal_info['email'])): ?>
                <div class="contact-item">
                    <?php echo htmlspecialchars($personal_info['email']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($personal_info['phone'])): ?>
                <div class="contact-item">
                    <?php echo htmlspecialchars($personal_info['phone']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($personal_info['location'])): ?>
                <div class="contact-item">
                    <?php echo htmlspecialchars($personal_info['location']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($personal_info['linkedin'])): ?>
                <div class="contact-item">
                    <a href="<?php echo htmlspecialchars($personal_info['linkedin']); ?>">
                        LinkedIn Profile
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Professional Summary -->
    <?php if (!empty($personal_info['summary'])): ?>
        <div class="section">
            <div class="section-title">Professional Summary</div>
            <div class="summary">
                <?php echo nl2br(htmlspecialchars($personal_info['summary'])); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Work Experience -->
    <?php if (!empty($experience)): ?>
        <div class="section">
            <div class="section-title">Work Experience</div>
            
            <?php foreach ($experience as $exp): ?>
                <?php if (!empty($exp['title']) || !empty($exp['company'])): ?>
                    <div class="job-item">
                        <div class="job-header">
                            <div>
                                <div class="job-title">
                                    <?php echo htmlspecialchars($exp['title'] ?? ''); ?>
                                </div>
                                <div class="company">
                                    <?php echo htmlspecialchars($exp['company'] ?? ''); ?>
                                </div>
                            </div>
                            <div class="date">
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
                            <div class="description">
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
        <div class="section">
            <div class="section-title">Education</div>
            
            <?php foreach ($education as $edu): ?>
                <?php if (!empty($edu['degree']) || !empty($edu['institution'])): ?>
                    <div class="education-item">
                        <div class="edu-header">
                            <div>
                                <div class="degree">
                                    <?php echo htmlspecialchars($edu['degree'] ?? ''); ?>
                                    <?php if (!empty($edu['field'])): ?>
                                        in <?php echo htmlspecialchars($edu['field']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="institution">
                                    <?php echo htmlspecialchars($edu['institution'] ?? ''); ?>
                                    <?php if (!empty($edu['gpa'])): ?>
                                        | GPA: <?php echo htmlspecialchars($edu['gpa']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="date">
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
        <div class="section">
            <div class="section-title">Skills</div>
            
            <div class="skills-section">
                <?php if (!empty($skills['technical'])): ?>
                    <div class="skill-category">
                        <div class="skill-title">Technical Skills</div>
                        <div class="skill-list">
                            <?php echo htmlspecialchars($skills['technical']); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($skills['soft'])): ?>
                    <div class="skill-category">
                        <div class="skill-title">Soft Skills</div>
                        <div class="skill-list">
                            <?php echo htmlspecialchars($skills['soft']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Projects -->
    <?php if (!empty($projects)): ?>
        <div class="section">
            <div class="section-title">Projects</div>
            
            <?php foreach ($projects as $project): ?>
                <?php if (!empty($project['name'])): ?>
                    <div class="project-item">
                        <div class="project-header">
                            <div class="project-name">
                                <?php if (!empty($project['url'])): ?>
                                    <a href="<?php echo htmlspecialchars($project['url']); ?>">
                                        <?php echo htmlspecialchars($project['name']); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($project['name']); ?>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($project['technologies'])): ?>
                                <div class="project-tech">
                                    <?php echo htmlspecialchars($project['technologies']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($project['description'])): ?>
                            <div class="description">
                                <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Certifications -->
    <?php if (!empty($certifications)): ?>
        <div class="section">
            <div class="section-title">Certifications</div>
            
            <?php foreach ($certifications as $cert): ?>
                <?php if (!empty($cert['name'])): ?>
                    <div class="cert-item">
                        <div class="cert-header">
                            <div>
                                <div class="cert-name">
                                    <?php if (!empty($cert['url'])): ?>
                                        <a href="<?php echo htmlspecialchars($cert['url']); ?>">
                                            <?php echo htmlspecialchars($cert['name']); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($cert['name']); ?>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($cert['issuer'])): ?>
                                    <div class="institution">
                                        <?php echo htmlspecialchars($cert['issuer']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($cert['date'])): ?>
                                <div class="date">
                                    <?php echo date('M Y', strtotime($cert['date'] . '-01')); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>