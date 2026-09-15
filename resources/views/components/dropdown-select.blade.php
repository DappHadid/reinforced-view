@props([
    'id' => '',
    'name' => '',
    'options' => [], // Array of ['value' => '...', 'label' => '...']
    'optionsJs' => null, // Optional Alpine JS expression/variable returning options
    'selected' => '',
    'placeholder' => 'Pilih opsi...',
    'extraClasses' => 'min-w-[200px]',
    'autoSubmit' => false,
])

<div x-data="{ 
        open: false, 
        selected: '{{ $selected }}',
        get optionsList() { return {{ $optionsJs ? $optionsJs : json_encode($options) }}; },
        get selectedLabel() {
            const opt = this.optionsList.find(o => o.value == this.selected);
            return opt ? opt.label : '{{ $placeholder }}';
        }
    }" 
    class="relative {{ $extraClasses }}">
    
    <input type="hidden" x-ref="input" name="{{ $name }}" id="{{ $id }}" :value="selected">
    
    <button type="button" @click="open = !open" @click.away="open = false" 
            class="flex w-full h-10 items-center justify-between gap-2 rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900 transition-colors shadow-sm">
        <span x-text="selectedLabel" class="truncate"></span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 opacity-60 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95" 
         class="absolute top-full z-50 mt-1 min-w-full w-max max-w-[90vw] overflow-hidden rounded-md border border-slate-200 bg-white p-1 text-slate-700 shadow-md outline-none"
         style="display: none;">
         
        <div class="max-h-60 overflow-y-auto">
            <template x-for="option in optionsList" :key="option.value">
                <div @click="
                        selected = option.value; 
                        open = false; 
                        $nextTick(() => { 
                            $refs.input.dispatchEvent(new Event('change', { bubbles: true }));
                            if ({{ $autoSubmit ? 'true' : 'false' }}) {
                                $el.closest('form').submit();
                            }
                        })
                    " 
                    class="relative flex cursor-pointer select-none items-center rounded-sm py-1.5 pl-8 pr-4 text-sm outline-none transition-colors hover:bg-slate-100 hover:text-slate-900 focus:bg-slate-100 focus:text-slate-900"
                    :class="selected == option.value ? 'bg-slate-50 text-slate-900 font-medium' : ''">
                    
                    <span class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
                        <svg x-show="selected == option.value" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    
                    <span x-text="option.label" class="whitespace-nowrap pr-2"></span>
                </div>
            </template>
        </div>
    </div>
</div>
