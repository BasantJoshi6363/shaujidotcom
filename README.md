# Shauji.com.np — Digital Khata (Credit Ledger) App
### Project Documentation — Beta Version

---

## 1. Overview

**Shauji** is a web/mobile platform that lets shopkeepers ("sellers") digitize the traditional *khata* (credit notebook) system. Instead of writing customer credit/debit entries on paper, a seller registers, adds customers, records what they've bought on credit, and tracks balances — with automatic notifications to customers via **Email and WhatsApp** whenever their account changes.

**Core value proposition:** Replace the paper credit ledger with a transparent, auditable, notification-driven digital system for local shops (kirana stores, etc.).

---

## 2. Goals & Objectives

- Give sellers a simple way to record customer purchases made on credit.
- Automatically notify customers (mail + WhatsApp) on every create/update to their ledger, so there's no dispute over "he said/she said" balances.
- Give sellers a dashboard summarizing their business: total customers, total credit outstanding, and a sortable list of who owes the most/least.
- Support seller payment collection (via QR code / eSewa-Fonepay-style details) so customers can pay back directly.

---

## 3. User Roles

| Role | Description |
|---|---|
| **Seller (Shopkeeper)** | Registers a shop, manages customers, creates/updates/deletes credit entries, views dashboard. |
| **Customer** | Receives notifications about their credit history; may have limited/no direct login in the beta (read-only via notification link, TBD). |

---

## 4. Data Models

### 4.1 Seller Model
| Field | Notes |
|---|---|
| `sellerId` | Primary key |
| `becomeSellerFlag` | Marks a user as upgraded to seller |
| `sellerName` | |
| `sellerPanId` | PAN/tax ID for business verification |
| `sellerAddress` | |
| `sellerShopName` | |
| `sellerShopLocation` | Ideally lat/long + text address for maps |
| `sellerGmailAddress` | Used for auth + notifications |
| `sellerPhoneNo` | Used for WhatsApp notifications |
| `password` | Hashed |
| `paymentDetails[]` | Array of payment methods — bank details + QR code, Fonepay/eSewa details, etc. (supports multiple accounts) |

### 4.2 Customer Model
| Field | Notes |
|---|---|
| `customerId` | Primary key |
| `customerName` | |
| `customerPhone` | Used for WhatsApp notifications |
| `customerEmail` | Used for email notifications |
| `customerAddress` | |
| `creditDebitHistory[]` | List of transactions: `{ amount, type: credit/debit, date, note, goodsPurchased[] }` |
| `sellerId` (FK) | Which seller this customer belongs to |
| `currentBalance` | Derived/cached from history for fast dashboard queries |

> **Relationship:** One Seller → Many Customers. Each customer's credit history is scoped to the seller who created them (a customer could theoretically appear under multiple shops with separate balances).

---

## 5. Core Functionality

### 5.1 Notification System
- **Trigger:** Every time a customer record is **created** or **updated** (new credit entry, payment received, edit).
- **Channels:** Email **and** WhatsApp simultaneously — not either/or.
- **Rationale:** Removes ambiguity/disputes about ledger changes; customer always has a real-time paper trail.
- **Implementation note (suggested):** Queue-based notification (e.g., a background job) so ledger updates aren't blocked waiting on WhatsApp/Email API latency. Retry with backoff if a channel fails.

### 5.2 Seller Authentication
- Email/password login.
- **Social login:** Google and Facebook auth.
- Forgot password / reset password flow.

### 5.3 Seller — Customer Management (CRUD)
- **Create customer** — add new customer + initial credit entry.
- **Update customer** — two sub-cases:
  - Record a **payment received** (reduces balance).
  - **Edit** existing customer info or past entry.
- **Delete customer**.

### 5.4 Welcome / Landing Flow
```
Seller ──► Users (store) ──► User Info Panel
                             • user info
                             • list of goods bought
                             • total amount with dates
                             • create another entry
                             • view all entry history
```
This is essentially the **customer detail view** a seller sees after selecting a customer — a running statement of account.

---

## 6. Dashboard

The seller dashboard shows, at a glance:

| Widget | Description |
|---|---|
| **Total Customers** | Count of all customers under this seller |
| **Total Credit Amount** | Sum of all outstanding balances across customers |
| **Credit User List** | List of customers with balances, **sortable** by highest/lowest credit amount — helps sellers prioritize who to follow up with |

**Suggested additions:**
- Filter by date range (e.g., "credit given this month").
- Overdue indicator (e.g., balance unpaid > 30 days, flagged in red).
- Export ledger to PDF/Excel per customer (useful for accounting/tax).
- Search bar for quick customer lookup.

---

## 7. Site Map / Routes

### 7.1 Public Routes (unauthenticated)
- `/` — Home page
- `/welcome` — Welcome + "Download our app" prompt
- `/login`, `/register`
- `/forgot-password`, `/reset-password`
- `/about`
- `/contact`
- `/services`

### 7.2 Seller Routes (authenticated)
- `/seller/customers/create` — Create customer
- `/seller/customers/:id/update` — Update customer (payment received / edit)
- `/seller/customers/:id/delete` — Delete customer
- `/seller/dashboard` — Dashboard (totals + credit list)
- `/seller/customers/:id` — Customer detail / full history view

### 7.3 Suggested Additional Routes
- `/seller/profile` — Edit shop/seller info, manage payment/QR details
- `/seller/notifications/settings` — Choose notification preferences per customer (email only / WhatsApp only / both)
- `/customer/statement/:token` — A tokenized, no-login link sent via notification so customers can view their own statement without a full account

---

## 8. Platform

- **Target:** Both mobile app and web (`app banaunu paryo main / web` — app is the priority, web as companion).
- Suggested stack (not yet decided in the notes — proposal only):
  - **Frontend:** React Native (mobile) + React/Next.js (web)
  - **Backend:** Node.js/Express or similar REST API
  - **Database:** PostgreSQL/MongoDB (given nested `creditDebitHistory` arrays, MongoDB may fit naturally, but PostgreSQL with JSONB works too)
  - **Notifications:** Email via SendGrid/SES; WhatsApp via WhatsApp Business API (Meta) or Twilio
  - **Auth:** Firebase Auth or custom JWT + OAuth (Google/Facebook)
  - **Payments:** eSewa/Fonepay/Khalti QR integration for Nepal market

---

## 9. Open Questions / Gaps to Resolve

1. **Does the customer get their own login**, or are they purely notification recipients (view-only via link)? Your notes suggest the latter but this should be confirmed.
2. **Multi-shop customers:** if the same person buys on credit from two different Shauji sellers, are they one global customer record or separate per-seller records?
3. **Currency/rounding rules** for the total credit amount calculations.
4. **What happens on partial payment?** Does the system auto-close entries or keep a running balance only?
5. **Data privacy:** Since PAN IDs, phone numbers, and bank/QR details are stored, plan encryption-at-rest and access control policy.
6. **Notification opt-out:** Should customers be able to mute WhatsApp/email notifications, or is it mandatory for the trust model to work?

---

## 10. Suggested Roadmap

| Phase | Scope |
|---|---|
| **Beta (current)** | Seller auth, customer CRUD, dashboard, dual-channel notifications |
| **v1** | Customer-facing statement view, payment collection via QR, overdue alerts |
| **v1.1** | Multi-payment-method support, PDF export, search/filter |
| **v2** | Analytics (top customers, monthly trends), multi-branch/staff accounts per seller, SMS fallback if WhatsApp fails |

---

*Document generated from project whiteboard notes — beta version scope. Update as decisions are finalized.*