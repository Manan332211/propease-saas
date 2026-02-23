# PropEase - Multi-Tenant Property Management SaaS

A comprehensive, headless Property Management System (PMS) designed to handle multi-landlord real estate portfolios. Built with a Laravel 11 API backend and a decoupled React (TypeScript) frontend.

## 🚀 The Architecture

This project demonstrates modern, decoupled SaaS architecture, specifically utilizing **Single-Database Multi-Tenancy**. 

Instead of spinning up separate databases for every landlord (which is expensive and hard to maintain), the system uses Eloquent Global Scopes to strictly isolate data. Landlords can only interact with their own properties, units, and tenants, ensuring strict data privacy within a single, scalable schema.

## 💻 Tech Stack

* **Backend:** Laravel 11, MySQL
* **Admin Panel:** FilamentPHP v3 (TALL Stack)
* **Frontend (Tenant Portal):** React, TypeScript, Vite, Tailwind CSS v4
* **API Security:** Laravel Sanctum (Token-based Authentication)

## ✨ Key Features & Business Logic

### 1. Landlord Admin Dashboard (Filament)
* **Property & Unit Management:** Track buildings and individual apartments with dynamic vacancy status tracking.
* **Lease Contracts:** Map tenants to units with strict date validation (start/end dates) and file uploads for signed PDF contracts.
* **Financial Tracking:** Real-time dashboard widgets calculating active revenue, total properties, and current vacancy rates.

### 2. Secure API Engine
* RESTful JSON API endpoints secured via Laravel Sanctum.
* Eager-loaded, constrained queries to ensure lightning-fast response times without N+1 query problems.

### 3. React Tenant Portal
* A decoupled frontend where tenants can view their active lease details.
* Strictly typed API responses using TypeScript interfaces.
* Modern, responsive UI built with Tailwind CSS.

## 🧠 Technical Highlights for Code Reviewers
* **`BelongsToOwner` Trait:** A custom Eloquent trait that automatically appends the `owner_id` on creation and applies a Global Scope on all queries, guaranteeing data isolation at the ORM level.
* **Dynamic Status Badges:** The UI automatically calculates if a lease is "Active" or "Expired" based on real-time date comparisons against the database without storing redundant status columns.
* **CORS & Preflight Handling:** Fully configured to allow secure, cross-origin requests from the standalone React application.