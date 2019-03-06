# Artisan Generators

For more information on each command and its options & arguments run `php artisan <command> --help`

```sh
# Generate a dashboard class
php artisan sharp:make:dashboard <class_name>

# Generate a sharp entity form
php artisan sharp:make:form <class_name> [--model=<model_name>]

# Generate a sharp entity list
php artisan sharp:make:entity-list <class_name> [--model=<model_name>]

# Generate a list command
php artisan sharp:make:entity-command <class_name> [--model=<model_name>]

# Generate a list filter
php artisan sharp:make:list-filter <class_name> [--required,--multiple]

# Generate a reorder handler
php artisan sharp:make:reorder-handler <class_name> [--model=<model_name>]

# Generate a model state
php artisan sharp:make:entity-state <class_name> [--model=<model_name>]

# Generate sharp media model
php artisan sharp:make:media <class_name> [--table=<db table_name>]

# Run a wizard that will create a model, list & form in one go
php artisan sharp:model-wizard [--model=<model_name>]
```
