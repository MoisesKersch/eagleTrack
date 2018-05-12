<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
@include('layouts.header')
<body class="without-overflow">
    <div id="app">
        @include('layouts.nav')
        @yield('content')
        @include('addons.modal_clean');
        @include('addons.modal_alerta')
    </div>
</body>
  <footer>
    <!-- Scripts -->
    <!--<script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>-->
    <script>
        moment.locale('pt-br');
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/locale/pt-br.js"></script>
    <script type="text/javascript"> var ROOT = "{{ url('') }}"; </script>
    <script type="text/javascript"> 
        var CLLATITUDE = "{{ Auth::User()->cliente->cllatitude }}"; var CLLONGITUDE = "{{ Auth::User()->cliente->cllongitude }}"; 
    </script>
    <script src="{{ mix('js/layout/app.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

  </footer>
</html>
