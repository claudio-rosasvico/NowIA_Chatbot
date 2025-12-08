@extends('layouts.panel')

@section('content')
  @livewire('chat-widget', ['conversationId' => null])
@endsection
