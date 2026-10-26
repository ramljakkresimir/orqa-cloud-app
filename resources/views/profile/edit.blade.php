<x-cloud-layout title="My Profile">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <div class="cloud-section-eyebrow mb-2">ACCOUNT SETTINGS</div>
            <h1 class="h2 fw-bold mb-1">My profile</h1>
            <p class="cloud-muted mb-0">Manage your information and account security.</p>
        </div>
    </div>

    @if ($user->hasRole('user'))
        <section class="card cloud-card border-0 mb-4" aria-label="My storage">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="cloud-file-icon"><i class="bi bi-cloud-check" aria-hidden="true"></i></span>
                    <div><h2 class="h5 mb-1">My Storage</h2><p class="cloud-muted mb-0 small">Your personal cloud storage quota</p></div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-4"><div class="text-secondary small">Total</div><strong>{{ number_format($totalBytes / (1024 ** 3), 2) }} GB</strong></div>
                    <div class="col-12 col-sm-4"><div class="text-secondary small">Used</div><strong>{{ number_format($usedBytes / (1024 ** 2), 2) }} MB</strong></div>
                    <div class="col-12 col-sm-4"><div class="text-secondary small">Free</div><strong class="text-success">{{ number_format($remainingBytes / (1024 ** 3), 2) }} GB</strong></div>
                </div>
                <div class="progress" role="progressbar" aria-label="Storage usage" aria-valuenow="{{ $usedPercentage }}" aria-valuemin="0" aria-valuemax="100" style="height: 10px;">
                    <div class="progress-bar bg-primary" style="width: {{ $usedPercentage }}%"></div>
                </div>
                <p class="small text-secondary mb-0 mt-2">{{ $usedPercentage }}% used</p>
            </div>
        </section>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <section class="card cloud-card border-0 h-100" aria-labelledby="profile-info-heading">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold" id="profile-info-heading">Profile Information</h2>
                    <p class="cloud-muted small">Update your name, surname, address and email.</p>
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success" role="status">Profile saved successfully.</div>
                    @endif
                    <form method="POST" action="{{ route('profile.update') }}" class="mt-3">
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="given-name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="surname" class="form-label">Surname</label>
                                <input id="surname" name="surname" class="form-control @error('surname') is-invalid @enderror" value="{{ old('surname', $user->surname) }}" maxlength="255" autocomplete="family-name">
                                @error('surname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <input id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->address) }}" maxlength="255" autocomplete="street-address">
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="username" class="form-label">Username</label>
                                <input id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required minlength="3" maxlength="50" autocomplete="username" aria-describedby="usernameHelp">
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div id="usernameHelp" class="form-text">3–50 characters; letters, numbers, dashes and underscores. Use your new username next time you log in.</div>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="form-text text-warning">Your email address is unverified.</div>
                                @endif
                            </div>
                            <div class="col-12"><button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2-circle me-2" aria-hidden="true"></i>Save Changes</button></div>
                        </div>
                    </form>
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <form method="POST" action="{{ route('verification.send') }}" class="mt-3">@csrf<button type="submit" class="btn btn-link p-0">Resend verification email</button></form>
                        @if (session('status') === 'verification-link-sent') <p class="text-success small mt-2">Verification link sent.</p> @endif
                    @endif
                </div>
            </section>
        </div>
        <div class="col-12 col-xl-5">
            <section class="card cloud-card border-0 mb-4" aria-labelledby="password-heading">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold" id="password-heading">Update Password</h2>
                    <p class="cloud-muted small">Use a strong password for your account.</p>
                    @if (session('status') === 'password-updated') <div class="alert alert-success">Password updated.</div> @endif
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3"><label for="current_password" class="form-label">Current Password</label><input type="password" id="current_password" name="current_password" autocomplete="current-password" class="form-control @if ($errors->updatePassword->has('current_password')) is-invalid @endif">@foreach ($errors->updatePassword->get('current_password') as $error)<div class="invalid-feedback">{{ $error }}</div>@endforeach</div>
                        <div class="mb-3"><label for="password" class="form-label">New Password</label><input type="password" id="password" name="password" autocomplete="new-password" class="form-control @if ($errors->updatePassword->has('password')) is-invalid @endif">@foreach ($errors->updatePassword->get('password') as $error)<div class="invalid-feedback">{{ $error }}</div>@endforeach</div>
                        <div class="mb-3"><label for="password_confirmation" class="form-label">Confirm Password</label><input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="form-control"></div>
                        <button type="submit" class="btn btn-outline-primary">Update Password</button>
                    </form>
                </div>
            </section>
            <section class="card cloud-card border-0" aria-labelledby="delete-heading">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold text-danger" id="delete-heading">Delete Account</h2>
                    <p class="cloud-muted small">This action permanently deletes your account. Download anything you need first.</p>
                    <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Delete Account</button>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf @method('DELETE')
                <div class="modal-header"><h2 class="modal-title fs-5" id="deleteAccountTitle">Confirm account deletion</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <p>Are you sure? This action cannot be undone. Enter your password to confirm.</p>
                    <label for="delete_password" class="form-label">Password</label>
                    <input id="delete_password" name="password" type="password" autocomplete="current-password" class="form-control @if ($errors->userDeletion->has('password')) is-invalid @endif">
                    @foreach ($errors->userDeletion->get('password') as $error)<div class="invalid-feedback">{{ $error }}</div>@endforeach
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete Account</button></div>
            </form>
        </div></div>
    </div>
    @if ($errors->userDeletion->isNotEmpty())
        <script>document.addEventListener('DOMContentLoaded', () => { document.querySelector('[data-bs-target="#deleteAccountModal"]')?.click(); });</script>
    @endif
</x-cloud-layout>
