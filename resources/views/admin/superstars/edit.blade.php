@extends('adminpages.layouts.app')

@section('title', 'Edit Superstar')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h1>Edit Superstar</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <h4>Validation Errors:</h4>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('admin.superstars.update', $superstar) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h2>User Information</h2>
                <p class="section-description">Change the user linked to this superstar profile</p>

                <div class="form-group">
                    <label for="user_id">User <span class="required">*</span></label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">-- Select User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" 
                                {{ old('user_id', $superstar->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h2>Superstar Profile Details</h2>
                <p class="section-description">Update the superstar profile information</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="display_name">Display Name <span class="required">*</span></label>
                        <input type="text" name="display_name" id="display_name" class="form-control" 
                               value="{{ old('display_name', $superstar->display_name) }}" 
                               placeholder="e.g., DJ Pro Max" required>
                        @error('display_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price_per_hour">Price Per Hour</label>
                        <div class="input-with-icon">
                            <span class="currency-icon">$</span>
                            <input type="number" name="price_per_hour" id="price_per_hour" class="form-control" 
                                   value="{{ old('price_per_hour', $superstar->price_per_hour) }}" 
                                   placeholder="0.00" step="0.01" min="0">
                        </div>
                        @error('price_per_hour')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea name="bio" id="bio" class="form-control" rows="4" 
                              placeholder="Write a bio about this superstar...">{{ old('bio', $superstar->bio) }}</textarea>
                    @error('bio')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="rating">Rating (0-5)</label>
                        <input type="number" name="rating" id="rating" class="form-control" 
                               value="{{ old('rating', $superstar->rating) }}" 
                               placeholder="0.00" step="0.01" min="0" max="5">
                        @error('rating')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="total_followers">Total Followers</label>
                        <input type="number" name="total_followers" id="total_followers" class="form-control" 
                               value="{{ old('total_followers', $superstar->total_followers) }}" 
                               placeholder="0" min="0">
                        @error('total_followers')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2>Status & Availability</h2>
                <p class="section-description">Manage the superstar's status and availability</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Status <span class="required">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active" {{ old('status', $superstar->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $superstar->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $superstar->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_available" id="is_available" value="1"
                                {{ old('is_available', $superstar->is_available) ? 'checked' : '' }}>
                            <span>Available for Sessions</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Superstar</button>
                <a href="{{ route('admin.superstars.show', $superstar) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
    .main-content {
        padding: 30px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a202c;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background-color: #fed7d7;
        color: #742a2a;
        border: 1px solid #fc8181;
    }

    .alert-danger h4 {
        margin-bottom: 10px;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }

    .form-container {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .form-section {
        margin-bottom: 35px;
        padding-bottom: 35px;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .form-section h2 {
        font-size: 18px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 8px;
    }

    .section-description {
        color: #718096;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: #2d3748;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .required {
        color: #f56565;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .currency-icon {
        position: absolute;
        left: 12px;
        color: #718096;
        font-weight: 500;
    }

    .input-with-icon .form-control {
        padding-left: 30px;
    }

    .error-message {
        display: block;
        color: #f56565;
        font-size: 12px;
        margin-top: 5px;
    }

    .checkbox-group {
        display: flex;
        align-items: flex-end;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        font-weight: normal;
        margin-bottom: 0;
        cursor: pointer;
    }

    .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 35px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background-color: #5a67d8;
    }

    .btn-secondary {
        background-color: #e2e8f0;
        color: #4a5568;
    }

    .btn-secondary:hover {
        background-color: #cbd5e0;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
