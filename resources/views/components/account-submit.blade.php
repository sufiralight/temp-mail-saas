@props(['pending' => null])

<button type="submit" {{ $attributes->class(['primary', 'account-submit']) }} @if($pending) data-pending-label="{{ $pending }}" @endif>
    <span data-submit-label>{{ $slot }}</span>
    <span data-spinner hidden class="size-4 rounded-full border-2 border-current border-r-transparent motion-safe:animate-spin" aria-hidden="true"></span>
    <x-icon name="arrow" class="size-4" data-submit-arrow />
</button>
