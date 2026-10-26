<x-cloud-auth-layout title="Reset password" subtitle="Choose a new password for your account.">
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $request->email) }}" autocomplete="username" required autofocus>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="new-password" required>
            @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary cloud-auth-submit w-100">Reset password</button>
    </form>
</x-cloud-auth-layout>
