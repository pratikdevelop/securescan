document.addEventListener('DOMContentLoaded', function () {
    constcodeInput = document.getElementById('codeInput');
    const promptInput = document.getElementById('promptInput');
    const scanButton = document.getElementById('scanButton');
    const scanForm = document.getElementById('scanForm');
    const loader = document.getElementById('loader');
    const resultsSection = document.getElementById('resultsSection');
    const riskScoreValue = document.getElementById('riskScoreValue');
    const riskScoreChart = document.getElementById('riskScoreChart');
    const issuesContainer = document.getElementById('issuesContainer');

    const detectionResult = document.getElementById('detectionResult');
    const detectedLanguage = document.getElementById('detectedLanguage');
    const detectionSuggestion = document.getElementById('detectionSuggestion');

    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    const handleCodeInputChange = debounce(async () => {
        const code = codeInput.value.trim();
        if (code.length < 10) { // Don't run for very short inputs
            detectionResult.style.display = 'none';
            return;
        }

        try {
            const response = await fetch('/api/detect-type.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code: code })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.language && data.language !== 'Text') {
                    detectedLanguage.textContent = data.language;
                    detectionSuggestion.textContent = data.suggestion;
                    detectionResult.style.display = 'block';
                } else {
                    detectionResult.style.display = 'none';
                }
            }
        } catch (error) {
            console.error('Error detecting language:', error);
            detectionResult.style.display = 'none';
        }
    }, 500); // 500ms delay

    codeInput.addEventListener('input', handleCodeInputChange);

    function validateInputs() {
        const code = codeInput.value.trim();
        const prompt = promptInput.value.trim();
        scanButton.disabled = !(code && prompt);
    }

    codeInput.addEventListener('input', validateInputs);
    promptInput.addEventListener('input', validateInputs);

    scanForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        document.querySelector('.scan-section').style.display = 'none';
        loader.style.display = 'block';

        // Simulate API call
        setTimeout(() => {
            loader.style.display = 'none';
            displayMockResults();
        }, 3000);
    });

    function displayMockResults() {
        const mockData = {
            "risk_score": 78,
            "issues": [
                {
                    "type": "SQL Injection",
                    "severity": "Critical",
                    "location": "Login form, 'username' parameter",
                    "explanation": "Your script appears to be vulnerable to SQL Injection because it doesn't properly sanitize user input before including it in a database query.",
                    "fix": "Use prepared statements and parameterized queries. Example in PHP: $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?'); $stmt->execute([$username]);"
                },
                {
                    "type": "Cross-Site Scripting (XSS)",
                    "severity": "High",
                    "location": "Search results page",
                    "explanation": "The search term is reflected back to the page without being sanitized, allowing an attacker to inject malicious scripts.",
                    "fix": "Always escape HTML output. In PHP, use: echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');"
                },
                {
                    "type": "Weak Password Policy",
                    "severity": "Medium",
                    "location": "User registration script",
                    "explanation": "The script does not enforce a strong password policy, making user accounts susceptible to brute-force attacks.",
                    "fix": "Require passwords to be at least 12 characters long and include a mix of uppercase, lowercase, numbers, and symbols."
                }
            ]
        };

        riskScoreValue.textContent = mockData.risk_score;
        updateRiskChart(mockData.risk_score);

        issuesContainer.innerHTML = '';
        mockData.issues.forEach(issue => {
            const severityClass = `badge-${issue.severity.toLowerCase()}`;
            const issueHtml = `
                <div class="card issue-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title mb-1">${issue.type}</h5>
                            <span class="badge badge-severity ${severityClass}">${issue.severity}</span>
                        </div>
                        <h6 class="card-subtitle mb-2 text-muted">Location: ${issue.location}</h6>
                        <p class="card-text mt-3">${issue.explanation}</p>
                        <h6>Suggested Fix:</h6>
                        <pre class="fix-code"><code>${issue.fix}</code></pre>
                    </div>
                </div>
            `;
            issuesContainer.insertAdjacentHTML('beforeend', issueHtml);
        });

        resultsSection.style.display = 'block';
    }

    function updateRiskChart(score) {
        const chart = new Chart(riskScoreChart, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: [getScoreColor(score), '#e2e8f0'],
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    tooltip: { enabled: false },
                    legend: { display: false }
                }
            }
        });
    }

    function getScoreColor(score) {
        if (score > 75) return '#ef4444'; // Critical
        if (score > 50) return '#f97316'; // High
        if (score > 25) return '#facc15'; // Medium
        return '#22c55e'; // Low
    }

});
