# 🎓 University Student Services Platform

A comprehensive university student services platform developed as a graduation project.
The system digitizes academic and administrative services for students, teachers, and university staff.

---

## 📌 Project Description

This platform was built to solve common problems in universities by providing a unified digital system that manages:

- Student academic records
- Attendance and schedules
- Exams and results
- University announcements
- Internal communication (chat & notifications)

The goal is to improve efficiency, transparency, and user experience in academic institutions.

---

## 🚀 Main Features

### 👨‍🎓 Student
- View academic profile
- Access schedules and attendance
- View exams and results
- Notifications and internal chat

### 👨‍🏫 Teacher
- Manage student attendance
- Publish grades and exams
- View schedules
- Notifications system

### 🛠️ Admin
- Full system control
- User roles & permissions
- Colleges, departments, courses management
- Academic years & schedules setup

---

## 🧰 Technologies Used

- **Backend:** Laravel
- **Database:** MySQL
- **Authentication:** Laravel Sanctum
- **Architecture:** RESTful API
- **Web Frontend:** Blade + CSS + JavaScript
- **Mobile App:** Flutter (separate repository)

---

## 🖼️ Screenshots

> If images do not render, make sure the `screenshots` folder exists at the repo root and filenames match exactly. Alternatively consider renaming files to ASCII (no spaces) to avoid URL issues.

### 🔐 Login Page
![Login](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/واجهه%20تسجيل%20الدخول%20للموقع.png)

### 🏠 Landing Page
![Landing](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/public%20landing%20page.png)

### 📋 Available Services
![Services]([https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/الخدمات%20المتوفره%20والمستعرضه%20في%20صفحه%20الهبوط.png](https://github.com/SultanMohammedAlkeel/student-services-backend-laravel/blob/main/screenshots/%D8%A7%D9%84%D8%AE%D8%AF%D9%85%D8%A7%D8%AA%20%D8%A7%D9%84%D9%85%D8%AA%D9%88%D9%81%D8%B1%D9%87%20%D9%88%D8%A7%D9%84%D9%85%D8%B3%D8%AA%D8%B9%D8%B1%D8%B6%D9%87%20%D9%81%D9%8A%20%D8%B5%D9%81%D8%AD%D9%87%20%D8%A7%D9%84%D9%87%D8%A8%D9%88%D8%B7.png))

### 👨‍🎓 Student Registration
![Student Register](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/واجهه%20انشاء%20حساب%20طالب.png)

### 👨‍🏫 Teacher Dashboard
![Teacher Panel](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/الواجهه%20الترحيبيه%20للاستاذ.png)

### 🛠️ Admin Dashboard
![Admin Panel](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/الواجهه%20الرئيسيه%20للمسؤولين.png)

### 🏫 University Setup
![University Setup](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/تهيئه%20بيانات%20الجامعه%20من%20كليات%20ومباني%20واسجلات%20الطلاب%20وغيرها.png)

### 📅 Academic Schedule Setup
![Schedule](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/واجهه%20اضافه%20بيانات%20الجدول%20الدرسي%20لقسم%20ما%20في%20ترم%20محدد.png)

### 📚 Library Management
![Library](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/واجهه%20اداره%20موارد%20المكتبه.png)

### 💬 Chat System
![Chat](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/واجهه%20الدردشات%20ويب.png)

### 🧪 Exam Results
![Exam Result](https://raw.githubusercontent.com/SultanMohammedAlkeel/student-services-backend-laravel/5a16089ca71016e0bd792978ecbda856b019a5ae/screenshots/نتيجة%20الاختبار.png)

---

## ⚙️ Installation

```bash
git clone https://github.com/USERNAME/student-services-backend-laravel.git
cd student-services-backend-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```
