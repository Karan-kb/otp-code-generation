@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Phone Verification') }}</div>

                    <div class="card-body">
                        @if ($errors && $errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="verify-form" method="POST" action="{{ route('verify_phone_otp', ['phone' => $phone]) }}">
                            @csrf

                            <div class="form-group row">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>
                                <div class="col-md-6">
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ $phone }}" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="otp" class="col-md-4 col-form-label text-md-right">{{ __('OTP') }}</label>
                                <div class="col-md-6">
                                    <input id="otp" type="text" class="form-control" name="otp" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary" id="verify-btn"{{ $remainingTime <= 0 ? ' disabled' : '' }}>
                                        {{ __('Verify') }}
                                    </button>
                                </div>
                            </div>

                            @if ($remainingTime > 0)
                                <div class="form-group row mt-3">
                                    <div class="col-md-6 offset-md-4">
                                        <p>Remaining Time: <span id="countdown">{{ gmdate('i:s', $remainingTime) }}</span></p>
                                    </div>
                                </div>
                            @endif

                            @if ($remainingTime <= 0)
                                <div class="form-group row mt-3">
                                    <div class="col-md-6 offset-md-4">
                                        <p>The remaining time has expired. Please request a new OTP.</p>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($remainingTime > 0)
        <script>
            var remainingTime = {{ $remainingTime }};
            var countdownElement = document.getElementById('countdown');
            var verifyButton = document.getElementById('verify-btn');

            var countdownInterval = setInterval(function() {
                remainingTime--;
                countdownElement.innerHTML = formatTime(remainingTime);

                if (remainingTime <= 0) {
                    clearInterval(countdownInterval);
                    countdownElement.style.display = 'none';
                    verifyButton.disabled = true;
                }
            }, 1000);

            function formatTime(time) {
                var minutes = Math.floor(time / 60);
                var seconds = time % 60;
                return padNumber(minutes) + ":" + padNumber(seconds);
            }

            function padNumber(number) {
                return (number < 10 ? "0" : "") + number;
            }
        </script>
    @endif
@endsection
