# Gowolo Customization Portal — Project Rules

## Project Overview
Standalone Laravel 11 customization portal for Gowolo. Separate app, separate DB.
Lives at `d:/gowolo/customization`. Main dashboard app is at `d:/gowolo/dashboardv2`.

## Database
- **Primary DB**: `customization` (MySQL) — all portal data
- **Secondary DB**: `dashboard_db` connection → `gowolov2` — reads `users`, `site_information`, `site_info_setting`; writes `e_wallet_management`
- Never create a local `users` table. SSO users come from `dashboard_db.users` via `App\Models\User`

## Authentication
- **Admin/Technician**: `portal` guard → `portal_users` table → Spatie roles
- **End users**: SSO only — token from dashboardv2 decoded in `SSOTokenService`, user fetched from `dashboard_db.users`, stored in `session('auth_user')`
- No separate SSO session table — keep it simple

## Roles (Spatie Permission, guard: `portal`)
- `super_admin` — full access including user management
- `admin` — manage requests, assign technicians, view reports
- `supervisor` — view all requests, update status
- `technician` — only sees assigned requests, can chat and update status

## Coding Rules
- Never add `Co-Authored-By` or any Claude reference in git commits
- Commit author is always the developer (Ashok Mehta)
- No `dd()` or debug statements left in code
- Use `session('auth_user')` for SSO user data — keys: `id`, `name`, `email`
- Use `Auth::guard('portal')->user()` for admin/tech users
- Raw SQL queries must use parameter binding — no string concatenation
- All file uploads: try Bunny first, fall back to local if not configured
- Activity logging via `spatie/laravel-activitylog` — never custom log tables

## File Storage
- Chat files → Bunny private zone: `chat/images/`, `chat/pdfs/`, `chat/documents/`
- Request files → Bunny: `requests/logos/`, `requests/icons/`, `requests/backgrounds/`, `requests/attachments/`
- Local fallback path: `public/uploads/`
- Serve files through controller with signed Bunny URLs — never expose direct paths
- `bunny_synced` column tracks migration status

## Chat
- Polling every 5 seconds via `GET /api/chat/{requestId}/poll?last_id=X&viewer=user|staff`
- Only fetch messages with `id > last_id`
- `sender_type`: `user` (SSO) or `portal_user` (admin/tech)

## Key Files
- `app/Services/SSOTokenService.php` — token decode + user fetch
- `app/Services/BunnyStorageService.php` — upload, signed URL, delete, migrate
- `app/Services/SiteInfoService.php` — reads from dashboard_db
- `app/Models/User.php` — points to dashboard_db.users (SSO users)
- `app/Models/PortalUser.php` — portal admin/tech users
- `routes/web.php` — all routes

## Packages
- `spatie/laravel-permission` — RBAC
- `spatie/laravel-activitylog` — audit log
- `intervention/image-laravel` — image processing

