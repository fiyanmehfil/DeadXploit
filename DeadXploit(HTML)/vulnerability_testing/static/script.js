document.getElementById('vulnerability-test').addEventListener('submit', async function(event) {
    event.preventDefault(); // Prevent immediate form submission

    const url = document.getElementById('url').value;
    const loadingText = document.getElementById('loading-text');
    loadingText.style.display = 'block'; // Show loading text

    try {
        const response = await fetch('/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ url: url })
        });

        loadingText.style.display = 'none'; // Hide loading text

        const result = await response.json();
        if (result.error) {
            alert(result.error);
        } else {
            displayResults(result);
        }
    } catch (error) {
        loadingText.style.display = 'none';
        alert('An error occurred. Please try again.');
    }
});

function displayResults(result) {
    const resultBox = document.getElementById('result');
    resultBox.innerHTML = `
        <h3>Test Results:</h3>
        <h4>Discovered URLs:</h4>
        <ul>
            ${result.discovered_urls.map(url => `<li>${url.type}: ${url.url}</li>`).join('')}
        </ul>
        <h4>Vulnerabilities Found:</h4>
        <ul>
            ${result.vulnerabilities.length > 0
              ? result.vulnerabilities.map(v => `<li>${v}</li>`).join('')
              : '<li>No vulnerabilities found.</li>'}
        </ul>
    `;
}
