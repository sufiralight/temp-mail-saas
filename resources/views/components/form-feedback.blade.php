@if ($errors->any())
    <div data-error-summary tabindex="-1" role="alert" class="mb-3 flex items-start gap-2 rounded-lg border border-zinc-300 bg-zinc-50 p-2.5 text-sm/5 text-zinc-900 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:outline-indigo-400">
        <x-icon name="alert" class="size-5 text-indigo-700 dark:text-indigo-300" />
        @if ($errors->has('connection'))
            <p>{{ $errors->first('connection') }}</p>
        @else
            <div>
                <p class="font-semibold">Check the highlighted fields.</p>
                <ul class="mt-1 grid gap-1">
                    @foreach ($errors->getMessages() as $field => $messages)
                        <li><a href="#{{ $field === 'token' ? 'email' : $field }}" class="rounded-sm underline underline-offset-2">{{ $messages[0] }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif

@if (session('status'))
    <div role="status" class="mb-5 flex items-start gap-2.5 rounded-lg border border-indigo-200 bg-indigo-50 p-3 text-sm/5 text-indigo-950 dark:border-indigo-900 dark:bg-indigo-950/50 dark:text-indigo-100">
        <x-icon name="check" class="size-5 text-indigo-700 dark:text-indigo-300" />
        <p>{{ session('status') }}</p>
    </div>
@endif
