<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureScan - AI-Powered Vulnerability Scanner</title>
    <meta name="description" content="<?php echo htmlspecialchars($_SERVER['PROJECT_DESCRIPTION'] ?? 'A simple, beautiful web app where anyone can check a URL or code snippet for security vulnerabilities using AI.'); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css?v=<?php echo time(); ?>">

    <!-- Platform-managed Meta Tags -->
    <?php if (!empty($_SERVER['PROJECT_IMAGE_URL'])): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($_SERVER['PROJECT_IMAGE_URL']); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($_SERVER['PROJECT_IMAGE_URL']); ?>">
    <?php endif; ?>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16" style="color: var(--primary-color);">
                    <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.06.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.952-.325-1.882-.626-2.837-.855C9.552 1.29 8.531 1.007 8 1.007c-.531 0-1.552.283-2.662.583zM8.5 1.531c.315.068.736.148 1.145.253c.41.106.772.24 1.1.352a.639.639 0 0 1 .325.32c.394 2.94.024 5.37-1.082 7.246a9.14 9.14 0 0 1-1.857 1.858a.48.48 0 0 1-.444.002a9.14 9.14 0 0 1-1.857-1.858C4.593 9.471 4.223 7.045 4.617 4.105a.638.638 0 0 1 .325-.321c.328-.112.69-.246 1.1-.352C6.469 1.68 6.89 1.6 7.205 1.531c.316-.068.649-.115.995-.115s.679.047.995.115z"/>
                    <path d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                </svg>
                SecureScan
            </a>
        </div>
    </nav>

    <main class="container my-5">
        <div class="scan-section mx-auto" style="max-width: 700px;">
            <div class="text-center mb-4">
                <h1 class="h2 text-dark">Scan for Vulnerabilities</h1>
                <p class="lead">Instantly analyze a URL or code snippet with AI.</p>
            </div>
            <form id="scanForm">
                <div class="mb-3">
                    <label for="codeInput" class="form-label">URL or Code Snippet</label>
                    <textarea class="form-control" id="codeInput" rows="8" placeholder="e.g., https://example.com or paste your code here"></textarea>
                    <div id="detectionResult" class="form-text mt-2" style="display: none;">
                        <span class="badge bg-secondary" id="detectedLanguage"></span>
                        <span id="detectionSuggestion" class="text-muted ms-2"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="promptInput" class="form-label">What should I check for?</label>
                    <input type="text" class="form-control" id="promptInput" placeholder="e.g., 'Check this login page for hacking risks'">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" id="scanButton" disabled>Scan Now</button>
                </div>
            </form>
        </div>

        <div id="loader" class="text-center my-5">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-dark">Analyzing... this may take a moment.</p>
        </div>

        <div id="resultsSection" class="results-section mx-auto" style="max-width: 800px;">
            <div class="text-center mb-5">
                 <h1 class="h2 text-dark">Scan Report</h1>
            </div>
           
            <div class="card p-4 mb-5" style="background-color: var(--surface-color); border-radius: var(--radius-md); box-shadow: var(--shadow-md);">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                        <div class="risk-meter mx-auto position-relative">
                            <canvas id="riskScoreChart"></canvas>
                             <div class="position-absolute top-50 start-50 translate-middle">
                                <span id="riskScoreValue" class="h1 text-dark fw-bold">--</span>
                                <div class="text-muted" style="margin-top: -5px;">Score</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4 class="text-dark">Overall Risk Score</h4>
                        <p>This score represents the estimated security risk based on the vulnerabilities found. A higher score indicates a greater risk that should be addressed immediately.</p>
                    </div>
                </div>
            </div>

            <h3 class="text-dark mb-4">Found Issues</h3>
            <div id="issuesContainer">
                <!-- Issues will be dynamically inserted here -->
            </div>
        </div>
    </main>
    
    <footer class="text-center text-muted py-4">
        <p>&copy; <?php echo date("Y"); ?> SecureScan. All Rights Reserved.</p>
    </footer>

    <!-- Chart.js for the risk meter -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <!-- Reminder to save changes -->
    <script>console.log("Reminder: click Save in the editor to sync changes.");</script>
</body>
</html>