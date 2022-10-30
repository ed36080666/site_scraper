<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Laravel</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Styles -->
  <style>
    html, body {
      color: #636b6f;
      font-family: 'Nunito', sans-serif;
    }
  </style>
</head>
<body>
  <div class="flex justify-center relative my-10" id="app">
    <div class="max-w-5xl text-center w-full flex flex-col gap-10">
      <div class="text-6xl mb-5 mt-5">
        Scraper
      </div>
      <div>
        <content-scraper
          :logo-map="logoMap"
          csrf="{{ csrf_token() }}"
        />
      </div>
      <scrape-list-table
        :items="videos"
        @delete="onItemDelete"
      />
    </div>
  </div>

  <script>
    window.__INITIAL_STATE__ = @json([
      'videos' => $videos,
      'logo_map' => $logo_map
    ]);
  </script>

  <script src="{{ mix('js/app.js') }}"></script>

</body>
</html>
