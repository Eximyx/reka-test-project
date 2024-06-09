<a href="{{url(is_null($task->image) ? '' : $task->getPathToImage().'/'. $task->image)}}">
    <img src="{{url(is_null($task->image) ? '' : $task->getPathToImage().'/thumbnail_'. $task->image)}}" alt="">
</a>
