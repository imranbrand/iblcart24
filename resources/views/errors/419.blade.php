@extends('errors.layout')

@section('code', '419')
@section('title', "Session expired")
@section('message', $exception->getMessage() ?: "Your session timed out. Please refresh the page and try again.")
