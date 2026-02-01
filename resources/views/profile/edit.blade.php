@extends('adminpages.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Edit Profile</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Profile Information</h2>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="name">Name</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                           type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="username">Username</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                           type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="email">Email</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                           type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="phone">Phone</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                           type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="profile_image">Profile Image</label>
                    @if ($user->profile_image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="w-24 h-24 rounded-lg">
                        </div>
                    @endif
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                           type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                    Update Profile
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Change Password</h2>

            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="current_password">Current Password</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                           type="password" id="current_password" name="current_password" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="password">New Password</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                           type="password" id="password" name="password" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2" for="password_confirmation">Confirm Password</label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                           type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                    Change Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
