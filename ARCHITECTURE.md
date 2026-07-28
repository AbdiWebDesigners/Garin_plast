# Garin Plast ERP

# Architecture Document

Version: 1.0

Author: Garin Plast Technical Team

Last Update: Sprint 2

---

# 1. Architecture

The system follows Layered Architecture.

```
Presentation Layer

↓

Controller Layer

↓

Service Layer

↓

Manager Layer

↓

Data Layer (PDO)

↓

MySQL
```

---

# 2. Folder Structure

```
includes/

core/

ReceiptService.php

IssueService.php

ProductionService.php

SalesService.php

StockManager.php

InventoryManager.php

TransactionManager.php

AverageCost.php

Validation.php

Repository/

(optional in future)
```

---

# 3. Responsibilities

## Controller

Only receives HTTP Request.

No SQL.

No Business Logic.

Example

inventory/receipts/store.php

↓

ReceiptService

---

## Service

Contains Business Logic.

Example

ReceiptService

IssueService

ProductionService

SalesService

---

## Manager

Shared reusable logic.

Example

StockManager

AverageCost

TransactionManager

Validation

---

## Database Layer

PDO only.

No business logic.

---

# 4. Receipt Workflow

```
store.php

↓

ReceiptService::store()

↓

Validation

↓

Save Receipt Header

↓

Save Receipt Items

↓

AverageCost

↓

StockManager

↓

TransactionManager

↓

Commit

↓

Return Success
```

---

# 5. Issue Workflow

```
store.php

↓

IssueService::store()

↓

Validation

↓

Stock Check

↓

Decrease Stock

↓

Transaction

↓

Commit
```

---

# 6. Inventory Engine

Inventory is updated ONLY by

StockManager

No page is allowed to update inventory directly.

---

# 7. Average Cost Engine

AverageCost class

is responsible for

Weighted Average

No SQL duplication.

---

# 8. Transaction Engine

Every stock movement creates one row

inside

inventory_transactions

No Exception.

---

# 9. Coding Rules

Business Logic

❌ Controller

✅ Service

Inventory Logic

❌ Controller

✅ StockManager

SQL

❌ Controller

✅ Service

Validation

❌ Controller

✅ Validation Class

---

# 10. Future Modules

Accounting

CRM

HR

Payroll

Maintenance

Production Planning

Business Intelligence

REST API

Mobile API

Barcode Engine

RFID Engine

Notification Engine
