@extends('layouts.auth')

@section('title', 'Login')

@section('login')
<form action="{{ route('login') }}" method="POST" role="form" class="text-start">
    @csrf

    <div class="mb-3">
        <div class="input-group input-group-outline @if (old('email')) is-filled @endif @error('email') is-invalid @enderror">
            <label for="email" class="form-label">Masukkan Email</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="{{ old('email') }}" required>
        </div>
        @error('email')
        <div class="text-danger text-xs ps-1 mt-1">{{ $message }}</div>
        @enderror  </div>
    <div class="mb-3">
        <div class="input-group input-group-outline position-relative @error('password') is-invalid @enderror">
            <label for="password" class="form-label">Masukkan Password</label>

            <input type="password" id="password" name="password"
                   class="form-control" style="padding-right: 40px;" required>

            <span id="togglePassword"
                  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 100;">
                <span class="material-symbols-rounded" style="font-size: 20px; vertical-align: middle;">
                    visibility_off
                </span>
            </span>
        </div>

        @error('password')
        <div class="text-danger text-xs ps-1 mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-check form-switch d-flex align-items-center mb-3">
        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" checked>
        <label class="form-check-label mb-0 ms-3" for="rememberMe">Ingat saya</label>
    </div>
    <div class="text-center">
        <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Login</button>
    </div>
</form>
@endsection
