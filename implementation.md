# JobNode Implementation Guide

This document outlines the phased, commit-based development plan for JobNode. The project utilizes a Laravel 11 + Vue 3 (Inertia.js) monolith architecture, stylized with Tailwind CSS based on our Sauce Labs-inspired design system.

---

## Phase 1: Infrastructure & Scaffolding
**Goal:** Establish the local environment, initialize the framework, and configure the base design tokens.

* [ ] **Commit 1.1: Initialize Project & Authentication**
  * Spin up the new application using (`laravel new jobnode`).
  * Install Laravel Breeze and select the Vue + Inertia stack.
  * Verify the local MySQL database connects.
* [ ] **Commit 1.2: Design System Configuration**
  * Update `tailwind.config.js` to include the specific `design.md` tokens:
    * Colors: Deep Charcoal (`#132322`), Emerald (`#3DDC91`), Sky Blue (`#007AFF`).
    * Fonts: `AeonikFono` and `Aeonik` (add fallbacks).
  * Configure custom spacing and border-radius scales matching the design spec.
* [ ] **Commit 1.3: Base UI Components**
  * Create reusable Vue components in `resources/js/Components/`:
    * `PrimaryButton.vue` (Emerald, 32px radius)
    * `HeroInput.vue` (52px text, bottom border only)
    * `DarkCard.vue` (Deep Charcoal background, subtle emerald border)

---

## Phase 2: Database Architecture & Eloquent Models
**Goal:** Translate the MVP schema into Laravel migrations and wire up the Eloquent ORM relationships.

* [ ] **Commit 2.1: Core User Migrations**
  * Update the default `users` migration to include the `role` enum.
  * Create migrations and models for `companies` and `candidate_profiles`.
* [ ] **Commit 2.2: Job & Application Migrations**
  * Create migrations and models for `jobs` (include analytics columns like `views_count`).
  * Create migrations and models for the `applications` pivot table.
* [ ] **Commit 2.3: Monetization & Comments Migrations**
  * Create migrations and models for `employer_candidate_unlocks`.
  * Create migrations and models for `job_comments`.
* [ ] **Commit 2.4: Model Relationships**
  * Define all Eloquent relationships (e.g., `hasMany`, `belongsTo`, `belongsToMany`) inside the newly created models.
  * Add `$fillable` arrays and cast JSON columns (like `skills` and `technologies`) to arrays.

---

## Phase 3: Access Control & Dashboards
**Goal:** Secure the routes and direct users to their respective interfaces based on their role.

* [ ] **Commit 3.1: Role Middleware**
  * Create custom Laravel middleware (`CheckRole`) to protect routes based on the user's `role` (Admin, Employer, Candidate).
  * Register the middleware in `bootstrap/app.php`.
* [ ] **Commit 3.2: Routing Architecture**
  * Group `web.php` routes under role-specific prefixes (e.g., `/employer/...`, `/candidate/...`).
* [ ] **Commit 3.3: Dashboard Scaffolding**
  * Create placeholder Vue pages for `EmployerDashboard.vue`, `CandidateDashboard.vue`, and `AdminDashboard.vue`.
  * Update the `AuthenticatedLayout.vue` to dynamically render navigation links based on the authenticated user's role.

---

## Phase 4: Employer Core Flows
**Goal:** Allow employers to create and manage job listings.

* [ ] **Commit 4.1: Job Creation Flow**
  * Build the `JobCreate.vue` form utilizing the Inertia `useForm` helper.
  * Implement the `JobController@store` method with strict request validation.
  * *Note: Set default status to 'pending' upon creation.*
* [ ] **Commit 4.2: Employer Listing Management**
  * Build the `EmployerJobsIndex.vue` to list all jobs owned by the authenticated employer.
  * Implement the `JobController@edit` and `@update` methods.
* [ ] **Commit 4.3: Job Status Updates (Comments)**
  * Implement the backend logic and Vue UI allowing employers to post one-way updates to the `job_comments` table.

---

## Phase 5: Candidate Core Flows
**Goal:** Build the public job board, search functionality, and the application submission process.

* [ ] **Commit 5.1: The Public Job Board**
  * Build the `JobIndex.vue` page displaying approved jobs.
  * Implement backend query scopes in the `Job` model to filter by keywords, location, category, and active status.
  * Wire up Vue watchers with a debounce function to dynamically trigger Inertia re-loads for real-time filtering.
* [ ] **Commit 5.2: Job Details & Analytics**
  * Build `JobShow.vue`.
  * Implement the logic in `JobController@show` to increment the `views_count` automatically.
* [ ] **Commit 5.3: Application Submission & Limits**
  * Build the application submission backend logic.
  * Implement the 12-application cap restriction: check `applications_count` before saving.
  * Dynamically disable the "Apply" button on the Vue frontend if the cap is reached.

---

## Phase 6: The Monetization Engine (Stripe)
**Goal:** Implement the hard gate that requires employers to pay to access candidate contact info.

* [ ] **Commit 6.1: Laravel Cashier Setup**
  * Install `laravel/cashier`.
  * Configure `.env` with Stripe API keys and run Cashier migrations.
* [ ] **Commit 6.2: The Triage View (Free)**
  * Build the employer-facing `ApplicationReview.vue` screen.
  * Ensure the backend explicitly strips/hides `phone` and `resume_path` from the JSON response for this view.
* [ ] **Commit 6.3: The Checkout Flow**
  * Implement the Stripe checkout session in the controller.
  * Create success/cancel webhooks or callback routes.
* [ ] **Commit 6.4: The Data Unlock**
  * Upon successful payment, insert a record into `employer_candidate_unlocks`.
  * Update the application review query to conditionally include the candidate's phone and resume file if an unlock record exists.

---

## Phase 7: Admin Moderation (Final Polish)
**Goal:** Give administrators the tools to maintain platform health.

* [ ] **Commit 7.1: Job Approval Queue**
  * Build an Admin view that lists all jobs with a `pending` status.
  * Implement toggle logic to switch status to `approved` or `rejected`.
* [ ] **Commit 7.2: Content Moderation**
  * Build the Admin view and logic to delete inappropriate entries from the `job_comments` table.