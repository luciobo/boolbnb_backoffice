@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row py-4">
            <div class="col col-md-6 m-auto d-flex justify-content-center">
                <div class="unauthorized_img">
                    <img src="{{ url('/no-trespassing.jpg') }}" alt="no trespassing citizen kane" class="img-fluid">
                </div>
            </div>
        </div>
        <h2 class="text-center">{{ $message }}</h2>
        <div class="d-none align-items-center loader">
            <strong>Redirecting...</strong>
            <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                const loader = document.querySelector('.loader');
                loader.classList.replace('d-none', 'd-flex');
            }, 1000);
            setTimeout(() => {
                window.location.href = "{{ url('user/dashboard') }}"
            }, 2000);
        }
    </script>
@endsection
