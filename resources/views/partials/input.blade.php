<div>
    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
        {{ $label }} @if(!empty($required)) <span class="text-amber-600">*</span> @endif
    </label>
    <input
        type="{{ $type ?? 'text' }}"
        name="{{ $name }}"
        value="{{ old($name) }}"
        @if(!empty($required)) required @endif
        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
    >
</div>
