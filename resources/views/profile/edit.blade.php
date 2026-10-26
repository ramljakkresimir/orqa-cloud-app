<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"> 
            @if ($user->hasRole('user'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">

                        <h3 class="text-lg font-medium text-gray-900">
                            My Storage
                        </h3>

                        <p class="mt-2 text-sm text-gray-600">
                            Total:
                            {{ number_format($totalBytes / (1024 ** 3), 2) }} GiB
                        </p>

                        <p class="mt-1 text-sm text-gray-600">
                            Used:
                            {{ number_format($usedBytes / (1024 ** 3), 2) }} GiB
                        </p>

                        <p class="mt-1 text-sm text-gray-600">
                            Free:
                            {{ number_format($remainingBytes / (1024 ** 3), 2) }} GiB
                        </p>

                        <div
                            class="w-full bg-gray-200 rounded-full h-3 mt-4"
                            role="progressbar"
                            aria-label="Storage usage"
                            aria-valuenow="{{ $usedPercentage }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        >
                            <div
                                class="bg-green-600 h-3 rounded-full"
                                style="width: {{ $usedPercentage }}%"
                            ></div>
                        </div>

                        <p class="mt-2 text-sm text-gray-600">
                            {{ $usedPercentage }}% used
                        </p>

                    </div>
                </div>
            @endif
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
