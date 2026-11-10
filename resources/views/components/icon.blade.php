@props(['name'])

<svg {{ $attributes->merge(['class' => 'size-5 shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>
            @break
        @case('moon')
            <path d="M20.9 13.3A9 9 0 0 1 10.7 3.1 9 9 0 1 0 20.9 13.3Z"/>
            @break
        @case('sun')
            <circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4m0-14.2-1.4 1.4M6.3 17.7l-1.4 1.4"/>
            @break
        @case('eye')
            <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="2.5"/>
            @break
        @case('eye-off')
            <path d="m3 3 18 18M10 6.2a10.9 10.9 0 0 1 2-.2c6.4 0 10 6 10 6a14 14 0 0 1-2.6 2.9M6.3 7.8A16 16 0 0 0 2 12s3.6 6 10 6a11.2 11.2 0 0 0 4.2-.8M10.2 10.2a2.5 2.5 0 0 0 3.6 3.6"/>
            @break
        @case('database')
            <ellipse cx="12" cy="5.5" rx="8" ry="3"/><path d="M4 5.5v13c0 1.7 3.6 3 8 3s8-1.3 8-3v-13M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>
            @break
        @case('arrow')
            <path d="M4 12h15m-6-6 6 6-6 6"/>
            @break
        @case('check')
            <path d="m5 12 4 4L19 6"/>
            @break
        @case('lock')
            <rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2"/>
            @break
        @case('alert')
            <circle cx="12" cy="12" r="9"/><path d="M12 7.5v5m0 4h.01"/>
            @break
        @case('user')
            <circle cx="12" cy="8" r="4"/><path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2"/>
            @break
        @case('inbox')
            <path d="M4 13 7 5h10l3 8v6H4v-6Zm0 0h5l1 2h4l1-2h5"/>
            @break
    @endswitch
</svg>
