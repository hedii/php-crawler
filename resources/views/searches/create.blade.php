@extends('layouts.app')

@section('body-class'){{ 'searches-create' }}@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Start a new search</div>

                    <div class="panel-body" id="app">
                        <search-create></search-create>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
