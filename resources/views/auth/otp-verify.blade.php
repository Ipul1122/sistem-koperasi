<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 font-body">
        Masukkan 6 digit kode OTP yang telah dikirim ke email: <b>{{ session('email') }}</b>
    </div>

    @if ($errors->any())
        <div class="mb-4 text-red-600 text-sm font-body">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.process') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">

        <div>
            <x-input-label for="otp" value="Kode OTP" class="font-heading" />
            <x-text-input id="otp" class="block mt-1 w-full font-body tracking-widest text-center" type="text" name="otp" required autofocus />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="font-heading">
                Verifikasi & Login
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>