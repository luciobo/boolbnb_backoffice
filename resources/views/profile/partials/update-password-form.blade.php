<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Modifica password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Assicurati che la tua password sia lunga e complicata per evitare problemi.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="mb-2">
            <label for="current_password">{{ __('Passoword corrente') }}</label>
            <input class="mt-1 form-control" type="password" name="current_password" id="current_password"
                autocomplete="current-password">
            @error('current_password')
                <span class="invalid-feedback mt-2" role="alert">
                    <strong>{{ $errors->updatePassword->get('current_password') }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-2">
            <label for="password">{{ __('Nuova password') }}</label>
            <input class="mt-1 form-control" type="password" name="password" id="password" autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback mt-2" role="alert">
                    <strong>{{ $errors->updatePassword->get('password') }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-2">

            <label for="password_confirmation">{{ __('Conferma password') }}</label>
            <input class="mt-2 form-control" type="password" name="password_confirmation" id="password_confirmation"
                autocomplete="new-password">
            @error('password_confirmation')
                <span class="invalid-feedback mt-2" role="alert">
                    <strong>{{ $errors->updatePassword->get('password_confirmation') }}</strong>
                </span>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-4">
            <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>

            @if (session('status') === 'password-updated')
                <script>
                    const show = true;
                    setTimeout(() => show = false, 2000)
                    const el = document.getElementById('status')
                    if (show) {
                        el.style.display = 'block';
                    }
                </script>
                <p id='profile-status' class="fs-5 m-0 text-success"><i
                        class="fa-solid text-success me-2 fa-circle-check"></i>Operazione effettuata.</p>
            @endif
        </div>
    </form>
</section>
