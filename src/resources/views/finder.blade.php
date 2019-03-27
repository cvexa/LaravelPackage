<!DOCTYPE html>
    <html lang="en">
    <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
          <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
          <title>Search by keywords</title>
          <style media="screen">
              li{
                  overflow-wrap:break-word;
              }

              label{
                  font-weight: bolder;
              }

              .red-text{
                  color:#f00;
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
                <input type="text" class="form-control" name="search"  id="search" placeholder="keyword, sentence...." value="{{isset($searched)?$searched:''}}">
              </div>

              <div class="form-group">
                  <label for="extension">File extensions list them by comma [something, .xml]</label>
                  <label>skip files with extensions to search by content in</label>
                  <ul>
                      <li class="red-text">microsoft office files extensions like</li>
                      <li class="red-text">*.doc</li>
                      <li class="red-text">*.docx</li>
                      <li class="red-text">*.xlsx</li>
                  </ul>
                  <input type="text" class="form-control" name="extensions" id="extensions" value="{{isset($extensions)?$extensions:''}}" placeholder=".txt,.pdf...">
              </div>
              <hr />
              <div class="form-group" id="custom_path_wrapper">
                  <label for="path">1.Custom Path Application</label><br/>
                  <label>permission denied for</label>
                  <ul>
                      <li class="red-text">/vendor</li>
                      <li class="red-text">/</li>
                  </ul>
                  {{base_path()}}<input type="text" name="path" id="path" value="{{isset($customPath)?$customPath:''}}" placeholder="/something/something">
                  <br/><span class="red-text">OR</span>
              </div>

              <div class="form-group">
                <label for="location">2.Browse public folder</label>
                <select class="form-control" name="location" id="location">
                    <option value=0>all</option>
                    @foreach($publicDirectories as $dir)
                        <option value="{{$dir}}">{{$dir}}</option>
                    @endforeach
                </select>
                 <span class="red-text">OR</span>
              </div>

              <div class="form-group" id="custom_path_outside_wrapper">
                  <label for="outside">3.Custom Path outside the Application folder</label><br/>
                  <input type="text" name="outside" id="outside" value="{{isset($outside)?$outside:''}}" placeholder="/something/something">
              </div>
                <hr />
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
                          Time:{{$timeEnd}}<br />
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
                          Time:{{$timeEnd}}<br />
                          No Results
                      </div>
                  @endif
              @endif

              <button type="submit" class="btn btn-primary" style="width: 500px; margin: 0 auto; margin-top: 10px;" id="search-btn">Search</button>
         </form>
         <a href="{{route('find')}}"><button type="submit" class="btn btn-info" style="width: 500px; margin: 0 auto; margin-top: 10px;" id="reset-btn">Clear All</button></a>
        </div>
        <script type="text/javascript">
            $(function(){
                $('#path').on('keyup',function(){
                    if(this.value.length > 1){
                        $('#location').prop('disabled', 'disabled');
                        $('#outside').val('');
                        $('#outside').prop('disabled', 'disabled');
                    }else{
                        $('#location').prop('disabled', '');
                        $('#outside').prop('disabled', '');
                    }
                });

                $('#outside').on('keyup',function(){
                    if(this.value.length > 1){
                        $('#location').prop('disabled', 'disabled');
                        $('#path').val('');
                        $('#path').prop('disabled', 'disabled');
                    }else{
                        $('#location').prop('disabled', '');
                        $('#path').prop('disabled', '');
                    }
                });

                $('#custom_path_wrapper').hover(function(){
                    $('#path').prop('disabled', '');
                });

                $('#custom_path_outside_wrapper').hover(function(){
                    $('#outside').prop('disabled', '');
                });

                $('#location').change(function() {
                    $('#path').prop('disabled', 'disabled');
                    $('#outside').prop('disabled', 'disabled');
                });
            })
        </script>
    </body>
    </html>
