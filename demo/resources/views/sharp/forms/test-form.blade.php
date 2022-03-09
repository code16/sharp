
@php
/**
 * @var \App\Sharp\TestForm\TestForm $form
 */
@endphp

<x-sharp::form>
    <x-sharp::tab title="Simple">
        <x-sharp::col class="col-md-6">
            <x-sharp::form.fieldset legend="Text fields">
                <x-sharp::col class="col-md-8">
                    <x-sharp::form.text-field
                        name="text"
                        :localized="true"
                    >
                        <x-slot name="label">
                            Text
                        </x-slot>
                    </x-sharp::form.text-field>
                </x-sharp::col>
            </x-sharp::form.fieldset>
        </x-sharp::col>
        <x-sharp::col class="col-md-6">

        </x-sharp::col>
    </x-sharp::tab>
    <x-sharp::tab title="Textarea">
        <x-sharp::col class="col-md-6">

        </x-sharp::col>
        <x-sharp::col class="col-md-6">

        </x-sharp::col>
    </x-sharp::tab>
    <x-sharp::tab title="Select">
        <x-sharp::col class="col-md-6">
            <x-sharp::form.autocomplete-field
                name="autocomplete_local"
                mode="local"
                label="Autocomplete local"
                :localized="true"
                :local-search-keys="['label']"
                :local-values="$form->options(true)"
            >
                <x-slot name="list_item">
                    @{{ label }}
                </x-slot>
                <x-slot name="result_item">
                    @{{ label }} (@{{ id }})
                </x-slot>
            </x-sharp::form.autocomplete-field>
        </x-sharp::col>
    </x-sharp::tab>
</x-sharp::form>
