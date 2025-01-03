from flask import Flask, request, jsonify, render_template
import os
from flask_cors import CORS
import logging
import mysql.connector

app = Flask(__name__)
CORS(app)

# Set up logging
logging.basicConfig(level=logging.INFO)

# Database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',  # Default username for XAMPP
    'password': '',  # Default password is empty
    'database': 'Users'
}

# SQL Injection Vulnerability
@app.route('/login', methods=['POST'])
def sql_injection():
    username = request.form.get('username')
    password = request.form.get('password')
    
    # Connect to the database
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor()
    
    # Intentionally vulnerable query
    query = f"SELECT * FROM users WHERE username='{username}' AND password='{password}'"
    
    cursor.execute(query)
    result = cursor.fetchall()
    
    cursor.close()
    conn.close()
    
    # Check if the login was successful based on the result
    if result:
        return "Login successful!"
    else:
        return "Invalid username or password."


# XSS Vulnerability
@app.route('/submit', methods=['GET'])
def xss():
    user_input = request.args.get('input')
    return f"<div>{user_input}</div>"

# File Upload Vulnerability
@app.route('/upload', methods=['POST'])
def upload():
    file = request.files['file']
    file.save(os.path.join("uploads", file.filename))
    return "File uploaded successfully!"

# Security Misconfiguration Vulnerability
@app.route('/details', methods=['GET'])
def security_misconfig():
    return "Hi, I created this website"

# Homepage
@app.route('/')
def index():
    return render_template('index.html')

if __name__ == '__main__':
    if not os.path.exists("uploads"):
        os.makedirs("uploads")
    app.run(port=5001, debug=True)
