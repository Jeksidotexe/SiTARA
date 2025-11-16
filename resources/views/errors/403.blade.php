@extends('errors::layout')

{{-- Ikon untuk akses ditolak --}}
@section('icon', 'lock')

@section('code', '403')
@section('title', 'Akses Ditolak')

@section('message', 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.')
