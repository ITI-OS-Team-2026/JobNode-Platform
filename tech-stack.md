### **1. Core Infrastructure & Backend**

| Feature / Requirement | Recommended Tool / Library | Why it fits the MVP |
| --- | --- | --- |
| **Local Environment** | **Laravel Herd** (Windows) + DBngin | Blazing fast, zero-dependency local development for PHP/Laravel. DBngin handles the database seamlessly. |
| **Backend Framework** | **Laravel 11** (PHP 8.x) | The robust foundation for routing, controllers, and business logic. |
| **Database** | **MySQL** | A solid, proven relational database to handle complex queries for job filtering and user relationships. |
| **Authentication** | **Laravel Breeze** (Vue / Inertia stack) | Provides lightweight scaffolding for user registration, login, and password resets. It is much easier to tear down and customize with your exact `design.md` specs than heavier packages like Jetstream. |

### **2. Frontend & Design System**

| Feature / Requirement | Recommended Tool / Library | Why it fits the MVP |
| --- | --- | --- |
| **Frontend Framework** | **Vue 3** (Composition API) | Highly reactive, component-based UI. The Composition API makes logic highly reusable. |
| **Backend/Frontend Bridge** | **Inertia.js** | Allows you to build single-page apps (SPAs) without building an API. You write standard Laravel controllers that return Vue components instead of Blade views. It vastly accelerates MVP development. |
| **Styling & Design System** | **Tailwind CSS** | The absolute best tool to translate your `design.md` into reality. You can configure your `tailwind.config.js` with your exact tokens (Deep Charcoal `#132322`, Emerald `#3DDC91`, Aeonik fonts, and custom spacing scales). |
| **Icons** | **Lucide Icons** (Vue package) | Clean, geometric icons that will match the technical, bold aesthetic of the Sauce Labs-inspired design. |

### **3. Platform Functionality**

| Feature / Requirement | Recommended Tool / Library | Why it fits the MVP |
| --- | --- | --- |
| **Payments (Unlocking Candidates)** | **Laravel Cashier** (Stripe) | An expressive, fluent interface to Stripe's subscription and single-charge billing services. Perfect for the "pay-to-unlock" gateway. |
| **File Storage (Resumes/Logos)** | **Laravel Storage** (Local for dev, AWS S3 for production) | Laravel's unified filesystem abstraction makes it trivial to upload files locally now and switch to an S3 bucket for production later. |
| **Form Handling & Validation** | **Inertia Form Helper** (`useForm`) | Handles data binding, submission, and displaying Laravel's backend validation errors seamlessly in Vue. |
| **Search & Filtering** | **Laravel Eloquent + Vue Watchers** | For the MVP, standard database queries triggered by Vue watchers (with lodash debounce) on your filter inputs will be more than fast enough. No need for heavy search engines like Algolia or Meilisearch yet. |