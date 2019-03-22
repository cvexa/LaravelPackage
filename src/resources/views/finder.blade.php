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

          <h3>Search keyword in files</h3>

          <form action="{{route('find.me')}}" method="POST">
              @csrf
              <div class="form-group">
                <label for="exampleFormControlInput1">What to search</label>
                <input type="text" class="form-control" name="search"  placeholder="keyword" value="{{isset($searched)?$searched:''}}">
              </div>

              <div class="form-group">
                <label for="exampleFormControlInput1">Where to search</label>
                <select class="" name="location">
                    <option value=0>all</option>
                    @foreach($publicDirectories as $dir)
                        <option value="{{$dir}}">{{$dir}}</option>
                    @endforeach
                </select>
              </div>

              @if(isset($output) && !empty($output))
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
                  <div class="form-group alert alert-success">
                      No Results
                  </div>
              @endif

              <button type="submit" class="btn btn-primary" style="width: 500px; margin: 0 auto; margin-top: 10px;">Search</button>
         </form>
        </div>
    </body>
    </html>
