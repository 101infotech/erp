# ERP System Documentation Index

This folder contains all organized documentation for the ERP system. All documentation has been organized into logical categories for easy navigation.

## 📁 Documentation Structure

### 🔧 [SETUP/](SETUP/)

Initial setup, configuration, and quick reference guides for getting the system up and running.

-   Environment setup
-   Email notification configuration
-   Modal system setup
-   Nepali date/calendar setup
-   Mailtrap testing

### 🎯 [MODULES/](MODULES/)

Core module documentation covering major system modules.

-   Announcement Module
-   Complaint Box Module
-   HRM (Human Resource Management) Module
-   **Resource Requests & Expense Claims Module** ⭐ NEW
-   Notification System
-   Weekly Feedback Module

### ⚡ [FEATURES/](FEATURES/)

Feature documentation including implementation guides and quick references.

-   API Documentation
-   Dark Mode Implementation
-   Password Reset System
-   Registration System
-   Jibble Integration & Sync

### 🐛 [FIXES/](FIXES/)

Bug fixes, issue resolutions, and optimization documents.

-   Bug fixes and completion reports
-   Payroll collision detection and fixes
-   Finance module fixes
-   UI/UX fixes
-   Leave policy fixes
-   Modal and email fixes

### 📋 [IMPLEMENTATION/](IMPLEMENTATION/)

Detailed implementation guides, checklists, and technical specifications.

-   Modal system implementation
-   Employee navigation and portal implementation
-   Content management system implementation
-   **Resource Requests & Expense Claims Implementation** ⭐ NEW
-   Implementation checklists and progress tracking

### 📚 [GUIDES/](GUIDES/)

Comprehensive guides, quick starts, and visual documentation.

-   Dashboard implementation and guides
-   HRM complete guides and deployment guides
-   Finance module comprehensive guides (phases 1-4)
-   Payroll system documentation
-   Nepali date/calendar implementation
-   Phase 1 & 2 completion guides
-   Weekly feedback system guides
-   Architecture and system verification reports

## 🚀 Quick Start

1. **New to the system?** Start with [SETUP/](SETUP/)
2. **Need a specific module?** Check [MODULES/](MODULES/)
3. **Looking for feature documentation?** See [FEATURES/](FEATURES/)
4. **Implementing something?** Refer to [IMPLEMENTATION/](IMPLEMENTATION/)
5. **Need comprehensive guides?** Browse [GUIDES/](GUIDES/)

## 📊 File Statistics

-   **SETUP**: Environment and configuration files
-   **MODULES**: Core system modules (8 modules)
-   **FEATURES**: Feature implementations (18 features)
-   **FIXES**: Bug fixes and optimizations (15+ documents)
-   **IMPLEMENTATION**: Technical implementation details (including Resource Requests & Expense Claims)
-   **GUIDES**: Comprehensive guides and specifications

## 🆕 Latest Updates (December 21, 2025)

### Brand Bird Agency Booking Form API (Enhanced)

Complete REST API with multi-step form support for Brand Flight Consultation bookings:

#### 🔹 Public Submission Endpoint
- Multi-step form handling (3 steps: Details, Business & Services, Goals & Vision)
- Rate limited (10 requests/minute) to prevent abuse
- Support for 10+ industry types
- Support for 10+ service selection options
- All form fields from the images implemented
- Automatic IP tracking and validation

#### 🔹 Protected Admin Endpoints
- View all booking submissions with filtering
- Search by name, email, or company
- Update booking status (new → contacted → scheduled → completed)
- Delete bookings
- Pagination support
- Authentication via Laravel Sanctum

**Documentation:**
- [Full API Documentation](API_BOOKING_FORM.md)
- [Quick Reference Guide](../BOOKING_FORM_API_QUICK_REFERENCE.md)
- [Postman Collection](POSTMAN_BOOKING_FORM.json)

---

### Saubhagya Group Meeting Scheduling API

Complete REST API implementation for scheduling meetings with Saubhagya Group:

#### 🔹 Public Submission Endpoint
- Form submission for scheduling meetings
- Rate limited (5 requests/minute) to prevent abuse
- Support for multiple meeting types (Partnership, Investment, Franchise, etc.)
- Automatic email and phone validation
- IP tracking and user agent logging

#### 🔹 Protected Admin Endpoints
- View all meeting requests with filtering
- Update meeting status (pending → confirmed → completed)
- Delete meeting requests
- Pagination support
- Authentication via Laravel Sanctum

**Documentation:**
- [Full API Documentation](API_SCHEDULE_MEETING.md)
- [Postman Collection](POSTMAN_SCHEDULE_MEETING.json)

---

### Resource Requests & Expense Claims Module

Complete implementation of two new features for staff resource management and expense reimbursement:

#### 🔹 Resource Request System

-   Staff can request items (office supplies, equipment, pantry items, etc.)
-   Admin approval workflow with fulfillment tracking
-   Priority levels and category classification
-   Cost tracking and vendor management

#### 🔹 Expense Claims System

-   Staff can submit expense claims with receipts
-   Automatic payroll integration for approved claims
-   Multiple expense types (travel, accommodation, meals, transportation, etc.)
-   Auto-generated claim numbers
-   File upload support for receipts

#### 🔹 Payroll Integration

-   Approved expense claims automatically included in payroll calculations
-   Claims added to gross salary
-   Automatic linking and tracking
-   No manual intervention required

**Documentation:**

-   [Full Module Documentation](MODULES/RESOURCE_REQUESTS_AND_EXPENSE_CLAIMS.md)
-   [Implementation Guide](IMPLEMENTATION/RESOURCE_REQUESTS_EXPENSE_CLAIMS_IMPLEMENTATION.md)

## 📝 Notes

All documentation is organized by purpose and category for easy discovery. Each folder contains related documents grouped logically.

---

_Last Updated: December 21, 2025_
