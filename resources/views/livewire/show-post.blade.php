<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $title }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                <div class="flex items-center justify-end py-4 text-right">
                    <x-jet-button wire:click="returnToPosts">
                        {{ __('Return to posts') }}
                    </x-jet-button>
                </div>


                <div class="mx-auto pb-5">
                    <img src="{{ asset('images/' . $image) }}" alt="{{ $title }}" class="mx-auto max-w-full">
                </div>

                <h1 class="text-center text-2xl font-extrabold pb-5">
                    {{ $title }}
                </h1>

                {!! $body !!}
            </div>
        </div>
    </div>
</div>
