# GoldPlatform Project State
**Version:** 0.6.0
**Date:** 2026-07-18
**Sprint:** Sprint 6 - Kimia Integration Started
**Status:** Stable ✅

---

# Project Overview

GoldPlatform is an online gold trading platform based on Laravel 12.

Current architecture:

- Laravel 12
- PHP 8.4
- Docker
- Nginx
- MySQL 8.4
- Redis
- Sanctum
- REST API
- Kimia ERP Integration

---

# Infrastructure

## Docker

Containers:

- goldplatform_nginx
- goldplatform_php
- goldplatform_mysql
- goldplatform_redis

Status:

Running ✅

---

# Database

Completed migrations

- users
- cache
- jobs
- personal_access_tokens
- permissions
- user_groups
- wallets
- wallet_transactions
- products
- product_categories
- orders
- order_items
- otps
- sms_logs
- jibit_logs
- kimia_logs

Database Status

Stable ✅

---

# Authentication

Completed

✔ OTP Login

✔ OTP Verification

✔ Sanctum Token Creation

✔ Automatic User Creation

✔ Login Timestamp

✔ SMS Service Layer

✔ AuthService

✔ OtpService

✔ User Repository

Current Flow

Mobile

↓

Generate OTP

↓

Save OTP

↓

Verify OTP

↓

Create User (if not exists)

↓

Issue Sanctum Token

↓

Login Completed

Status

Working ✅

---

# Users Table

Current Fields

- id
- mobile
- name
- national_code
- group_id
- mobile_verified
- is_active
- last_login_at
- email
- email_verified_at
- password
- remember_token
- created_at
- updated_at

Notes

Email and password still exist for Laravel compatibility.

Future cleanup planned.

---

# Kimia Integration

Completed

✔ KimiaService

✔ HTTP Client

✔ Basic Authentication

✔ Environment Configuration

✔ Connection Test Command

Environment

KIMIA_BASE_URL

KIMIA_USERNAME

KIMIA_PASSWORD

KIMIA_TIMEOUT

Connection Test

Command

php artisan kimia:test

Result

HTTP 200

Connection Successful

Kimia API is reachable.

Status

Production Ready ✅

---

# Implemented Services

App\Services

- AuthService
- OtpService
- SmsService
- KimiaService

---

# Implemented Controllers

Api

- AuthController

---

# Current API

POST

/api/auth/login

POST

/api/auth/verify-otp

POST

/api/auth/logout

Status

Working ✅

---

# Git

Repository

https://github.com/1alirezabahramian/GoldPlatform

Branch

main

Last Stable Point

Sprint 6
Kimia Connection Successful

---

# Remaining Sprint 6

Next Target

Kimia Customer Sync

Tasks

- Read customer information
- Sync customer
- Read customer group
- Read Rial balance
- Read Gold balance
- Sync wallet

---

# Upcoming Sprint

Sprint 7

Customer synchronization with Kimia

Features

- Customer lookup
- Wallet synchronization
- Customer group synchronization
- Balance synchronization

---

# Architecture Rules

Business logic

↓

Service Layer

↓

Repository

↓

Database

Controllers must remain thin.

No business logic inside controllers.

---

# Current Project Health

Docker

✅

Laravel

✅

Database

✅

OTP Login

✅

Sanctum

✅

Kimia Connection

✅

Architecture

✅

Ready for Customer Synchronization

🚀