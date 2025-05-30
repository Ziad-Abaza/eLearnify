# eLearnify

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

eLearnify is a comprehensive, scalable, and secure e-learning backend API built with Laravel. It provides features for managing courses, videos, quizzes, certificates, users, and roles with dedicated APIs for administrators, instructors, and learners.

---

## Table of Contents

* [About](#about)
* [Features](#features)
* [Technology Stack](#technology-stack)
* [Project Structure](#project-structure)
* [API Endpoints Overview](#api-endpoints-overview)
* [Installation and Setup](#installation-and-setup)
* [Usage](#usage)
* [Testing](#testing)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)

---

## About

eLearnify is designed to empower online education platforms by providing a robust backend service with fine-grained role management, course content delivery, video lessons, quizzes, progress tracking, and certificate generation. It supports multiple user roles such as Admin, Instructor, and Learner with appropriate access control and functionality.

---

## Features

* **User Authentication & Authorization**

  * User registration and login via Sanctum API tokens.
  * Role-based access control (Admin, Instructor, Learner).
  * Email verification and password reset.

* **Course Management**

  * Create, update, delete, and list courses.
  * Organize content into videos and quizzes.

* **Video Lessons**

  * Upload and manage video lessons within courses.
  * Track user progress per video.

* **Quiz System**

  * Create and manage quiz questions and options.
  * Users can attempt quizzes with submission evaluation.
  * Quiz attempt history per user.

* **Certificate Generation**

  * Automatic certificate creation for course completion.

* **User Progress Tracking**

  * Track progress on courses and videos.
  * Provide APIs for progress retrieval and updates.

* **Role Management**

  * Admins can create, update, and delete roles.
  * Manage user roles dynamically.

* **Admin, Instructor, and Learner API Segregation**

  * Separate API routes and controllers per role.
  * Middleware to enforce role-specific access.

---

## Technology Stack

* **Backend Framework:** Laravel (PHP)
* **Authentication:** Laravel Sanctum (API Token based)
* **Database:** MySQL (configurable)
* **ORM:** Eloquent
* **API Development:** RESTful API with Resource Controllers and API Resources
* **Middleware:** Role-based Middleware for security
* **Dependency Management:** Composer

---

## Project Structure

```plaintext
eLearnify/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── CertificateController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── Instructor/ (Course, Question, Video controllers)
│   │   │   │   ├── User/ (Course, Progress, Quiz, Video controllers)
│   │   │   │   └── Other Controllers...
│   │   ├── Middleware/ (AdminMiddleware, InstructorMiddleware, etc.)
│   │   ├── Requests/ (Form Request Validation Classes)
│   │   └── Resources/ (API Resources for JSON responses)
│   ├── Models/ (Eloquent Models)
│   ├── Services/ (CertificateGeneratorService.php)
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── artisan
├── composer.json
└── README.md
```

---

## API Endpoints Overview

### Authentication

| Method | Endpoint    | Description           |
| ------ | ----------- | --------------------- |
| POST   | `/register` | Register a new user   |
| POST   | `/login`    | Login user, get token |
| POST   | `/logout`   | Logout user           |

### User APIs (Authenticated)

* Courses list and detail
* Enroll in courses
* Fetch enrolled courses and progress
* Mark video as complete
* Get videos and quizzes
* Submit quiz answers
* View quiz attempts

### Instructor APIs (Authenticated & Instructor Role)

* Manage courses, videos, quiz questions, and options

### Admin APIs (Authenticated & Admin Role)

* User management (list, update, delete)
* Role management (CRUD)
* Manage categories, courses, videos, questions, quiz attempts, certificates, and user progress

**Full route details are defined in `routes/api.php` and guarded with middleware to ensure proper role-based access.**

---

## Installation and Setup

### Prerequisites

* PHP 8.1 or higher
* Composer
* MySQL or any supported relational database
* Laravel CLI (optional but recommended)

### Steps

1. **Clone the repository**

```bash
git clone https://github.com/Ziad-Abaza/eLearnify.git
cd eLearnify
```

2. **Install dependencies**

```bash
composer install
```

3. **Copy `.env` file and configure**

```bash
cp .env.example .env
```

* Set your database credentials in `.env` file
* Configure mail settings for email verification and password reset if needed

4. **Generate application key**

```bash
php artisan key:generate
```

5. **Run migrations and seeders**

```bash
php artisan migrate --seed
```

6. **Serve the application**

```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`.

---

## Usage

### Authentication

* Register a user via `/register`
* Login to obtain Sanctum API token
* Use token in Authorization header (`Bearer <token>`) for protected routes

### Accessing Endpoints

* Use tools like Postman or integrate with frontend consuming APIs.
* Admin, Instructor, and User routes are protected by middleware and require proper authentication and roles.

### Role-Based Access

| Role       | Permissions                                                |
| ---------- | ---------------------------------------------------------- |
| Admin      | Full access to manage users, roles, courses, quizzes, etc. |
| Instructor | Manage their own courses, videos, and quizzes              |
| Learner    | Browse courses, enroll, watch videos, attempt quizzes      |

---

## Testing

Currently, automated tests are not included.
To manually test, use API clients (Postman/Insomnia) and interact with routes as per role.

---

## Contributing

Contributions are welcome and appreciated! Please follow these guidelines:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/YourFeature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/YourFeature`)
5. Open a Pull Request describing your changes

**Before contributing:**

* Follow PSR-12 coding standards
* Write meaningful commit messages
* Test your changes thoroughly

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## Contact

For questions or feedback, you can reach out to:

* **Ziad Abaza**
* GitHub: [https://github.com/Ziad-Abaza](https://github.com/Ziad-Abaza)

