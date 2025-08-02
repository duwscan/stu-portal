@php
    $state = $getState();
    $canRegister = $state['canRegister'] ?? false;
    $description = $state['description'] ?? '';
@endphp

<div class="flex flex-col gap-1">
    {{-- Status Badge --}}
    <div class="flex items-center">
        <span @class([
            'inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg shadow-sm transition-all duration-200',
            'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800' => $canRegister,
            'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800' => !$canRegister,
        ])>
            @if($canRegister)
                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">Có thể đăng ký</span>
            @else
                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">Không thể đăng ký</span>
            @endif
        </span>
    </div>

    {{-- Description --}}
    @if($description)
        <div class="flex items-start">
            <svg class="w-3 h-3 mt-0.5 mr-1.5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <span class="text-xs leading-relaxed text-gray-600 dark:text-gray-400 max-w-xs">
                {{ $description }}
            </span>
        </div>
    @endif
</div>
