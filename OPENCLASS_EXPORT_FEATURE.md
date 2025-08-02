# OpenClassRequest Excel Export Feature

## Overview
This feature allows exporting OpenClassRequest data to Excel format, providing comprehensive information about each request including subject details and participating students.

## Features Added

### 1. Multiple Export Options
- **Bulk Export**: Export all OpenClassRequests in separate sheets within one Excel file
- **Individual Export**: Export a single OpenClassRequest with detailed information

### 2. Export Classes Created

#### `OpenClassRequestExport.php`
- Main export class for multiple requests
- Creates separate sheets for each request
- Uses `WithMultipleSheets` interface

#### `SingleOpenClassRequestExport.php`  
- Export class for individual requests
- Comprehensive formatting with styles and borders
- Uses `FromArray`, `ShouldAutoSize`, and `WithStyles` interfaces

#### `OpenClassRequestSheetExport.php`
- Helper class for individual sheet formatting in multi-sheet exports
- Includes title generation and mapping functionality

### 3. Excel File Structure

Each exported Excel contains:

#### Request Information Section:
- Môn học (Subject name)
- Mã môn học (Subject code)  
- Số tín chỉ (Credits)
- Học kỳ (Semester)
- Người tạo yêu cầu (Request creator)
- Mã sinh viên tạo (Creator student code)
- Trạng thái (Status)
- Ngày tạo (Creation date)
- Ghi chú sinh viên (Student notes)
- Ghi chú admin (Admin notes)
- Tổng số sinh viên tham gia (Total participating students)

#### Student List Section:
- STT (Sequential number)
- Mã sinh viên (Student code)
- Họ và tên (Full name)
- Lớp (Class)
- Khoa (Faculty)
- Vai trò (Role - Creator vs Participant)
- Ngày tham gia (Join date)

### 4. UI Integration

#### Export Actions Added To:
1. **List Page** (`ListOpenClassRequests.php`)
   - Header action to export all requests
   - Individual table action for each request
   
2. **View Page** (`ViewOpenClassRequest.php`)
   - Header action to export the current request

### 5. File Naming Convention
- Bulk export: `yeu-cau-mo-lop-YYYY-MM-DD_HH-ii-ss.xlsx`
- Individual export: `yeu-cau-mo-lop-{subject_code}-YYYY-MM-DD_HH-ii-ss.xlsx`

### 6. Styling Features
- Professional Excel formatting with colors and borders
- Bold headers and section titles
- Merged cells for better visual organization
- Auto-sizing columns
- Highlighted role identification (Creator vs Participant)

## Testing
Created unit tests in `OpenClassRequestExportTest.php` to verify:
- Class existence and interface implementation
- Array structure and data integrity
- Export functionality compliance

## Dependencies
Uses existing Laravel Excel package (`maatwebsite/excel`) already available in the project.