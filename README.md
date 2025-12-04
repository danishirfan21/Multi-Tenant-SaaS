# Multi-Tenant SaaS Platform

A production-grade, full-stack multi-tenant SaaS platform built with **Laravel 11** (backend API) and **React 18 + TypeScript** (frontend SPA).

## Features

### Backend (Laravel API)
- **Multi-tenancy**: Single database with tenant_id isolation
- **JWT Authentication**: Secure token-based auth via tymon/jwt-auth
- **RESTful API**: Versioned endpoints (`/api/v1`)
- **Role-Based Access Control**: Owner, Admin, and User roles
- **Clean Architecture**: MVC + Service Layer + Repository Pattern
- **Database Optimization**: Composite indexes for tenant-scoped queries
- **Comprehensive Testing**: Feature and unit tests included

### Frontend (React SPA)
- **Modern Stack**: React 18, TypeScript, Vite, TailwindCSS
- **Authentication**: Context-based auth with protected routes
- **Tenant-Aware**: All API calls automatically scoped to user's tenant
- **Responsive UI**: Mobile-friendly design
- **Type Safety**: Full TypeScript coverage

### Core Resources
- **Projects**: Manage projects with status tracking
- **Tasks**: Kanban-style task management (To Do, In Progress, Done)
- **Users**: Team member management (Admin/Owner only)
- **Dashboard**: Project statistics and quick actions

---

## Tech Stack

### Backend
- PHP 8.2+
- Laravel 11
- MySQL 8.0+ (InnoDB, utf8mb4)
- JWT Authentication (tymon/jwt-auth)
- PHPUnit for testing

### Frontend
- React 18.3+
- TypeScript 5.4+
- Vite 5.2+
- TailwindCSS 3.4+
- Axios for HTTP
- React Router v6
- Vitest for testing

---

## Setup Instructions

### Prerequisites
- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **npm** or **yarn**
- **MySQL** >= 8.0

### Backend Setup

1. **Navigate to backend folder**
   ```bash
   cd backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```

   Edit `.env` and configure:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=multitenant_saas
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Generate JWT secret**
   ```bash
   php artisan jwt:secret
   ```

6. **Run migrations and seed database**
   ```bash
   php artisan migrate --seed
   ```

   This creates:
   - **Acme Corporation** tenant with 3 users
   - **TechStart Inc** tenant with 1 user
   - Demo projects and tasks

7. **Start development server**
   ```bash
   php artisan serve
   ```

   Backend will run at: `http://localhost:8000`

8. **Run tests** (optional)
   ```bash
   php artisan test
   ```

### Frontend Setup

1. **Navigate to frontend folder**
   ```bash
   cd frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```

   Edit `.env`:
   ```env
   VITE_API_BASE_URL=http://localhost:8000/api/v1
   ```

4. **Start development server**
   ```bash
   npm run dev
   ```

   Frontend will run at: `http://localhost:5173`

5. **Build for production** (optional)
   ```bash
   npm run build
   npm run preview
   ```

6. **Run tests** (optional)
   ```bash
   npm run test
   ```

---

## Demo Credentials

### Acme Corporation (Tenant 1)

**Owner:**
- Email: `john@acme.com`
- Password: `password123`

**Admin:**
- Email: `jane@acme.com`
- Password: `password123`

**User:**
- Email: `bob@acme.com`
- Password: `password123`

### TechStart Inc (Tenant 2)

**Owner:**
- Email: `alice@techstart.com`
- Password: `password123`

---

## API Documentation

### Authentication Endpoints

**POST** `/api/v1/auth/register`
```json
{
  "tenant_id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

**POST** `/api/v1/auth/login`
```json
{
  "email": "john@acme.com",
  "password": "password123"
}
```

**POST** `/api/v1/auth/logout`
Headers: `Authorization: Bearer {token}`

**GET** `/api/v1/auth/me`
Headers: `Authorization: Bearer {token}`

### Projects Endpoints

**GET** `/api/v1/projects`
Query params: `?status=active&search=website&per_page=15`

**POST** `/api/v1/projects`
```json
{
  "name": "New Project",
  "description": "Project description",
  "status": "active",
  "start_date": "2024-01-01",
  "end_date": "2024-12-31"
}
```

**GET** `/api/v1/projects/{id}`

**PUT** `/api/v1/projects/{id}`

**DELETE** `/api/v1/projects/{id}`

**GET** `/api/v1/projects/stats`

### Tasks Endpoints

**GET** `/api/v1/tasks`
Query params: `?project_id=1&status=todo&priority=high`

**POST** `/api/v1/tasks`
```json
{
  "project_id": 1,
  "title": "New Task",
  "description": "Task description",
  "status": "todo",
  "priority": "medium",
  "due_date": "2024-12-31"
}
```

**GET** `/api/v1/tasks/{id}`

**PUT** `/api/v1/tasks/{id}`

**DELETE** `/api/v1/tasks/{id}`

### Users Endpoints (Admin/Owner only)

**GET** `/api/v1/users`

**POST** `/api/v1/users`
```json
{
  "name": "New User",
  "email": "user@example.com",
  "password": "password123",
  "role": "user"
}
```

**GET** `/api/v1/users/{id}`

**PUT** `/api/v1/users/{id}`

**DELETE** `/api/v1/users/{id}`

---

## Architecture

### Multi-Tenancy Strategy

**Single Database, Tenant ID Column**
- Simpler to manage
- Cost-effective
- Tenant isolation via global scopes
- `BelongsToTenant` trait auto-scopes all queries

### Security Features

1. **JWT Token Authentication**
   - Tokens expire after 60 minutes
   - Refresh tokens valid for 14 days

2. **Tenant Isolation**
   - Global Eloquent scopes prevent cross-tenant data access
   - Middleware validates tenant access on every request

3. **Role-Based Permissions**
   - Owner: Full access including user management
   - Admin: Project and task management, user management
   - User: View and manage assigned projects/tasks

4. **SQL Injection Prevention**
   - Eloquent ORM parameterized queries
   - Form Request validation

5. **Mass Assignment Protection**
   - Fillable properties defined on all models

6. **Rate Limiting**
   - Configured on API routes

### Database Optimization

**Composite Indexes:**
```sql
-- Users
INDEX (tenant_id, email) UNIQUE
INDEX (tenant_id, role)

-- Projects
INDEX (tenant_id, status)
INDEX (tenant_id, created_at)

-- Tasks
INDEX (tenant_id, project_id, status)
INDEX (tenant_id, project_id, order)
```

These indexes optimize the most common tenant-scoped queries.

---

## Project Structure

```
Multi-Tenant-SaaS/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/V1/
│   │   │   ├── Middleware/
│   │   │   ├── Requests/Api/V1/
│   │   │   └── Resources/V1/
│   │   ├── Models/
│   │   ├── Repositories/
│   │   ├── Services/
│   │   └── Traits/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/api.php
│   ├── tests/
│   └── composer.json
│
└── frontend/                   # React SPA
    ├── src/
    │   ├── api/
    │   ├── components/
    │   │   ├── auth/
    │   │   ├── common/
    │   │   └── layout/
    │   ├── contexts/
    │   ├── pages/
    │   ├── types/
    │   └── App.tsx
    ├── tests/
    └── package.json
```

---

## Testing

### Backend Tests

```bash
cd backend
php artisan test
```

**Test Coverage:**
- Authentication flow
- Tenant isolation
- CRUD operations
- Service layer logic

### Frontend Tests

```bash
cd frontend
npm run test
```

**Test Coverage:**
- Component rendering
- User interactions
- Auth context

---

## Production Deployment

### Backend

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure production database
4. Run `composer install --optimize-autoloader --no-dev`
5. Run `php artisan config:cache`
6. Run `php artisan route:cache`
7. Run `php artisan view:cache`
8. Set up queue workers (if using queues)
9. Configure CORS for production frontend URL

### Frontend

1. Update `VITE_API_BASE_URL` to production API URL
2. Run `npm run build`
3. Deploy `dist/` folder to hosting (Vercel, Netlify, etc.)
4. Configure environment variables on hosting platform

---

## Security Considerations

### localStorage JWT Storage

⚠️ The frontend currently stores JWTs in `localStorage`, which is convenient but vulnerable to XSS attacks.

**Production Recommendations:**
- Use httpOnly cookies instead
- Implement Content Security Policy (CSP)
- Add input sanitization library (DOMPurify)
- Consider refresh token rotation

### CORS

Update `backend/config/cors.php` to allow only your production frontend domain.

---

## Troubleshooting

### Backend Issues

**"JWT secret not set"**
```bash
php artisan jwt:secret
```

**"Class not found" errors**
```bash
composer dump-autoload
```

**Migration errors**
```bash
php artisan migrate:fresh --seed
```

### Frontend Issues

**"Module not found"**
```bash
rm -rf node_modules package-lock.json
npm install
```

**CORS errors**
- Ensure backend CORS config includes frontend URL
- Check `.env` API_BASE_URL is correct

---

## License

MIT

---

## Support

For issues or questions, please open an issue on the repository.
