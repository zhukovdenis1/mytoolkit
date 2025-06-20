@extends('layouts.mtk')

@section('title', 'ESB')

@section('content')
    <h1>ESB</h1>
    <form action="" method="post">
        @csrf
        <textarea name="content" style="width: 100%; height: 100px">
CREATE TABLE `tokens` (
  `refresh_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `access_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        </textarea>
        <input type="submit" value=">">
    </form>
    <pre>{!! $content['json'] ?? '' !!}</pre>
    <hr />
    <pre>{!! $content['postman'] ?? '' !!}</pre>
@endsection
