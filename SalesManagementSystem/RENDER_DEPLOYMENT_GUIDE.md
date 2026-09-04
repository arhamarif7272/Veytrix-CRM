# 🚀 Veytrix Production Deployment Guide on Render

This guide provides step-by-step instructions to deploy **Veytrix — Enterprise Customer Relationship & Workflow Management System** (Laravel 12 + MongoDB Atlas + Nginx) to **Render** using Docker.

---

## 📋 Prerequisites

1. **GitHub Account**: A GitHub repository with your Veytrix project.
2. **Render Account**: Free account at [render.com](https://render.com).
3. **MongoDB Atlas Account**: An active MongoDB Atlas cluster with your connection URI.

---

## 1️⃣ Step 1: Whitelist Render in MongoDB Atlas

Render's free tier uses dynamic cloud IP addresses. You must allow Render to connect to your MongoDB Atlas cluster:

1. Log into your [MongoDB Atlas Console](https://cloud.mongodb.com/).
2. In the left navigation, under **Security**, click **Network Access**.
3. Click the green **+ Add IP Address** button.
4. Click **Allow Access from Anywhere** (enters `0.0.0.0/0`).
5. Click **Confirm**.

> [!NOTE]
> MongoDB Atlas authentication is secured by your database username and strong password.

---

## 2️⃣ Step 2: Push Your Code to GitHub

Commit the newly created Docker and Render deployment files to your Git repository:

```bash
git add .
git commit -m "Add Docker production deployment files for Render"
git push origin main
```

---

## 3️⃣ Step 3: Create the Web Service on Render

### Option A: 1-Click Blueprint (Recommended)
1. Log into your [Render Dashboard](https://dashboard.render.com/).
2. Click **New +** at the top right and choose **Blueprint**.
3. Select your GitHub repository (`SalesManagementSystem`).
4. Render will automatically detect `render.yaml` and configure the Web Service with Docker.
5. Provide the required secret values (e.g. `APP_KEY`, `MONGODB_URI`) when prompted.

---

### Option B: Manual Web Service Setup
If you prefer setting up manually without Blueprints:

1. In the Render Dashboard, click **New +** -> **Web Service**.
2. Select **Build and deploy from a Git repository** and connect your repository.
3. Configure the service settings:
   - **Name:** `veytrix-crm`
   - **Region:** Choose the region closest to your MongoDB Atlas cluster (e.g. `Oregon (US West)` or `Frankfurt (EU)`).
   - **Branch:** `main`
   - **Runtime:** **Docker**
   - **Instance Type:** **Free**
4. Click **Advanced** and set:
   - **Health Check Path:** `/login`

---

## 4️⃣ Step 4: Configure Production Environment Variables

In your Render Service Dashboard, navigate to the **Environment** tab and add the following variables:

| Key | Example / Recommended Value | Description |
| :--- | :--- | :--- |
| `APP_NAME` | `Veytrix` | Application brand name |
| `APP_ENV` | `production` | Production mode |
| `APP_DEBUG` | `false` | Disable stack traces for security |
| `APP_KEY` | *Your 32-character key* | Copy from your local `.env` |
| `APP_URL` | `https://veytrix-crm.onrender.com` | Your assigned Render URL |
| `DB_CONNECTION` | `mongodb` | MongoDB database driver |
| `MONGODB_URI` | `mongodb+srv://user:pass@cluster.mongodb.net/...` | Atlas connection string |
| `MONGODB_DATABASE` | `salesmanagementsystem` | Target database name |
| `SESSION_DRIVER` | `file` | Serverless-safe file sessions |
| `SESSION_LIFETIME` | `120` | Session lifetime (minutes) |
| `CACHE_STORE` | `file` | File cache |
| `QUEUE_CONNECTION` | `sync` | Synchronous execution |
| `MAIL_MAILER` | `smtp` | Mail driver |
| `MAIL_HOST` | `smtp.gmail.com` | Gmail SMTP host |
| `MAIL_PORT` | `587` | TLS port |
| `MAIL_USERNAME` | `your-email@gmail.com` | SMTP account |
| `MAIL_PASSWORD` | `xxxx xxxx xxxx xxxx` | 16-character Gmail App Password |
| `MAIL_ENCRYPTION` | `tls` | Transport encryption |
| `MAIL_FROM_ADDRESS` | `your-email@gmail.com` | From email |
| `MAIL_FROM_NAME` | `Veytrix — Enterprise CRM` | From name |
| `AUTH_PASSWORD_RESET_EXPIRE` | `3` | Password reset link expires in 3 min |

> [!TIP]
> If you need a fresh `APP_KEY`, you can generate one locally by running:
> ```bash
> php artisan key:generate --show
> ```

---

## 5️⃣ Step 5: Deploy & Verify

1. Click **Save Changes** / **Manual Deploy** -> **Deploy latest commit**.
2. Watch the deployment logs in the Render terminal. You will see:
   - Docker building PHP 8.2 Alpine with `mongodb`, `gd`, `zip`, and `nginx`.
   - Composer installing production dependencies without dev packages.
   - PHP artisan caching config, routes, and views.
   - Nginx and PHP-FPM starting successfully.
3. Once the deployment shows **Live**, visit your Render URL (e.g. `https://veytrix-crm.onrender.com`).
4. **Verification Checklist**:
   - [ ] Sign In page loads with the circular Veytrix logo badge.
   - [ ] Log in with your admin credentials.
   - [ ] Open the Dashboard and verify the upper 8 KPI cards and multi-panel charts.
   - [ ] Test the public registration flow at `/register` (assigns `customer` role).
   - [ ] Test the Admin promotion flow at `/users` to assign team roles.
   - [ ] Test a password reset email and verify the 3-minute expiration notice.
