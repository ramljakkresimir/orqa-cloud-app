<x-cloud-layout title="User Storage">
    <div class="mb-4">
        <div class="cloud-section-eyebrow mb-2">ADMINISTRATION</div>
        <h1 class="h2 fw-bold mb-1">User Storage</h1>
        <p class="cloud-muted mb-0">Storage quota and usage for users with the user role.</p>
    </div>
    <section class="card cloud-card border-0" aria-label="User storage overview">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                <h2 class="h5 fw-semibold mb-0">Users</h2>
                <span class="cloud-count-pill">{{ $users->total() }} users</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle cloud-dashboard-table mb-0">
                    <thead><tr><th>Username</th><th>Full Name</th><th>Total Space</th><th>Used Space</th><th>Free Space</th><th>Usage</th></tr></thead>
                    <tbody>
                    @forelse ($users as $user)
                        @php
                            $total = (int) $user->quota_bytes;
                            $used = (int) ($user->used_bytes ?? 0);
                            $free = max(0, $total - $used);
                            $percent = $total > 0 ? min(100, round($used / $total * 100, 1)) : 0;
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $user->username }}</td>
                            <td>{{ trim($user->name . ' ' . ($user->surname ?? '')) }}</td>
                            <td class="text-nowrap">{{ number_format($total / (1024 ** 3), 2) }} GB</td>
                            <td class="text-nowrap">{{ number_format($used / (1024 ** 2), 2) }} MB</td>
                            <td class="text-nowrap text-success fw-semibold">{{ number_format($free / (1024 ** 3), 2) }} GB</td>
                            <td style="min-width:140px">
                                <div class="progress" role="progressbar" aria-label="Storage usage for {{ $user->username }}" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="height:8px"><div class="progress-bar bg-primary" style="width:{{ $percent }}%"></div></div>
                                <small class="text-secondary">{{ $percent }}%</small>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-4">No users found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links('pagination::bootstrap-5') }}</div>
        </div>
    </section>
</x-cloud-layout>
