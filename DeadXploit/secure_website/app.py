from flask import Flask, request, render_template, escape
from flask_wtf.csrf import CSRFProtect
import os
import logging
import mysql.connector

app = Flask(__name__)

# Set a secret key for CSRF protection
app.config['SECRET_KEY'] = 'your_secret_key_here'  # Replace with a strong secret key
csrf = CSRFProtect(app)

# Set up logging
logging.basicConfig(level=logging.INFO)

# Database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',  # Default username for XAMPP
    'password': '',  # Default password is empty
    'database': 'Users'
}

# Secure Login
@app.route('/login', methods=['POST'])
def login():
    username = request.form.get('username')
    password = request.form.get('password')

    try:
        # Connect to the database
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        # Validate the credentials against the database
        query = "SELECT * FROM users WHERE username=%s AND password=%s"
        cursor.execute(query, (username, password))
        result = cursor.fetchone()

        cursor.close()
        conn.close()

        # Check if login was successful
        if result:
            return "Login successful!"
        return "Invalid credentials", 401
    except mysql.connector.Error as err:
        logging.error("Database error: %s", err)
        return "Database error", 500

# Secure Query Submission
@app.route('/submit', methods=['GET'])
def submit_query():
    user_input = request.args.get('input')
    safe_input = escape(user_input) if user_input else ''
    return f'<h2>Your Query</h2><p>{safe_input}</p>'

# Secure File Upload
@app.route('/upload', methods=['POST'])
def secure_upload():
    file = request.files['file']
    
    # Ensure the file is safe (e.g., check file extension)
    if not file.filename.endswith('.txt'):
        return "Invalid file type", 400

    # Save the file safely
    file.save(os.path.join("uploads", file.filename))
    return "File uploaded successfully!"

@app.after_request
def add_security_headers(response):
    response.headers['X-Content-Type-Options'] = 'nosniff'
    response.headers['X-XSS-Protection'] = '1; mode=block'
    return response

# About Us Page
@app.route('/details', methods=['GET'])
def about_us():
    return "Welcome to the secure webpage!"

# Homepage
@app.route('/')
def index():
    return render_template('index.html')

if __name__ == '__main__':
    if not os.path.exists("uploads"):
        os.makedirs("uploads")
    app.run(port=5002, debug=True)
