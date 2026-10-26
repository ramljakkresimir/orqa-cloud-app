<x-cloud-auth-layout title="Forgot password" subtitle="Enter your email address and we'll send you a password reset link.">
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary cloud-auth-submit w-100">Send reset link</button>
        <p class="cloud-auth-switch">Remembered your password? <a href="{{ route('login') }}">Back to login</a></p>
    </form>
</x-cloud-auth-layout>
