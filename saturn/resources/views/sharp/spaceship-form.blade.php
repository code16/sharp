


<x-sharp::form :form="$form">
    <x-sharp::col :size="6">
        <x-sharp::col :size="6">
            <x-sharp::form.text-field
                name="name"
                localized
                :max-length="30"
            >
                <x-slot name="label">
                    Name
                </x-slot>
            </x-sharp::form.text-field>
        </x-sharp::col>
        <x-sharp::col :size="6">
            <x-sharp::form.text-field
                name="last_name"
            >
                <x-slot name="label">
                    Last name
                </x-slot>
            </x-sharp::form.text-field>
        </x-sharp::col>
    </x-sharp::col>

</x-sharp::form>
