<div class="max-w-2xl">
    <img src="{{ asset('logo.svg') }}" class="mb-5 w-1/2 mx-auto" alt="Amsterdam Standard" />
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Amsterdam Standard Wiki Assistant</h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
                <p>Enter you question and the assistant will answer based on our wiki.</p>
            </div>
            <form class="mt-5 sm:flex sm:items-center" wire:submit.prevent="ask">
                <div class="w-full sm:max-w-xs">
                    <label for="question" class="sr-only">Question</label>
                    <input type="text"
                           name="question"
                           wire:model="question"
                           class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                           placeholder="Your question..."
                    >
                </div>
                <button type="submit"
                        class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:ml-3 sm:mt-0 sm:w-auto"
                >
                    <span wire:loading.class="invisible">Ask</span>
                    <x-spinner class="absolute invisible" wire:loading.class.remove="invisible" />
                </button>
            </form>
            @if($answer)
                <hr class="my-5" />
                <div class="mb-2">
                    <x-markdown>{!! nl2br($answer) !!}</x-markdown>
                </div>
            @endif
        </div>
    </div>
    <div class="mt-5 text-center text-slate-300 text-sm">
        &copy; 2024. Created by Adam "Chip" Matysiak, for Pizza Talk, 07.03.2024, Amsterdam Standard.
    </div>
</div>