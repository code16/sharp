


<x-sharp::form :form="$form">
    <x-sharp::tab title="Simple">
        <x-sharp::col class="col-md-6">
            <x-sharp::form.fieldset legend="Fields">
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

    </x-sharp::tab>
</x-sharp::form>
