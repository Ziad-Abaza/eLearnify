# eLearnify

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

eLearnify is a comprehensive, scalable, and secure e-learning backend API built with Laravel. It provides features for managing courses, videos, quizzes, certificates, users, and roles with dedicated APIs for administrators, instructors, and learners. This project now includes a frontend powered by **Vue.js** and **Tailwind CSS**, making it ready to be used as a full-stack solution for online education platforms.

---

## Table of Contents

* [About](#about)
* [Features](#features)
* [Technology Stack](#technology-stack)
* [Project Structure](#project-structure)
* [API Endpoints Overview](#api-endpoints-overview)
* [Installation and Setup](#installation-and-setup)
* [Frontend Setup (Vue + Tailwind CSS)](#frontend-setup-vue--tailwind-css)
* [Usage](#usage)
* [Testing](#testing)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)

---

## About

eLearnify is designed to empower online education platforms by providing a robust backend service with fine-grained role management, course content delivery, video lessons, quizzes, progress tracking, and certificate generation. It supports multiple user roles such as Admin, Instructor, and Learner with appropriate access control and functionality.

This version integrates **Vue.js** for the frontend along with **Tailwind CSS** for styling, allowing developers to build dynamic and responsive educational interfaces seamlessly connected to the Laravel backend.

---

## Features

### Backend

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

### Frontend (Vue + Tailwind CSS)

* Vue 3 Composition API support
* Tailwind CSS for utility-first styling
* Vite for fast development server and hot module replacement (HMR)
* Modular structure for reusable components and pages
* Ready-to-integrate with Laravel backend APIs

---

## Technology Stack

### Backend

* **Backend Framework:** Laravel (PHP)
* **Authentication:** Laravel Sanctum (API Token based)
* **Database:** MySQL (configurable)
* **ORM:** Eloquent
* **API Development:** RESTful API with Resource Controllers and API Resources
* **Middleware:** Role-based Middleware for security
* **Dependency Management:** Composer

### Frontend

* **Framework:** Vue.js 3
* **Styling:** Tailwind CSS
* **Build Tool:** Vite
* **Asset Management:** Vite with Laravel plugin

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
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       └── index.blade.php
├── routes/
│   └── api.php
├── artisan
├── composer.json
├── package.json
├── vite.config.js
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
* Node.js and npm/yarn
* Laravel CLI (optional but recommended)

### Steps

1. **Clone the repository**

```bash
git clone https://github.com/Ziad-Abaza/eLearnify.git  
cd eLearnify
```

2. **Install backend dependencies**

```bash
composer install
```

3. **Install frontend dependencies**

```bash
npm install
```

4. **Copy `.env` file and configure**

```bash
cp .env.example .env
```

* Set your database credentials in `.env` file
* Configure mail settings for email verification and password reset if needed

5. **Generate application key**

```bash
php artisan key:generate
```

6. **Run migrations and seeders**

```bash
php artisan migrate --seed
```

7. **Serve the application using Vite**

```bash
npm run dev
```

The frontend will be available at `http://localhost:5173`, while the Laravel backend runs at `http://localhost:8000`.

---

## Frontend Setup (Vue + Tailwind CSS)

### Vite Configuration (`vite.config.js`)

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

### Tailwind CSS Setup

Ensure you have a `tailwind.config.js` file:

```js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.vue',
    './resources/**/*.js',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

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

For the frontend, test UI interactions and API integrations using browser developer tools and inspect network requests to verify correct behavior.

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
