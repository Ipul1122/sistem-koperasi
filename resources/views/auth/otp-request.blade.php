<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 font-body">
        Masukkan email yang terdaftar. Kami akan mengirimkan 6 digit kode OTP.
    </div>

    @if ($errors->any())
        <div class="mb-4 text-red-600 text-sm font-body">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.send') }}">
        @csrf
        <div>
            <x-input-label for="email" value="Email" class="font-heading" />
            <x-text-input id="email" class="block mt-1 w-full font-body" type="email" name="email" :value="old('email')" required autofocus />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="font-heading">
                Kirim OTP
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>