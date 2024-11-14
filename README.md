# Course Scheduling System API

This project is a Laravel-based API application that includes user authentication, role-based access control, and CRUD functionality for courses and schedules. Roles include `Admin`, `Lecturer`, and `Student`, each with specific permissions.

## ERD
![logo](/docs/ERD.png)


## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/mnaufalrifqir/sistem-penjadwalan-matkul.git
   cd sistem-penjadwalan-matkul
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Set Up Environment**
   - Copy `.env.example` to `.env` and configure the environment variables for database, JWT, and other configurations.
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Generate JWT Secret**
   ```bash
   php artisan jwt:secret
   ```

6. **Run Migrations and Seed Database**
   ```bash
   php artisan migrate --seed
   ```

7. **Start the Server**
   ```bash
   php artisan serve
   ```

## Running the Application

After starting the server, the application will be available at `http://localhost:8000`.

To access authenticated routes, you need to:
1. Register a user or login to receive an access token.
2. Use the token as a Bearer Token in the Authorization header for subsequent requests.

## API Routes and Role-Based Access
You can access the Postman collection in the docs folder for easier testing of all available API endpoints.

### No Authentication Required

| Method | Endpoint               | Description  |
|--------|-------------------------|--------------|
| POST   | `/api/auth/login`           | User Login   |
| POST   | `/api/auth/register`        | User Register |

### General Auth (All Authenticated Users: `Admin`, `Student`, `Lecturer`)

| Method | Endpoint                        | Description                             |
|--------|---------------------------------|-----------------------------------------|
| POST   | `/api/auth/logout`                   | Logout                                  |
| GET    | `/api/auth/me`                       | Get Authenticated User Profile          |
| GET    | `/api/courses`                  | Get All Courses                         |
| GET    | `/api/courses/{id}`             | Get Course by ID                        |
| GET    | `/api/course-schedules`         | Get All Course Schedules                |
| GET    | `/api/course-schedules/{id}`    | Get Course Schedule by ID               |
| GET    | `/api/course-schedules/{id}/students` | Get All Students by Course Schedule |

### Admin Only

| Method | Endpoint                 | Description              |
|--------|---------------------------|--------------------------|
| POST   | `/api/courses`            | Create Course            |
| PUT    | `/api/courses/{id}`       | Update Course            |
| DELETE | `/api/courses/{id}`       | Delete Course            |
| GET    | `/api/users`              | Get All Users            |
| GET    | `/api/students`     | Get All Students         |
| GET    | `/api/lecturers`    | Get All Lecturers        |
| GET    | `/api/users/{id}`         | Get User by ID           |
| PUT    | `/api/users/{id}`         | Update User              |
| DELETE | `/api/users/{id}`         | Delete User              |

### Lecturer + Admin

| Method | Endpoint                         | Description                             |
|--------|----------------------------------|-----------------------------------------|
| POST   | `/api/course-schedules`   | Enroll Course Schedule by Lecturer      |
| PUT    | `/api/course-schedules/{id}`     | Update Course Schedule                  |
| DELETE | `/api/course-schedules/{id}`     | Delete Course Schedule                  |

### Student + Admin

| Method | Endpoint                              | Description                             |
|--------|---------------------------------------|-----------------------------------------|
| GET    | `/api/student-schedules`              | Get All Schedules                       |
| GET    | `/api/student-schedules/{id}`         | Get Student Schedule by ID              |
| POST   | `/api/student-schedules`       | Enroll Student Schedule by Student      |
| PUT    | `/api/student-schedules/{id}`         | Update Student Schedule                 |
| DELETE | `/api/student-schedules/{id}`         | Delete Student Schedule                 |

## Notes

Make sure to include the JWT token in the request header for routes that require authentication:

```
Authorization: Bearer <token>
```