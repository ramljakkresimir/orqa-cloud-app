<x-cloud-auth-layout title="Confirm password" subtitle="Please confirm your password to continue to this secure area.">
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-4">
            <label for="password" class="form-label">Current password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required autofocus>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary cloud-auth-submit w-100">Confirm password</button>
    </form>
</x-cloud-auth-layout>
