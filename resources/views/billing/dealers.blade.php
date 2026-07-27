@extends('layouts.app')

@section('title', 'Dealer Accounts Directory - BioFriends Synergy Solutions')

@section('content')
<div class="space-y-6">
    
    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('billing.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">Billing</a>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Dealer Directory</span>
            </div>
            <h1 class="text-2xl font-heading font-extrabold text-slate-900 tracking-tight mt-1">Dealer Accounts & Customer Directory</h1>
            <p class="text-slate-500 text-sm mt-0.5">Manage distributor profiles, contact details, and account histories.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="openModal('addDealerModal')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>+ Register New Dealer</span>
            </button>
        </div>
    </div>

    <!-- Dealer Directory Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($dealers as $d)
            <div class="kb-card rounded-2xl p-6 space-y-4 flex flex-col justify-between hover:border-blue-300 transition">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                            🏢
                        </div>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            {{ $d->sales_count }} Bills Issued
                        </span>
                    </div>
                    <div>
                        <h3 class="text-base font-heading font-bold text-slate-900">{{ $d->name }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $d->contact_info ?? 'No phone/email recorded' }}</p>
                    </div>
                    @if($d->address)
                        <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200 whitespace-pre-line">{{ $d->address }}</p>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <button onclick="openEditDealerModal({{ $d->id }}, '{{ addslashes($d->name) }}', '{{ addslashes($d->contact_info ?? '') }}', '{{ addslashes($d->address ?? '') }}')" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1 rounded-lg border border-blue-200 font-bold">Edit Profile</button>
                    <form action="{{ route('billing.dealers.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Delete dealer {{ addslashes($d->name) }} and related sales history?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 px-3 py-1 rounded-lg border border-rose-200 font-bold">Delete Account</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full kb-card rounded-2xl p-12 text-center text-slate-500">
                No dealers registered yet. Click "+ Register New Dealer" to add one.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal: Register Dealer -->
<div id="addDealerModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 text-slate-900">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-heading font-bold text-lg text-slate-900">+ Register Dealer / Customer</h3>
            <button onclick="closeModal('addDealerModal')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form action="{{ route('billing.dealers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Dealer Name</label>
                <input type="text" name="name" required placeholder="e.g. AgriTech Bio Distributors Ltd" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Contact Info</label>
                <input type="text" name="contact_info" placeholder="+91 98765 43210 | sales@agritech.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Billing Address</label>
                <textarea name="address" rows="2" placeholder="Corporate address details..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeModal('addDealerModal')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-sm">Save Dealer Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Dealer -->
<div id="editDealerModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 text-slate-900">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-heading font-bold text-lg text-slate-900">Edit Dealer Details</h3>
            <button onclick="closeModal('editDealerModal')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="editDealerForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Dealer Name</label>
                <input type="text" name="name" id="edit_dealer_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Contact Info</label>
                <input type="text" name="contact_info" id="edit_dealer_contact" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Address</label>
                <textarea name="address" id="edit_dealer_address" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeModal('editDealerModal')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-sm">Update Dealer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditDealerModal(id, name, contact, address) {
        document.getElementById('editDealerForm').action = '/billing/dealers/' + id;
        document.getElementById('edit_dealer_name').value = name;
        document.getElementById('edit_dealer_contact').value = contact;
        document.getElementById('edit_dealer_address').value = address;
        document.getElementById('editDealerModal').classList.remove('hidden');
    }
</script>
@endsection
