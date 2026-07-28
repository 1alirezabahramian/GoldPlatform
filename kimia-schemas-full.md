# AccountDto

**Description:** حساب

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `AccountId` | `integer` | `int32` | `True` | `None` |
| `AccountCode` | `integer` | `int32` | `True` | `None` |
| `Name` | `string` | `` | `True` | `None` |
| `NationalCode` | `string` | `` | `True` | `None` |
| `ShopName` | `string` | `` | `True` | `None` |
| `EconomicCode` | `string` | `` | `True` | `None` |
| `Tel` | `string` | `` | `True` | `None` |
| `Mobile` | `string` | `` | `True` | `None` |
| `PostalCode` | `string` | `` | `True` | `None` |
| `Address` | `string` | `` | `True` | `None` |
| `DateBirthday` | `string` | `date-time` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Type` | `integer` | `int32` | `True` | `None` |
| `IsVisible` | `boolean` | `` | `True` | `True` |

---

# AccountGroupDto

**Description:** گروه حساب

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `Id` | `integer` | `int32` | `` | `` |
| `Name` | `string` | `` | `True` | `` |
| `AccountType` | `integer` | `int32` | `True` | `None` |

---

# AdjustmentRequest

**Description:** درخواست کسر/اضافه

**Required:**

- `AccountId`
- `Action`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `RequestId` | `string` | `` | `True` | `None` |
| `AddToExistingDateVoucher` | `boolean` | `` | `` | `False` |
| `AccountId` | `integer` | `int32` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Action` | `integer` | `int32` | `` | `` |
| `TagId` | `integer` | `int32` | `True` | `` |
| `Money` | `number` | `decimal` | `True` | `` |
| `CurrencyId` | `integer` | `int32` | `True` | `` |
| `Weight` | `number` | `decimal` | `True` | `` |
| `Fineness` | `number` | `decimal` | `True` | `` |

---

# BalanceDto

**Description:** مانده حساب

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `AccountId` | `integer` | `int32` | `` | `` |
| `AccountName` | `string` | `` | `True` | `` |
| `GroupId` | `integer` | `int32` | `` | `` |
| `Weight` | `number` | `decimal` | `True` | `` |
| `Money` | `number` | `decimal` | `True` | `` |
| `CurrencyId` | `integer` | `int32` | `` | `` |
| `CurrencySymbol` | `string` | `` | `True` | `None` |
| `GoldPeaks` | `array` | `` | `True` | `` |
| `MoneyPeaks` | `array` | `` | `True` | `` |

---

# BarcodeDto

**Description:** بارکد

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `BarcodeId` | `integer` | `int32` | `` | `` |
| `RFID` | `string` | `` | `True` | `None` |
| `ProductId` | `integer` | `int32` | `` | `` |
| `Name` | `string` | `` | `True` | `None` |
| `Producer` | `string` | `` | `True` | `None` |
| `Fineness` | `number` | `decimal` | `True` | `` |
| `GoldWeight` | `number` | `decimal` | `True` | `` |
| `GemWeight` | `number` | `decimal` | `True` | `` |
| `TotalWeight` | `number` | `decimal` | `` | `` |
| `GemQuantity` | `number` | `decimal` | `True` | `` |
| `GemMoney` | `number` | `decimal` | `True` | `` |
| `UnitPrice` | `number` | `decimal` | `True` | `` |
| `Cent` | `number` | `decimal` | `True` | `` |
| `Comment` | `string` | `` | `True` | `None` |
| `GoldColor` | `string` | `` | `True` | `None` |
| `GemColor` | `string` | `` | `True` | `None` |
| `EnamelColor` | `string` | `` | `True` | `None` |
| `LeatherColor` | `string` | `` | `True` | `None` |
| `Size` | `string` | `` | `True` | `None` |
| `ProductImageUrl` | `string` | `` | `True` | `None` |
| `BarcodeImageUrl` | `string` | `` | `True` | `None` |

---

# CoinDto

**Description:** سکه

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `CoinId` | `integer` | `int32` | `True` | `` |
| `Name` | `string` | `` | `True` | `None` |
| `Fineness` | `number` | `decimal` | `True` | `` |
| `Weight` | `number` | `decimal` | `True` | `` |
| `Type` | `integer` | `int32` | `` | `` |
| `IsVisible` | `boolean` | `` | `True` | `` |

---

# CurrencyDto

**Description:** ارز

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `CurrencyId` | `integer` | `int32` | `True` | `` |
| `Name` | `string` | `` | `True` | `None` |
| `IsVisible` | `boolean` | `` | `True` | `` |

---

# ExchangeCurrencyRequest

**Description:** درخواست پولی کردن ارز یا سکه

**Required:**

- `AccountId`
- `Action`
- `Quantity`
- `SourceId`
- `UnitPrice`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `RequestId` | `string` | `` | `True` | `None` |
| `AddToExistingDateVoucher` | `boolean` | `` | `` | `False` |
| `AccountId` | `integer` | `int32` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Action` | `integer` | `int32` | `` | `` |
| `SourceId` | `integer` | `int32` | `` | `` |
| `TargetId` | `integer` | `int32` | `True` | `` |
| `UnitPrice` | `number` | `decimal` | `` | `` |
| `DivideUnitPrice` | `boolean` | `` | `True` | `` |
| `Quantity` | `number` | `decimal` | `` | `` |
| `GoldPrice` | `number` | `decimal` | `True` | `` |
| `GoldUnit` | `integer` | `int32` | `True` | `` |

---

# ExchangeRequest

**Description:** درخواست پولی کردن

**Required:**

- `AccountId`
- `Action`
- `GoldPrice`
- `Value`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `RequestId` | `string` | `` | `True` | `None` |
| `AddToExistingDateVoucher` | `boolean` | `` | `` | `False` |
| `AccountId` | `integer` | `int32` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Action` | `integer` | `int32` | `` | `` |
| `CurrencyId` | `integer` | `int32` | `True` | `` |
| `GoldPrice` | `number` | `decimal` | `` | `` |
| `GoldUnit` | `integer` | `int32` | `True` | `` |
| `Value` | `number` | `decimal` | `` | `` |

---

# PeakDto

**Description:** رأس

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `Date` | `string` | `date-time` | `True` | `` |
| `Days` | `integer` | `int32` | `True` | `` |
| `Value` | `number` | `decimal` | `` | `` |

---

# ProductDto

**Description:** جنس

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `ProductId` | `integer` | `int32` | `True` | `` |
| `Name` | `string` | `` | `True` | `None` |
| `Fineness` | `number` | `decimal` | `True` | `` |
| `Weight` | `number` | `decimal` | `True` | `` |
| `Type` | `integer` | `int32` | `` | `` |
| `IsVisible` | `boolean` | `` | `True` | `` |

---

# RFIDDto

**Description:** تگ رادیویی

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `Epc` | `string` | `` | `True` | `` |
| `Type` | `integer` | `int32` | `` | `` |
| `BarcodeId` | `integer` | `int32` | `` | `` |
| `Serial` | `integer` | `int32` | `` | `` |
| `Name` | `string` | `` | `True` | `None` |
| `Producer` | `string` | `` | `True` | `None` |
| `Fineness` | `number` | `decimal` | `True` | `` |
| `GoldWeight` | `number` | `decimal` | `True` | `` |
| `GemWeight` | `number` | `decimal` | `True` | `` |
| `TotalWeight` | `number` | `decimal` | `` | `` |
| `GemQuantity` | `number` | `decimal` | `True` | `` |
| `GemMoney` | `number` | `decimal` | `True` | `` |
| `UnitPrice` | `number` | `decimal` | `True` | `` |
| `Cent` | `number` | `decimal` | `True` | `` |
| `ProductImageUrl` | `string` | `` | `True` | `None` |
| `BarcodeImageUrl` | `string` | `` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Status` | `integer` | `int32` | `` | `` |

---

# RecordDto

**Description:** ردیف سند

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `AccountId` | `integer` | `int32` | `` | `` |
| `AccountCode` | `integer` | `int32` | `` | `` |
| `AccountName` | `string` | `` | `True` | `None` |
| `VoucherId` | `integer` | `int32` | `` | `` |
| `VoucherNumber` | `integer` | `int32` | `` | `` |
| `RecordId` | `integer` | `int32` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `` |
| `Action` | `integer` | `int32` | `True` | `` |
| `ActionName` | `string` | `` | `True` | `None` |
| `ProductId` | `integer` | `int32` | `True` | `` |
| `ProductName` | `string` | `` | `True` | `None` |
| `Description` | `string` | `` | `True` | `None` |
| `Weight` | `number` | `decimal` | `True` | `` |
| `Fineness` | `number` | `decimal` | `True` | `` |
| `UnitPrice` | `number` | `decimal` | `True` | `` |
| `Cent` | `number` | `decimal` | `True` | `` |
| `GoldUnit` | `integer` | `int32` | `True` | `` |
| `GoldUnitName` | `string` | `` | `True` | `None` |
| `GoldPrice` | `number` | `decimal` | `True` | `` |
| `Quantity` | `number` | `decimal` | `True` | `` |
| `Weight750` | `number` | `decimal` | `` | `` |
| `SumMoney` | `number` | `decimal` | `` | `` |
| `CurrencyId` | `integer` | `int32` | `` | `` |
| `CurrencySymbol` | `string` | `` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `CumulativeWeight750` | `number` | `decimal` | `True` | `` |
| `CumulativeSumMoney` | `number` | `decimal` | `True` | `` |
| `RelatedRecord` | `RecordDto` | `` | `` | `` |

---

# RecordDtoPagedList

**Description:** لیست صفحه بندی

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `FromDate` | `string` | `date-time` | `True` | `` |
| `ToDate` | `string` | `date-time` | `True` | `` |
| `TotalCount` | `integer` | `int32` | `` | `` |
| `TotalPages` | `integer` | `int32` | `` | `` |
| `PageSize` | `integer` | `int32` | `` | `` |
| `PageNumber` | `integer` | `int32` | `` | `` |
| `Items` | `array` | `` | `True` | `` |

---

# TradeBarcodeRequest

**Description:** درخواست معامله بارکد

**Required:**

- `AccountId`
- `Action`
- `BarcodeIds`
- `GoldPrice`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `RequestId` | `string` | `` | `True` | `None` |
| `AddToExistingDateVoucher` | `boolean` | `` | `` | `False` |
| `AccountId` | `integer` | `int32` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Action` | `integer` | `int32` | `` | `` |
| `BarcodeIds` | `array` | `` | `` | `` |
| `GoldPrice` | `number` | `decimal` | `` | `` |
| `GoldUnit` | `integer` | `int32` | `True` | `` |

---

# TradeCashRequest

**Description:** درخواست مبادله نقدی

**Required:**

- `AccountId`
- `Action`
- `Value`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `RequestId` | `string` | `` | `True` | `None` |
| `AddToExistingDateVoucher` | `boolean` | `` | `` | `False` |
| `AccountId` | `integer` | `int32` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Action` | `integer` | `int32` | `` | `` |
| `Value` | `number` | `decimal` | `` | `` |

---

# TradeCurrencyRequest

**Description:** درخواست مبادله ارز یا سکه

**Required:**

- `AccountId`
- `Action`
- `TargetId`
- `Value`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `RequestId` | `string` | `` | `True` | `None` |
| `AddToExistingDateVoucher` | `boolean` | `` | `` | `False` |
| `AccountId` | `integer` | `int32` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Action` | `integer` | `int32` | `` | `` |
| `TargetId` | `integer` | `int32` | `` | `` |
| `Value` | `number` | `decimal` | `` | `` |

---

# TransferRequest

**Description:** درخواست حواله

**Required:**

- `AccountId`
- `Action`
- `TargetId`
- `Value`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `RequestId` | `string` | `` | `True` | `None` |
| `AddToExistingDateVoucher` | `boolean` | `` | `` | `False` |
| `AccountId` | `integer` | `int32` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `None` |
| `Comment` | `string` | `` | `True` | `None` |
| `Action` | `integer` | `int32` | `` | `` |
| `TargetId` | `integer` | `int32` | `` | `` |
| `Value` | `number` | `decimal` | `` | `` |
| `CurrencyId` | `integer` | `int32` | `True` | `` |

---

# VoucherDto

**Description:** شناسه سند

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `Voucher` | `integer` | `int32` | `` | `` |
| `Records` | `array` | `` | `True` | `` |

---

# WalletDto

**Description:** شماره حساب

**Required:**

- `Amount`
- `Bank`
- `Name`
- `Number`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `AccountId` | `integer` | `int32` | `True` | `` |
| `Tag` | `string` | `` | `True` | `None` |
| `Bank` | `string` | `` | `` | `None` |
| `Number` | `string` | `` | `` | `None` |
| `Name` | `string` | `` | `` | `None` |
| `Amount` | `number` | `decimal` | `` | `` |
| `Comment` | `string` | `` | `True` | `None` |
| `Id` | `string` | `uuid` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `` |
| `RemainAmount` | `number` | `decimal` | `True` | `` |
| `PayedAmount` | `number` | `decimal` | `True` | `` |
| `Items` | `array` | `` | `True` | `` |

---

# WalletPaymentRequest

**Description:** درخواست پرداخت شماره حساب

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `Id` | `string` | `uuid` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `` |
| `AccountId` | `integer` | `int32` | `True` | `` |
| `Amount` | `number` | `decimal` | `` | `` |
| `RemainAmount` | `number` | `decimal` | `True` | `` |
| `PayedAmount` | `number` | `decimal` | `True` | `` |
| `Comment` | `string` | `` | `True` | `None` |
| `Items` | `array` | `` | `True` | `` |

---

# WalletPaymentTransferRequest

**Description:** درخواست حواله شماره حساب

**Required:**

- `Amount`
- `PaymentId`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `PaymentId` | `string` | `uuid` | `` | `` |
| `RecordId` | `integer` | `int32` | `True` | `` |
| `Amount` | `number` | `decimal` | `` | `` |
| `Date` | `string` | `date-time` | `True` | `` |
| `Comment` | `string` | `` | `True` | `None` |

---

# WalletRequest

**Description:** درخواست شماره حساب

**Required:**

- `Amount`
- `Bank`
- `Name`
- `Number`

| Field | Type | Format | Nullable | Default |
|---|---|---|---|---|
| `AccountId` | `integer` | `int32` | `True` | `` |
| `Tag` | `string` | `` | `True` | `None` |
| `Bank` | `string` | `` | `` | `None` |
| `Number` | `string` | `` | `` | `None` |
| `Name` | `string` | `` | `` | `None` |
| `Amount` | `number` | `decimal` | `` | `` |
| `Comment` | `string` | `` | `True` | `None` |

---

