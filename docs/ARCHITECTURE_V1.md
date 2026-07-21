# GoldPlatform Architecture V1

Version: 1.0

Date: 2026-07-20

Author: GoldPlatform Team

---

# Vision

GoldPlatform is not a custom project.

GoldPlatform is a commercial SaaS platform for Gold & Jewelry Stores.

The platform must support hundreds of stores with different subscription plans.

---

# Product Goals

• Integration with Kimia Accounting

• Online Buy / Sell Gold

• Wallet

• Customer Management

• Identity Verification (Jibit)

• SMS Authentication

• Admin Panel

• Reports

• API

---

# Domains

Authentication

Users

Accounts

Wallet

Trading

Orders

Kimia

Jibit

SMS

Notification

Admin

Licensing

---

# Layers

Presentation Layer

↓

Controllers

↓

Services

↓

Repositories

↓

Models

↓

Database

---

# External Providers

Kimia

SMS.ir

Jibit

Payment Gateway

Redis

Queue

---

# Coding Standards

Controllers

Only receive Request

Never contain Business Logic

Return ApiResponse

---

Services

Business Logic only

Never access Request directly

---

Repositories

Database only

---

Providers

External APIs only

---

Models

Relationships

Scopes

Casts

Fillables

---

# Subscription Plans

Bronze

Silver

Gold

Enterprise

---

# Feature Based System

Every feature must be enabled or disabled independently.

Examples:

BUY

SELL

REPORTS

API

MULTI_BRANCH

JIBIT

KIMIA

SMS

---

# API Standard

Every endpoint returns

Success

Message

Data

Errors

---

# Authentication

OTP Login

SMS.ir

Sanctum

Jibit Verification

---

# Future

Multi Tenant

Marketplace

Accounting Plugins

Mobile App

White Label

International Version
