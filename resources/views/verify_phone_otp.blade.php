@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Phone Verification') }}</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verify_phone_otp') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>

                                <div class="col-md-6">
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required autofocus>
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
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Verify') }}
                                    </button>
                                </div>
                            </div>

                            @if (isset($remainingTime) && $remainingTime > 0)
                                <div class="form-group row mt-3">
                                    <div class="col-md-6 offset-md-4">
                                        <p>Remaining Time: <span id="countdown">{{ gmdate('i:s', $remainingTime - time()) }}</span></p>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var remainingTime = parseInt(document.getElementById('countdown').innerHTML);

        var countdownInterval = setInterval(function() {
            remainingTime--;
            document.getElementById('countdown').innerHTML = formatTime(remainingTime);

            if (remainingTime <= 0) {
                clearInterval(countdownInterval);
                document.getElementById('countdown').style.display = 'none';
                window.location.href = "{{ route('home') }}";
            }
        }, 1000);

        function formatTime(time) {
            var minutes = Math.floor(time / 60).toString().padStart(2, '0');
            var seconds = (time % 60).toString().padStart(2, '0');
            return minutes + ':' + seconds;
        }
    </script>
@endsection
