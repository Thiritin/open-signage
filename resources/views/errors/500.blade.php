@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))

<!-- This makes sure that we can fix production errors on unmanaged screens -->
<script>
    setTimeout(function(){
        window.location.reload(1);
    }, 10000);
</script>
