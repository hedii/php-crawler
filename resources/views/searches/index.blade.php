@extends('layouts.app')

@section('body-class'){{ 'searches-index' }}@stop

@section('content')
    <script>
        var userId = {{ Auth::user()->id }};
    </script>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">My searches</div>

                    <div class="panel-body" id="app">
                        <search-index></search-index>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
