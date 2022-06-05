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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

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
    </style>
</head>
<body>
<div class="flex-center position-ref" id="app">
    <div class="content">
        <div class="title mb-5 mt-5">
            Scraper
        </div>

        <div class="">
            <form action="/scrape-page" method="POST">
                @csrf
                <input class="form-control" name="video_url" placeholder="URL"/>
                <input class="form-control mt-3" name="filename" placeholder="Filename"/>
                <button type="submit" class="btn btn-success d-flex mt-3" style="font-weight: bold;">
                    Scrape
                </button>
            </form>
        </div>

        <h2 class="text-start mt-5 pt-3">In Process</h2>
        <input type="text" class="form-control" v-model="query" placeholder="Filter by name..." />
        <table class="table table-striped text-start">
            <thead>
            <tr>
                <th>Name</th>
                <th>Started At</th>
                <th>Resolution</th>
                <th>Progress</th>
                <th colspan="3"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="video in filteredVideos">
                <td>
                    <span
                      class="badge"
                      :class="{
                        'bg-secondary' : video.status === 'queued',
                        'bg-primary' : video.status === 'processing',
                        'bg-success' : video.status === 'done' || video.progress === 100,
                        'bg-danger' : video.status === 'error'
                      }"
                      style="position: relative; top: -1px;"
                    >
                      @{{ video.progress === 100 ? 'done' : video.status }}
                    </span>
                    &nbsp;
                    @{{ video.name }}
                </td>

                <td>@{{ video.started_at }}</td>
                <td>@{{ resolution(video) }}</td>
                <td>
                    <div class="progress" style="width: 12rem; font-weight: bold; position: relative; top: 5px;">
                        <div
                          class="progress-bar"
                          :class="{'bg-success' : video.progress === 100 }"
                          role="progressbar"
                          :title="video.progress + '%'"
                          :style="{ width: video.progress + '%' }"
                          aria-valuenow="0"
                          aria-valuemin="0"
                          aria-valuemax="100"
                        >
                            @{{ video.progress }}%
                        </div>
                    </div>
                </td>

                <td class="text-danger" @click="deleteVideo(video)">
                    <span style="height: 1rem; width: 1rem; display: inline-block; position: relative; top: -2px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </span>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-if="!filteredVideos.length">
            No results found.
        </div>
    </div>
</div>

<script>
  window.__INITIAL_STATE__ = @json(['videos' => $videos])
</script>

<script src="{{ mix('js/app.js') }}"></script>

</body>
</html>
