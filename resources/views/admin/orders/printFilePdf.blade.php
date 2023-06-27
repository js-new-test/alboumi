<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Print Files</title>
    <style>
        body {
            border: 1px solid #000;
        }
        .margin{
            margin-top:5%;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div style="  display: block; float: right; margin-right:0px; position: relative;margin-left: 350px;font-size: 15px !important;">
            @if(!empty($filesToDownload))
                @foreach($filesToDownload as $file)
                    <img src="{{ base_path('design-tool/data/printfiles/'.$file) }}" height="{{ $imgHeight }}" width="{{ $imgWidth }}" class="margin">
                    <br>
                @endforeach
            @endif
        </div>
    </div>
</body>

</html>
