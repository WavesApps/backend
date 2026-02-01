@extends('adminpages.layouts.app')

@section('title', 'Create Superstar')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h1>Create New Superstar</h1>
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
        <form action="{{ route('admin.superstars.store') }}" method="POST">
            @csrf

            <!-- User Information Section -->
            <div class="form-section">
                <h2>User Information</h2>
                <p class="section-description">Create a new user account for the superstar</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., John Doe" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" name="username" id="username" class="form-control" 
                               value="{{ old('username') }}" 
                               placeholder="e.g., johndoe" required>
                        @error('username')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" 
                               value="{{ old('email') }}" 
                               placeholder="e.g., john@example.com" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" 
                               value="{{ old('phone') }}" 
                               placeholder="+1 (555) 000-0000">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Minimum 8 characters" required>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                               placeholder="Confirm password" required>
                    </div>
                </div>
            </div>

            <!-- Superstar Profile Section -->
            <div class="form-section">
                <h2>Superstar Profile Details</h2>
                <p class="section-description">Set up the superstar profile information</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="display_name">Display Name <span class="required">*</span></label>
                        <input type="text" name="display_name" id="display_name" class="form-control" 
                               value="{{ old('display_name') }}" 
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
                                   value="{{ old('price_per_hour') }}" 
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
                              placeholder="Write a bio about this superstar...">{{ old('bio') }}</textarea>
                    @error('bio')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="rating">Initial Rating (0-5)</label>
                    <input type="number" name="rating" id="rating" class="form-control" 
                           value="{{ old('rating', '0') }}" 
                           placeholder="0.00" step="0.01" min="0" max="5">
                    @error('rating')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="info-box">
                    <strong>Default Settings:</strong>
                    <ul>
                        <li>Status: <span class="badge badge-success">Active</span></li>
                        <li>Available: <span class="badge badge-success">Yes</span></li>
                        <li>Total Followers: <span class="badge badge-info">0</span></li>
                    </ul>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Create Superstar
                </button>
                <a href="{{ route('admin.superstars.index') }}" class="btn btn-secondary">Cancel</a>
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

    .info-box {
        background-color: #f0f4ff;
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        padding: 15px;
        margin-top: 20px;
    }

    .info-box strong {
        display: block;
        color: #1a202c;
        margin-bottom: 10px;
    }

    .info-box ul {
        margin: 0;
        padding-left: 20px;
        list-style-type: none;
    }

    .info-box li {
        margin: 8px 0;
        color: #4a5568;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-success {
        background-color: #c6f6d5;
        color: #22543d;
    }

    .badge-info {
        background-color: #bee3f8;
        color: #2c5282;
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
