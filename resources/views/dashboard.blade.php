@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-xl-3">
                <ul class="nav flex-column mb-4">
                    <li class="nav-item">
                        <router-link :to="{ name: 'dashboard' }"
                                     class="nav-link">
                            Dashboard
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{ name: 'searches.index' }"
                                     class="nav-link">
                            Searches
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{ name: 'account.edit' }"
                                     class="nav-link">
                            My account
                        </router-link>
                    </li>
                </ul>
            </div>
            <div class="col-lg-10 col-xl-9">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <transition name="fade">
                    <router-view></router-view>
                </transition>
            </div>
        </div>
    </div>
@endsection
