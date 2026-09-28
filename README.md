# AfriCrew Laravel Staffing Marketplace

This is a Laravel/Blade implementation starter for the AfriCrew workflow:

- Public AfriCrew-style landing page
- Hire Staff Now request form
- Join Our Crew / professional registration
- Admin login/dashboard
- Staffing request management
- Professional approval/rejection
- Crew matching and assignment
- Professional dashboard
- Job status management

## Installation

1. Copy these files into an existing Laravel application.
2. Run:
   php artisan migrate
   php artisan storage:link
3. Add the routes from `routes/web.php`.
4. Add the models/controllers/views.
5. Configure database credentials in `.env`.
6. Create an admin user using your preferred seeder/auth system.

This module assumes Laravel 10/11/12 style routing and Blade views. It uses the existing Laravel authentication/session system for admin/professional login where applicable.

## Main URLs

- `/` - AfriCrew landing page
- `/hire-staff` - Hire Staff form
- `/join-our-crew` - Professional registration
- `/admin/login` - Admin login
- `/admin` - Admin dashboard
- `/professional/dashboard` - Professional dashboard
