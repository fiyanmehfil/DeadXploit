from flask import Flask, request, jsonify, render_template, redirect, url_for, session
import requests
from bs4 import BeautifulSoup
from concurrent.futures import ThreadPoolExecutor
import validators
import logging

app = Flask(__name__)
app.secret_key = 'your_secret_key'  # Necessary for using sessions

logging.basicConfig(level=logging.INFO)

def is_valid_url(url):
    # Allow localhost URLs
    if 'localhost' in url or '127.0.0.1' in url:
        return True
    return validators.url(url)

def crawl_website(main_url):
    response = requests.get(main_url)
    soup = BeautifulSoup(response.content, 'html.parser')

    forms = soup.find_all('form')
    links = soup.find_all('a', href=True)

    discovered_urls = []

    with ThreadPoolExecutor() as executor:
        form_futures = [executor.submit(process_form, form, main_url) for form in forms]
        link_futures = [executor.submit(process_link, link, main_url) for link in links]

        for future in form_futures + link_futures:
            result = future.result()
            if result:
                discovered_urls.append(result)

    return discovered_urls

def process_form(form, main_url):
    action = form.get('action')
    if action:
        full_url = requests.compat.urljoin(main_url, action)
        return {'type': 'form', 'url': full_url}
    return None

def process_link(link, main_url):
    href = link['href']
    full_url = requests.compat.urljoin(main_url, href)
    return {'type': 'link', 'url': full_url}

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/test', methods=['POST'])
def test_website():
    url = request.form.get('url')

    # Validate URL using the custom function
    if not is_valid_url(url):
        return redirect(url_for('error_page', message="Invalid URL format"))

    discovered_urls = crawl_website(url)
    vulnerabilities_found = run_vulnerability_tests(discovered_urls)

    # Store results in session
    session['discovered_urls'] = discovered_urls
    session['vulnerabilities'] = vulnerabilities_found

    return redirect(url_for('result'))

@app.route('/result')
def result():
    discovered_urls = session.get('discovered_urls', [])
    vulnerabilities = session.get('vulnerabilities', [])
    return render_template('result.html', discovered_urls=discovered_urls, vulnerabilities=vulnerabilities)

def run_vulnerability_tests(urls):
    vulnerabilities = []
    for url in urls:
        if url['type'] == 'form':
            if "upload" not in url['url']:
                if check_sql_injection(url['url']):
                    vulnerabilities.append(f"SQL Injection vulnerability found in {url['url']}")
            if check_xss(url['url']):
                vulnerabilities.append(f"XSS vulnerability found in {url['url']}")
            if check_csrf(url['url']):
                vulnerabilities.append(f"CSRF vulnerability found in {url['url']}")
            if check_file_upload(url['url']): 
                vulnerabilities.append(f"File Upload vulnerability found in {url['url']}")
        elif url['type'] == 'link':
            if check_security_misconfiguration(url['url']): 
                vulnerabilities.append(f"Security Misconfiguration vulnerability found in {url['url']}")
    return vulnerabilities

def check_sql_injection(url):
    payload = "' OR '1'='1"
    try:
        logging.info(f"Sending SQL injection payload to {url}")
        response_with_payload = requests.post(url, data={'username': payload, 'password': payload})
        
        # Check for known indicators of successful SQL injection
        if "Invalid username or password." not in response_with_payload.text and "success" in response_with_payload.text.lower():
            logging.info(f"SQL Injection succeeded at {url}")
            return True
        
        logging.info(f"Response from {url}: {response_with_payload.text}")
    except requests.exceptions.RequestException as e:
        logging.error("Request failed: %s", e)
    return False

def check_xss(url):
    payload = "<script>alert('XSS')</script>"
    try:
        response_with_payload = requests.get(url, params={'input': payload})
        if payload in response_with_payload.text:
            logging.info(f"XSS vulnerability found on {url}")
            return True    
    except requests.exceptions.RequestException as e:
        logging.error(f"Request failed: {e}")
    
    return False

def check_csrf(url):
    try:
        response_without_csrf = requests.post(url, data={'some_field': 'test'})
        if "success" in response_without_csrf.text.lower() or "uploaded" in response_without_csrf.text.lower():
            logging.info(f"CSRF vulnerability found on {url}")
            return True
    except requests.exceptions.RequestException as e:
        logging.error("Request failed: %s", e)
    return False

def check_file_upload(url):
    try:
        files = {'file': ('malicious.php', '<?php echo "File uploaded"; ?>')}
        response = requests.post(url, files=files)
        if "success" in response.text.lower() or "uploaded" in response.text.lower():
            return True
    except requests.exceptions.RequestException as e:
        logging.error("Request failed: %s", e)
    return False

def check_security_misconfiguration(url):
    try:
        response = requests.get(url)
        if "X-Content-Type-Options" not in response.headers or "X-XSS-Protection" not in response.headers:
            return True
    except requests.exceptions.RequestException as e:
        logging.error("Request failed: %s", e)
    return False

@app.route('/error')
def error_page():
    message = request.args.get('message', 'An error occurred')
    return render_template('error.html', message=message)

if __name__ == '__main__':
    app.run(debug=True)
