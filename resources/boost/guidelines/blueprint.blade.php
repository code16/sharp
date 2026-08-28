# Sharp Blueprint

A Sharp Blueprint is a detailed implementation plan that helps AI agents write correct Sharp code. It bridges the gap between requirements and implementation by providing a structured specification for Sharp's unique patterns.

## Core Philosophy

Sharp separates the data structure from its visual representation. A blueprint should clearly map domain models to Sharp components:
- **Entity Lists**: The primary data table view
- **Forms**: Data entry and validation
- **Show Pages**: Detailed read-only views
- **Commands**: Business logic triggers (instance, entity, wizard)
- **State Handlers**: Lifecycle management
- **Filters**: Data filtering for entity lists

## Blueprint Structure

When asking an AI to create a blueprint, it should produce:
1. **Overview**: Brief description of the system and key decisions
2. **User Flows**: Step-by-step navigation through features
3. **Commands**: Required Artisan commands for scaffolding
4. **Models**: Detailed attributes, relationships, casts, and methods
5. **Sharp Resources**: Specific configurations for Lists, Forms, Shows, Commands, and State Handlers

---

# SaaS Invoicing System - Sharp Implementation Plan

## Overview

A single-tenant admin panel for managing customers, products, invoices with line items, and tracking payments. All authenticated users can access all data.

**Key decisions:**
- Single-tenant (all authenticated users see all data)
- Manual invoice sending (mark as sent, no email automation)
- Partial payments supported (multiple payments until balance = 0)
- In-app Sharp notifications only

---

## User Flows

### Flow 1: Creating an Invoice
1. User navigates to Invoices → Create
2. User selects a Customer (autocomplete field with search)
3. User sets invoice date and due date
4. User adds line items via List field:
    - Select Product (autocomplete, shows name + price)
    - Enter quantity
    - Line total auto-calculates (quantity × unit_price)
5. Tax rate is entered (percentage)
6. Subtotal, tax amount, and total auto-calculate
7. Invoice saves as Draft status with auto-generated invoice number

### Flow 2: Sending an Invoice
1. User views a Draft invoice in Show Page
2. User clicks "Mark as Sent" (Instance Command)
3. Confirmation modal appears
4. On confirm: status → Sent, sent_at → now()
5. Success notification shown

### Flow 3: Recording a Payment
1. User views a Sent invoice (or Overdue)
2. User clicks "Record Payment" (Instance Command)
3. Modal form appears with:
    - Amount (defaults to balance due, validates ≤ balance)
    - Payment method (select)
    - Reference (optional)
    - Payment date
4. On submit: Payment record created
5. If total payments ≥ invoice total: status → Paid, paid_at → now()
6. Success notification shown

### Flow 4: Viewing Invoice Status
1. User navigates to Invoices list
2. Table shows status badges (Draft/Sent/Paid/Overdue/Cancelled)
3. User can filter by status, customer, date range
4. User can search by invoice number or customer name

---

## Commands

Run these in order:

```bash
# 1. Create models with migrations and factories
php artisan make:model Customer -mf --no-interaction
php artisan make:model Product -mf --no-interaction
php artisan make:model Invoice -mf --no-interaction
php artisan make:model InvoiceItem -mf --no-interaction
php artisan make:model Payment -mf --no-interaction

# 2. Create enums
php artisan make:enum InvoiceStatus
php artisan make:enum PaymentMethod

# 3. Create Sharp resources
php artisan sharp:make:entity-list InvoiceList --model=Invoice
php artisan sharp:make:form InvoiceForm --model=Invoice
php artisan sharp:make:show InvoiceShow --model=Invoice
php artisan sharp:make:entity-list CustomerList --model=Customer
php artisan sharp:make:form CustomerForm --model=Customer
php artisan sharp:make:show CustomerShow --model=Customer
php artisan sharp:make:entity-list ProductList --model=Product
php artisan sharp:make:form ProductForm --model=Product

# 4. Create state handler
php artisan sharp:make:state-handler InvoiceStateHandler

# 5. Create commands
php artisan sharp:make:instance-command SendInvoiceCommand
php artisan sharp:make:instance-command RecordPaymentCommand

# 6. Create filters
php artisan sharp:make:entity-list-filter InvoiceStatusFilter
php artisan sharp:make:entity-list-filter CustomerFilter
php artisan sharp:make:entity-list-filter InvoiceDateRangeFilter
```

---

## Models

### Enum: InvoiceStatus

```
Enum: InvoiceStatus
  Location: App\Enums\InvoiceStatus
  Type: string backed enum
  Cases:
    - Draft = 'draft'
    - Sent = 'sent'
    - Paid = 'paid'
    - Overdue = 'overdue'
    - Cancelled = 'cancelled'
  Methods:
    - label(): string (Draft, Sent, Paid, Overdue, Cancelled)
    - color(): string (gray, blue, green, red, orange)
```

### Enum: PaymentMethod

```
Enum: PaymentMethod
  Location: App\Enums\PaymentMethod
  Type: string backed enum
  Cases:
    - Cash = 'cash'
    - Check = 'check'
    - BankTransfer = 'bank_transfer'
    - CreditCard = 'credit_card'
    - Other = 'other'
  Methods:
    - label(): string
```

### Model: Customer

```
Model: Customer
  Table: customers
  Attributes:
    - id: bigint, primary
    - name: string, required
    - email: string, required
    - phone: string, nullable
    - address_line_1: string, nullable
    - address_line_2: string, nullable
    - city: string, nullable
    - state: string, nullable
    - postal_code: string, nullable
    - country: string, nullable
    - notes: text, nullable
    - created_at: timestamp
    - updated_at: timestamp
    - deleted_at: timestamp, nullable
  Relationships:
    - hasMany: Invoice via customer_id
  Traits:
    - SoftDeletes
```

### Model: Product

```
Model: Product
  Table: products
  Attributes:
    - id: bigint, primary
    - name: string, required
    - sku: string, nullable, unique
    - description: text, nullable
    - unit_price: integer, required (stored in cents)
    - is_active: boolean, default:true
    - created_at: timestamp
    - updated_at: timestamp
    - deleted_at: timestamp, nullable
  Relationships:
    - hasMany: InvoiceItem via product_id
  Traits:
    - SoftDeletes
  Casts:
    - unit_price: integer
    - is_active: boolean
```

### Model: Invoice

```
Model: Invoice
  Table: invoices
  Attributes:
    - id: bigint, primary
    - customer_id: bigint, foreign(customers.id), required
    - invoice_number: string, required, unique
    - status: string, default:'draft' (uses InvoiceStatus enum)
    - invoice_date: date, required
    - due_date: date, required
    - subtotal: integer, default:0 (cents)
    - tax_rate: decimal(5,2), default:0
    - tax_amount: integer, default:0 (cents)
    - total: integer, default:0 (cents)
    - amount_paid: integer, default:0 (cents)
    - notes: text, nullable
    - sent_at: timestamp, nullable
    - paid_at: timestamp, nullable
    - created_at: timestamp
    - updated_at: timestamp
    - deleted_at: timestamp, nullable
  Relationships:
    - belongsTo: Customer via customer_id
    - hasMany: InvoiceItem via invoice_id
    - hasMany: Payment via invoice_id
  Traits:
    - SoftDeletes
  Casts:
    - status: InvoiceStatus::class
    - invoice_date: date
    - due_date: date
    - tax_rate: decimal:2
    - sent_at: datetime
    - paid_at: datetime
  Accessors:
    - balance_due: int (total - amount_paid)
  Methods:
    - generateInvoiceNumber(): string (format: INV-YYYYMM-XXXX)
    - recalculateTotals(): void (sum line items, apply tax)
    - markAsSent(): void
    - markAsPaid(): void
    - recordPayment(int $amount, PaymentMethod $method, ?string $reference, Carbon $date): Payment
```

### Model: InvoiceItem

```
Model: InvoiceItem
  Table: invoice_items
  Attributes:
    - id: bigint, primary
    - invoice_id: bigint, foreign(invoices.id), required, onDelete:cascade
    - product_id: bigint, foreign(products.id), nullable
    - description: string, required
    - quantity: integer, required, default:1
    - unit_price: integer, required (cents)
    - total: integer, required (cents, quantity × unit_price)
    - sort_order: integer, default:0
    - created_at: timestamp
    - updated_at: timestamp
  Relationships:
    - belongsTo: Invoice via invoice_id
    - belongsTo: Product via product_id (nullable)
  Casts:
    - quantity: integer
    - unit_price: integer
    - total: integer
    - sort_order: integer
```

### Model: Payment

```
Model: Payment
  Table: payments
  Attributes:
    - id: bigint, primary
    - invoice_id: bigint, foreign(invoices.id), required, onDelete:cascade
    - amount: integer, required (cents)
    - method: string, required (uses PaymentMethod enum)
    - reference: string, nullable
    - payment_date: date, required
    - notes: text, nullable
    - created_at: timestamp
    - updated_at: timestamp
  Relationships:
    - belongsTo: Invoice via invoice_id
  Casts:
    - method: PaymentMethod::class
    - amount: integer
    - payment_date: date
```

---

## Sharp Resources

### CustomerList

@verbatim
```
Resource: CustomerList
  Location: App\Sharp\Customers\CustomerList
  Extends: Code16\Sharp\EntityList\SharpEntityList
  Docs: https://sharp.code16.fr/docs/guide/building-entity-list

  Method: buildList(EntityListFieldsContainer $fields): void
    Fields:
      - EntityListField::make('name')
          ->setLabel('Name')
          ->setSortable()

      - EntityListField::make('email')
          ->setLabel('Email')
          ->setSortable()

      - EntityListField::make('phone')
          ->setLabel('Phone')
          ->hideOnSmallScreens()

      - EntityListField::make('city')
          ->setLabel('City')
          ->hideOnSmallScreens()

      - EntityListField::make('invoices_count')
          ->setLabel('Invoices')
          ->setSortable()

  Method: buildListConfig(): void
    Config:
      - configureSearchable()
      - configureDefaultSort('name', 'asc')
      - configureReorderable(false)
      - configurePaginated()

  Method: getListData(EntityListQueryParams $params): array
    - Query: Customer::query()->withCount('invoices')
    - Search: name, email, phone
    - Transform: id, name, email, phone, city, invoices_count
```
@endverbatim

### CustomerForm

@verbatim
```
Resource: CustomerForm
  Location: App\Sharp\Customers\CustomerForm
  Extends: Code16\Sharp\Form\SharpForm
  Docs: https://sharp.code16.fr/docs/guide/building-form

  Method: buildFormFields(FieldsContainer $formFields): void
    Fields:
      - SharpFormTextField::make('name')
          ->setLabel('Name')
          ->setMaxLength(255)

      - SharpFormTextField::make('email')
          ->setLabel('Email')
          ->setMaxLength(255)

      - SharpFormTextField::make('phone')
          ->setLabel('Phone')
          ->setMaxLength(50)

      - SharpFormTextField::make('address_line_1')
          ->setLabel('Address Line 1')
          ->setMaxLength(255)

      - SharpFormTextField::make('address_line_2')
          ->setLabel('Address Line 2')
          ->setMaxLength(255)

      - SharpFormTextField::make('city')
          ->setLabel('City')
          ->setMaxLength(100)

      - SharpFormTextField::make('state')
          ->setLabel('State')
          ->setMaxLength(100)

      - SharpFormTextField::make('postal_code')
          ->setLabel('Postal Code')
          ->setMaxLength(20)

      - SharpFormTextField::make('country')
          ->setLabel('Country')
          ->setMaxLength(100)

      - SharpFormTextareaField::make('notes')
          ->setLabel('Notes')
          ->setRowCount(4)

  Method: buildFormLayout(FormLayout $formLayout): void
    Layout:
      - Column 8:
          - Fieldset "Contact Information":
              - name (full width)
              - Row: email, phone
          - Fieldset "Address":
              - address_line_1 (full width)
              - address_line_2 (full width)
              - Row: city, state, postal_code
              - country (full width)
      - Column 4:
          - Fieldset "Additional Information":
              - notes (full width)

  Method: create(): array
    - Return: empty customer data array

  Method: update(mixed $id, array $data): bool
    - Find/create Customer model
    - Save attributes
    - Return true

  Method: find(mixed $id): array
    - Find Customer by id
    - Transform to array
```
@endverbatim

### InvoiceList

@verbatim
```
Resource: InvoiceList
  Location: App\Sharp\Invoices\InvoiceList
  Extends: Code16\Sharp\EntityList\SharpEntityList
  Docs: https://sharp.code16.fr/docs/guide/building-entity-list

  Method: buildList(EntityListFieldsContainer $fields): void
    Fields:
      - EntityListField::make('invoice_number')
          ->setLabel('Number')
          ->setSortable()

      - EntityListField::make('customer:name')
          ->setLabel('Customer')
          ->setSortable()

      - EntityListField::make('invoice_date')
          ->setLabel('Date')
          ->setSortable()

      - EntityListField::make('due_date')
          ->setLabel('Due Date')
          ->setSortable()
          ->hideOnSmallScreens()

      - EntityListField::make('total')
          ->setLabel('Total')
          ->setSortable()

      - EntityListStateField::make()
          ->setLabel('Status')

  Method: buildListConfig(): void
    Config:
      - configureSearchable()
      - configureDefaultSort('invoice_date', 'desc')
      - configureEntityState('status', InvoiceStateHandler::class)
      - configurePaginated()

  Filters:
    - InvoiceStatusFilter
    - CustomerFilter
    - InvoiceDateRangeFilter

  Method: getListData(EntityListQueryParams $params): array
    - Query: Invoice::with('customer')
    - Search: invoice_number, customer.name
    - Filters: status, customer_id, date range
    - Transform: id, invoice_number, customer:name, invoice_date, due_date, total (formatted), status
```
@endverbatim

### InvoiceForm

@verbatim
```
Resource: InvoiceForm
  Location: App\Sharp\Invoices\InvoiceForm
  Extends: Code16\Sharp\Form\SharpForm
  Docs: https://sharp.code16.fr/docs/guide/building-form

  Method: buildFormFields(FieldsContainer $formFields): void
    Fields:
      - SharpFormAutocompleteField::make('customer_id', 'remote')
          ->setLabel('Customer')
          ->setRemoteEndpoint('/sharp/api/autocomplete/customers')
          ->setResultItemInlineTemplate('{{ $name }} - {{ $email }}')
          ->setListItemInlineTemplate('{{ $name }}')

      - SharpFormTextField::make('invoice_number')
          ->setLabel('Invoice Number')
          ->setReadOnly()
          ->setMaxLength(50)

      - SharpFormDateField::make('invoice_date')
          ->setLabel('Invoice Date')

      - SharpFormDateField::make('due_date')
          ->setLabel('Due Date')

      - SharpFormListField::make('items')
          ->setLabel('Line Items')
          ->setAddable()
          ->setRemovable()
          ->setSortable()
          ->setOrderAttribute('sort_order')
          ->addItemField(
              SharpFormAutocompleteField::make('product_id', 'remote')
                  ->setLabel('Product')
                  ->setRemoteEndpoint('/sharp/api/autocomplete/products')
                  ->setResultItemInlineTemplate('{{ $name }} - {{ $unit_price }}')
          )
          ->addItemField(
              SharpFormTextField::make('description')
                  ->setLabel('Description')
                  ->setMaxLength(255)
          )
          ->addItemField(
              SharpFormTextField::make('quantity')
                  ->setLabel('Quantity')
          )
          ->addItemField(
              SharpFormTextField::make('unit_price')
                  ->setLabel('Unit Price')
          )
          ->addItemField(
              SharpFormTextField::make('total')
                  ->setLabel('Total')
                  ->setReadOnly()
          )

      - SharpFormTextField::make('tax_rate')
          ->setLabel('Tax Rate (%)')
          ->setInputTypeNumber()
          ->setStep(0.01)

      - SharpFormTextField::make('subtotal')
          ->setLabel('Subtotal')
          ->setReadOnly()

      - SharpFormTextField::make('tax_amount')
          ->setLabel('Tax Amount')
          ->setReadOnly()

      - SharpFormTextField::make('total')
          ->setLabel('Total')
          ->setReadOnly()

      - SharpFormTextareaField::make('notes')
          ->setLabel('Notes')
          ->setRowCount(4)

  Method: buildFormLayout(FormLayout $formLayout): void
    Layout:
      - Column 8:
          - Fieldset "General":
              - customer_id (full width)
              - invoice_number (full width)
              - Row: invoice_date, due_date
          - Fieldset "Line Items":
              - items (full width)
          - Fieldset "Notes":
              - notes (full width)
      - Column 4:
          - Fieldset "Totals":
              - tax_rate (full width)
              - subtotal (full width)
              - tax_amount (full width)
              - total (full width)

  Method: create(): array
    - Generate invoice number
    - Set default dates
    - Return empty invoice data

  Method: update(mixed $id, array $data): bool
    - Find/create Invoice model
    - Save attributes and relationships
    - Recalculate totals
    - Return true

  Method: find(mixed $id): array
    - Find Invoice with items
    - Transform to array with calculated totals
```
@endverbatim

### InvoiceShow

@verbatim
```
Resource: InvoiceShow
  Location: App\Sharp\Invoices\InvoiceShow
  Extends: Code16\Sharp\Show\SharpShow
  Docs: https://sharp.code16.fr/docs/guide/building-show-page

  Method: buildShowFields(FieldsContainer $showFields): void
    Fields:
      - SharpShowTextField::make('invoice_number')
          ->setLabel('Invoice Number')

      - SharpShowTextField::make('status')
          ->setLabel('Status')

      - SharpShowTextField::make('customer')
          ->setLabel('Customer')

      - SharpShowTextField::make('invoice_date')
          ->setLabel('Invoice Date')

      - SharpShowTextField::make('due_date')
          ->setLabel('Due Date')

      - SharpShowListField::make('items')
          ->setLabel('Line Items')
          ->addItemField(SharpShowTextField::make('description')->setLabel('Description'))
          ->addItemField(SharpShowTextField::make('quantity')->setLabel('Quantity'))
          ->addItemField(SharpShowTextField::make('unit_price')->setLabel('Unit Price'))
          ->addItemField(SharpShowTextField::make('total')->setLabel('Total'))

      - SharpShowTextField::make('subtotal')
          ->setLabel('Subtotal')

      - SharpShowTextField::make('tax_rate')
          ->setLabel('Tax Rate')

      - SharpShowTextField::make('tax_amount')
          ->setLabel('Tax Amount')

      - SharpShowTextField::make('total')
          ->setLabel('Total')

      - SharpShowTextField::make('amount_paid')
          ->setLabel('Amount Paid')

      - SharpShowTextField::make('balance_due')
          ->setLabel('Balance Due')

      - SharpShowEntityListField::make('payments', PaymentEntity::class)
          ->setLabel('Payments')
          ->hideFilterWithValue('invoice', fn($instanceId) => $instanceId)

      - SharpShowTextField::make('notes')
          ->setLabel('Notes')

  Method: buildShowLayout(ShowLayout $showLayout): void
    Layout:
      - Section "Invoice Details":
          - Column 8:
              - invoice_number, status, customer
              - invoice_date, due_date
          - Column 4: (empty for spacing)
      - Section "Line Items":
          - Column 12:
              - items (full width)
      - Section "Totals":
          - Column 8: (empty)
          - Column 4:
              - subtotal, tax_rate, tax_amount, total
              - amount_paid, balance_due
      - Section "Payments":
          - Column 12:
              - payments (full width)
      - Section "Additional Information":
          - Column 12:
              - notes (full width)

  Method: find(mixed $id): array
    - Find Invoice with customer, items, payments
    - Transform to array with formatted values

  Commands:
    - SendInvoiceCommand (instance)
    - RecordPaymentCommand (instance)
```
@endverbatim

### InvoiceStateHandler

@verbatim
<code-snippet name="Invoice State Handler" lang="php">
namespace App\Sharp\Invoices;

use App\Enums\InvoiceStatus;
use Code16\Sharp\EntityList\Commands\EntityState;

class InvoiceStateHandler extends EntityState
{
    protected function buildStates(): void
    {
        $this
            ->addState(InvoiceStatus::Draft->value, InvoiceStatus::Draft->label(), InvoiceStatus::Draft->color())
            ->addState(InvoiceStatus::Sent->value, InvoiceStatus::Sent->label(), InvoiceStatus::Sent->color())
            ->addState(InvoiceStatus::Paid->value, InvoiceStatus::Paid->label(), InvoiceStatus::Paid->color())
            ->addState(InvoiceStatus::Overdue->value, InvoiceStatus::Overdue->label(), InvoiceStatus::Overdue->color())
            ->addState(InvoiceStatus::Cancelled->value, InvoiceStatus::Cancelled->label(), InvoiceStatus::Cancelled->color());
    }

    protected function updateState(mixed $instanceId, string $stateId): array
    {
        $invoice = \App\Models\Invoice::findOrFail($instanceId);

        $invoice->update([
            'status' => InvoiceStatus::from($stateId),
        ]);

        if ($stateId === InvoiceStatus::Sent->value && !$invoice->sent_at) {
            $invoice->update(['sent_at' => now()]);
        }

        if ($stateId === InvoiceStatus::Paid->value && !$invoice->paid_at) {
            $invoice->update(['paid_at' => now()]);
        }

        return $this->reload();
    }
}
</code-snippet>
@endverbatim

### SendInvoiceCommand

@verbatim
<code-snippet name="Send Invoice Command" lang="php">
namespace App\Sharp\Invoices\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

class SendInvoiceCommand extends InstanceCommand
{
    public function label(): string
    {
        return 'Mark as Sent';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        $invoice = Invoice::findOrFail($instanceId);

        if ($invoice->status !== InvoiceStatus::Draft) {
            return $this->error('Only draft invoices can be sent.');
        }

        $invoice->markAsSent();

        return $this->reload();
    }

    public function authorizeFor(mixed $instanceId): bool
    {
        $invoice = Invoice::find($instanceId);

        return $invoice && $invoice->status === InvoiceStatus::Draft;
    }
}
</code-snippet>
@endverbatim

### RecordPaymentCommand

@verbatim
<code-snippet name="Record Payment Command" lang="php">
namespace App\Sharp\Invoices\Commands;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class RecordPaymentCommand extends InstanceCommand
{
    public function label(): string
    {
        return 'Record Payment';
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('amount')
                    ->setLabel('Amount')
            )
            ->addField(
                SharpFormSelectField::make('method', collect(PaymentMethod::cases())->map(fn($case) => [
                    'id' => $case->value,
                    'label' => $case->label(),
                ])->all())
                    ->setLabel('Payment Method')
            )
            ->addField(
                SharpFormTextField::make('reference')
                    ->setLabel('Reference')
                    ->setMaxLength(255)
            )
            ->addField(
                SharpFormDateField::make('payment_date')
                    ->setLabel('Payment Date')
            )
            ->addField(
                SharpFormTextareaField::make('notes')
                    ->setLabel('Notes')
                    ->setRowCount(3)
            );
    }

    public function initialData(mixed $instanceId): array
    {
        $invoice = Invoice::findOrFail($instanceId);

        return [
            'amount' => $invoice->balance_due / 100,
            'payment_date' => now()->format('Y-m-d'),
        ];
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        $invoice = Invoice::findOrFail($instanceId);

        $amountInCents = (int) ($data['amount'] * 100);

        if ($amountInCents > $invoice->balance_due) {
            return $this->error('Payment amount cannot exceed balance due.');
        }

        $invoice->recordPayment(
            $amountInCents,
            PaymentMethod::from($data['method']),
            $data['reference'] ?? null,
            \Carbon\Carbon::parse($data['payment_date'])
        );

        return $this->reload();
    }

    public function authorizeFor(mixed $instanceId): bool
    {
        $invoice = Invoice::find($instanceId);

        return $invoice && $invoice->balance_due > 0;
    }
}
</code-snippet>
@endverbatim
