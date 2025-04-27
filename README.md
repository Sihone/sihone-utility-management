/README.md
markdown
Copy
Edit
# Sihone Utility Management System

This project is a full-stack Utility Management application built with:

- **Laravel 11** (Backend API)
- **Vue 3 + Vite** (Frontend, inside `/client/`)
- **Vuetify 3** (Material Design UI framework)
- **Tailwind CSS** (Optional utility classes)
- **MySQL** (Remote database)

## 🏗️ Project Structure

/ (Laravel root) /app /bootstrap /config /database /public /client (Built Vue app assets) /resources /routes /storage /tests /client (Vue 3 source project) /src /public /vite.config.js .env (Laravel backend settings) client/.env (optional frontend settings)

bash
Copy
Edit

## ⚙️ Setup Instructions

### 1. Clone the repository

```bash
git clone https://your-repo-link.git
cd your-project-name
2. Install Laravel backend dependencies
bash
Copy
Edit
composer install
cp .env.example .env
php artisan key:generate
Set your database credentials in .env.

3. Install Client (Vue frontend)
bash
Copy
Edit
cd client
npm install
🛠 Local Development
Backend (Laravel)
bash
Copy
Edit
php artisan serve
Available at http://127.0.0.1:8000.

Frontend (Vue)
bash
Copy
Edit
cd client
npm run dev
Available at http://127.0.0.1:5173.

✅ Vue proxies /api requests to Laravel backend.

🚀 Production Build
bash
Copy
Edit
cd client
npm run build
Output goes into /public/client/ automatically.

Laravel serves:

APIs via /api/*

Frontend via /client/index.html (or / using fallback routes)

⚡ Deployment
SSH into server

Clone repository

Install backend (composer install)

Install frontend (cd client && npm install)

Build frontend (npm run build)

Set file permissions (storage/, bootstrap/cache/)

Configure web server DocumentRoot to /public

🔥 Features
Apartments management (List, Create, Edit, Delete)

Vuetify Material Design UI

Toast notifications for UX feedback

Loading spinner while fetching

Full Laravel + Vue 3 architecture

📜 License
This project is proprietary to Sihone Services. Contact: [your-email@example.com]
