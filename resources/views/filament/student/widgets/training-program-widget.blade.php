<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">
                <h2 class="text-lg font-bold tracking-tight">
                    Chương trình đào tạo
                </h2>

                @if($this->getTrainingProgram())
                    <div class="mt-2">
                        <div class="flex flex-col gap-y-1">
                            <div class="text-sm">
                                <span class="font-medium">Tên chương trình:</span>
                                {{ $this->getTrainingProgram()->name }}
                            </div>
                            <div class="text-sm">
                                <span class="font-medium">Mã chương trình:</span>
                                {{ $this->getTrainingProgram()->code }}
                            </div>
                            <div class="text-sm">
                                <span class="font-medium">Số tín chỉ:</span>
                                {{ $this->getTrainingProgram()->total_credits }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-2 text-sm text-gray-500">
                        Chưa có thông tin chương trình đào tạo
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
