@if ($errors->any())
    <div data-error-summary tabindex="-1" role="alert" class="feedback-error mb-5 flex items-start gap-2 rounded-lg p-3 text-sm/5 focus:outline-2">
        <x-icon name="alert" class="size-5" />
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
    <div role="status" class="feedback-success mb-5 flex items-start gap-2.5 rounded-lg p-3 text-sm/5">
        <x-icon name="check" class="size-5" />
        <p>{{ session('status') }}</p>
    </div>
@endif
