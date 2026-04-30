CREATE TABLE student_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100),
    dob DATE,
    gender ENUM('Male', 'Female', 'Other'),
    email VARCHAR(100),
    phone VARCHAR(15),
    address TEXT,
    course VARCHAR(50),
    department VARCHAR(100),
    semester VARCHAR(20),
    admission_date DATE,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);