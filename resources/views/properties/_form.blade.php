@php
    $p = $property ?? null;
    $old = fn($key, $default = '') => old($key, $default);

    $selectedAmenities = old('amenities', $p ? $p->amenities->pluck('id')->toArray() : []);
    $selectedUtilities = old('utilities', $p ? $p->utilities->pluck('id')->toArray() : []);
@endphp

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-house-door"></i> Basic Information
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Title (Category) *</label>
            <select name="property_category_id"
                    class="form-control @error('property_category_id') is-invalid @enderror"
                    required>
                <option value="">— Select a title —</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            @selected(old('property_category_id', $p->property_category_id ?? '') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('property_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Unit *</label>
            <input type="text"
                   name="unit_type"
                   maxlength="100"
                   class="form-control @error('unit_type') is-invalid @enderror"
                   value="{{ old('unit_type', $p->unit_type ?? '') }}"
                   placeholder="e.g. Apartment 302, Suite B, Unit 5B"
                   required>
            @error('unit_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Address *</label>
            <input type="text"
                   name="address"
                   class="form-control @error('address') is-invalid @enderror"
                   value="{{ old('address', $p->address ?? '') }}"
                   placeholder="e.g. 100 Beacon St"
                   required>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">ZIP Code * <small style="color:var(--brand-muted);font-weight:400;">(5 digits)</small></label>
            <input type="text"
                   name="zip_code"
                   inputmode="numeric"
                   pattern="\d{5}"
                   maxlength="5"
                   class="form-control @error('zip_code') is-invalid @enderror"
                   value="{{ old('zip_code', $p->zip_code ?? '') }}"
                   placeholder="00000"
                   required>
            @error('zip_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">City</label>
            <input type="text"
                   name="city"
                   class="form-control @error('city') is-invalid @enderror"
                   value="{{ old('city', $p->city ?? '') }}"
                   placeholder="e.g. Boston">
            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">State</label>
            <input type="text"
                   name="state"
                   class="form-control @error('state') is-invalid @enderror"
                   value="{{ old('state', $p->state ?? '') }}"
                   placeholder="e.g. MA">
            @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-rulers"></i> Unit Details
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Bathrooms *</label>
            <input type="text"
                   name="bathrooms"
                   inputmode="decimal"
                   class="form-control @error('bathrooms') is-invalid @enderror"
                   value="{{ old('bathrooms', $p->bathrooms ?? '') }}"
                   placeholder="e.g. 1, 1.5, 2"
                   required>
            <small class="text-muted">Decimals allowed (1.5, 2.5, etc.)</small>
            @error('bathrooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Square Feet *</label>
            <input type="number"
                   name="square_feet"
                   min="100"
                   class="form-control @error('square_feet') is-invalid @enderror"
                   value="{{ old('square_feet', $p->square_feet ?? '') }}"
                   placeholder="Minimum 100"
                   required>
            @error('square_feet')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Virtual Tour URL</label>
            <input type="url"
                   name="virtual_tour_url"
                   maxlength="500"
                   class="form-control @error('virtual_tour_url') is-invalid @enderror"
                   value="{{ old('virtual_tour_url', $p->virtual_tour_url ?? '') }}"
                   placeholder="https://...">
            @error('virtual_tour_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-cash-stack"></i> Pricing & Lease
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Lease Term *</label>
            <select name="lease_term_id"
                    class="form-control @error('lease_term_id') is-invalid @enderror"
                    required>
                <option value="">— Select a lease term —</option>
                @foreach($leaseTerms as $opt)
                    <option value="{{ $opt->id }}"
                            @selected(old('lease_term_id', $p->lease_term_id ?? '') == $opt->id)>
                        {{ $opt->name }}
                    </option>
                @endforeach
            </select>
            @error('lease_term_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Rent (USD) *</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number"
                       name="rent"
                       step="0.01"
                       min="0"
                       class="form-control @error('rent') is-invalid @enderror"
                       value="{{ old('rent', $p->rent ?? '') }}"
                       placeholder="0.00"
                       required>
            </div>
            @error('rent')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Deposit (USD) *</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number"
                       name="deposit"
                       step="0.01"
                       min="0"
                       class="form-control @error('deposit') is-invalid @enderror"
                       value="{{ old('deposit', $p->deposit ?? '') }}"
                       placeholder="0.00"
                       required>
            </div>
            @error('deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Date Available for Move-in *</label>
            <input type="text"
                   name="available_date"
                   placeholder="MM/DD/YYYY"
                   maxlength="10"
                   class="form-control @error('available_date') is-invalid @enderror"
                   value="{{ old('available_date', $p && $p->available_date ? $p->available_date->format('m/d/Y') : '') }}"
                   required>
            @error('available_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-stars"></i> Amenities
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Parking Type</label>
            <select name="parking_type_id"
                    class="form-control @error('parking_type_id') is-invalid @enderror">
                <option value="">— Select —</option>
                @foreach($parkingTypes as $opt)
                    <option value="{{ $opt->id }}"
                            @selected(old('parking_type_id', $p->parking_type_id ?? '') == $opt->id)>
                        {{ $opt->name }}
                    </option>
                @endforeach
            </select>
            @error('parking_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Parking Spot</label>
            <input type="text"
                   name="parking_spot"
                   inputmode="numeric"
                   maxlength="20"
                   class="form-control @error('parking_spot') is-invalid @enderror"
                   value="{{ old('parking_spot', $p->parking_spot ?? '') }}"
                   placeholder="Numbers only">
            @error('parking_spot')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Cooling System</label>
            <select name="cooling_system_id"
                    class="form-control @error('cooling_system_id') is-invalid @enderror">
                <option value="">— Select —</option>
                @foreach($coolingSystems as $opt)
                    <option value="{{ $opt->id }}"
                            @selected(old('cooling_system_id', $p->cooling_system_id ?? '') == $opt->id)>
                        {{ $opt->name }}
                    </option>
                @endforeach
            </select>
            @error('cooling_system_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Heating System</label>
            <select name="heating_system_id"
                    class="form-control @error('heating_system_id') is-invalid @enderror">
                <option value="">— Select —</option>
                @foreach($heatingSystems as $opt)
                    <option value="{{ $opt->id }}"
                            @selected(old('heating_system_id', $p->heating_system_id ?? '') == $opt->id)>
                        {{ $opt->name }}
                    </option>
                @endforeach
            </select>
            @error('heating_system_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">Laundry</label>
            <select name="laundry_id"
                    class="form-control @error('laundry_id') is-invalid @enderror">
                <option value="">— Select —</option>
                @foreach($laundryOptions as $opt)
                    <option value="{{ $opt->id }}"
                            @selected(old('laundry_id', $p->laundry_id ?? '') == $opt->id)>
                        {{ $opt->name }}
                    </option>
                @endforeach
            </select>
            @error('laundry_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Other Amenities</label>
        <div class="chip-grid">
            @foreach($amenities as $am)
                <label class="chip">
                    <input type="checkbox"
                           name="amenities[]"
                           value="{{ $am->id }}"
                           @checked(in_array($am->id, $selectedAmenities))>
                    @if($am->icon)<i class="bi {{ $am->icon }}"></i>@endif
                    <span>{{ $am->name }}</span>
                </label>
            @endforeach
        </div>
        @error('amenities.*')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-plug"></i> Utilities Included
    </div>

    <div class="form-group">
        <div class="chip-grid">
            @foreach($utilities as $util)
                <label class="chip">
                    <input type="checkbox"
                           name="utilities[]"
                           value="{{ $util->id }}"
                           @checked(in_array($util->id, $selectedUtilities))>
                    @if($util->icon)<i class="bi {{ $util->icon }}"></i>@endif
                    <span>{{ $util->name }}</span>
                </label>
            @endforeach
        </div>
        @error('utilities.*')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-github"></i> Pet Policy
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="toggle-row">
                <input type="hidden" name="pet_friendly" value="0">
                <input type="checkbox"
                       name="pet_friendly"
                       value="1"
                       id="petFriendly"
                       @checked(old('pet_friendly', $p->pet_friendly ?? false))>
                <span class="toggle-switch"></span>
                <span>Pet Friendly</span>
            </label>
        </div>

        <div class="form-group" style="grid-column: span 1;" id="maxPetsWrap">
            <label class="form-label">Max number of pets</label>
            <input type="number"
                   name="max_pets"
                   min="0"
                   max="20"
                   class="form-control @error('max_pets') is-invalid @enderror"
                   value="{{ old('max_pets', $p->max_pets ?? 0) }}">
            @error('max_pets')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="toggle-row">
                <input type="hidden" name="allow_cats" value="0">
                <input type="checkbox"
                       name="allow_cats"
                       value="1"
                       @checked(old('allow_cats', $p->allow_cats ?? false))>
                <span class="toggle-switch"></span>
                <span>Allow Cats</span>
            </label>
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="toggle-row">
                <input type="hidden" name="allow_small_dogs" value="0">
                <input type="checkbox"
                       name="allow_small_dogs"
                       value="1"
                       id="allowSmallDogs"
                       @checked(old('allow_small_dogs', $p->allow_small_dogs ?? false))>
                <span class="toggle-switch"></span>
                <span>Allow Small Dogs</span>
            </label>
        </div>

        <div class="form-group" style="grid-column: span 1;" id="smallDogWeightWrap">
            <label class="form-label">Small dog max weight (lbs)</label>
            <input type="number"
                   name="small_dog_max_weight"
                   min="1"
                   max="200"
                   class="form-control @error('small_dog_max_weight') is-invalid @enderror"
                   value="{{ old('small_dog_max_weight', $p->small_dog_max_weight ?? '') }}"
                   placeholder="e.g. 25">
            @error('small_dog_max_weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="toggle-row">
                <input type="hidden" name="allow_large_dogs" value="0">
                <input type="checkbox"
                       name="allow_large_dogs"
                       value="1"
                       @checked(old('allow_large_dogs', $p->allow_large_dogs ?? false))>
                <span class="toggle-switch"></span>
                <span>Allow Large Dogs</span>
            </label>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-clipboard-check"></i> Screening Criteria
    </div>

    <div class="row">
        <div class="form-group" style="grid-column: span 1;">
            <label class="form-label">
                Require an income to rent ratio of at least
                <input type="number"
                       name="income_to_rent_ratio"
                       min="1"
                       max="20"
                       class="form-control inline-number @error('income_to_rent_ratio') is-invalid @enderror"
                       value="{{ old('income_to_rent_ratio', $p->income_to_rent_ratio ?? '') }}"
                       placeholder="—">
                times rent
            </label>
            <small class="text-muted">Leave empty for no requirement.</small>
            @error('income_to_rent_ratio')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="grid-column: span 1;">
            <label class="toggle-row">
                <input type="hidden" name="allow_eviction_older_than_5_years" value="0">
                <input type="checkbox"
                       name="allow_eviction_older_than_5_years"
                       value="1"
                       @checked(old('allow_eviction_older_than_5_years', $p->allow_eviction_older_than_5_years ?? false))>
                <span class="toggle-switch"></span>
                <span>Allow leads with an eviction, but only if the eviction is more than 5 years old</span>
            </label>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-text-paragraph"></i> Description & Restrictions
    </div>

    <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description"
                  rows="4"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="Describe the property...">{{ old('description', $p->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label class="form-label">Restrictions</label>
        <textarea name="restrictions"
                  rows="3"
                  class="form-control @error('restrictions') is-invalid @enderror"
                  placeholder="e.g. No smoking, no parties, minimum 6 months...">{{ old('restrictions', $p->restrictions ?? '') }}</textarea>
        @error('restrictions')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label class="form-label">Status *</label>
        <select name="status"
                class="form-control @error('status') is-invalid @enderror"
                required>
            @php $st = old('status', $p->status ?? 'active'); @endphp
            <option value="active"   @selected($st === 'active')>Active</option>
            <option value="inactive" @selected($st === 'inactive')>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     IMAGES
     ═══════════════════════════════════════════════════════ --}}
<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-images"></i> Images
    </div>

    @if($p && $p->images->isNotEmpty())
        <div class="existing-images">
            @foreach($p->images as $img)
                <label class="existing-image">
                    <img src="{{ $img->image_url }}" alt="">
                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}">
                    <span class="existing-image-overlay">
                        <i class="bi bi-trash"></i> Delete
                    </span>
                    @if($img->is_primary)
                        <span class="existing-image-primary">Primary</span>
                    @endif
                </label>
            @endforeach
        </div>
        <small class="text-muted" style="display:block;margin-bottom:1rem;">
            Check the images you want to delete and save.
        </small>
    @endif

    <div class="form-group">
        <label class="form-label">{{ $p ? 'Add images' : 'Images' }}</label>
        <div class="file-upload-box" id="imagesUploadBox">
            <input type="file"
                   name="images[]"
                   id="imagesInput"
                   multiple
                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
            <i class="bi bi-cloud-arrow-up file-upload-icon"></i>
            <div class="file-upload-title">Click to upload or drag & drop</div>
            <div class="file-upload-subtitle">
                <strong>JPG, PNG, GIF, WEBP</strong> — Max 5MB per image
            </div>
            <div class="file-upload-files" id="imagesFileList"></div>
        </div>
        @error('images.*')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     VIDEOS
     ═══════════════════════════════════════════════════════ --}}
<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-camera-video"></i> Videos
    </div>

    @if($p && $p->videos->isNotEmpty())
        <div class="existing-images">
            @foreach($p->videos as $vid)
                <label class="existing-image">
                    <video src="{{ $vid->video_url }}" muted></video>
                    <input type="checkbox" name="delete_videos[]" value="{{ $vid->id }}">
                    <span class="existing-image-overlay">
                        <i class="bi bi-trash"></i> Delete
                    </span>
                </label>
            @endforeach
        </div>
        <small class="text-muted" style="display:block;margin-bottom:1rem;">
            Check the videos you want to delete and save.
        </small>
    @endif

    <div class="form-group">
        <label class="form-label">{{ $p ? 'Add videos' : 'Videos' }}</label>
        <div class="file-upload-box" id="videosUploadBox">
            <input type="file"
                   name="videos[]"
                   id="videosInput"
                   multiple
                   accept="video/mp4,video/webm,video/quicktime">
            <i class="bi bi-camera-video file-upload-icon"></i>
            <div class="file-upload-title">Click to upload or drag & drop</div>
            <div class="file-upload-subtitle">
                <strong>MP4, WEBM, MOV</strong> — Max 100MB per video
            </div>
            <div class="file-upload-files" id="videosFileList"></div>
        </div>
        @error('videos.*')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
    </div>
</div>