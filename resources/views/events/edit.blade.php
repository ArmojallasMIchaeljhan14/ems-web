<x-app-layout>
<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800">
            Edit Event Request
        </h2>

        <a href="{{ route('events.show', $event) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
            Back
        </a>
    </div>
</x-slot>

<div class="py-10" x-data="eventForm">
<div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

<form method="POST" action="{{ route('events.update', $event) }}"
      class="bg-white shadow-sm rounded-xl border border-gray-200">
@csrf
@method('PUT')

<div class="p-6 space-y-10">

{{-- ================= BASIC DETAILS ================= --}}
<div>
<h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">
Event Details
</h3>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

<input type="text"
       name="title"
       value="{{ old('title', $event->title) }}"
       class="w-full rounded-lg border-gray-300 md:col-span-2">

<select name="venue_id"
        class="w-full rounded-lg border-gray-300">
    @foreach($venues as $venue)
        <option value="{{ $venue->id }}"
            {{ old('venue_id', $event->venue_id) == $venue->id ? 'selected' : '' }}>
            {{ $venue->name }}
        </option>
    @endforeach
</select>

<input type="datetime-local"
       name="start_at"
       value="{{ old('start_at', $event->start_at->format('Y-m-d\TH:i')) }}"
       class="w-full rounded-lg border-gray-300">

<input type="datetime-local"
       name="end_at"
       value="{{ old('end_at', $event->end_at->format('Y-m-d\TH:i')) }}"
       class="w-full rounded-lg border-gray-300">

<textarea name="description"
          rows="4"
          class="w-full rounded-lg border-gray-300 md:col-span-2">{{ old('description', $event->description) }}</textarea>

</div>
</div>

{{-- ================= LOGISTICS ================= --}}
<div x-data="logisticsRepeater">
<h3 class="text-lg font-bold text-gray-900 mb-4">
Logistics & Budget
</h3>

<template x-for="(row, index) in rows" :key="index">
<div class="grid grid-cols-12 gap-4 p-4 bg-gray-50 rounded-xl border items-end mb-3">

<div class="col-span-5">
<input type="text"
       :name="`logistics_items[${index}][resource_name]`"
       x-model="row.resource_name"
       class="w-full rounded-lg border-gray-300">
</div>

<div class="col-span-2">
<input type="number"
       min="1"
       :name="`logistics_items[${index}][quantity]`"
       x-model.number="row.quantity"
       class="w-full rounded-lg border-gray-300">
</div>

<div class="col-span-2">
<input type="number"
       step="0.01"
       :name="`logistics_items[${index}][unit_price]`"
       x-model.number="row.unit_price"
       class="w-full rounded-lg border-gray-300">
</div>

<div class="col-span-2 text-right font-bold">
₱<span x-text="formatNumber(row.quantity * row.unit_price)"></span>
</div>

<div class="col-span-1 text-right">
<button type="button"
        @click="removeRow(index)"
        class="text-red-500">✕</button>
</div>

</div>
</template>

<button type="button"
        @click="addRow()"
        class="text-indigo-600 text-sm font-bold">
+ Add Item
</button>

<div class="mt-4 text-right font-bold text-xl text-indigo-600">
₱<span x-text="formatNumber(calculateTotal())"></span>
</div>

</div>

{{-- ================= CUSTODIAN ================= --}}
<div x-data="custodianRepeater">
<h3 class="text-lg font-bold text-gray-900 mb-4">
Custodian Equipment
</h3>

<template x-for="(row, index) in rows" :key="index">
<div class="grid grid-cols-3 gap-4 mb-3">

<select :name="`custodian_items[${index}][material_id]`"
        x-model="row.material_id"
        class="rounded-lg border-gray-300">
    <option value="">Select</option>
    @foreach($custodianMaterials as $mat)
        <option value="{{ $mat->id }}">
            {{ $mat->name }}
        </option>
    @endforeach
</select>

<input type="number"
       min="1"
       :name="`custodian_items[${index}][quantity]`"
       x-model.number="row.quantity"
       class="rounded-lg border-gray-300">

<button type="button"
        @click="removeRow(index)"
        class="text-red-600">Remove</button>

</div>
</template>

<button type="button"
        @click="addRow()"
        class="text-indigo-600 text-sm font-bold">
+ Add Equipment
</button>

</div>

{{-- ================= COMMITTEE ================= --}}
<div x-data="committeeRepeater">
<h3 class="text-lg font-bold text-gray-900 mb-4">
Committee
</h3>

<template x-for="(row, index) in rows" :key="index">
<div class="grid grid-cols-2 gap-4 mb-3">

<select :name="`committee[${index}][employee_id]`"
        x-model="row.employee_id"
        class="rounded-lg border-gray-300">
    <option value="">Select</option>
    @foreach($employees as $emp)
        <option value="{{ $emp->id }}">
            {{ $emp->last_name }}, {{ $emp->first_name }}
        </option>
    @endforeach
</select>

<input type="text"
       :name="`committee[${index}][role]`"
       x-model="row.role"
       class="rounded-lg border-gray-300">

</div>
</template>

<button type="button"
        @click="addRow()"
        class="text-indigo-600 text-sm font-bold">
+ Add Member
</button>

</div>

</div>

<div class="px-6 py-4 border-t bg-gray-50 text-right">
<button type="submit"
        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase">
Update Request
</button>
</div>

</form>
</div>
</div>

<script>
document.addEventListener('alpine:init', () => {

Alpine.data('eventForm', () => ({
formatNumber(val) {
return new Intl.NumberFormat('en-PH',{
minimumFractionDigits:2,
maximumFractionDigits:2
}).format(val || 0);
}
}));

/* ================= LOGISTICS FIX ================= */
Alpine.data('logisticsRepeater', () => ({
rows: {!! json_encode(
old('logistics_items',
$event->logisticsItems->map(fn($l)=>[
'resource_name'=>$l->description,
'quantity'=>$l->quantity,
'unit_price'=>$l->unit_price
])->values()
)
) !!},

addRow(){ this.rows.push({resource_name:'',quantity:1,unit_price:0}) },
removeRow(i){ if(this.rows.length>1) this.rows.splice(i,1) },
calculateTotal(){
return this.rows.reduce((sum,row)=>{
return sum+(parseFloat(row.quantity||0)*parseFloat(row.unit_price||0))
},0);
}
}));

Alpine.data('custodianRepeater', () => ({
rows: {!! json_encode(
old('custodian_items',
$event->custodianRequests->map(fn($c)=>[
'material_id'=>$c->custodian_material_id,
'quantity'=>$c->quantity
])->values()
)
) !!},
addRow(){ this.rows.push({material_id:'',quantity:1}) },
removeRow(i){ if(this.rows.length>1) this.rows.splice(i,1) }
}));

Alpine.data('committeeRepeater', () => ({
rows: {!! json_encode(
old('committee',
$event->participants->map(fn($p)=>[
'employee_id'=>$p->employee_id,
'role'=>$p->role
])->values()
)
) !!},
addRow(){ this.rows.push({employee_id:'',role:''}) },
removeRow(i){ if(this.rows.length>1) this.rows.splice(i,1) }
}));

});
</script>

</x-app-layout>
