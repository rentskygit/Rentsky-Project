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

    <div class="form-group" style="grid-column: span 1;">
        <label class="form-label">Date Available for Move-in *</label>
        <input type="text"
            name="available_date"
            placeholder="MM/DD/YYYY"
            class="form-control date-input @error('available_date') is-invalid @enderror"
            value="{{ old('available_date', $p && $p->available_date ? $p->available_date->format('m/d/Y') : '') }}"
            required>
        @error('available_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
        <div class="ms-wrapper" data-ms>
            <button type="button" class="ms-trigger" data-ms-trigger>
                <span class="ms-label placeholder" data-ms-label>— Select —</span>
                <i class="bi bi-chevron-down ms-arrow" data-ms-arrow></i>
            </button>
            <div class="ms-menu" data-ms-menu style="display:none;">
                @foreach($amenities as $am)
                    <label class="ms-item {{ in_array($am->id, $selectedAmenities) ? 'checked' : '' }}">
                        <input type="checkbox" name="amenities[]" value="{{ $am->id }}"
                               {{ in_array($am->id, $selectedAmenities) ? 'checked' : '' }}>
                        <span>{{ $am->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-plug"></i> Utilities Included
    </div>

    <div class="form-group">
        <div class="ms-wrapper" data-ms>
            <button type="button" class="ms-trigger" data-ms-trigger>
                <span class="ms-label placeholder" data-ms-label>— Select —</span>
                <i class="bi bi-chevron-down ms-arrow" data-ms-arrow></i>
            </button>
            <div class="ms-menu" data-ms-menu style="display:none;">
                @foreach($utilities as $util)
                    <label class="ms-item {{ in_array($util->id, $selectedUtilities) ? 'checked' : '' }}">
                        <input type="checkbox" name="utilities[]" value="{{ $util->id }}"
                               {{ in_array($util->id, $selectedUtilities) ? 'checked' : '' }}>
                        <span>{{ $util->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
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
                <span>Allow leads with an eviction, but only if the eviction is more than 7 years old</span>
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
        <label class="form-label">
            Restrictions
            <small style="color:var(--brand-muted);font-weight:400;">(public — visible to clients)</small>
        </label>
        <textarea name="restrictions"
                  rows="3"
                  class="form-control @error('restrictions') is-invalid @enderror"
                  placeholder="e.g. No smoking, no parties, minimum 6 months...">{{ old('restrictions', $p->restrictions ?? '') }}</textarea>
        @error('restrictions')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label class="form-label">
            Internal Restrictions
            <small style="color:#b91c1c;font-weight:600;">
                <i class="bi bi-lock-fill"></i> private — admin / super admin / adviser only
            </small>
        </label>
        <textarea name="internal_restrictions"
                  rows="3"
                  class="form-control @error('internal_restrictions') is-invalid @enderror"
                  placeholder="Internal notes, screening rules, or restrictions only visible to staff...">{{ old('internal_restrictions', $p->internal_restrictions ?? '') }}</textarea>
        @error('internal_restrictions')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted" style="display:block;margin-top:0.35rem;">
            This field is <strong>never</strong> shown on the public apartment page.
        </small>
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

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-images"></i> Images
        <span style="margin-left:auto;font-size:0.78rem;font-weight:600;color:var(--brand-muted);">
            <span id="imagesCounter">0</span> / 35
        </span>
    </div>

    @if($p && $p->images->isNotEmpty())
        <div class="existing-images" id="existingImages">
            @foreach($p->images as $img)
                <label class="existing-image"
                       data-image-id="{{ $img->id }}"
                       draggable="true">
                    <img src="{{ $img->image_url }}" alt="">
                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="js-delete-image">
                    <span class="existing-image-overlay">
                        <i class="bi bi-trash"></i> Delete
                    </span>
                    <span class="existing-image-drag" title="Drag to reorder">
                        <i class="bi bi-grip-vertical"></i>
                    </span>
                    <span class="existing-image-primary" @if(!$img->is_primary) style="display:none;" @endif>Primary</span>
                </label>
            @endforeach
        </div>
        <small class="text-muted" style="display:block;margin-bottom:1rem;">
            Check the images you want to delete and save. Drag thumbnails to reorder. The <strong>first image</strong> (leftmost) will be the primary image when saved.
        </small>
    @endif

    <div class="form-group">
        <label class="form-label">{{ $p ? 'Add images' : 'Images' }}</label>

        <div class="preview-grid" id="imagesPreview"></div>

        <div class="file-upload-box" id="imagesUploadBox">
            <input type="file"
                   name="images[]"
                   id="imagesInput"
                   multiple
                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                   data-existing="{{ $p ? $p->images->count() : 0 }}"
                   data-max="35">
            <i class="bi bi-cloud-arrow-up file-upload-icon"></i>
            <div class="file-upload-title">Click to upload or drag & drop</div>
            <div class="file-upload-subtitle">
                <strong>JPG, PNG, GIF, WEBP</strong> — Max 5MB per image · up to 35 images total
            </div>
            <div class="file-upload-files" id="imagesFileList"></div>
        </div>
        @error('images.*')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
        @error('images')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
        <small class="text-muted" style="display:block;margin-top:0.5rem;">
            <i class="bi bi-arrows-move"></i>
            Drag preview thumbnails to reorder. New images are appended at the end.
        </small>
    </div>
</div>

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

        <div class="video-preview-grid" id="videosPreview"></div>

        <div class="file-upload-box" id="videosUploadBox">
            <input type="file"
                   name="videos[]"
                   id="videosInput"
                   multiple
                   accept="video/mp4,video/webm,video/quicktime"
                   data-existing="{{ $p ? $p->videos->count() : 0 }}">
            <i class="bi bi-camera-video file-upload-icon"></i>
            <div class="file-upload-title">Click to upload or drag & drop</div>
            <div class="file-upload-subtitle">
                <strong>MP4, WEBM, MOV</strong> — Max 20MB per video
            </div>
            <div class="file-upload-files" id="videosFileList"></div>
        </div>
        @error('videos.*')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
        @error('videos')<div class="invalid-feedback" style="display:block;margin-top:.5rem;">{{ $message }}</div>@enderror
        <small class="text-muted" style="display:block;margin-top:0.5rem;">
            <i class="bi bi-arrows-move"></i>
            Drag preview thumbnails to reorder. New videos are appended at the end.
        </small>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-ms]').forEach(function (ms) {
        const trigger = ms.querySelector('[data-ms-trigger]');
        const menu = ms.querySelector('[data-ms-menu]');
        const label = ms.querySelector('[data-ms-label]');
        const arrow = ms.querySelector('[data-ms-arrow]');
        const checkboxes = ms.querySelectorAll('input[type="checkbox"]');

        function updateLabel() {
            const checked = Array.from(checkboxes).filter(c => c.checked);
            if (checked.length === 0) {
                label.textContent = '— Select —';
                label.classList.add('placeholder');
            } else {
                label.classList.remove('placeholder');
                if (checked.length === 1) label.textContent = checked[0].parentElement.querySelector('span').textContent;
                else if (checked.length === 2) label.textContent = checked.map(c => c.parentElement.querySelector('span').textContent).join(', ');
                else label.textContent = checked.length + ' selected';
            }
            checkboxes.forEach(cb => cb.parentElement.classList.toggle('checked', cb.checked));
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            arrow.classList.toggle('rotated', !isOpen);
        });

        document.addEventListener('click', function (e) {
            if (!ms.contains(e.target)) {
                menu.style.display = 'none';
                arrow.classList.remove('rotated');
            }
        });

        checkboxes.forEach(cb => cb.addEventListener('change', updateLabel));
        updateLabel();
    });

    document.querySelectorAll('input.date-input').forEach(function (el) {
        if (typeof window.flatpickr !== 'function' || el._flatpickr) return;
        window.flatpickr(el, {
            dateFormat: 'm/d/Y',
            allowInput: false,
            disableMobile: true,
            defaultDate: el.value || null,
        });
    });

    (function () {
        const input = document.getElementById('imagesInput');
        const previewGrid = document.getElementById('imagesPreview');
        const counter = document.getElementById('imagesCounter');
        const existingBox = document.getElementById('existingImages');
        const fileList = document.getElementById('imagesFileList');

        if (!input || !previewGrid) return;

        const MAX = parseInt(input.dataset.max || '35', 10);
        const existing = parseInt(input.dataset.existing || '0', 10);
        let pendingFiles = [];
        let dragIdx = null;

        function deletedCount() {
            if (!existingBox) return 0;
            return existingBox.querySelectorAll('.js-delete-image:checked').length;
        }

        function totalCount() {
            return Math.max(0, existing - deletedCount()) + pendingFiles.length;
        }

        function updateCounter() {
            if (counter) {
                counter.textContent = totalCount();
                counter.style.color = totalCount() > MAX ? '#dc2626' : '';
            }
            if (fileList) {
                fileList.innerHTML = pendingFiles.length === 0
                    ? ''
                    : '<span>' + pendingFiles.length + ' new image' + (pendingFiles.length > 1 ? 's' : '') + '</span>';
            }
        }

        function syncInput() {
            const dt = new DataTransfer();
            pendingFiles.forEach(f => dt.items.add(f));
            input.files = dt.files;
        }

        function render() {
            previewGrid.querySelectorAll('img').forEach(img => {
                if (img.src.startsWith('blob:')) URL.revokeObjectURL(img.src);
            });
            previewGrid.innerHTML = '';

            pendingFiles.forEach((file, idx) => {
                const url = URL.createObjectURL(file);
                const item = document.createElement('div');
                item.className = 'preview-item';
                item.draggable = true;
                item.dataset.index = idx;
                item.innerHTML = '<img src="' + url + '" alt="">'
                    + '<span class="preview-order">' + (idx + 1) + '</span>'
                    + '<button type="button" class="preview-remove" data-idx="' + idx + '" aria-label="Remove">&times;</button>';
                previewGrid.appendChild(item);
            });

            wireItemEvents();
            wireRemoveButtons();
            updateCounter();
        }

        function wireItemEvents() {
            previewGrid.querySelectorAll('.preview-item').forEach(item => {
                item.addEventListener('dragstart', e => {
                    dragIdx = parseInt(item.dataset.index, 10);
                    item.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    try { e.dataTransfer.setData('text/plain', String(dragIdx)); } catch (_) {}
                });
                item.addEventListener('dragend', () => {
                    item.classList.remove('dragging');
                    previewGrid.querySelectorAll('.preview-item').forEach(i => i.classList.remove('drag-over'));
                });
                item.addEventListener('dragover', e => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    item.classList.add('drag-over');
                });
                item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
                item.addEventListener('drop', e => {
                    e.preventDefault();
                    item.classList.remove('drag-over');
                    const dropIdx = parseInt(item.dataset.index, 10);
                    if (dragIdx === null || dragIdx === dropIdx) return;
                    const moved = pendingFiles.splice(dragIdx, 1)[0];
                    pendingFiles.splice(dropIdx, 0, moved);
                    dragIdx = null;
                    render();
                    syncInput();
                });
            });
        }

        function wireRemoveButtons() {
            previewGrid.querySelectorAll('.preview-remove').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const idx = parseInt(btn.dataset.idx, 10);
                    pendingFiles.splice(idx, 1);
                    render();
                    syncInput();
                });
            });
        }

        input.addEventListener('change', () => {
            const incoming = Array.from(input.files || []);
            if (incoming.length === 0) return;

            const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            const valid = [];

            for (const f of incoming) {
                if (!allowed.includes(f.type)) {
                    if (window.showAlert) window.showAlert('error', 'Invalid file', f.name + ' is not an allowed image type.');
                    continue;
                }
                if (f.size > 5 * 1024 * 1024) {
                    if (window.showAlert) window.showAlert('error', 'File too large', f.name + ' exceeds 5MB.');
                    continue;
                }
                valid.push(f);
            }

            const currentTotal = Math.max(0, existing - deletedCount()) + pendingFiles.length;
            const room = Math.max(0, MAX - currentTotal);

            if (valid.length > room) {
                if (window.showAlert) window.showAlert('error', 'Too many images', 'A property can have at most ' + MAX + ' images.');
            }

            pendingFiles.push(...valid.slice(0, room));

            input.value = '';
            render();
            syncInput();
        });

        if (existingBox) {
            existingBox.querySelectorAll('.js-delete-image').forEach(cb => {
                cb.addEventListener('change', updateCounter);
            });
        }

        const form = input.closest('form');
        if (form) form.addEventListener('submit', syncInput);

        render();
    })();

    (function () {
        const input = document.getElementById('videosInput');
        const previewGrid = document.getElementById('videosPreview');
        const fileList = document.getElementById('videosFileList');
        if (!input || !previewGrid) return;

        let pendingFiles = [];
        let dragIdx = null;
        const MAX_BYTES = 20 * 1024 * 1024;

        function syncInput() {
            const dt = new DataTransfer();
            pendingFiles.forEach(f => dt.items.add(f));
            input.files = dt.files;
        }

        function updateFileList() {
            if (!fileList) return;
            fileList.innerHTML = pendingFiles.length === 0
                ? ''
                : '<span>' + pendingFiles.length + ' new video' + (pendingFiles.length > 1 ? 's' : '') + '</span>';
        }

        function render() {
            previewGrid.querySelectorAll('video').forEach(v => {
                if (v.src.startsWith('blob:')) URL.revokeObjectURL(v.src);
            });
            previewGrid.innerHTML = '';

            pendingFiles.forEach((file, idx) => {
                const url = URL.createObjectURL(file);
                const item = document.createElement('div');
                item.className = 'video-preview-item';
                item.draggable = true;
                item.dataset.index = idx;
                item.innerHTML = '<video src="' + url + '" muted preload="metadata" playsinline></video>'
                    + '<span class="video-preview-order">' + (idx + 1) + '</span>'
                    + '<button type="button" class="video-preview-remove" data-idx="' + idx + '" aria-label="Remove">&times;</button>';
                previewGrid.appendChild(item);
            });

            wireItemEvents();
            wireRemoveButtons();
            updateFileList();
        }

        function wireItemEvents() {
            previewGrid.querySelectorAll('.video-preview-item').forEach(item => {
                item.addEventListener('dragstart', e => {
                    dragIdx = parseInt(item.dataset.index, 10);
                    item.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    try { e.dataTransfer.setData('text/plain', String(dragIdx)); } catch (_) {}
                });
                item.addEventListener('dragend', () => {
                    item.classList.remove('dragging');
                    previewGrid.querySelectorAll('.video-preview-item').forEach(i => i.classList.remove('drag-over'));
                });
                item.addEventListener('dragover', e => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    item.classList.add('drag-over');
                });
                item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
                item.addEventListener('drop', e => {
                    e.preventDefault();
                    item.classList.remove('drag-over');
                    const dropIdx = parseInt(item.dataset.index, 10);
                    if (dragIdx === null || dragIdx === dropIdx) return;
                    const moved = pendingFiles.splice(dragIdx, 1)[0];
                    pendingFiles.splice(dropIdx, 0, moved);
                    dragIdx = null;
                    render();
                    syncInput();
                });
            });
        }

        function wireRemoveButtons() {
            previewGrid.querySelectorAll('.video-preview-remove').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const idx = parseInt(btn.dataset.idx, 10);
                    pendingFiles.splice(idx, 1);
                    render();
                    syncInput();
                });
            });
        }

        input.addEventListener('change', () => {
            const incoming = Array.from(input.files || []);
            if (incoming.length === 0) return;

            const allowed = ['video/mp4', 'video/webm', 'video/quicktime'];
            const valid = [];

            for (const f of incoming) {
                if (!allowed.includes(f.type)) {
                    if (window.showAlert) window.showAlert('error', 'Invalid video', f.name + ' is not an allowed video type.');
                    continue;
                }
                if (f.size > MAX_BYTES) {
                    if (window.showAlert) window.showAlert('error', 'Video too large', f.name + ' exceeds 20MB.');
                    continue;
                }
                valid.push(f);
            }

            pendingFiles.push(...valid);

            input.value = '';
            render();
            syncInput();
        });

        const form = input.closest('form');
        if (form) form.addEventListener('submit', syncInput);

        render();
    })();

    (function () {
        const existingBox = document.getElementById('existingImages');
        if (!existingBox) return;

        const form = existingBox.closest('form');
        let dragExistingIdx = null;

        function items() {
            return Array.from(existingBox.querySelectorAll('.existing-image'));
        }

        function updatePrimaryBadge() {
            const all = items();
            const firstNonDeleted = all.find(item => {
                const cb = item.querySelector('.js-delete-image');
                return !cb || !cb.checked;
            });

            all.forEach(item => {
                const badge = item.querySelector('.existing-image-primary');
                if (!badge) return;
                badge.style.display = (item === firstNonDeleted) ? '' : 'none';
            });
        }

        function wireExisting() {
            items().forEach((item, idx) => {
                item.dataset.dragIndex = idx;
                item.setAttribute('draggable', 'true');

                item.addEventListener('dragstart', (e) => {
                    dragExistingIdx = idx;
                    item.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    try { e.dataTransfer.setData('text/plain', String(idx)); } catch (_) {}
                });

                item.addEventListener('dragend', () => {
                    item.classList.remove('dragging');
                    items().forEach(i => i.classList.remove('drag-over'));
                });

                item.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    item.classList.add('drag-over');
                });

                item.addEventListener('dragleave', () => item.classList.remove('drag-over'));

                item.addEventListener('drop', (e) => {
                    e.preventDefault();
                    item.classList.remove('drag-over');
                    const dropIdx = parseInt(item.dataset.dragIndex, 10);
                    if (dragExistingIdx === null || dragExistingIdx === dropIdx) return;

                    const all = items();
                    const dragged = all[dragExistingIdx];
                    const target = all[dropIdx];
                    if (!dragged || !target) return;

                    if (dragExistingIdx < dropIdx) {
                        target.parentNode.insertBefore(dragged, target.nextSibling);
                    } else {
                        target.parentNode.insertBefore(dragged, target);
                    }

                    dragExistingIdx = null;
                    wireExisting();
                    updatePrimaryBadge();
                });
            });
        }

        wireExisting();
        updatePrimaryBadge();

        existingBox.querySelectorAll('.js-delete-image').forEach(cb => {
            cb.addEventListener('change', updatePrimaryBadge);
        });

        if (form) {
            form.addEventListener('submit', () => {
                form.querySelectorAll('input[data-image-order]').forEach(el => el.remove());
                items().forEach(item => {
                    const checkbox = item.querySelector('.js-delete-image');
                    if (checkbox && checkbox.checked) return;
                    const id = item.dataset.imageId;
                    if (!id) return;
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'image_order[]';
                    inp.value = id;
                    inp.dataset.imageOrder = '1';
                    form.appendChild(inp);
                });
            });
        }
    })();

});
</script>
@endpush