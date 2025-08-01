# Technical Architecture Documentation

## System Architecture Overview

The Student Portal System follows a modern MVC architecture built on Laravel framework with Filament admin interface, designed for scalability and maintainability.

## Directory Structure

```
stu-portal/
├── app/
│   ├── Filament/
│   │   ├── Resources/          # Admin panel resources
│   │   └── Student/            # Student portal resources
│   ├── Http/
│   │   ├── Controllers/        # HTTP controllers
│   │   └── Middleware/         # Custom middleware
│   ├── Imports/                # Excel import classes
│   ├── Models/                 # Eloquent models
│   ├── Providers/              # Service providers
│   └── ValueObjects/           # Value object classes
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── js/                     # Frontend JavaScript
│   └── views/                  # Blade templates
└── routes/                     # Route definitions
```

## Core Models and Relationships

### Entity Relationship Diagram (Conceptual)

```
User ──┐
        ├── Student ──── TrainingProgram
        └── Teacher      │
                        └── ProgramSubject ──── Subject
                            │                    │
                            ├── Prerequisites    │
                            └── Corequisites     │
                                                 │
Semester ──── ClassRoom ──────────────────────────┘
    │             │
    └─────────────┼── StudentSubject
                  │
                  └── ClassAdjustmentRequest
                  └── OpenClassRequest
```

### Model Relationships

#### Student Model
```php
// Relationships
user() : BelongsTo           // Links to User account
trainingProgram() : BelongsTo // Academic program
classRooms() : BelongsToMany  // Enrolled classes
subjects() : HasMany          // Academic records
semesters() : BelongsToMany   // Enrolled semesters
```

#### ClassRoom Model
```php
// Relationships
subject() : BelongsTo         // Course subject
semester() : BelongsTo        // Academic semester
students() : BelongsToMany    // Enrolled students
teacher() : BelongsTo         // Instructor

// Computed Attributes
registered_count : int        // Current enrollment
is_full : bool               // Capacity status
can_register : bool          // Registration eligibility
```

#### ProgramSubject Model
```php
// Relationships
trainingProgram() : BelongsTo     // Parent program
subject() : BelongsTo             // Course subject
prerequisites() : BelongsToMany   // Required prior courses
corequisites() : BelongsToMany    // Concurrent courses
```

## Database Schema Highlights

### Key Tables

#### students
- `id` - Primary key
- `user_id` - Foreign key to users table
- `student_code` - Unique student identifier
- `training_program_id` - Foreign key to training programs
- `gender`, `address`, `class`, `faculty`, `birth_date` - Personal info

#### class_rooms
- `id` - Primary key
- `subject_id` - Foreign key to subjects
- `semester_id` - Foreign key to semesters
- `code` - Class identifier
- `capacity` - Maximum enrollment
- `is_open` - Registration status
- `user_id` - Instructor (foreign key to users)

#### program_subjects
- `id` - Primary key
- `training_program_id` - Foreign key
- `subject_id` - Foreign key
- `semester` - Recommended semester
- `is_required` - Required/elective flag
- `is_active` - Status flag

#### Pivot Tables
- `class_room_student` - Student class enrollments
- `prerequisite_subjects` - Subject prerequisites
- `subject_corequisites` - Subject corequisites
- `semester_student` - Student semester enrollments

## Filament Architecture

### Admin Panel Structure
```
app/Filament/
├── Resources/
│   ├── StudentResource.php         # Student management
│   ├── TrainingProgramResource.php # Program management
│   ├── SubjectResource.php         # Subject catalog
│   ├── ClassRoomResource.php       # Class management
│   ├── SemesterResource.php        # Semester management
│   ├── ProgramSubjectResource.php  # Program-subject links
│   ├── ClassAdjustmentRequestResource.php
│   ├── OpenClassRequestResource.php
│   └── UserResource.php            # User management
```

### Student Portal Structure
```
app/Filament/Student/
├── Pages/
│   ├── Auth/Login.php              # Student authentication
│   └── Dashboard.php               # Student dashboard
├── Resources/
│   ├── ClassRoomResource.php       # Available classes
│   ├── OpenClassRoomResource.php   # Open class requests
│   ├── ClassAdjustmentRequestResource.php
│   ├── OpenClassRequestResource.php
│   └── StudentSubjectResource.php  # Academic records
└── Widgets/
    ├── CurrentSemesterWidget.php   # Current semester info
    ├── StudentAccountWidget.php    # Account overview
    └── TrainingProgramWidget.php   # Program progress
```

## Business Logic Components

### 1. Registration Validation System

The system implements intelligent course registration validation through the `ClassRoom::getRegisterStatus()` method:

```php
// Key validation checks:
1. Class is open for registration
2. Class has available capacity
3. Student not already registered
4. Subject belongs to student's training program
5. Prerequisites completed (passed status)
6. Corequisites enrolled or completed
```

### 2. Import System

Located in `app/Imports/`, handles bulk data import:
- `StudentImport.php` - Student data import
- `SubjectImport.php` - Subject catalog import
- `GradeImport.php` - Grade/score import

### 3. Grade Calculation System

Automatic grade processing in `StudentSubject` model:
- Grade to letter conversion (A+ to F scale)
- Automatic pass/fail status determination
- Model event handlers for auto-calculation

### 4. Request Management System

Two types of student requests:
- **Class Adjustment Requests**: Transfer between classes
- **Open Class Requests**: Request new class openings with student participation

## Security and Authentication

### 1. Multi-Panel Authentication
- **Admin Panel**: Requires admin role verification
- **Student Portal**: Student-specific authentication

### 2. Middleware Implementation
```php
AdminAccessMiddleware::class  // Admin panel access control
Authenticate::class          // General authentication
```

### 3. Role-Based Access Control
Using Spatie Laravel Permission package:
- Admin users: Full system access
- Student users: Limited to personal data and services

## API Endpoints

### Public Routes
- `/` - Welcome page
- `/admin` - Admin panel login
- `/student` - Student portal login

### Internal API
Filament handles internal AJAX requests for:
- CRUD operations
- Data filtering and searching
- File uploads
- Form submissions

## Performance Considerations

### 1. Database Optimization
- Foreign key constraints for data integrity
- Indexes on frequently queried columns
- Soft deletes for important data preservation

### 2. Query Optimization
- Eloquent eager loading for relationships
- Computed attributes for frequently accessed data
- Scoped queries for current semester data

### 3. Caching Strategy
- Session-based authentication caching
- Laravel's built-in query result caching
- Filament's component caching

## Development Workflow

### 1. Migration System
Sequential migrations for database versioning:
```bash
php artisan migrate          # Apply migrations
php artisan migrate:rollback # Rollback migrations
```

### 2. Model Factories and Seeders
Located in `database/` for testing and development data

### 3. Testing Infrastructure
PHPUnit configuration for automated testing

## Deployment Architecture

### Development Environment
```bash
composer dev    # Concurrent development servers
```
Runs:
- Laravel development server
- Queue worker
- Log monitoring
- Vite development server

### Production Considerations
- Asset compilation with Vite
- Queue processing setup
- Database optimization
- Cache configuration

## Extensibility Points

### 1. Custom Filament Resources
Easy addition of new admin interfaces by extending Filament Resource classes

### 2. Model Event Hooks
Eloquent model events for custom business logic

### 3. Service Provider Registration
Custom service providers for additional functionality

### 4. Middleware Pipeline
Extensible middleware stack for request processing

---

*This technical documentation provides developers with the necessary information to understand, maintain, and extend the Student Portal System architecture.*