<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Laravel</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"  integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Styles -->
  <style>
    html, body {
      background-color: #fff;
      color: #636b6f;
      font-family: 'Nunito', sans-serif;
      font-weight: 200;
      height: 100vh;
      margin: 0;
    }

    .flex-center {
      align-items: center;
      display: flex;
      justify-content: center;
    }

    .position-ref {
      position: relative;
    }

    .top-right {
      position: absolute;
      right: 10px;
      top: 18px;
    }

    .content {
      text-align: center;
      max-width: 1024px;
      width: 100%;
      padding: 16px;
    }

    .title {
      font-size: 84px;
    }

    .links > a {
      color: #636b6f;
      padding: 0 25px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: .1rem;
      text-decoration: none;
      text-transform: uppercase;
    }

    .in-progress {
      margin-top: 15px;
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      grid-column-gap: 10px;
    }

    .logo-wrap {
      display: flex;
      gap: 1rem;
      text-align: left;
      margin-bottom: 1rem;
    }

    .logo {
      width: auto;
      height: 100%;
      max-height: 30px;
      filter: grayscale(100%);
      opacity: 0.2;
      transition: all 0.15s ease;
    }

    .logo.--active {
      opacity: 1;
      filter: grayscale(0);
    }

    .streaming {
      background-image: repeating-linear-gradient(120deg, #cdd0d1, #cdd0d1 20px, #eee 20px, #eee 40px);
      color: #000;
      background-size: 5000%;
      animation: animatedBackground 200s linear infinite;
    }

    @keyframes animatedBackground {
      from {
        background-position: 0 0;
      }
      to {
        background-position: -100% 0;
      }
    }
  </style>
</head>
<body>
<div class="flex-center position-ref" id="app">
  <div class="content">
    <div class="title mb-5 mt-5">
      Scraper
    </div>

    {{-- TODO UNCOMMENT THIS NSFW --}}
{{--    <div class="logo-wrap">--}}
{{--      <a v-for="logo in logoMap" :href="'https://' + logo.base_url" target="_blank">--}}
{{--        <img--}}
{{--          class="logo"--}}
{{--          :class="{'--active': (videoUrl || '').includes(logo.base_url)}"--}}
{{--          :src="logo.src"--}}
{{--        />--}}
{{--      </a>--}}
{{--    </div>--}}
    <div class="mb-5">
      <form action="/scrape-page" method="POST">
        @csrf
        <input class="form-control" name="video_url" placeholder="URL" v-model="videoUrl"/>
        <input class="form-control mt-3" name="filename" placeholder="Filename"/>
        <button type="submit" class="btn btn-success d-flex mt-3" style="font-weight: bold;">
          Scrape
        </button>
      </form>
    </div>

    <div>
      <scrape-list-table
        :items="videos"
        @delete="onItemDelete"
      />
    </div>
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
