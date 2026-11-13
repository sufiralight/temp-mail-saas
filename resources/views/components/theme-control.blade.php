@php($modes = ['light' => 'sun', 'dark' => 'moon', 'system' => 'monitor'])

<div class="relative size-10 shrink-0" data-theme-control>
    <button type="button" id="theme-trigger" class="icon-button" hidden
        aria-label="Appearance" aria-haspopup="menu" aria-expanded="false" aria-controls="theme-menu"
        title="Appearance" data-theme-trigger>
        @foreach ($modes as $mode => $icon)
            <x-icon :name="$icon" class="size-[18px]" data-theme-icon="{{ $mode }}" hidden />
        @endforeach
    </button>

    <div id="theme-menu" class="theme-menu" role="menu" aria-labelledby="theme-trigger" hidden>
        @foreach ($modes as $mode => $icon)
            <button type="button" class="theme-option" role="menuitemradio" aria-checked="false"
                tabindex="-1" data-theme-mode="{{ $mode }}">
                <x-icon :name="$icon" class="size-4" />
                <span>{{ ucfirst($mode) }}</span>
                <x-icon name="check" class="theme-check ml-auto size-4" />
            </button>
        @endforeach
    </div>
</div>
