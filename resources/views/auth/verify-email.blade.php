<x-cloud-auth-layout title="Verify email" subtitle="Please verify your email address using the link we sent you.">
    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success" role="status">A new verification link has been sent to your email address.</div>
    @endif
    <p class="text-secondary small">If you haven't received the email, you can request another verification link below.</p>
    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-primary cloud-auth-submit w-100">Resend verification email</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="text-center">
        @csrf
        <button type="submit" class="btn btn-link cloud-auth-link text-decoration-none">Log out</button>
    </form>
</x-cloud-auth-layout>
