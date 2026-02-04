# API Documentation

Complete API reference for the Employee Management System.

## Base URL

```
http://localhost/api
```

## Authentication

All endpoints except authentication routes require Bearer token authentication.

**Header Format:**
```
Authorization: Bearer {your-token}
```

---

## 📌 Authentication Endpoints

### Register User

Create a new user account.

**Endpoint:** `POST /api/auth/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!"
}
```

**Response (201 Created):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2024-02-03T10:00:00.000000Z",
    "updated_at": "2024-02-03T10:00:00.000000Z"
  },
  "access_token": "1|abcdefghijklmnopqrstuvwxyz",
  "token_type": "Bearer"
}
```

### Login

Authenticate and receive access token.

**Endpoint:** `POST /api/auth/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "Password123!"
}
```

**Response (200 OK):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "access_token": "1|abcdefghijklmnopqrstuvwxyz",
  "token_type": "Bearer"
}
```

**Error Response (401 Unauthorized):**
```json
{
  "message": "Invalid credentials"
}
```

### Logout

Revoke current access token.

**Endpoint:** `POST /api/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Logged out successfully"
}
```

### Get Authenticated User

Get current user information.

**Endpoint:** `GET /api/auth/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2024-02-03T10:00:00.000000Z",
    "updated_at": "2024-02-03T10:00:00.000000Z"
  }
}
```

### Forgot Password

Request password reset link.

**Endpoint:** `POST /api/auth/forgot-password`

**Request Body:**
```json
{
  "email": "john@example.com"
}
```

**Response (200 OK):**
```json
{
  "message": "Password reset link sent to your email"
}
```

### Reset Password

Reset password using token from email.

**Endpoint:** `POST /api/auth/reset-password`

**Request Body:**
```json
{
  "token": "reset-token-from-email",
  "email": "john@example.com",
  "password": "NewPassword123!",
  "password_confirmation": "NewPassword123!"
}
```

**Response (200 OK):**
```json
{
  "message": "Password reset successful"
}
```

---

## 👥 Employee Endpoints

All employee endpoints require authentication.

### List Employees

Get paginated list of employees.

**Endpoint:** `GET /api/employees`

**Query Parameters:**
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "first_name": "Jane",
      "last_name": "Smith",
      "full_name": "Jane Smith",
      "email": "jane@example.com",
      "employee_identifier": "EMP001",
      "phone_number": "+1234567890",
      "user_id": null,
      "created_at": "2024-02-03T10:00:00.000000Z",
      "updated_at": "2024-02-03T10:00:00.000000Z",
      "deleted_at": null
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

### Create Employee

Create a new employee record.

**Endpoint:** `POST /api/employees`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "first_name": "Jane",
  "last_name": "Smith",
  "email": "jane@example.com",
  "employee_identifier": "EMP001",
  "phone_number": "+1234567890",
  "user_id": null
}
```

**Response (201 Created):**
```json
{
  "message": "Employee created successfully",
  "data": {
    "id": 1,
    "first_name": "Jane",
    "last_name": "Smith",
    "full_name": "Jane Smith",
    "email": "jane@example.com",
    "employee_identifier": "EMP001",
    "phone_number": "+1234567890",
    "user_id": null,
    "created_at": "2024-02-03T10:00:00.000000Z",
    "updated_at": "2024-02-03T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

**Validation Errors (422 Unprocessable Entity):**
```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

### Get Employee

Get details of a specific employee.

**Endpoint:** `GET /api/employees/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "first_name": "Jane",
    "last_name": "Smith",
    "full_name": "Jane Smith",
    "email": "jane@example.com",
    "employee_identifier": "EMP001",
    "phone_number": "+1234567890",
    "user_id": null,
    "attendances": [],
    "created_at": "2024-02-03T10:00:00.000000Z",
    "updated_at": "2024-02-03T10:00:00.000000Z",
    "deleted_at": null
  }
}
```

### Update Employee

Update employee information.

**Endpoint:** `PUT /api/employees/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body (all fields optional):**
```json
{
  "first_name": "Janet",
  "phone_number": "+9876543210"
}
```

**Response (200 OK):**
```json
{
  "message": "Employee updated successfully",
  "data": {
    "id": 1,
    "first_name": "Janet",
    "last_name": "Smith",
    "full_name": "Janet Smith",
    "email": "jane@example.com",
    "employee_identifier": "EMP001",
    "phone_number": "+9876543210",
    "user_id": null,
    "created_at": "2024-02-03T10:00:00.000000Z",
    "updated_at": "2024-02-03T11:00:00.000000Z",
    "deleted_at": null
  }
}
```

### Delete Employee

Soft delete an employee.

**Endpoint:** `DELETE /api/employees/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Employee deleted successfully"
}
```

---

## ⏰ Attendance Endpoints

All attendance endpoints require authentication.

### List Attendances

Get paginated list of attendance records with optional filters.

**Endpoint:** `GET /api/attendances`

**Query Parameters:**
- `employee_id` (optional): Filter by employee ID
- `from_date` (optional): Filter from date (YYYY-MM-DD)
- `to_date` (optional): Filter to date (YYYY-MM-DD)
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number

**Headers:**
```
Authorization: Bearer {token}
```

**Example Request:**
```
GET /api/attendances?employee_id=1&from_date=2024-02-01&to_date=2024-02-28
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "employee": {
        "id": 1,
        "first_name": "Jane",
        "last_name": "Smith",
        "full_name": "Jane Smith",
        "email": "jane@example.com",
        "employee_identifier": "EMP001"
      },
      "check_in": "2024-02-03T09:00:00.000000Z",
      "check_out": "2024-02-03T17:30:00.000000Z",
      "attendance_date": "2024-02-03",
      "hours_worked": 8.5,
      "created_at": "2024-02-03T09:00:00.000000Z",
      "updated_at": "2024-02-03T17:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

### Record Check-In

Create a new attendance record (check-in).

**Endpoint:** `POST /api/attendances`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "employee_id": 1,
  "check_in": "2024-02-03T09:00:00",
  "attendance_date": "2024-02-03"
}
```

**Note:** If `check_in` and `attendance_date` are not provided, current timestamp is used.

**Response (201 Created):**
```json
{
  "message": "Check-in recorded successfully",
  "data": {
    "id": 1,
    "employee_id": 1,
    "check_in": "2024-02-03T09:00:00.000000Z",
    "check_out": null,
    "attendance_date": "2024-02-03",
    "hours_worked": null,
    "created_at": "2024-02-03T09:00:00.000000Z",
    "updated_at": "2024-02-03T09:00:00.000000Z"
  }
}
```

**Note:** An email notification is queued and sent to the employee.

### Record Check-Out

Record check-out time for an attendance record.

**Endpoint:** `POST /api/attendances/{id}/check-out`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body (optional):**
```json
{
  "check_out": "2024-02-03T17:30:00"
}
```

**Note:** If `check_out` is not provided, current timestamp is used.

**Response (200 OK):**
```json
{
  "message": "Check-out recorded successfully",
  "data": {
    "id": 1,
    "employee_id": 1,
    "check_in": "2024-02-03T09:00:00.000000Z",
    "check_out": "2024-02-03T17:30:00.000000Z",
    "attendance_date": "2024-02-03",
    "hours_worked": 8.5,
    "created_at": "2024-02-03T09:00:00.000000Z",
    "updated_at": "2024-02-03T17:30:00.000000Z"
  }
}
```

**Error Response (400 Bad Request):**
```json
{
  "message": "Check-out already recorded"
}
```

### Get Attendance

Get details of a specific attendance record.

**Endpoint:** `GET /api/attendances/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "employee_id": 1,
    "employee": {
      "id": 1,
      "first_name": "Jane",
      "last_name": "Smith",
      "full_name": "Jane Smith"
    },
    "check_in": "2024-02-03T09:00:00.000000Z",
    "check_out": "2024-02-03T17:30:00.000000Z",
    "attendance_date": "2024-02-03",
    "hours_worked": 8.5,
    "created_at": "2024-02-03T09:00:00.000000Z",
    "updated_at": "2024-02-03T17:30:00.000000Z"
  }
}
```

### Update Attendance

Update attendance record.

**Endpoint:** `PUT /api/attendances/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "check_in": "2024-02-03T08:45:00",
  "check_out": "2024-02-03T17:15:00"
}
```

**Response (200 OK):**
```json
{
  "message": "Attendance updated successfully",
  "data": {
    "id": 1,
    "employee_id": 1,
    "check_in": "2024-02-03T08:45:00.000000Z",
    "check_out": "2024-02-03T17:15:00.000000Z",
    "attendance_date": "2024-02-03",
    "hours_worked": 8.5,
    "created_at": "2024-02-03T09:00:00.000000Z",
    "updated_at": "2024-02-03T18:00:00.000000Z"
  }
}
```

### Delete Attendance

Delete an attendance record.

**Endpoint:** `DELETE /api/attendances/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Attendance deleted successfully"
}
```

---

## 📊 Report Endpoints

All report endpoints require authentication.

### Generate PDF Report

Generate and download a PDF attendance report.

**Endpoint:** `GET /api/reports/attendance/pdf`

**Query Parameters:**
- `employee_id` (optional): Filter by employee ID
- `from_date` (optional): Start date (YYYY-MM-DD)
- `to_date` (optional): End date (YYYY-MM-DD)

**Headers:**
```
Authorization: Bearer {token}
```

**Example Request:**
```
GET /api/reports/attendance/pdf?from_date=2024-02-01&to_date=2024-02-28
```

**Response:**
- Content-Type: `application/pdf`
- File download with name: `attendance-report-YYYY-MM-DD-HHMMSS.pdf`

### Generate Excel Report

Generate and download an Excel attendance report.

**Endpoint:** `GET /api/reports/attendance/excel`

**Query Parameters:**
- `employee_id` (optional): Filter by employee ID
- `from_date` (optional): Start date (YYYY-MM-DD)
- `to_date` (optional): End date (YYYY-MM-DD)

**Headers:**
```
Authorization: Bearer {token}
```

**Example Request:**
```
GET /api/reports/attendance/excel?employee_id=1
```

**Response:**
- Content-Type: `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`
- File download with name: `attendance-report-YYYY-MM-DD-HHMMSS.xlsx`

**Excel Contents:**
- Sheet name: "Attendance Report"
- Columns: ID, Employee ID, Employee Name, Email, Date, Check-In, Check-Out, Hours Worked, Status
- Styled headers with gray background
- All data filtered by provided parameters

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Not Found (404)
```json
{
  "message": "Resource not found."
}
```

### Server Error (500)
```json
{
  "message": "Server Error"
}
```

---

## Rate Limiting

- Authentication endpoints: 5 requests per minute per IP
- Other endpoints: 60 requests per minute per user

---

## Testing with cURL

### Complete Example Flow

```bash
# 1. Register
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "Password123!",
    "password_confirmation": "Password123!"
  }'

# 2. Login and save token
TOKEN=$(curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "Password123!"
  }' | jq -r '.access_token')

# 3. Create employee
curl -X POST http://localhost/api/employees \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Jane",
    "last_name": "Doe",
    "email": "jane@example.com",
    "employee_identifier": "EMP001",
    "phone_number": "+1234567890"
  }'

# 4. Record check-in
curl -X POST http://localhost/api/attendances \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"employee_id": 1}'

# 5. Record check-out
curl -X POST http://localhost/api/attendances/1/check-out \
  -H "Authorization: Bearer $TOKEN"

# 6. Download PDF report
curl -X GET "http://localhost/api/reports/attendance/pdf" \
  -H "Authorization: Bearer $TOKEN" \
  -o attendance-report.pdf
```

---

## Postman Collection

Import this base URL and configure your Postman environment:

```
{{base_url}} = http://localhost/api
{{token}} = your-auth-token
```

Set up Authorization as "Bearer Token" with `{{token}}` variable.

---

For more information, see the [README.md](README.md) file.
