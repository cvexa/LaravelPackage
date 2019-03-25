<!DOCTYPE html>
    <html lang="en">
    <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
          <title>Search by keywords</title>
          <style media="screen">
              li{
                  overflow-wrap:break-word;
              }
          </style>
    </head>
        <body>

          <div style="width: 500px; margin: 0 auto; margin-top: 90px;">
            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

          <h3>Search in files</h3>

          <form action="{{route('find.me')}}" method="GET">
              @csrf
                @if ($errors->any())
                    <div class="alert alert-danger slide-on">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
              <div class="form-group">
                <label for="search">What to search</label>
                <input type="text" class="form-control" name="search"  placeholder="keyword, sentence...." value="{{isset($searched)?$searched:''}}">
              </div>

              <div class="form-group">
                  <label for="extension">File extensions list them by comma [something, .xml]</label>
                  <input type="text" class="form-control" name="extensions" value="{{isset($extensions)?$extensions:''}}" placeholder=".txt,.pdf...">
              </div>

              <div class="form-group">
                  <label for="extension">Custom Path Application</label><br/>
                  <label>permission denied for</label>
                  <ul>
                      <li style="color:#f00">/vendor</li>
                      <li style="color:#f00">/</li>
                  </ul>
                  <input type="text" class="form-control" name="path" value="{{isset($customPath)?$customPath:''}}" placeholder="/something/something">
              </div>

              <div class="form-group">
                <label for="location">Browse</label>
                <select class="form-control" name="location">
                    <option value=0>all</option>
                    @foreach($publicDirectories as $dir)
                        <option value="{{$dir}}">{{$dir}}</option>
                    @endforeach
                </select>
              </div>

              <div class="form-group">
                  <label for="extension">Search filter</label>
                  @if(isset($filter))
                      @if($filter > 0)
                          <input type="radio" name="filter" id="name" value=1 checked="checked"><label for="name">by file name</label>
                          <input type="radio" name="filter" id="content" value=0><label for="content">by file content</label>
                      @else
                          <input type="radio" name="filter" id="name" value=1><label for="name">by file name</label>
                          <input type="radio" name="filter" id="content" value=0 checked="checked"><label for="content">by file content</label>
                      @endif
                  @else
                      <input type="radio" name="filter" id="name" value=1 checked="checked"><label for="name">by file name</label>
                      <input type="radio" name="filter" id="content" value=0><label for="content">by file content</label>
                  @endif
              </div>
              @if(isset($output))
                  @if(!empty($output))
                      <div class="form-group alert alert-success">
                          Results:
                          <ol>
                          @foreach($output as $result)
                              <li>
                                  {{$result}}
                              </li>
                          @endforeach
                        </ol>
                      </div>
                  @else
                      <div class="form-group alert alert-info">
                          No Results
                      </div>
                  @endif
              @endif

              <button type="submit" class="btn btn-primary" style="width: 500px; margin: 0 auto; margin-top: 10px;">Search</button>
         </form>
         <a href="{{route('find')}}"><button type="submit" class="btn btn-info" style="width: 500px; margin: 0 auto; margin-top: 10px;">Clear All</button></a>
        </div>
    </body>
    </html>
