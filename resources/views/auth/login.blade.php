<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <label class="flex items-center gap-2 mt-2 bg-white shadow-sm input input-bordered border-slate-300">
                <x-text-input id="email" class="w-full max-w-xs input-sm" type="email" name="email"
                    :value="old('email')" required autofocus autocomplete="username" />
            </label>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <label class="flex items-center gap-2 mt-2 bg-white shadow-sm input input-bordered border-slate-300">
                <x-text-input id="password" class="w-full max-w-xs input-sm" type="password" name="password" required
                    autocomplete="current-password" />
            </label>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex justify-between mt-4 align-middle ">
            <label for="remember_me" class="inline-flex">
                <input id="remember_me" type="checkbox" name="remember" class="shadow-md checkbox checkbox-sm" />
                <span class="text-sm text-gray-600 ms-2">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="divider max-sm:mt-5">
            OR
        </div>

        <div class="flex flex-row justify-center w-48 gap-2 mx-auto max-sm:flex-col max-sm:w-full">

            <a href="auth/google/redirect" class="w-full text-xl btn btn-outline hover:bg-slate-50">
                <img src="https://www.gstatic.com/images/branding/googlelogo/svg/googlelogo_clr_74x24px.svg"
                    alt="Google Logo" class="w-max h-7">
            </a>

            <a href="auth/github/redirect" class="w-full text-xl btn btn-neutral hover:bg-white hover:text-neutral"><i
                    class="ri-github-fill"></i>
                Github</a>
        </div>

        <div class="flex items-center justify-center w-full py-5 align-middle">
            <span class="text-sm">create an new account <a class="font-semibold text-sky-600 hover:underline"
                    href="{{ route('register') }}">here</a></span>
        </div>

        <div class="flex items-center justify-end mt-4 max-sm:mt-7">
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
