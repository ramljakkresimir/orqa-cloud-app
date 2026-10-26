<x-cloud-auth-layout title="Welcome back" subtitle="Sign in to manage your files securely.">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <div class="cloud-input-wrap">
                <i class="bi bi-person" aria-hidden="true"></i>
                <input id="username" type="text" name="username" value="{{ old('username') }}"
                       class="form-control cloud-input @error('username') is-invalid @enderror"
                       autocomplete="username" required autofocus placeholder="Enter your username">
            </div>
            @error('username') <div class="text-danger small mt-1" role="alert">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <label for="password" class="form-label">Password</label>
                @if (Route::has('password.request'))
                    <a class="cloud-auth-link small mb-2" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <div class="cloud-input-wrap">
                <i class="bi bi-lock" aria-hidden="true"></i>
                <input id="password" type="password" name="password"
                       class="form-control cloud-input @error('password') is-invalid @enderror"
                       required autocomplete="current-password" placeholder="Enter your password">
            </div>
            @error('password') <div class="text-danger small mt-1" role="alert">{{ $message }}</div> @enderror
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember_me" value="1"
                   {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label cloud-muted" for="remember_me">Remember me</label>
        </div>
        <button class="btn btn-primary cloud-auth-submit w-100" type="submit">
            Sign in <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
        </button>
    </form>
    @if (Route::has('register'))
        <p class="cloud-auth-switch">Don't have an account? <a href="{{ route('register') }}">Create an account</a></p>
    @endif
</x-cloud-auth-layout>
