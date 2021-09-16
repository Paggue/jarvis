@extends('jarvis-mail::layouts.mail')

@section('template_title')
    Confirmação de cadastro
@endsection

@section('title')
    Seja bem vindo {{$name}}!
@endsection

@section('text')
    Geramos a senha abaixo pra você, porém ela <br>vai servir somente  para o primeiro acesso.<br>
    Logo após realizar o login, você poderá colocar a senha que preferir.
@endsection

@section('token')
    {{$password}}
@endsection

@section('button_url')
    {{config('jarvis.app.url_site')}}
@endsection

@section('button_text')
    Entrar
@endsection

