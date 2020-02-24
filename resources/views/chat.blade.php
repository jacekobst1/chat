@extends('layouts.app')

@section('content')
    <chat-component
            :messages-prop="{{ $messages }}"
            :user-id-prop="{{ auth()->id() }}"
    >
    </chat-component>
@endsection
