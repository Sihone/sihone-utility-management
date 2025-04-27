
# Sihone Utility Management - Frontend (Vue 3)

This is the Vue 3 frontend client for the Sihone Utility Management System.

## 📦 Tech Stack

- **Vue 3** with **Vite**
- **Vuetify 3** (Material UI Framework)
- **Vue Toastification 2** (Toast notifications)
- **Axios** (HTTP client)
- **Tailwind CSS** (optional)

## 🛠 Setup Instructions

```bash
cd client
npm install
```

## 🚀 Local Development

```bash
npm run dev
```

The app will be available at:

```
http://127.0.0.1:5173
```

✅ API requests (`/api/*`) are proxied to Laravel backend.

## 🚀 Production Build

```bash
npm run build
```

- Output is placed inside `/public/client/`
- Ready to be served through Laravel backend.

## ✨ Features

- Apartments Management Dashboard
- Create/Edit Modals
- Toast Notifications for Success/Error
- Loading Spinner while Fetching
- Fully Responsive Design (Vuetify 3)
