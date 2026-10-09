# CLAUDE.md — Quick Context & Instructions for AI Agents

> **Project**: LensStore (Camera Lenses E-Commerce & Mini-ERP Platform)  
> **Framework**: Laravel 13 (PHP 8.3+) | Tailwind CSS v4 | Vite 8 | Spatie Permission  
> **Database**: MySQL (`lar_demo`) / SQLite  

---

## ⚡ Primary Context & Single Source of Truth
This repository uses a comprehensive AI Agent context suite. For full details on architecture, database schema, coding rules, and domain state machines, always read:
- **Primary Agent Guide**: [`AGENTS.md`](file:///c:/xampp/htdocs/lar_demo/AGENTS.md)
- **Architecture & DB Schema**: [`.agents/rules/architecture.md`](file:///c:/xampp/htdocs/lar_demo/.agents/rules/architecture.md)
- **Coding Standards & Rules**: [`.agents/rules/coding-standards.md`](file:///c:/xampp/htdocs/lar_demo/.agents/rules/coding-standards.md)
- **Domain Workflows (Stock, GHN, MoMo, RBAC)**: [`.agents/rules/domain-workflows.md`](file:///c:/xampp/htdocs/lar_demo/.agents/rules/domain-workflows.md)

---

## 🛠️ Common Commands

```powershell
# Development Servers
php artisan serve
npm run dev

# Asset Compilation
npm run build

# Database Operations
php artisan migrate
php artisan db:seed --class=LensDataSeeder

# Testing & Linting
php artisan test
vendor/bin/pint

# Clear Cache
php artisan optimize:clear
```

---

## ⚠️ Golden Rules for Any Code Modification

1. **Inventory State Machine Integrity**:
   - Never directly decrement `Product.stock` when completing orders. Orders hold quantity in `Product.reserved_stock` upon placement.
   - On completion (`completed`/`finished`), deduct `reserved_stock` and automatically generate a `GoodsIssue` (type: `sale`) and an `InventoryTransaction`.
   - On cancellation (`cancelled`), return quantity from `reserved_stock` back to `stock`.

2. **Mandatory DB Transactions**:
   - Wrap any multi-step writes (orders, goods receipts, QC inspections, cancellations) in `DB::beginTransaction()` and `DB::rollBack()` inside `try-catch`.

3. **Modern Laravel 13 Style**:
   - Use PHP 8 Attributes on models: `#[Fillable([...])]`, `#[Hidden([...])]`.
   - Use `protected function casts(): array` instead of `$casts` property.
   - Eloquent relations must have explicit return types (`: BelongsTo`, `: HasMany`, `: MorphTo`).

4. **Blade & Design Tokens**:
   - Inherit master layouts: `@extends('layouts.app')` (storefront), `@extends('layouts.admin')` (admin), `@extends('layouts.auth')` (auth).
   - Use existing CSS variables (`--primary`, `--surface`, `--line`, etc.) rather than arbitrary inline styles.
   - Always include `@csrf` and `@method(...)` on mutating forms.
