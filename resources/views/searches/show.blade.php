@extends('layouts.app')

@section('body-class'){{ 'searches-show' }}@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Search for {{ $search->type }}s with entry point <a href="{{ $search->entrypoint }}" target="_blank">{{ str_limit($search->entrypoint, 50) }}</a></div>

                    <div class="panel-body" id="app">
                        <search-show></search-show>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
