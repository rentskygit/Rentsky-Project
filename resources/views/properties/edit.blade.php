@extends('layouts.app')

@section('title', 'Edit Property')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit <span>Property</span></h1>
        <a href="{{ route('properties.index') }}" class="btn-create" style="background:#6b7280;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-error" style="background:#fee2e2;color:#991b1b;padding:1rem;border-radius:.5rem;margin-bottom:1rem;">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('properties.update', $property) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('properties._form', [
            'categories'     => $categories,
            'leaseTerms'     => $leaseTerms,
            'parkingTypes'   => $parkingTypes,
            'coolingSystems' => $coolingSystems,
            'heatingSystems' => $heatingSystems,
            'laundryOptions' => $laundryOptions,
            'amenities'      => $amenities,
            'utilities'      => $utilities,
            'property'       => $property,
        ])

        <div style="display:flex; gap:1rem; margin-top:1rem;">
            <button type="submit" class="btn-create">
                <i class="bi bi-check-lg"></i> Update Property
            </button>
            <a href="{{ route('properties.index') }}" class="btn-create" style="background:#6b7280;">
                Cancel
            </a>
        </div>
    </form>
@endsection

@push('styles')
@include('properties._styles')
@endpush

@push('scripts')
<script>
(function () {
    const pet           = document.getElementById('petFriendly');
    const maxPets       = document.getElementById('maxPetsWrap');

    const smallDogs     = document.getElementById('allowSmallDogs');
    const smallDogWeight = document.getElementById('smallDogWeightWrap');

    function togglePet() {
        if (maxPets) maxPets.style.display = pet?.checked ? '' : 'none';
    }

    function toggleSmallDogs() {
        if (smallDogWeight) smallDogWeight.style.display = smallDogs?.checked ? '' : 'none';
    }

    pet?.addEventListener('change', togglePet);
    smallDogs?.addEventListener('change', toggleSmallDogs);

    togglePet();
    toggleSmallDogs();
})();
</script>
@endpush