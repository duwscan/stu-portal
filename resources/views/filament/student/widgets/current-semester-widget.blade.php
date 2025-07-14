<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">
                <h2 class="text-lg font-bold tracking-tight">
                    Kỳ học hiện tại
                </h2>

                @if($this->getCurrentSemester())
                    <div class="mt-2">
                        <div class="flex flex-col gap-y-1">
                            <div class="text-sm">
                                <span class="font-medium">Kỳ học:</span>
                                {{ $this->getCurrentSemester()->name }}
                            </div>
                            <div class="text-sm">
                                <span class="font-medium">Năm học:</span>
                                {{ $this->getCurrentSemester()->start_date->year }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-2 text-sm text-gray-500">
                        Chưa có thông tin kỳ học hiện tại
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
