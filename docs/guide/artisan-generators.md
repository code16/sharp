# Artisan Generators

For more information on each command and its options & arguments run `php artisan <command> --help`

```bash
# Prompt Generator (interactive)
php artisan sharp:generator

# Generate an entity
php artisan sharp:make:entity <class_name> [--label,--dashboard,--show,--form,--policy,--single]

# Generate the Menu class
php artisan sharp:make:menu <class_name>

# Generate the SharpServiceProvider class
php artisan sharp:make:provider <class_name>

# Generate a Dashboard class
php artisan sharp:make:dashboard <class_name>

# Generate an Entity List
php artisan sharp:make:entity-list <class_name> [--model=<model_name>]

# Generate a Form
php artisan sharp:make:form <class_name> [--model=<model_name>,--single]

# Generate a Show Page
php artisan sharp:make:show-page <class_name> [--model=<model_name>,--single]

# Generate a Policy
php artisan sharp:make:policy <class_name> [--entity-only]

# Generate an Entity Command
php artisan sharp:make:entity-command <class_name> [--model=<model_name>,--wizard,--form]

# Generate an Instance Command
php artisan sharp:make:intance-command <class_name> [--model=<model_name>,--wizard,--form]

# Generate a Entity List Filter
php artisan sharp:make:entity-list-filter <class_name> [--required,--multiple,--date-range,--check]

# Generate a ReorderHandler
php artisan sharp:make:reorder-handler <class_name> [--model=<model_name>]

# Generate a Entity State
php artisan sharp:make:entity-state <class_name> [--model=<model_name>]

# Generate sharp media model
php artisan sharp:make:media <class_name> [--table=<db table_name>]
```
