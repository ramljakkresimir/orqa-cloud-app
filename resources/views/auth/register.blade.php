<x-cloud-auth-layout title="Create your account" subtitle="Start managing your files in one secure place.">
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label class="form-label" for="name">First name</label>
                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                       type="text" value="{{ old('name') }}" autocomplete="given-name" required autofocus>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label" for="surname">Last name</label>
                <input class="form-control @error('surname') is-invalid @enderror" id="surname" name="surname"
                       type="text" value="{{ old('surname') }}" autocomplete="family-name" required>
                @error('surname') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control @error('username') is-invalid @enderror" id="username" name="username"
                   type="text" value="{{ old('username') }}" autocomplete="username" required>
            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Email address</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                   type="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                   type="password" autocomplete="new-password" required>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-4">
            <label class="form-label" for="password_confirmation">Confirm password</label>
            <input class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation"
                   name="password_confirmation" type="password" autocomplete="new-password" required>
            @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-primary cloud-auth-submit w-100" type="submit">
            Create account <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
        </button>
    </form>
    <p class="cloud-auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</x-cloud-auth-layout>
