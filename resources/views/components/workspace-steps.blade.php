@props(['step'])

<div class="grid gap-1.5">
    <p class="text-[11px] font-semibold tracking-[0.14em] text-zinc-600 uppercase dark:text-zinc-400">Workspace setup</p>
    <ol aria-label="Workspace setup progress" class="grid grid-cols-2 gap-1 rounded-xl border border-(--line) bg-(--surface) p-1">
        @foreach ([1 => 'Connect database', 2 => 'Create admin account'] as $number => $label)
            <li @if($number === $step) aria-current="step" @endif @class([
                'flex min-h-8 items-center gap-2 rounded-lg px-2 py-1 text-xs leading-tight font-semibold sm:px-3 sm:text-sm',
                'bg-(--control-hover) text-(--ink)' => $number === $step,
                'text-zinc-600 dark:text-zinc-400' => $number !== $step,
            ])>
                <span @class([
                    'grid size-5 shrink-0 place-items-center rounded-full text-[10px] font-bold',
                    'bg-(--primary-fill) text-(--primary-ink)' => $number === $step,
                    'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' => $number !== $step,
                ]) aria-hidden="true">
                    @if ($number < $step)
                        <x-icon name="check" class="size-3" />
                    @else
                        {{ $number }}
                    @endif
                </span>
                {{ $label }}
                @if ($number < $step)
                    <span class="sr-only">(complete)</span>
                @endif
            </li>
        @endforeach
    </ol>
</div>
