## Sharp Blueprint
A Sharp Blueprint is a detailed implementation plan that helps AI agents write correct Sharp code. It bridges the gap between requirements and implementation by providing a structured specification for Sharp's unique patterns.

### Core Philosophy
Sharp separates the data structure from its visual representation. A blueprint should clearly map domain models to Sharp components:
- **Entity Lists**: The primary data table.
- **Forms**: Data entry and validation.
- **Show Pages**: Detailed read-only views.
- **Commands**: Business logic triggers.
- **State Handlers**: Lifecycle management.

### Blueprint Structure
When asking an AI to create a blueprint, it should produce:
1. **User Flows**: Step-by-step navigation through the feature.
2. **Commands**: Required Artisan commands for scaffolding.
3. **Models**: Detailed attribute, relationship, and cast definitions.
4. **Sharp Components**: Specific configurations for Lists, Forms, Shows, and Commands.

---

## SaaS Invoicing System - Sharp Implementation Plan

### Overview
A single-tenant admin panel for managing customers, products, invoices with line items, and tracking payments.

### User Flows
#### Flow 1: Creating an Invoice
1. User navigates to Invoices List -> "New"
2. User selects a Customer (Select field with autocomplete)
3. User sets invoice date and due date
4. User adds line items via List field:
    - Select Product
    - Enter quantity
    - Line total auto-calculates (handled in model/transformer)
5. Tax rate is entered
6. Invoice saves as Draft status

#### Flow 2: Sending an Invoice
1. User views a Draft invoice in its Show Page
2. User clicks "Send" (Instance Command)
3. Confirmation modal appears
4. On confirm: status -> Sent, notification sent

### Models
#### Model: Customer
- **Attributes**: `name`, `email`, `phone`, `address`, `notes`
- **Relationships**: `hasMany` Invoices

#### Model: Invoice
- **Attributes**: `invoice_number`, `status` (Enum), `invoice_date`, `due_date`, `tax_rate`
- **Relationships**: `belongsTo` Customer, `hasMany` InvoiceItem, `hasMany` Payment

### Sharp Components

#### InvoiceEntity
- **Location**: `App\Sharp\Entities\InvoiceEntity`
- **Config**:
  - `list`: `InvoiceList`
  - `show`: `InvoiceShow`
  - `form`: `InvoiceForm`
  - `state`: `InvoiceStateHandler`

#### InvoiceList
- **Columns**:
  - `invoice_number`: label "Number", sortable
  - `customer:name`: label "Customer"
  - `invoice_date`: label "Date", sortable
  - `total`: label "Total", money format
  - `state`: label "Status", state badge
- **Filters**: `InvoiceStatusFilter`, `DateRangeFilter`

#### InvoiceForm
- **Layout**:
  - Section "General": `customer_id`, `invoice_date`, `due_date`
  - Section "Items": `items` (List field)
  - Section "Totals": `tax_rate`, `notes`
- **Fields**:
@verbatim
<code-snippet name="Invoice Form List Field" lang="php">
SharpFormListField::make('items')
    ->setLabel('Items')
    ->addItemField(
        SharpFormSelectField::make('product_id', $products)->setLabel('Product')
    )
    ->addItemField(
        SharpFormTextField::make('quantity')->setLabel('Quantity')->setInputModeNumeric()
    )
</code-snippet>
@endverbatim

#### InvoiceShow
- **Sections**:
  - Header: Number, Status, Customer Info
  - Content: `items` (Entity List section or List field)
  - Sidebar: `payments` (Entity List section)
- **Commands**: `SendInvoiceCommand`, `RecordPaymentCommand`

#### InvoiceStateHandler
@verbatim
<code-snippet name="Invoice State Handler" lang="php">
class InvoiceStateHandler extends SharpEntityStateHandler
{
    public function buildStates(): void
    {
        $this->addState('draft', 'Draft', 'gray')
            ->addState('sent', 'Sent', 'blue')
            ->addState('paid', 'Paid', 'green')
            ->addState('cancelled', 'Cancelled', 'red');
    }
}
</code-snippet>
@endverbatim
