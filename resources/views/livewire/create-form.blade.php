<form wire:submit="create">
    <div class="flex flex-row w-full items-center justify-center">
        <div class="flex flex-col w-full mt-4">
            <section>
                <h2>{{ $formTitle }}</h2>
                <p class="mt-2">{{ $formDescription }}</p>
            </section>
            <section>
                <div class="flex flex-col space-y-4 mt-8 w-4/5">
                    @foreach($this->fields() as $field)
                        <x-field :$field />
                    @endforeach
                    <div class="flex flex-row justify-end">
                        <x-primary-button class="mt-2">Create</x-primary-button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</form>
