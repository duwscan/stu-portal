# OpenClassRequest Export Feature

## Overview
This feature allows users to export OpenClassRequest data to Excel format from the Filament admin panel.

## Features

### 1. Basic Export
- **Location**: Header actions on the OpenClassRequest list page
- **Button**: "Xuất Excel" (Export Excel)
- **Functionality**: Exports all filtered OpenClassRequest records with basic information

### 2. Detailed Export
- **Location**: Header actions on the OpenClassRequest list page
- **Button**: "Xuất Excel chi tiết" (Export Detailed Excel)
- **Functionality**: Exports detailed information including all participating students for each request

### 3. Bulk Export (Selected Records)
- **Location**: Bulk actions when records are selected
- **Options**: 
  - "Xuất dữ liệu đã chọn" (Export Selected Data) - Basic export
  - "Xuất chi tiết đã chọn" (Export Selected Detailed) - Detailed export
- **Functionality**: Exports only the selected records

## Export Columns

### Basic Export (OpenClassRequestExport)
1. ID
2. Môn học (Subject Name)
3. Mã môn học (Subject Code)
4. Người tạo yêu cầu (Request Creator)
5. MSSV người tạo (Creator Student ID)
6. Học kỳ (Semester)
7. Trạng thái (Status)
8. Số sinh viên tham gia (Number of Participating Students)
9. Ghi chú của sinh viên (Student Note)
10. Ghi chú của admin (Admin Note)
11. Ngày tạo (Created Date)
12. Ngày cập nhật (Updated Date)

### Detailed Export (OpenClassRequestDetailedExport)
1. ID Yêu cầu (Request ID)
2. Môn học (Subject Name)
3. Mã môn học (Subject Code)
4. Người tạo yêu cầu (Request Creator)
5. MSSV người tạo (Creator Student ID)
6. Học kỳ (Semester)
7. Trạng thái (Status)
8. Tổng số SV tham gia (Total Participating Students)
9. MSSV tham gia (Participating Student ID)
10. Tên SV tham gia (Participating Student Name)
11. Email SV tham gia (Participating Student Email)
12. Lớp SV tham gia (Participating Student Class)
13. Ghi chú của sinh viên (Student Note)
14. Ghi chú của admin (Admin Note)
15. Ngày tạo (Created Date)
16. Ngày tham gia (Join Date)

## File Naming Convention
- Basic export: `yeu-cau-mo-lop-YYYY-MM-DD-HH-ii-ss.xlsx`
- Detailed export: `yeu-cau-mo-lop-chi-tiet-YYYY-MM-DD-HH-ii-ss.xlsx`
- Selected records: `yeu-cau-mo-lop-selected-YYYY-MM-DD-HH-ii-ss.xlsx`
- Selected detailed: `yeu-cau-mo-lop-chi-tiet-selected-YYYY-MM-DD-HH-ii-ss.xlsx`

## Implementation Details

### Classes
- `App\Exports\OpenClassRequestExport`: Basic export functionality
- `App\Exports\OpenClassRequestDetailedExport`: Detailed export with participating students

### Dependencies
- Uses Maatwebsite\Excel package for Excel generation
- Integrates with Filament table filters and selections
- Supports both query-based and collection-based exports

### Error Handling
- Gracefully handles missing relationships with "N/A" values
- Safe counting of participating students with try-catch
- Proper null checking for optional fields